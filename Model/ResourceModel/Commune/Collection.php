<?php
/**
 * Communes resource collection
 *
 * @category     SoftwareAgil
 * @package      SoftwareAgil_StarkenPro
 * @author       SoftwareAgil (info@softwareagil.com)
 * @copyright    Copyright (c) 2020 SoftwareAgil (www.softwareagil.com)
 */
namespace SoftwareAgil\StarkenPro\Model\ResourceModel\Commune;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var boolean
     */
    protected $_regionAdded = false;

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('SoftwareAgil\StarkenPro\Model\Commune', 'SoftwareAgil\StarkenPro\Model\ResourceModel\Commune');
        $this->addOrder('main_table.name', \Magento\Framework\Data\Collection::SORT_ORDER_ASC);
    }

    /**
     * {@inheritdoc}
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->changeColumns();
        return $this;
    }

    /**
     * Add region table to collection
     *
     * @return \SoftwareAgil\StarkenPro\Model\ResourceModel\Commune\Collection
     */
    public function addRegionTable()
    {
        $this->getSelect()
            ->join(
                ['region' => $this->getTable('directory_country_region')],
                "region.region_id = main_table.region_id AND region.country_id = 'CL'",
                ['sp_region_id' => 'code', 'region_id' => 'region.region_id', 'region_name' => 'default_name']
            );
        $this->_regionAdded = true;

        return $this;
    }

    /**
     * Change columns in collection
     *
     * @return \SoftwareAgil\StarkenPro\Model\ResourceModel\Commune\Collection
     */
    public function changeColumns()
    {
        $columnsToAdd = ['commune_id_orig' => 'commune_id', 'commune_id' => 'sp_commune_id', 'name', 'sp_city_id' => 'main_table.city_id', 'region_id'];
        if ($this->_regionAdded) {
            $columnsToAdd['region_name'] = 'region.default_name';
        }
        $this->getSelect()->reset(\Zend_Db_Select::COLUMNS)->columns($columnsToAdd);
        return $this;
    }

    /**
     * Filter by region_id
     *
     * @param string|array $regionId
     * @return \SoftwareAgil\StarkenPro\Model\ResourceModel\Commune\Collection
     */
    public function addRegionFilter($regionId)
    {
        if (!empty($regionId)) {
            if (is_array($regionId)) {
                $this->addFieldToFilter('main_table.region_id', ['in' => $regionId]);
            } else {
                $this->addFieldToFilter('main_table.region_id', $regionId);
            }
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
            'value' => 'commune_id',
            'title' => 'name',
            'region_id' => 'region_id',
        ];

        foreach ($this as $item) {
            $option = [];
            foreach ($propertyMap as $code => $field) {
                $option[$code] = $item->getData($field);
            }
            $option['label'] = $item->getName();
            if ($this->_regionAdded) {
                $option['label'] = $item->getName() . " (" . $item->getRegionName() . ")";
            }
            $options[] = $option;
        }

        if (count($options) > 0) {
            array_unshift(
                $options,
                ['title' => '', 'value' => '', 'label' => __('Please select a commune.')]
            );
        }
        return $options;
    }
}
