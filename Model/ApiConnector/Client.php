<?php
/**
 * Starken Pro API connector client
 *
 * @category     SoftwareAgil
 * @package      SoftwareAgil_StarkenPro
 * @author       SoftwareAgil (info@softwareagil.com)
 * @copyright    Copyright (c) 2020 SoftwareAgil (www.softwareagil.com)
 */
namespace SoftwareAgil\StarkenPro\Model\ApiConnector;

use SoftwareAgil\StarkenPro\Helper\Data;
use Magento\Framework\App\Config\ReinitableConfigInterface;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Backend\App\Action\Context;

/**
 * Client class for API interface.
 */
class Client
{
    /**
     * @var Data
     */
    private $_helper;

    /**
     * @var Curl
     */
    private $_clientFactory;

    /**
     * @var Json
     */
    private $_json;

    /**
     * @var Context
     */
    private $_context;

    /**
     * @var ManagerInterface
     */
    private $_messageManager;

    /**
     * @var string
     */
    private $_endPointBase;

    /**
     * @var string
     */
    private $_apiToken;

    /**
     * Test constructor.
     *
     * @param Data $helperData
     * @param ReinitableConfigInterface $config
     * @param Curl $clientFactory
     * @param Json $json
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Data $helperData,
        ReinitableConfigInterface $config,
        Curl $clientFactory,
        Json $json,
        Context $context,
        array $data = []
    ) {
        $this->_helper = $helperData;
        $this->_clientFactory = $clientFactory;
        $this->_json = $json;
        $this->_context        = $context;
        $this->_messageManager = $context->getMessageManager();
    }

    /**
     * Set Endpoint Base.
     *
     * @param string $endPointBase
     * @return \SoftwareAgil\StarkenPro\Model\ApiConnector\Client
     */
    public function setEndPointBase($endPointBase)
    {
        $this->_endPointBase = $endPointBase;
        return $this;
    }

    /**
     * Get Endpoint Base.
     *
     * @return string
     */
    public function getEndPointBase()
    {
        return $this->_endPointBase;
    }

    /**
     * Set API Token.
     *
     * @param string $apiToken
     * @return \SoftwareAgil\StarkenPro\Model\ApiConnector\Client
     */
    public function setApiToken($apiToken)
    {
        $this->_apiToken = $apiToken;
        return $this;
    }

    /**
     * Get API Token.
     *
     * @return string
     */
    public function getApiToken()
    {
        return $this->_apiToken;
    }

    /**
     * Get HTTP client.
     *
     * @return \Magento\Framework\HTTP\ZendClientFactory
     */
    public function getHttpClient()
    {
        return $this->_clientFactory;
    }

    /**
     * Make API call to the Starken Pro server.
     *
     * @param string $action
     * @param array $payload
     * @param string $method
     * @param mixed $endPointBase
     * @return void
     */
    public function apiCall($action, $payload, $method = 'GET')
    {
        try {
            $result = ['success' => false];
            $url = $this->getEndPointBase() . '/' . $action;
            $headers = [
                'accept' => 'application/json',
                'Authorization' => 'Bearer ' . $this->getApiToken()
            ];
            $this->_clientFactory->setHeaders($headers);
            $this->_clientFactory->setOption(CURLOPT_MAXREDIRS, 0);
            $this->_clientFactory->setOption(CURLOPT_TIMEOUT, 30);
            if (strtoupper($method) == 'GET') {
                $this->_clientFactory->get($url, $payload);
            } else {
                $this->_clientFactory->post($url, $payload);
            }
            $response = $this->_clientFactory->getBody();
            $responseBodyArray = $this->_json->unserialize($response);
            $this->_helper->log(['raw_response' => $responseBodyArray]);
            if ($this->_clientFactory->getStatus() == 200 || $this->_clientFactory->getStatus() == 201) {
                if (isset($responseBodyArray['status']) && $responseBodyArray['status'] != "500") {
                    $result['success'] = true;
                    if (is_array($responseBodyArray)) {
                        $result['items'] = $responseBodyArray;
                    }
                } elseif (isset($responseBodyArray['error'])) {
                    $result['error'] = __("Starken Pro") . " : " . __($responseBodyArray['error'])->getText();
                    $result['code'] = $responseBodyArray['status'];
                } else {
                    $result['success'] = true;
                    $result['items'] = $responseBodyArray;
                }
            } else {
                $result['code'] = "";
                $result['error'] = __("Starken Pro")." : ".__($responseBodyArray['error'])->getText();
                if (isset($responseBodyArray['status'])) {
                    $result['code'] = $responseBodyArray['status'];
                }
                if (isset($responseBodyArray['statusCode'])) {
                    $result['code'] = $responseBodyArray['statusCode'];
                }
            }
            $this->_helper->apiCallLog($action, $this->_clientFactory->getStatus(), $this->_json->serialize($payload), $response);
        } catch (\Exception $e) {
            $result['error'] = __("Starken Pro")." : ".__($e->getMessage())->getText();
            $result['code'] = $e->getCode();
            $this->_helper->apiCallLog($action, $e->getCode(), $this->_json->serialize($payload), "{}");
        }
        //$this->_helper->log(['response' => $result]);
        return $result;
    }

    /**
     * Validate apiuser on save.
     *
     * @param string $apiUsername
     * @param string $apiPassword
     *
     * @return bool|mixed
     */
    public function validateConnection($apiEndpoint, $apiPassword)
    {
        $website = $this->_helper->getWebsiteForSelectedScopeInAdmin();

        if (!$this->_helper->isEnabledFromBackend($website)) {
            return false;
        }

        $client = $this->getHttpClient();
        if ($apiEndpoint && $apiPassword) {
            $this->setEndPointBase($apiEndpoint)
                ->setApiToken($apiPassword);

            $response = $this->apiCall('agency/region/13', []); //Testing with Región Metropolitana de Santiago

            if ($response['success']) {
                $this->_messageManager->addSuccessMessage(__('API Credentials Valid.'));
            } else {
                $this->_messageManager->addWarningMessage(__('Authorization has been denied for this request.'));
            }
            return $response['success'];
        } else {
            $this->_messageManager->addSuccessMessage(__('API Credentials Valid.'));
            return false;
        }
    }
}
