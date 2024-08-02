<?php
/**
 * Starken Pro carrier model
 *
 * @category     SoftwareAgil
 * @package      SoftwareAgil_StarkenPro
 * @author       SoftwareAgil (info@softwareagil.com)
 * @copyright    Copyright (c) 2020 SoftwareAgil (www.softwareagil.com)
 */
namespace SoftwareAgil\StarkenPro\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Pricing\Helper\Data;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Shipping\Model\Shipment\Request;
use SoftwareAgil\StarkenPro\Helper\Data as HelperData;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\State;
use Magento\Sales\Model\Order\Shipment\TrackFactory;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Psr\Log\LoggerInterface;
use Magento\Shipping\Model\Rate\ResultFactory;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use SoftwareAgil\StarkenPro\Model\ApiConnector\Client;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Framework\App\Area;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Mail\Template\TransportBuilder;

class Carrier extends \SoftwareAgil\StarkenPro\Model\Carrier\AbstractCarrier implements
    \Magento\Shipping\Model\Carrier\CarrierInterface
{
    protected $_code = 'starkenpro';

    protected $_isFixed = true;

    protected $_rateResultFactory;

    protected $_rateMethodFactory;

    protected $_client;

    /**
     * @var CheckoutSession
     */
    protected $_checkoutSession;

    /**
     * @var PricingHelper
     */
    protected $_pricingHelper;

    /**
     * @var PricingHelper
     */
    protected $_cache;

    /**
     * @var Json
     */
    protected $_json;

    /**
     * @var Data
     */
    protected $_helper;

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var ProductRepositoryInterfac
     */
    protected $_productRepository;

    /**
     * @var State
     */
    protected $_state;

    /**
     * @var ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var TrackFactory
     */
    protected $_trackFactory;

    /**
     * @var CommuneFactory
     */
    protected $_communeFactory;

    /**
     * @var AddressRepositoryInterface
     */
    protected $_addressRepository;

    protected $_paymentTypes = [];

    protected $_deliveryNames = [];

    protected $_serviceNames = [];

    /**
     * @var AccountFactory
     */
    protected $_accountFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var StateInterface
     */
    protected $_inlineTranslation;

    /**
     * @var TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * Constructor
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory $rateErrorFactory
     * @param LoggerInterface $logger
     * @param ResultFactory $rateResultFactory
     * @param MethodFactory $rateMethodFactory
     * @param Client $client
     * @param Session $checkoutSession
     * @param Data $pricingHelper
     * @param CacheInterface $cache
     * @param Json $json
     * @param Data $helperData
     * @param ScopeConfigInterface $scopeConfig
     * @param ProductRepositoryInterface $productRepository
     * @param State $state
     * @param ManagerInterface $messageManager
     * @param TrackFactory $trackFactory
     * @param CommuneFactory $communeFactory
     * @param AddressRepositoryInterface $addressRepository
     * @param AccountFactory $accountFactory
     * @param StoreManagerInterface $storeManager
     * @param StateInterface $inlineTranslation
     * @param TransportBuilder $transportBuilder
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        ResultFactory $rateResultFactory,
        MethodFactory $rateMethodFactory,
        Client $client,
        CheckoutSession $checkoutSession,
        Data $pricingHelper,
        CacheInterface $cache,
        Json $json,
        HelperData $helperData,
        ProductRepositoryInterface $productRepository,
        State $state,
        ManagerInterface $messageManager,
        TrackFactory $trackFactory,
        CommuneFactory $communeFactory,
        AddressRepositoryInterface $addressRepository,
        AccountFactory $accountFactory,
        StoreManagerInterface $storeManager,
        StateInterface $inlineTranslation,
        TransportBuilder $transportBuilder,
        array $data = []
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->_client = $client;
        $this->_checkoutSession = $checkoutSession;
        $this->_pricingHelper = $pricingHelper;
        $this->_cache = $cache;
        $this->_json = $json;
        $this->_helper = $helperData;
        $this->_scopeConfig = $scopeConfig;
        $this->_productRepository = $productRepository;
        $this->_state = $state;
        $this->_messageManager = $messageManager;
        $this->_trackFactory = $trackFactory;
        $this->_communeFactory = $communeFactory;
        $this->_addressRepository = $addressRepository;
        $this->_accountFactory = $accountFactory;
        $this->_storeManager = $storeManager;
        $this->_inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder;

        $this->_paymentTypes = [
            self::PAYMENT_TYPE_ON_ORIGIN => [
                'id' => 1,
                'codigo_dls' => "2",
                'title' => __('Origin')
            ],
            self::PAYMENT_TYPE_ON_ARRIVAL => [
                'id' => 2,
                'codigo_dls' => "3",
                'title' => __('Destination')
            ]
        ];
        $this->_deliveryNames = [
            self::DELIVERY_TYPE_AGENCY => [
                'id' => 1,
                'codigo_dls' => "1",
                'title' => __('Agency')
            ],
            self::DELIVERY_TYPE_AGENCY2 => [
                'id' => 1,
                'codigo_dls' => "1",
                'title' => __('Agency')
            ],
            self::DELIVERY_TYPE_RESIDENCE => [
                'id' => 2,
                'codigo_dls' => "2",
                'title' => __('Residence')
            ]
        ];
        $this->_serviceNames = [
            self::METHOD_NORMAL => [
                'id' => 1,
                'codigo_dls' => "0",
                'title' => __('Normal')
            ],
            self::METHOD_EXPRESS => [
                'id' => 2,
                'codigo_dls' => "1",
                'title' => __('Express')
            ]
        ];

        $this->_client->setEndPointBase($this->getConfigData('gateway_url'));
        $this->_client->setApiToken($this->getConfigData('password'));
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * Get configuration data of carrier
     *
     * @param string $type
     * @param string $code
     * @return array|false
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function getCode($type, $code = '')
    {
        $codes = [
            'method' => [
                self::METHOD_NORMAL => __('Normal'),
                self::METHOD_EXPRESS => __('Express')
            ],
            'option' => []
        ];

        if ($this->_hasCheckingAccounts()) {
            foreach ($this->_serviceNames as $mk => $mv) {
                foreach ($this->_deliveryNames as $snk => $snv) {
                    if ($snk == self::DELIVERY_TYPE_AGENCY2) continue;
                    $key = strtolower($mk . '_' . $snk . '_' . $this->_paymentTypes[self::PAYMENT_TYPE_ON_ORIGIN]['codigo_dls']);
                    $title = $mv['title'] . ' ' . __(' to ') . ' ' . $snv['title'];
                    $codes['option'][$key] = $title;
                }
            }
        }
        foreach ($this->_serviceNames as $mk => $mv) {
            foreach ($this->_deliveryNames as $snk => $snv) {
                if ($snk == self::DELIVERY_TYPE_AGENCY2) continue;
                $key = strtolower($mk . '_' . $snk . '_' . $this->_paymentTypes[self::PAYMENT_TYPE_ON_ARRIVAL]['codigo_dls']);
                $title = $mv['title'] . ' ' . __(' to ') . ' ' . $snv['title'] . ' - ' . __('Pay on') . ' ' . $this->_paymentTypes[self::PAYMENT_TYPE_ON_ARRIVAL]['title'];
                $codes['option'][$key] = $title;
            }
        }

        if (!isset($codes[$type])) {
            return false;
        } elseif ('' === $code) {
            return $codes[$type];
        }

        if (!isset($codes[$type][$code])) {
            return false;
        } else {
            return $codes[$type][$code];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        if ($request->getDestCountryId() != 'CL' && $this->_state->getAreaCode() == "frontend") {
            if ($request->getDestCountryId() != '') {
                $this->_helper->log('This carrier only works for Chile country');
            }
            return false;
        }

        $weightUnit = $this->_scopeConfig->getValue('general/locale/weight_unit');
        if ($weightUnit != 'kgs') {
            $this->_helper->log('Invalid unit of weight. Kilograms is required in Magento Catalog configuration');
            return false;
        }

        $allowedMethods = $this->_getAllowedOptions();
        if (!count($allowedMethods)) {
            $this->_helper->log('Invalid allowed methods in delivery method system configuration');
            return false;
        }

        $data = [];
        try {
            $phpInput = file_get_contents('php://input');
            if (substr($phpInput, 0, 1) == "{" || substr($phpInput, 0, 1) == "[") {
                $data = $this->_json->unserialize($phpInput);
            } else {
                parse_str(urldecode($phpInput), $data);
            }
        } catch (Exception $e) {}
        $cityId = $this->_getCityId($data);
        if (!$cityId) {
            return false;
        }

        $requestParams = $this->_getRequestParams($request, $cityId);

        if (!$requestParams) {
            $result = $this->_rateResultFactory->create();
            $method = $this->_rateMethodFactory->create();
            $method->setCarrier($this->_code);
            $method->setCarrierTitle($this->getConfigData('title'));
            $method->setMethod('missing_product_data');
            $method->setMethodTitle(__('Pending payment for shipment'));
            $method->setPrice(0);
            $method->setCost(0);
            $result->append($method);
            $this->_helper->log('Pending payment shipping rate forced because one or some products have not set dimensions or weight');
            return $result;
        }

        return $this->_getQuotes($requestParams);
    }

    /**
     * getAllowedMethods
     *
     * @param array
     */
    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('name')];
    }

    /**
     * Get City ID extension attribute value
     *
     * Returns cached response or null
     *
     * @param array $data
     * @return empty|string
     */
    protected function _getCityId($data)
    {
        $shippingAddress = [];
        $communeId = '';
        if (isset($data['addressId'])) {
            $addressModel = $this->_addressRepository->getById($data['addressId']);
            if ($addressModel->getId()) {
                $extAttributes = $addressModel->getCustomAttributes();
                if (isset($extAttributes["commune_id"])) {
                    $communeId = $extAttributes["commune_id"]->getValue();
                }
                //$communeId = $extAttributes->getCommuneId();
            }
        }
        if (!$communeId && isset($data['address'])) {
            $shippingAddress = $data['address'];
        }
        if (!$communeId && !count($shippingAddress) && isset($data['addressInformation']['shipping_address'])) {
            $shippingAddress = $data['addressInformation']['shipping_address'];
        }
        if (!$communeId && !count($shippingAddress) && isset($data['order']['shipping_address'])) {
            $shippingAddress = $data['order']['shipping_address'];
        }
        if (!$communeId && !count($shippingAddress) && isset($data['order']['billing_address'])) {
            $shippingAddress = $data['order']['billing_address'];
        }
        if (!$communeId && !count($shippingAddress)) {
            $quote = $this->_checkoutSession->getQuote();
            $quoteShippingAddress = $quote->getShippingAddress();
            if ($quoteShippingAddress->getCommuneId()) {
                $shippingAddress['custom_attributes'] = [
                    0 => [
                        'attribute_code' => 'commune_id',
                        'value' => $quoteShippingAddress->getCommuneId()
                    ]
                ];
            }
        }
        if (!$communeId && !isset($shippingAddress)) {
            return false;
        }
        if (!$communeId) {
            $customAttributes = [];
            if (isset($shippingAddress['custom_attributes'])) {
                $customAttributes = $shippingAddress['custom_attributes'];
            } elseif (isset($shippingAddress['customAttributes'])) {
                $customAttributes = $shippingAddress['customAttributes'];
            } elseif (isset($shippingAddress['commune_id'])) {
                $communeId = $shippingAddress['commune_id'];
            }

            $cityId = '';
            foreach ($customAttributes as $k => $ca) {
                if ((string)$k == "commune_id" && !is_array($ca)) {
                    $communeId = $ca;
                    break;
                }
                if (isset($ca['attribute_code']) && $ca['attribute_code'] == "commune_id") {
                    $communeId = $ca['value'];
                    break;
                }
            }
        }
        if ($communeId) {
            $communeModel = $this->_communeFactory->create()->load($communeId, 'sp_commune_id');
            if ($communeModel->getCityId()) {
                $cityId = $communeModel->getCityId();
            }
        }

        return $cityId;
    }

    /**
     * Get request parameters
     *
     * Returns cached response or null
     *
     * @param RateRequest $request
     * @param string $cityId
     * @return array|boolean
     */
    protected function _getRequestParams($request, $cityId)
    {
        $quoteItems = $this->getAllItems($request);
        $weightAcum = 0;
        $dimensionMax = 0;
        $volumeAcum = 0;
        $qtyAcum = 0;
        foreach ($quoteItems as $item) {
            $product = $this->_productRepository->get($item->getProduct()->getSku());
            $productWeight = (float)$product->getWeight();
            $productWidth = (float)$product->getSpDimensionsWidth();
            $productHeight = (float)$product->getSpDimensionsHeight();
            $productLength = (float)$product->getSpDimensionsLength();
            if ($productWeight == 0 || $productWidth == 0 || $productHeight == 0 || $productLength == 0) {
                //return false;
            }
            $qtyAcum += $item->getQty();
            $weightAcum += $item->getQty() * $productWeight;
            $volumeAcum += $item->getQty() * $productWidth * $productHeight * $productLength;
            if ($productWidth > $dimensionMax) {
                $dimensionMax = $productWidth;
            }
            if ($productHeight > $dimensionMax) {
                $dimensionMax = $productHeight;
            }
            if ($productLength > $dimensionMax) {
                $dimensionMax = $productLength;
            }
        }

        if ($weightAcum == 0 || $dimensionMax == 0) {
            return false;
        }

        if ($qtyAcum > 1) {
            $dimension1 = round($dimensionMax, 2);
            $dimension2 = round(sqrt((2/3)*$volumeAcum/$dimension1), 2);
            $dimension3 = round($volumeAcum/$dimension1/$dimension2, 2);
        } else {
            $dimension1 = $productWidth;
            $dimension2 = $productHeight;
            $dimension3 = $productLength;
        }

        $checkingAccount = $this->getConfigData('checking_account');
        $checkingAccountArray = [];
        if ($checkingAccount) {
            $checkingAccountArray = explode('-', $checkingAccount);
        }

        $communeId = $this->getConfigData('commune_origin');
        $communeModel = $this->_communeFactory->create()->load($communeId, 'sp_commune_id');
        $originSpCityId = 0;
        if ($communeModel->getCityId()) {
            $originSpCityId = $communeModel->getCityId();
        }

        $requestParams = [
            'origen' => $originSpCityId,
            'destino' => $cityId,
            'run' => $this->getConfigData('rut'),
            'bulto' => $this->getConfigData('package'),
            'alto' => $dimension1,
            'ancho' => $dimension2,
            'largo' => $dimension3,
            'kilos' => $weightAcum,
            'todas_alternativas' => true
        ];
        if (isset($checkingAccountArray[0])) {
            $requestParams['ctacte'] = $checkingAccountArray[0];
            $requestParams['ctacte_dv'] = $checkingAccountArray[1];
        }

        return $requestParams;
    }

    /**
     * Get Shipping Label request parameters
     *
     * Returns cached response or null
     *
     * @param Shipment $shipment
     * @return array|boolean
     */
    protected function _getAssociatedEntityArray($elms, $elmValue)
    {
        foreach ($elms as $k => $v) {
            if (strtoupper($elmValue) == strtoupper($k)) {
                return [
                    "id" => $v["id"],
                    "codigo_dls" => $v["codigo_dls"],
                    "nombre" => $v["title"],
                    "descripcion" => $v["title"]
                ];
            }
        }

        return [];
    }

    /**
     * Get Shipping Label request parameters
     *
     * Returns cached response or null
     *
     * @param Shipment $shipment
     * @return array|boolean
     */
    protected function _getShippingLabelRequestParams($shipment)
    {
        $order = $shipment->getOrder();
        $shippingMethod = $order->getShippingMethod();
        if (strpos($shippingMethod, "missing_product_data") !== false) {
            return false;
        }
        $shippingMethodArray = explode("_", $shippingMethod);
        $shippingAddress = $order->getShippingAddress();
        if (!$shippingAddress) {
            return false;
        }
        if (!$shippingAddress->getCommuneId()) {
            return false;
        }
        $quoteItems = $this->getAllItems(null, $order);
        $weightAcum = 0;
        $dimensionMax = 0;
        $volumeAcum = 0;
        $packageContent = [];
        $qtyAcum = 0;
        foreach ($quoteItems as $item) {
            $product = $this->_productRepository->get($item->getSku());
            $productWeight = (float)$product->getWeight();
            $productWidth = (float)$product->getSpDimensionsWidth();
            $productHeight = (float)$product->getSpDimensionsHeight();
            $productLength = (float)$product->getSpDimensionsLength();
            if ($productWeight == 0 || $productWidth == 0 || $productHeight == 0 || $productLength == 0) {
                //return false;
            }
            $qtyAcum += $item->getQtyOrdered();
            $weightAcum += $item->getQtyOrdered() * $productWeight;
            $volumeAcum += $item->getQtyOrdered() * $productWidth * $productHeight * $productLength;
            if ($productWidth > $dimensionMax) {
                $dimensionMax = $productWidth;
            }
            if ($productHeight > $dimensionMax) {
                $dimensionMax = $productHeight;
            }
            if ($productLength > $dimensionMax) {
                $dimensionMax = $productLength;
            }
            $packageContent[] = $product->getName();
        }

        if ($weightAcum == 0 || $dimensionMax == 0) {
            return false;
        }

        if ($qtyAcum > 1) {
            $dimension1 = round($dimensionMax, 2);
            $dimension2 = round(sqrt((2/3)*$volumeAcum/$dimension1), 2);
            $dimension3 = round($volumeAcum/$dimension1/$dimension2, 2);
        } else {
            $dimension1 = $productWidth;
            $dimension2 = $productHeight;
            $dimension3 = $productLength;
        }

        $addressStreet = $shippingAddress->getStreet();
        $checkingAccountWithSep = $this->getConfigData('checking_account');
        $checkingAccountArray = [];
        if ($checkingAccountWithSep) {
            $checkingAccountArray = explode('-', $checkingAccountWithSep);
        }

        $requestParams = [
            "id" => 0,
            "tipo_entrega" => $this->_getAssociatedEntityArray($this->_deliveryNames, $shippingMethodArray[2]),
            "tipo_pago" => $this->_getAssociatedEntityArray($this->_paymentTypes, $shippingMethodArray[3]),
            "tipo_servicio" => $this->_getAssociatedEntityArray($this->_serviceNames, $shippingMethodArray[1]),
            "valor" => (int)$order->getBaseShippingAmount(),
            "codigo_agencia_origen" => $this->getConfigData('agency_origin'),
            "codigo_agencia_destino" => (int)$shippingAddress->getAgencyId(),
            "destinatario_rut" => $shippingAddress->getRut(),
            "destinatario_nombres" => $shippingAddress->getFirstname(),
            "destinatario_paterno" => $shippingAddress->getLastname(),
            "destinatario_direccion" => $addressStreet[0],
            "destinatario_numeracion" => (isset($addressStreet[1]))?$addressStreet[1]:"",
            "destinatario_departamento" => $shippingAddress->getRegion(),
            "destinatario_codigo_comuna" => $shippingAddress->getCommuneId(),
            "destinatario_telefono" => $shippingAddress->getTelephone(),
            "destinatario_email" => $shippingAddress->getEmail(),
            "contenido" => implode(", ", $packageContent),
            "kilos_total" => $weightAcum,
            "valor_declarado" => (int)$order->getBaseGrandTotal() - (int)$order->getBaseShippingAmount(),
            "orden_flete" => 0,
            "estado" => "ORIGEN",
            "impresiones" => 0,
            "encargos" => [
                [
                    "tipo_encargo" => ($volumeAcum <= 2250 && $weightAcum <= 0.3) ? 'SOBRE' : 'BULTO',
                    "descripcion" => implode(", ", $packageContent),
                    "kilos" => $weightAcum,
                    "alto" => $dimension1,
                    "ancho" => $dimension2,
                    "largo" => $dimension3,
                    "fragil" => "true",
                    "nuevo" => "true"
                ]
            ]
        ];

        if ($requestParams['valor_declarado'] >= 50000) {
            $requestParams['encargos'][] = [
                "tipo_documento" => [
                    "id" => 6,
                    "codigo_dls" => "1686",
                    "nombre" => "Orden de compra",
                    "descripcion" => "Orden de compra"
                ],
                "tipo_encargo" => 'DOCUMENTO',
                "descripcion" => implode(", ", $packageContent),
                "numero_documento" => 8
            ];
        }

        if (isset($checkingAccountArray[0])) {
            $requestParams["cuenta_corriente"] = $checkingAccountArray[0];
        }
        if ($this->getConfigData('cost_center')) {
            $requestParams["centro_costo"] = $this->getConfigData('cost_center');
        }

        //Removing cuenta_corriente and centro_costo when tipo_pago = 3
        if ($shippingMethodArray[3] == "3") {
            if (isset($requestParams["cuenta_corriente"])) {
                unset($requestParams["cuenta_corriente"]);
            }
            if (isset($requestParams["centro_costo"])) {
                unset($requestParams["centro_costo"]);
            }
        }

        return $requestParams;
    }

    /**
     * Return items for further shipment rate evaluation. We need to pass children of a bundle instead passing the
     * bundle itself, otherwise we may not get a rate at all (e.g. when total weight of a bundle exceeds max weight
     * despite each item by itself is not)
     *
     * @param RateRequest $request
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @api
     */
    public function getAllItems(RateRequest $request = null, $order = null)
    {
        $items = [];
        $obj = $request;
        if (!$obj) {
            $obj = $order;
        }
        if ($obj->getAllItems()) {
            foreach ($obj->getAllItems() as $item) {
                /* @var $item \Magento\Quote\Model\Quote\Item */
                if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
                    // Don't process children here - we will process (or already have processed) them below
                    continue;
                }

                if ($item->getHasChildren() && $item->isShipSeparately()) {
                    foreach ($item->getChildren() as $child) {
                        if (!$child->getFreeShipping() && !$child->getProduct()->isVirtual()) {
                            $items[] = $child;
                        }
                    }
                } else {
                    // Ship together - count compound item as one solid
                    $items[] = $item;
                }
            }
        }

        return $items;
    }

    /**
     * Get quotes from web services or cache
     *
     * @param array $requestParams
     * @return string
     */
    protected function _getQuotes($requestParams)
    {
        $result = $this->_rateResultFactory->create();
        $response = $this->_getCachedQuotes($requestParams);
        if ($response == null) {
            $response = $this->_client->apiCall('quote/cotizador-multiple', $requestParams, 'POST');
            if ($response['success']) {
                if (isset($response['items']) && !isset($response['items']['error'])) {
                    $this->_setCachedQuotes($requestParams, $response);
                } else {
                    unset($response['success']);
                    $response['error'] = $response['items']['error'];
                    $response['code'] = $response['items']['status'];
                }
            }
        } else {
            $this->_helper->log('Shipping rates recovered from cache');
        }

        if (!$response['success']) {
            $error = $this->_rateErrorFactory->create();
            $error->setCarrier($this->_code);
            $error->setCarrierTitle($this->getConfigData('title'));
            $error->setErrorMessage($response['error'] . " (" . $response['code'] . ").");
            $result->append($error);
            return $result;
        }

        if (!isset($response['items']['alternativas'])) {
            $error = $this->_rateErrorFactory->create();
            $error->setCarrier($this->_code);
            $error->setCarrierTitle($this->getConfigData('title'));
            $error->setErrorMessage($this->getConfigData('specificerrmsg'));
            $result->append($error);
            return $result;
        }

        $ratesFlag = false;
        $allowedMethods = $this->_getAllowedOptions();
        foreach($response['items']['alternativas'] as $rate) {
            $methodCode = strtolower($this->_serviceNames[$rate['servicio']]['title']).'_'.strtolower($this->_deliveryNames[$rate['entrega']]['title']).'_'.$rate['codigo_tipo_pago'];
            if (!in_array($methodCode, $allowedMethods)) {
                $methodCodeTemp = preg_replace('/agency/', 'agencia', $methodCode);
                if (!in_array($methodCodeTemp, $allowedMethods)) {
                    continue;
                }
            }

            $ratesFlag = true;
            $shippingPrice = $rate['precio'];
            $method = $this->_rateMethodFactory->create();

            $method->setCarrier($this->_code);
            $method->setCarrierTitle($this->getConfigData('title'));

            if ($rate['codigo_tipo_pago'] == self::PAYMENT_TYPE_ON_ARRIVAL) {
                $methodTitle = __($this->_serviceNames[$rate['servicio']]['title']).__(' to ').__($this->_deliveryNames[$rate['entrega']]['title']).' - '.__('Pay on').' '.$this->_paymentTypes[$rate['codigo_tipo_pago']]['title']." (".$this->_pricingHelper->currency($shippingPrice, true, false).")";
                $shippingPrice = 0;
            } else {
                if ($rate['codigo_tipo_pago'] == 2) {
                    $methodTitle = __($this->_serviceNames[$rate['servicio']]['title']).__(' to ').__($this->_deliveryNames[$rate['entrega']]['title']);
                } else {
                    $methodTitle = __($this->_serviceNames[$rate['servicio']]['title']).__(' to ').__($this->_deliveryNames[$rate['entrega']]['title']).' - '.__('Pay on').' '.$this->_paymentTypes[$rate['codigo_tipo_pago']]['title'];
                }
            }
            $method->setMethod($methodCode);
            $method->setMethodTitle($methodTitle);

            $method->setPrice($shippingPrice);
            $method->setCost($shippingPrice);

            $result->append($method);
        }

        if (!$ratesFlag) {
            $error = $this->_rateErrorFactory->create();
            $error->setCarrier($this->_code);
            $error->setCarrierTitle($this->getConfigData('title'));
            $error->setErrorMessage($this->getConfigData('specificerrmsg'));
            $result->append($error);
            return $result;
        }

        return $result;
    }

    /**
     * Returns cache key for some request to carrier quotes service
     *
     * @param array $requestParams
     * @return string
     */
    protected function _getQuotesCacheKey($requestParams)
    {
        if (is_array($requestParams)) {
            $requestParams = implode(
                ',',
                array_merge([$this->getCarrierCode()], $this->_getAllowedOptions(), array_keys($requestParams), $requestParams)
            );
        }

        return crc32($requestParams);
    }

    /**
     * Sets received carrier quotes to cache
     *
     * @param array $requestParams
     * @param string $response
     * @return \SoftwareAgil\StarkenPro\Model\Carrier\AbstractCarrier
     */
    protected function _setCachedQuotes($requestParams, $response)
    {
        $key = $this->_getQuotesCacheKey($requestParams);
        $this->_cache->save($this->_json->serialize($response), $key, [$this->getCarrierCode()], 300);

        return $this;
    }

    /**
     * Checks whether some request to rates have already been done, so we have cache for it
     * Used to reduce number of same requests done to carrier service during one session
     *
     * Returns cached response or null
     *
     * @param array $requestParams
     * @return null|string
     */
    protected function _getCachedQuotes($requestParams)
    {
        $key = $this->_getQuotesCacheKey($requestParams);
        if ($this->_cache->load($key)) {
            return $this->_json->unserialize($this->_cache->load($key));
        } else {
            return null;
        }
    }

    /**
     * Check if carrier has shipping label option available
     *
     * @return boolean
     */
    public function isShippingLabelsAvailable()
    {
        return false;
    }

    /**
     * Do first request to shipment label to get shipment label ID
     *
     * @param Request $request
     * @return \Magento\Framework\DataObject
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function generateShippingLabelId($shipment)
    {
        $requestParams = $this->_getShippingLabelRequestParams($shipment);
        if (!$requestParams) {
            throw new LocalizedException(__('The shipping label cannot be generated for this order because its shipping rate was not calculated due to lack of information on its products or shipping address.'));
            return $this;
        }
        $response = $this->_client->apiCall('emision/emision', $requestParams, 'POST');
        if ($response['success']) {
            if (isset($response['items']) && !isset($response['items']['error'])) {
                $shipment->setSaSpShippingLabelId($response['items']['id']);
                $shipment->setSaSpFreightOrderContent($this->_json->serialize($response['items']));
                $shipment->save();
                $this->_messageManager->addSuccessMessage(__('You created the shipping label request #%1.', $response['items']['id']));
                return $this;
            } else {
                unset($response['success']);
                if (isset($response['items'])) {
                    $response['error'] = $response['items']['error'];
                    $response['code'] = $response['items']['status'];
                    throw new LocalizedException(__($response['items']['error']." (".$response['items']['status'].")."));
                } else {
                    $response['error'] = $response['error'];
                    $response['code'] = $response['code'];
                    throw new LocalizedException(__($response['error']." (".$response['code'].")."));
                }
            }
        }
        throw new LocalizedException(__('Shipping label could not be created.'));
    }

    /**
     * Do second request to get shipment label PDF document
     *
     * @param Request $request
     * @return \Magento\Framework\DataObject
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function generateShippingLabel($order)
    {
        foreach ($order->getShipmentsCollection() as $shipment) {
            if (!$shipment->getSaSpShippingLabelId()) continue;
            $response = $this->_client->apiCall('emision/consulta/' . $shipment->getSaSpShippingLabelId(), [], 'GET');
            if ($response['success']) {
                if (isset($response['items']) && !isset($response['items']['error'])) {
                    if ($response['items']['etiqueta']) {
                        $pdfContent = file_get_contents($response['items']['etiqueta']);
                        $shipment->setShippingLabel($pdfContent);
                        $this->_messageManager->addSuccessMessage(__('You created the shipping label PDF document for order %1.', $order->getIncrementId()));
                    }
                    if ($response['items']['orden_flete']) {
                        $shipment->setSaSpFreightOrder($response['items']['orden_flete']);
                        if (!count($shipment->getAllTracks())) {
                            $data = [
                                'carrier_code' => "starkenpro",
                                'title' => "Starken",
                                'number' => $shipment->getSaSpFreightOrder()
                            ];
                            $track = $this->_trackFactory->create()->addData($data);
                            $shipment->addTrack($track);
                        }
                        $this->updateShipTrackingInfo($order);
                        $this->_messageManager->addSuccessMessage(__('You created the first shipping tracking records for order %1.', $order->getIncrementId()));
                    }
                    $shipment->setSaSpFreightOrderContent($this->_json->serialize($response['items']));
                    $shipment->save();
                    if ($response['items']['orden_flete']) {
                        try {
                            $this->sendTrackingNotification($order, $shipment->getId(), $response['items']['orden_flete']);
                        } catch (Exception $e) {
                            $this->_messageManager->addErrorMessage(__('Error sending email notification. Please make sure that sending emails is enabled.'));
                        }
                    }
                } else {
                    unset($response['success']);
                    $response['error'] = $response['items']['error'];
                    $response['code'] = $response['items']['status'];
                    $this->_messageManager->addErrorMessage(__($response['items']['error']." (".$response['items']['status'].")."));
                }
            }

            return $this;
        }
    }

    /**
     * Do request to get shipment tracking info
     *
     * @param Request $request
     * @return \Magento\Framework\DataObject
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateShipTrackingInfo($order)
    {
        foreach ($order->getShipmentsCollection() as $shipment) {
            if (!$shipment->getSaSpFreightOrder()) continue;
            $response = $this->_client->apiCall('tracking/orden-flete/of/' . $shipment->getSaSpFreightOrder(), [], 'GET');
            if ($response['success']) {
                if (isset($response['items']) && !isset($response['items']['error'])) {
                    $shipment->setSaSpTrackingContent($this->_json->serialize($response['items']));
                    $shipment->save();
                } else {
                    unset($response['success']);
                    if (isset($response['items'])) {
                        $response['error'] = $response['items']['error'];
                        $response['code'] = $response['items']['status'];
                        $this->_messageManager->addErrorMessage(__($response['items']['error']." (".$response['items']['status'].")."));
                    } else {
                        $response['error'] = $response['error'];
                        $response['code'] = $response['code'];
                        $this->_messageManager->addErrorMessage(__($response['error']." (".$response['code'].")."));
                    }
                }
            } else {
                $response['error'] = $response['error'];
                $response['code'] = $response['code'];
                $this->_messageManager->addErrorMessage(__($response['error']." (".$response['code'].")."));
            }

            return $this;
        }
    }


    /**
     * Check if carrier has shipping tracking option available
     *
     * @return bool
     */
    public function isTrackingAvailable()
    {
        return true;
    }

    /**
     * Check if carrier has shipping label option available
     *
     * @return bool
     */
    public function getTrackingInfo($trackingNumber)
    {
        $tracking = $this->_trackFactory->create();

        $url = 'https://www.starken.cl/seguimiento'; // this is the tracking URL of stamps.com, replace this with your's

        $tracking->setData([
            'carrier' => $this->_code,
            'carrier_title' => $this->getConfigData('title'),
            'tracking' => $trackingNumber,
            'url' => $url,
        ]);
        return $tracking;
    }

    /**
     * Has Checking Accounts
     *
     * @param array $requestParams
     * @return null|string
     */
    protected function _hasCheckingAccounts()
    {
        $accountCollection = $this->_accountFactory->create()->getCollection();
        $accountCollection->getSelect()->where("checking_account IS NOT NULL AND checking_account != ''");

        return (boolean)$accountCollection->getSize();
    }

    /**
     * Get allowed options/methods
     *
     * @return array
     */
    protected function _getAllowedOptions()
    {
        $options = $this->getConfigData('allowed_methods');
        if (trim($options) == "") return [];

        return explode(",", $options);
    }

    /**
     * Send tracking number email notification to customer
     *
     * @param $order
     * @param $shipmentId
     * @param $trackingLink
     * @return void
     */
    protected function sendTrackingNotification($order, $shipmentId, $trackingLink)
    {
        try {
            $templateOptions = array('area' => Area::AREA_FRONTEND, 'store' => $this->_storeManager->getStore()->getId());
            $templateVars = array(
                'store' => $this->_storeManager->getStore(),
                'store_name' => $this->_storeManager->getStore()->getName(),
                'order' => $order,
                'order_id' => $order->getId(),
                'shipment_id' => $shipmentId,
                'tracking_number' => $trackingLink,
                'tracking_link' => "https://www.starken.cl/seguimiento?codigo=" . $trackingLink
            );

            $senderEmail = $this->_scopeConfig->getValue('trans_email/ident_general/email',ScopeInterface::SCOPE_STORE);
            $senderName = $this->_scopeConfig->getValue('trans_email/ident_general/name',ScopeInterface::SCOPE_STORE);
            $from = ['email' => $senderEmail, 'name' => $senderName];
            $this->_inlineTranslation->suspend();
            $to = [$order->getCustomerEmail()];

            $templateId = $this->_scopeConfig->getValue ( 'carriers/starkenpro/shipping_guide_notification', ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore()->getId());
            $transport = $this->_transportBuilder->setTemplateIdentifier($templateId)->setTemplateOptions($templateOptions)
                ->setTemplateVars($templateVars)
                ->setFrom($from)
                ->addTo($to)
                ->getTransport();
            $transport->sendMessage();
            $this->_inlineTranslation->resume();

            $this->_messageManager->addSuccessMessage(__('Message sent successfully'));
        } catch (Exception $e) {
            $this->_messageManager->addErrorMessage(__('Error sending email notification'));
        }
    }
}
