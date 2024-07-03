<?php
/**
 * Main helper
 *
 * @category     SoftwareAgil
 * @package      SoftwareAgil_StarkenPro
 * @author       SoftwareAgil (info@softwareagil.com)
 * @copyright    Copyright (c) 2020 SoftwareAgil (www.softwareagil.com)
 */
namespace SoftwareAgil\StarkenPro\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\HTTP\ZendClientFactory;
use Magento\Framework\Encryption\EncryptorInterface;
use SoftwareAgil\StarkenPro\Model\LogFactory;

/**
 * Usps data helper
 */
class Data extends AbstractHelper
{
    /**
     * @constant
     */
    const XML_PATH_CARRIER_ENABLED = 'carriers/starkenpro/active';

    /**
     * @constant
     */
    const XML_PATH_DEBUG_ENABLED = 'carriers/starkenpro/debug';

    /**
     * @constant
     */
    const XML_PATH_API_TOKEN = 'carriers/starkenpro/password';

    /**
     * @constant
     */
    const XML_PATH_API_GATEWAY_URL= 'carriers/starkenpro/gateway_url';

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var LogFactory
     */
    protected $_logFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\HTTP\ZendClientFactory
     */
    protected $_clientFactory;

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    private $_encryptor;

    /**
     * Available shipping methods
     *
     * @var array
     */
    protected $availableShippingMethods = [
        \SoftwareAgil\StarkenPro\Model\Carrier\AbstractCarrier::METHOD_NORMAL,
        \SoftwareAgil\StarkenPro\Model\Carrier\AbstractCarrier::METHOD_EXPRESS
    ];

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Psr\Log\LoggerInterface $logger
     * @param LogFactory $logFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\HTTP\ZendClientFactory $clientFactory
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     */
    public function __construct(
        Context $context,
        LoggerInterface $logger,
        LogFactory $logFactory,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        ZendClientFactory $clientFactory,
        EncryptorInterface $encryptor
    ) {
        $this->_logger = $logger;
        $this->_logFactory   = $logFactory;
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        $this->_clientFactory = $clientFactory;
        $this->_encryptor = $encryptor;
        parent::__construct($context);
    }

    /**
     * Get website config.
     *
     * @param string $path
     * @param int $website
     * @param string $scope
     *
     * @return string|boolean
     */
    public function getWebsiteConfig($path, $website = 0, $scope = \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE)
    {
        return $this->scopeConfig->getValue(
            $path,
            $scope,
            $website
        );
    }

    /**
     * Get website for selected scope in admin
     *
     * @return \Magento\Store\Api\Data\WebsiteInterface
     */
    public function getWebsiteForSelectedScopeInAdmin()
    {
        //If website param does not exist then default value returned 0 "default scope"
        //This is because there is no website param in default scope
        $websiteId = $this->_request->getParam('website', 0);
        return $this->_storeManager->getWebsite($websiteId);
    }

    /**
     * @param int $website
     *
     * @return string|boolean
     */
    public function getApiPasswordFromBackend($website = 0)
    {
        $value = $this->getWebsiteConfig(
            self::XML_PATH_API_TOKEN,
            $website
        );
        return $value;
    }

    /**
     * @param int $website
     *
     * @return string|boolean
     */
    public function getApiUrlFromBackend($website = 0)
    {
        $value = $this->getWebsiteConfig(
            self::XML_PATH_API_GATEWAY_URL,
            $website
        );
        return $value;
    }

    /**
     * Check if shipping method is enabled from the backend.
     *
     * @param int $website
     *
     * @return bool
     */
    public function isEnabledFromBackend($website = 0)
    {
        $website = $this->_storeManager->getWebsite($website);
        $enabled = $this->_scopeConfig->isSetFlag(
            self::XML_PATH_CARRIER_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
            $website
        );
        return $enabled;
    }

    /**
     * Check if shipping method is enabled from the frontend.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_CARRIER_ENABLED
        );
    }

    /**
     * Get if the log is enabled for connector.
     *
     * @return bool
     */
    public function isDebugEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_DEBUG_ENABLED
        );
    }

    /**
     * Logging Utility
     *
     * @param $message
     * @param bool|false $useSeparator
     */
    public function log($message)
    {
        if (!$this->isDebugEnabled()) return $this;
        if (is_array($message)) {
            $this->_logger->debug("StarkenPro: ".json_encode($message));
        } else {
            $this->_logger->debug("StarkenPro: ".$message);
        }
    }

    /**
     * API Calls Logging Utility
     *
     * @param $message
     * @param bool|false $useSeparator
     */
    public function apiCallLog($endpoint, $responseHttpCode, $request, $response)
    {
        if (!$this->isDebugEnabled()) return $this;
        /** @var Log $log */
        $log = $this->_logFactory->create();
        try {
            $log->saveLog($endpoint, $responseHttpCode, $request, $response);
        } catch (Exception $e) {
            $this->_logger->critical($e->getMessage());
        }
    }
}
