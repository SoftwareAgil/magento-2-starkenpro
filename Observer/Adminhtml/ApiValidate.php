<?php
/**
 * Admin API validations observer
 *
 * @category     SoftwareAgil
 * @package      SoftwareAgil_StarkenPro
 * @author       SoftwareAgil (info@softwareagil.com)
 * @copyright    Copyright (c) 2020 SoftwareAgil (www.softwareagil.com)
 */
namespace SoftwareAgil\StarkenPro\Observer\Adminhtml;

use SoftwareAgil\StarkenPro\Helper\Data;
use SoftwareAgil\StarkenPro\Model\ApiConnector\Client;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Config\Storage\Writer;
use Magento\Framework\App\Config\ReinitableConfigInterface;
use Magento\Directory\Model\Country;
use Magento\Directory\Model\RegionFactory;
use SoftwareAgil\StarkenPro\Model\CommuneFactory;
use SoftwareAgil\StarkenPro\Model\ResourceModel\Commune as ResourceModelCommune;
use SoftwareAgil\StarkenPro\Model\AgencyFactory;
use SoftwareAgil\StarkenPro\Model\ResourceModel\Agency as ResourceModelAgency;
use SoftwareAgil\StarkenPro\Model\AccountFactory;

/**
 * Validate api when saving API credentials in admin.
 */
class ApiValidate implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \SoftwareAgil\StarkenPro\Helper\Data
     */
    private $_helper;

    /**
     * @var \Magento\Backend\App\Action\Context
     */
    private $_context;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $_messageManager;

    /**
     * @var \Magento\Framework\App\Config\Storage\Writer
     */
    private $_writer;

    /**
     * @var \SoftwareAgil\StarkenPro\Model\ApiConnector\Test
     */
    private $_client;

    /**
     * @var \Magento\Framework\App\Config\ReinitableConfigInterface
     */
    private $_reinitableConfig;

    /**
     * @var \Magento\Directory\Model\Country
     */
    private $_country;

    /**
     * @var \Magento\Directory\Model\RegionFactory
     */
    private $_regionFactory;

    /**
     * @var CommuneFactory
     */
    private $_communeFactory;

    /**
     * @var AgencyFactory
     */
    private $_agencyFactory;

    /**
     * @var AccountFactory
     */
    private $_accountFactory;


    /**
     * @var string
     */
    protected $_apiPassword;


    /**
     * ApiValidate constructor.
     *
     * @param \SoftwareAgil\StarkenPro\Helper\Data $helperData
     * @param \SoftwareAgil\StarkenPro\Model\ApiConnector\Client $client
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Config\Storage\Writer $writer
     * @param \Magento\Framework\App\Config\ReinitableConfigInterface $reinitableConfig
     * @param \Magento\Directory\Model\Country $country
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \SoftwareAgil\StarkenPro\Model\CommuneFactory $communeFactory
     * @param \SoftwareAgil\StarkenPro\Model\AgencyFactory $agencyFactory
     * @param \SoftwareAgil\StarkenPro\Model\AccountFactory $accountFactory
     */
    public function __construct(
        Data $helperData,
        Client $client,
        Context $context,
        Writer $writer,
        ReinitableConfigInterface $reinitableConfig,
        Country $country,
        RegionFactory $regionFactory,
        CommuneFactory $communeFactory,
        AgencyFactory $agencyFactory,
        AccountFactory $accountFactory,
        protected ResourceModelCommune $resourceCommune,
        protected ResourceModelAgency $resourceAgency,
    ) {
        $this->_client         = $client;
        $this->_helper         = $helperData;
        $this->_writer         = $writer;
        $this->_context        = $context;
        $this->_messageManager = $context->getMessageManager();
        $this->_reinitableConfig = $reinitableConfig;
        $this->_country = $country;
        $this->_regionFactory = $regionFactory;
        $this->_communeFactory = $communeFactory;
        $this->_agencyFactory = $agencyFactory;
        $this->_accountFactory = $accountFactory;
    }

    /**
     * Validate if exist the Chile regions.
     *
     * @param void
     *
     * @return $this
     */
    public function existsCLDirectoryEntities()
    {
        $regionCollection = $this->_country->loadByCode('CL')->getRegions();
        $regions = $regionCollection->loadData()->toOptionArray(false);
        if (count($regions)) {
            foreach ($regionCollection as $r) {
                if (is_numeric($r->getCode())) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Validate if exist the merchant accounts.
     *
     * @param void
     *
     * @return $this
     */
    public function existsMerchantAccountEntities()
    {
        $accountCollection = $this->_accountFactory->create()->getCollection();
        $accounts = $accountCollection->loadData()->toOptionArray(false);
        if (count($accounts)) {
            return true;
        }

        return false;
    }

    /**
     * Validate if exist the merchant accounts.
     *
     * @param void
     *
     * @return $this
     */
    public function wasChangedUserToken()
    {
        $accountCollection = $this->_accountFactory->create()->getCollection();
        if ($accountCollection->getSize()) {
            $firstAccount = $accountCollection->getFirstItem();
            if ($firstAccount->getUserToken() == $this->_apiPassword && $this->_apiPassword != "") {
                return false;
            }
        }

        return true;
    }

    /**
     * Validate if exist the merchant accounts.
     *
     * @param void
     *
     * @return $this
     */
    public function truncateTables()
    {
        $accountCollection = $this->_accountFactory->create()->getCollection();
        $tableName = $accountCollection->getMainTable();
        $connection = $accountCollection->getConnection();
        $connection->truncateTable($tableName);
        $regionCollection = $this->_country->loadByCode('CL')->getRegions();
        foreach ($regionCollection as $r) {
            if (!is_numeric($r->getCode())) {
                $r->delete();
            }
        }
        $communeCollection = $this->_communeFactory->create()->getCollection();
        $tableName = $communeCollection->getMainTable();
        $connection = $communeCollection->getConnection();
        $connection->truncateTable($tableName);
        $agencyCollection = $this->_agencyFactory->create()->getCollection();
        $tableName = $agencyCollection->getMainTable();
        $connection = $agencyCollection->getConnection();
        $connection->truncateTable($tableName);
    }

    /**
     * Validate if exist the commune entities.
     *
     * @param void
     *
     * @return $this
     */
    public function existsCLCommuneEntities()
    {
        $communeCollection = $this->_communeFactory->create()->getCollection();
        $communes = $communeCollection->loadData()->toOptionArray(false);
        if (count($communes)) {
            return true;
        }

        return false;
    }

    /**
     * Create the Chile directory entities like regions, cities, and communes.
     *
     * @param void
     *
     * @return $this
     */
    public function createCLDirectoryEntities($createRegions = false)
    {
        try {
            $response = $this->_client->apiCall('agency/region', []);
            if (!$response['success']) {
                if (isset($response['error'])) {
                    $errorMsg = __('Error creating Chile directory entities: %1.', $response['error']);
                    $this->_messageManager->addErrorMessage($errorMsg);
                    $this->_helper->log($errorMsg);
                }
                return false;
            }
            $addedCommunes = [];
            $addedAgencies = [];
            $regionsArray = [];
            if (!$createRegions) {
                $regionCollection = $this->_country->loadByCode('CL')->getRegions();
                foreach ($regionCollection as $r) {
                    $regionsArray[$r->getCode()] = $r->getRegionId();
                }
            }
            $regionCollection = $this->_country->loadByCode('CL')->getRegions();
            foreach ($regionCollection as $r) {
                if (!is_numeric($r->getCode())) {
                    $r->delete();
                }
            }
            foreach ($response['items'] as $region) {
                if ($createRegions) {
                    $regionObj = $this->_regionFactory->create();
                    $regionObj
                        ->setCode($region['code_dls']) //Starken Pro Region ID value into default core field.
                        ->setCountryId('CL')
                        ->setDefaultName($region['name']);
                    $regionObj->save();
                    $regionId = $regionObj->getRegionId();
                } else {
                    if (!isset($regionsArray[$region['code_dls']])) {
                        continue;
                    }
                    $regionId = $regionsArray[$region['code_dls']];
                }
                foreach($region['cities'] as $city) {
                    foreach($city['comunas'] as $commune) {
                        if (in_array($commune['code_dls'], $addedCommunes)) {
                            continue;
                        }
                        $communeObj = $this->_communeFactory->create();
                        $communeObj->setData([
                                'sp_commune_id' => $commune['code_dls'],
                                'region_id' => $regionId,
                                'city_id' => $city['code_dls'],
                                'name' => $commune['name']
                            ])
                            ->isObjectNew(true);
                        $this->resourceCommune->save($communeObj);
                        $addedCommunes[] = $communeObj->getData('sp_commune_id');
                        foreach($commune['agencies'] as $agency) {
                            if (in_array($agency['code_dls'], $addedAgencies)) {
                                continue;
                            }
                            $agencyObj = $this->_agencyFactory->create();
                            $agencyObj->setData([
                                    'sp_agency_id' => $agency['code_dls'],
                                    'name' => $agency['name'],
                                    'commune_id' => $commune['code_dls'],
                                    'address' => $agency['address'],
                                    'latitude' => $agency['latitude'],
                                    'longitude' => $agency['longitude']
                                ])
                                ->isObjectNew(true);
                            $this->resourceAgency->save($agencyObj);
                            $addedAgencies[] = $agencyObj->getData('sp_agency_id');
                        }
                    }
                }
            }
            $this->_messageManager->addSuccessMessage(__('Directory entities added correctly.'));
        } catch (Exception $e) {
            $errorMsg = __('Error creating Chile directory entities: %1.', __($e->getMessage()));
            $this->_messageManager->addWarningMessage($errorMsg);
            $this->_helper->log($errorMsg);
        }
    }

    /**
     * Create the merchant account entities.
     *
     * @param void
     *
     * @return $this
     */
    public function createMerchantAccountEntities()
    {
        try {
            $response = $this->_client->apiCall('emision/credito-cliente/ctacte', []);
            if (!$response['success']) {
                if (isset($response['error'])) {
                    $errorMsg = __('Error creating Merchant Account entities: %1.', $response['error']);
                    $this->_messageManager->addErrorMessage($errorMsg);
                    $this->_helper->log($errorMsg);
                }
                return false;
            }
            foreach ($response['items'] as $account) {
                $response2 = $this->_client->apiCall('emision/credito-cliente/cc/' . $account['codigo'], []);
                if (isset($response2['items'])) {
                    foreach($response2['items'] as $cc) {
                        $accountObj = $this->_accountFactory->create();
                        $accountObj
                            ->setRut($account['rut'])
                            ->setCheckingAccount($account['codigo'])
                            ->setCheckingAccountDv($account['dv'])
                            ->setUserToken($this->_apiPassword);
                        $accountObj->setCostCenter($cc['id'])
                            ->setCostCenterName($cc['descripcion'])
                            ->isObjectNew(true);
                        $accountObj->save();
                    }
                    $this->_messageManager->addSuccessMessage(__('Merchant Account entities added correctly.'));
                } else {
                    $errorMsg = __('Unknow error creating Merchant Account entities.');
                    $this->_messageManager->addWarningMessage($errorMsg);
                    $this->_helper->log($errorMsg);
                }
            }
        } catch (Exception $e) {
            $errorMsg = __('Error creating Merchant Account entities: %1.', __($e->getMessage()));
            $this->_messageManager->addWarningMessage($errorMsg);
            $this->_helper->log($errorMsg);
        }
    }

    /**
     * Execute method.
     *
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $groups = $this->_context->getRequest()->getPost('groups');

        if (!$groups['starkenpro']['fields']['active']['value']) {
            return $this;
        }

        if (isset($groups['starkenpro']['fields']['password']['inherit'])) {
            $this->_messageManager->addWarningMessage(__('Invalid API User Token.'));
            return $this;
        }

        if (!$groups['starkenpro']['fields']['password']['value']) {
            $this->_messageManager->addWarningMessage(__('Invalid API User Token.'));
            return $this;
        }

        $website = $this->_helper->getWebsiteForSelectedScopeInAdmin();
        $apiEndpoint = "";
        if (isset($groups['starkenpro']['fields']['gateway_url']['inherit'])) {
            $apiEndpoint = $this->_helper->getApiUrlFromBackend($website);
        }
        if (!$apiEndpoint) {
            $apiEndpoint = isset($groups['starkenpro']['fields']['gateway_url']['value'])
                ? $groups['starkenpro']['fields']['gateway_url']['value'] : false;
        }
        if (!$apiEndpoint) {
            $this->_messageManager->addWarningMessage(__('Invalid API URL.'));
            return $this;
        }

        $this->_apiPassword = isset($groups['starkenpro']['fields']['password']['value'])
            ? $groups['starkenpro']['fields']['password']['value'] : false;

        //Clear config cache
        $this->_reinitableConfig->reinit();

        $this->_apiPassword = $this->_helper->getApiPasswordFromBackend($website);

        if (!$this->_client->validateConnection($apiEndpoint, $this->_apiPassword)) return $this;

        //Added validation about User Token change to update dependent tables
        if ($this->wasChangedUserToken()) {
            $this->truncateTables();
        }

        if (!$this->existsCLDirectoryEntities()) {
            $this->createCLDirectoryEntities(true);
        } elseif (!$this->existsCLCommuneEntities()) {
            $this->createCLDirectoryEntities();
        }

        if (!$this->existsMerchantAccountEntities()) {
            $this->createMerchantAccountEntities();
        }

        return $this;
    }
}
