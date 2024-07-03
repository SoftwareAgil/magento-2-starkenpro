<?php
/**
 * Merchant Account resource collection
 *
 * @category     SoftwareAgil
 * @package      SoftwareAgil_StarkenPro
 * @author       SoftwareAgil (info@softwareagil.com)
 * @copyright    Copyright (c) 2020 SoftwareAgil (www.softwareagil.com)
 */
namespace SoftwareAgil\StarkenPro\Model\ResourceModel\Account;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('SoftwareAgil\StarkenPro\Model\Account', 'SoftwareAgil\StarkenPro\Model\ResourceModel\Account');
        $this->addOrder('rut', \Magento\Framework\Data\Collection::SORT_ORDER_ASC);
        $this->addOrder('checking_account', \Magento\Framework\Data\Collection::SORT_ORDER_ASC);
        $this->addOrder('cost_center', \Magento\Framework\Data\Collection::SORT_ORDER_ASC);
    }

    /**
     * Filter by checking account
     *
     * @param string|array $communeId
     * @return \SoftwareAgil\StarkenPro\Model\ResourceModel\Agency\Collection
     */
    public function addCcteFilter($ccteId)
    {
        if (!empty($ccteId)) {
            $ccteIdArray = explode('-', $ccteId);
            $this->addFieldToFilter('main_table.checking_account', $ccteIdArray[0]);
        }

        return $this;
    }

    /**
     * Convert collection items to select options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        $propertyMap = [
            'value' => 'cost_center',
            'title' => 'cost_center_name',
            'checking_account' => 'checking_account',
        ];

        foreach ($this as $item) {
            $option = [];
            foreach ($propertyMap as $code => $field) {
                $option[$code] = $item->getData($field);
            }
            $option['label'] = $item->getCostCenterName();
            $options[] = $option;
        }

        if (count($options) > 0) {
            array_unshift(
                $options,
                ['title' => '', 'value' => '', 'label' => __('Please select a center cost.')]
            );
        }
        return $options;
    }
}
