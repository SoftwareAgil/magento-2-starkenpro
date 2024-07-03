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
use Magento\Framework\HTTP\ZendClientFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Backend\App\Action\Context;

/**
 * Client class for API interface.
 */
class Client
{
    /**
     * @var \SoftwareAgil\StarkenPro\Helper\Data
     */
    private $_helper;

    /**
     * @var \Magento\Framework\HTTP\ZendClientFactory
     */
    private $_clientFactory;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $_json;

    /**
     * @var \Magento\Backend\App\Action\Context
     */
    private $_context;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
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
     * @param \SoftwareAgil\StarkenPro\Helper\Data $helperData
     * @param \Magento\Framework\App\Config\ReinitableConfigInterface $config
     * @param \Magento\Framework\HTTP\ZendClientFactory $clientFactory
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     * @param \Magento\Backend\App\Action\Context $context
     * @param array $data
     */
    public function __construct(
        Data $helperData,
        ReinitableConfigInterface $config,
        ZendClientFactory $clientFactory,
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
            $url = $this->getEndPointBase().'/'.$action;
            //$this->_helper->log(['request' => ['endpoint' => $url, 'method' => $method, 'payload' => $payload]]);
            $client = $this->getHttpClient()->create();
            $client->setUri($url);
            $client->setMethod($method);
            $client->setConfig(['maxredirects' => 0, 'timeout' => 30]);
            $client->setHeaders('accept', 'application/json');
            $client->setHeaders('Authorization', 'Bearer '.$this->getApiToken());
            if (count($payload)) {
                $client->setHeaders('Content-Type', 'application/json');
                $client->setRawData($this->_json->serialize($payload));
            }
            $response = $client->request();
            $responseBody = $response->getBody();

            $responseBodyArray = $this->_json->unserialize($responseBody);
            $this->_helper->log(['raw_response' => $responseBodyArray]);
            if ($response->getStatus() == 200 || $response->getStatus() == 201) {
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
            $this->_helper->apiCallLog($action, $response->getStatus(), $this->_json->serialize($payload), $responseBody);
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
