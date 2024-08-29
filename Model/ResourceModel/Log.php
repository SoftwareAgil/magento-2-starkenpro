<?php
/**
 * Log Resource factory
 *
 * @category     SoftwareAgil
 * @package      SoftwareAgil_StarkenPro
 * @author       SoftwareAgil (info@softwareagil.com)
 * @copyright    Copyright (c) 2020 SoftwareAgil (www.softwareagil.com)
 */

namespace SoftwareAgil\StarkenPro\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Log
 * @package SoftwareAgil\StarkenPro\Model\ResourceModel
 */
class Log extends AbstractDb
{
    /**
     * @inheritdoc
     */
    public function _construct()
    {
        $this->_init('sa_sp_api_calls_log', 'id');
    }

    /**
     * @inheritdoc
     */
    protected function _beforeSave(AbstractModel $object)
    {
        if ($object->isObjectNew()) {
            $object->setData('created_at', date('Y-m-d H:i:s'));
        }

        return parent::_beforeSave($object);
    }
}
