<?php
/**
 * Log factory
 *
 * @category     SoftwareAgil
 * @package      SoftwareAgil_StarkenPro
 * @author       SoftwareAgil (info@softwareagil.com)
 * @copyright    Copyright (c) 2020 SoftwareAgil (www.softwareagil.com)
 */

namespace SoftwareAgil\StarkenPro\Model;

use Exception;
use Magento\Framework\App\Area;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Store\Model\Store;

/**
 * Class Log
 * @package SoftwareAgil\StarkenPro\Model
 */
class Log extends AbstractModel
{
    /**
     * @return void
     */
    public function _construct()
    {
        $this->_init(ResourceModel\Log::class);
    }

    /**
     * Save API Calls log
     *
     * @param $message
     * @param $status
     */
    public function saveLog($endpoint, $responseHttpCode, $request, $response)
    {
        $this->setEndpoint($endpoint)
            ->setHttpCode($responseHttpCode)
            ->setRequest($request)
            ->setResponse($response)
            ->save();
    }
}
