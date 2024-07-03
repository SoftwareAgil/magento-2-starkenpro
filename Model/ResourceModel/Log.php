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
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class Log
 * @package SoftwareAgil\StarkenPro\Model\ResourceModel
 */
class Log extends AbstractDb
{
    /**
     * @var DateTime
     */
    protected $date;

    /**
     * Log constructor.
     *
     * @param DateTime $date
     * @param Context $context
     */
    public function __construct(
        DateTime $date,
        Context $context
    ) {
        $this->date = $date;
        parent::__construct($context);
    }

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
            $object->setCreatedAt($this->date->date());
        }

        return parent::_beforeSave($object);
    }
}
