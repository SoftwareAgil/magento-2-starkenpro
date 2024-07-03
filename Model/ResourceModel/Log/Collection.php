<?php
/**
 * Log Resource Collection factory
 *
 * @category     SoftwareAgil
 * @package      SoftwareAgil_StarkenPro
 * @author       SoftwareAgil (info@softwareagil.com)
 * @copyright    Copyright (c) 2020 SoftwareAgil (www.softwareagil.com)
 */

namespace SoftwareAgil\StarkenPro\Model\ResourceModel\Log;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use SoftwareAgil\StarkenPro\Model\ResourceModel\Log;

/**
 * Class Collection
 * @package SoftwareAgil\StarkenPro\Model\ResourceModel\Log
 */
class Collection extends AbstractCollection
{
    /**
     * @type string
     */
    protected $_idFieldName = 'id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\SoftwareAgil\StarkenPro\Model\Log::class, Log::class);
    }

    /**
     * Truncate table log
     *
     * @return void
     */
    public function clearLog()
    {
        $this->getConnection()->delete($this->getMainTable(), "created_at < now() - interval 1 month");
    }
}
