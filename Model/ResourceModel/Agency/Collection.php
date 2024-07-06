<?php
/**
 * Agencies resource collection
 *
 * @category     SoftwareAgil
 * @package      SoftwareAgil_StarkenPro
 * @author       SoftwareAgil (info@softwareagil.com)
 * @copyright    Copyright (c) 2020 SoftwareAgil (www.softwareagil.com)
 */
namespace SoftwareAgil\StarkenPro\Model\ResourceModel\Agency;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('SoftwareAgil\StarkenPro\Model\Agency', 'SoftwareAgil\StarkenPro\Model\ResourceModel\Agency');
        $this->addOrder('name', \Magento\Framework\Data\Collection::SORT_ORDER_ASC);
    }

    /**
     * {@inheritdoc}
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addCommuneTable()->changeColumns();
        return $this;
    }

    /**
     * Add commune table to collection
     *
     * @return \SoftwareAgil\StarkenPro\Model\ResourceModel\Commune\Collection
     */
    public function addCommuneTable()
    {
        $this->getSelect()
            ->join(
                ['commune' => $this->getTable('sa_sp_commune')],
                'commune.sp_commune_id = main_table.commune_id',
                ['sp_commune_id', 'commune_name' => 'name']
            );

        return $this;
    }

    /**
     * Change columns in collection
     *
     * @return \SoftwareAgil\StarkenPro\Model\ResourceModel\Commune\Collection
     */
    public function changeColumns()
    {
        $columnsToAdd = ['agency_id' => 'sp_agency_id', 'name' => "GROUP_CONCAT(main_table.name SEPARATOR ', ' )", 'commune_id', 'sp_commune_id' => 'commune.sp_commune_id'];
        $this->getSelect()->reset(\Zend_Db_Select::COLUMNS)->columns($columnsToAdd);
        $this->getSelect()->group(['sp_agency_id', 'commune_id', 'sp_commune_id' => 'commune.sp_commune_id']);

        return $this;
    }

    /**
     * Filter by commune_id
     *
     * @param string|array $communeId
     * @return \SoftwareAgil\StarkenPro\Model\ResourceModel\Agency\Collection
     */
    public function addCommuneFilter($communeId)
    {
        if (!empty($communeId)) {
            if (is_array($communeId)) {
                $this->addFieldToFilter('main_table.commune_id', ['in' => $communeId]);
            } else {
                $this->addFieldToFilter('main_table.commune_id', $communeId);
            }
        }
        return $this;
    }

    /**
     * Filter by sp_commune_id
     *
     * @param string|array $communeId
     * @return \SoftwareAgil\StarkenPro\Model\ResourceModel\Agency\Collection
     */
    public function addCommuneSpFilter($communeId)
    {
        if (!empty($communeId)) {
            if (is_array($communeId)) {
                $this->addFieldToFilter('commune.sp_commune_id', ['in' => $communeId]);
            } else {
                $this->addFieldToFilter('commune.sp_commune_id', $communeId);
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
            'value' => 'agency_id',
            'title' => 'name',
            'commune_id' => 'commune_id',
        ];

        foreach ($this as $item) {
            $option = [];
            foreach ($propertyMap as $code => $field) {
                $option[$code] = $item->getData($field);
            }
            $option['label'] = $item->getName();
            $options[] = $option;
        }

        if (count($options) > 0) {
            array_unshift(
                $options,
                ['title' => '', 'value' => '', 'label' => __('To send to Agency select an option')]
            );
        }
        return $options;
    }
}
