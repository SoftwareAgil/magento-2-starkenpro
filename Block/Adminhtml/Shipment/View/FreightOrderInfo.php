<?php
/**
 * Freight Order Info block
 *
 * @category     SoftwareAgil
 * @package      SoftwareAgil_StarkenPro
 * @author       SoftwareAgil (info@softwareagil.com)
 * @copyright    Copyright (c) 2020 SoftwareAgil (www.softwareagil.com)
 */
namespace SoftwareAgil\StarkenPro\Block\Adminhtml\Shipment\View;

class FreightOrderInfo extends \Magento\Backend\Block\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Column 1 fields
     *
     * @var array
     */
    protected $_column1Fields = null;

    /**
     * Column 2 fields
     *
     * @var array
     */
    protected $_column2Fields = null;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $_json;

    /**
     * @var \SoftwareAgil\StarkenPro\Model\CommuneFactory
     */
    protected $_agencyFactory;

    /** @var PriceCurrencyInterface $priceCurrency */
    protected $_priceCurrency;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     * @param \SoftwareAgil\StarkenPro\Model\CommuneFactory $agencyFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Serialize\Serializer\Json $json,
        \SoftwareAgil\StarkenPro\Model\AgencyFactory $agencyFactory,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_json = $json;
        $this->_agencyFactory = $agencyFactory;
        $this->_priceCurrency = $priceCurrency;
        $this->_column1Fields = [
            'tracking.origin' => __('Origin'),
            'tracking.destination' => __('Destination'),
            'tracking.issuer_rut' => __('RUT of the issuer'),
            'tracking.issuer_name' => __('Issuer name'),
            'tracking.issuer_email' => __('Issuer email'),
            'tracking.issuer_phone' => __('Issuer phone'),
            'tracking.issuer_mobile' => __('Issuer mobile phone'),
            'tracking.receiver_rut' => __('Recipient RUT'),
            'tracking.receiver_name' => __('Recipient name'),
            'tracking.receiver_email' => __('Recipient email'),
            'tracking.receiver_phone' => __('Recipient phone'),
            'tracking.receiver_mobile' => __('Recipient mobile phone number'),
            'tracking.receiver_address' => __('Recipient address')
        ];
        $this->_column2Fields = [
            'emision.tipo_servicio' => __('Service type'),
            'emision.tipo_pago' => __('Payment type'),
            'emision.cuenta_corriente' => __('Ckecking account'),
            'emision.centro_costo' => __('Cost center'),
            'tracking.origin_region' => __('Agency origin'),
            'emision.origen_agencia_direccion' => __('Agency origin address'),
            'tracking.destin_region' => __('Agency destination'),
            'emision.destino_agencia_direccion' => __('Agency destination address'),
            'order.base_shipping_amount' => __('Transport price'),
            'emision.valor_declarado' => __('Declared value'),
            'tracking.status' => __('Status'),
            'tracking.commitmen_date' => __('Commitment date'),
            'tracking.created_at' => __('Created'),
            'tracking.updated_at' => __('Updated')
        ];
        $this->_column1OptionalFields = [
            'tracking.origin' => 'codigo_agencia_origen',
            'tracking.destination' => 'codigo_agencia_destino',
            'tracking.issuer_name' => 'user.nombres',
            'tracking.issuer_email' => 'user.email',
            'tracking.issuer_phone' => 'user.telefono',
            'tracking.issuer_mobile' => 'user.celular',
            'tracking.receiver_rut' => 'destinatario_rut',
            'tracking.receiver_name' => 'destinatario_nombres',
            'tracking.receiver_email' => 'destinatario_email',
            'tracking.receiver_phone' => 'destinatario_telefono',
            'tracking.receiver_address' => 'destinatario_direccion'
        ];
        parent::__construct($context, $data);
    }

    /**
     * Retrieve order model instance
     *
     * @return \Magento\Sales\Model\Order\Shipment
     */
    public function getOrder()
    {
        $order = $this->_coreRegistry->registry('current_order');
        if (!$order) {
            $shipment = $this->_coreRegistry->registry('current_shipment');
            $order = $shipment->getOrder();
        }
        return $order;
    }

    /**
     * Retrieve shipment model instance
     *
     * @return \Magento\Sales\Model\Order\Shipment
     */
    public function getShipment()
    {
        $currentShipment = new \Magento\Framework\DataObject();
        if ($this->getOrder()->getShipmentsCollection()->getSize()) {
            $currentShipment = $this->getOrder()->getShipmentsCollection()->getFirstItem();
        }

        return $currentShipment;
    }

    /**
     * Retrieve Shipment Tracking Content
     *
     * @return \Magento\Sales\Model\Order\Shipment
     */
    public function getSaSpFreightOrderContent()
    {
        return $this->getShipment()->getSaSpFreightOrderContent();
    }

    /**
     * Is a StarkenPro shipping method?
     *
     * @return \Magento\Sales\Model\Order\Shipment
     */
    public function isStarkenPro()
    {

        if (strpos($this->getOrder()->getShippingMethod(), "starkenpro_") === false) {
            return false;
        }
        return true;
    }

    /**
     * Get items Col1
     *
     * @return \Magento\Sales\Model\Order\Shipment
     */
    public function getItemsCol1()
    {
        $items = [];
        $arrayTemp = [];
        if ($this->getShipment()->getSaSpTrackingContent()) {
            $arrayTemp = $this->_json->unserialize($this->getShipment()->getSaSpTrackingContent());
        }
        $array2Temp = [];
        if ($this->getShipment()->getSaSpFreightOrderContent()) {
            $array2Temp = $this->_json->unserialize($this->getShipment()->getSaSpFreightOrderContent());
        }
        foreach ($this->_column1Fields as $k => $v) {
            $kArray = explode(".", $k);
            $kEntity = $kArray[0];
            $kField = $kArray[1];
            if (isset($arrayTemp[$kField])) {
                $items[(string)$v] = $arrayTemp[$kField];
            } else {
                if (isset($this->_column1OptionalFields[$k]) && isset($array2Temp[$this->_column1OptionalFields[$k]])) {
                    $k2Array = explode(".", $this->_column1OptionalFields[$k]);
                    if (count($k2Array) == 1) {
                        if (isset($array2Temp[$k2Array[0]])) {
                            $valueTemp = $array2Temp[$k2Array[0]];
                            if ($array2Temp[$k2Array[0]] && ($k2Array[0] == "codigo_agencia_origen" || $k2Array[0] == "codigo_agencia_destino")) {
                                $agencyModel = $this->_agencyFactory->create()->load($array2Temp[$k2Array[0]], 'sp_agency_id');
                                if ($agencyModel->getAgencyId()) {
                                    $valueTemp = $agencyModel->getName();
                                }
                            }
                            $items[(string)$v] = $valueTemp;
                        }
                    } elseif (count($k2Array) == 2) {
                        if (isset($array2Temp[$k2Array[0]][$k2Array[1]])) {
                            $items[(string)$v] = $array2Temp[$k2Array[0]][$k2Array[1]];
                        }
                    }
                }
                if (!isset($items[(string)$v])) {
                    $items[(string)$v] = "--";
                }
            }
        }

        return $items;
    }

    /**
     * Get items Col2
     *
     * @return \Magento\Sales\Model\Order\Shipment
     */
    public function getItemsCol2()
    {
        $items = [];
        $foArray = [];
        $tArray = [];
        if ($this->getShipment()->getSaSpFreightOrderContent()) {
            $foArray = $this->_json->unserialize($this->getShipment()->getSaSpFreightOrderContent());
        }
        if ($this->getShipment()->getSaSpTrackingContent()) {
            $tArray = $this->_json->unserialize($this->getShipment()->getSaSpTrackingContent());
        }
        foreach ($this->_column2Fields as $k => $v) {
            $kArray = explode(".", $k);
            $kEntity = $kArray[0];
            $kField = $kArray[1];
            switch ($kEntity) {
                case "tracking":
                    if (isset($tArray[$kField])) {
                        $items[(string)$v] = $tArray[$kField];
                    } else {
                        $items[(string)$v] = "--";
                    }
                    break;
                case "emision":
                    if (isset($foArray[$kField])) {
                        if (!is_array($foArray[$kField])) {
                            if ($kField == "valor_declarado") {
                                $items[(string)$v] = $this->_priceCurrency->convertAndFormat($foArray[$kField]);
                            } else {
                                $items[(string)$v] = $foArray[$kField];
                            }
                        } else {
                            switch ($kField) {
                                case "tipo_pago":
                                case "tipo_servicio":
                                    $items[(string)$v] = $foArray[$kField]["nombre"];
                                    break;
                                case "origen_agencia_direccion":
                                    if ((int)$foArray["codigo_agencia_origen"]) {
                                        $agencyModel = $this->_agencyFactory->create()->load($foArray["codigo_agencia_origen"], 'sp_agency_id');
                                        if ($agencyModel->getCityId()) {
                                            $items[(string)$v] = $agencyModel->getAddress();
                                        }
                                    }
                                    break;
                                case "destino_agencia_direccion":
                                    if ((int)$foArray["codigo_agencia_destino"]) {
                                        $agencyModel = $this->_agencyFactory->create()->load($foArray["codigo_agencia_destino"], 'sp_agency_id');
                                        if ($agencyModel->getCityId()) {
                                            $items[(string)$v] = $agencyModel->getAddress();
                                        }
                                    }
                                    break;
                                default:
                                    $items[(string)$v] = "--";
                                    break;
                            }
                        }
                    } else {
                        switch ($kField) {
                            case "origen_agencia_direccion":
                                if (isset($foArray["codigo_agencia_origen"])) {
                                    $agencyModel = $this->_agencyFactory->create()->load((int)$foArray["codigo_agencia_origen"], 'sp_agency_id');
                                    if ($agencyModel->getAddress()) {
                                        $items[(string)$v] = $agencyModel->getAddress();
                                    }
                                }
                                break;
                            case "destino_agencia_direccion":
                                if (isset($foArray["codigo_agencia_destino"])) {
                                    $agencyModel = $this->_agencyFactory->create()->load((int)$foArray["codigo_agencia_destino"], 'sp_agency_id');
                                    if ($agencyModel->getAddress()) {
                                        $items[(string)$v] = $agencyModel->getAddress();
                                    }
                                }
                                break;
                            default:
                                $items[(string)$v] = "--";
                                break;
                        }
                    }
                    break;
                case "order":
                    switch ($kField) {
                        case "base_shipping_amount":
                            $sa = $this->getOrder()->getData($kField);
                            $sd = $this->getOrder()->getData("shipping_description");
                            $pp = strpos($sd, "(");
                            if ($sa == 0 && $pp !== false) {
                                $priceTemp = substr($sd, $pp + 1, 10);
                                $priceTemp = preg_replace("/[^0-9,]/", "", $priceTemp);
                                $items[(string)$v] = $this->_priceCurrency->convertAndFormat($priceTemp);
                            } else {
                                $items[(string)$v] = $this->_priceCurrency->convertAndFormat((int)$sa);
                            }
                            break;
                        default:
                            $items[(string)$v] = $this->getOrder()->getData($kField);
                            break;
                    }
                    break;
            }
        }
        return $items;
    }
}
