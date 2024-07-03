<?php
/**
 * Address agency attribute source
 *
 * @category     SoftwareAgil
 * @package      SoftwareAgil_StarkenPro
 * @author       SoftwareAgil (info@softwareagil.com)
 * @copyright    Copyright (c) 2020 SoftwareAgil (www.softwareagil.com)
 */
namespace SoftwareAgil\StarkenPro\Model\ResourceModel\Address\Attribute\Source;

class Agency extends \Magento\Eav\Model\Entity\Attribute\Source\Table
{
    /**
     * @var \SoftwareAgil\StarkenPro\Model\ResourceModel\Agency\CollectionFactory
     */
    protected $_agencyFactory;

    /**
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $attrOptionFactory
     * @param \SoftwareAgil\StarkenPro\Model\ResourceModel\Agency\CollectionFactory $agencyFactory
     */
    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $attrOptionFactory,
        \SoftwareAgil\StarkenPro\Model\ResourceModel\Agency\CollectionFactory $agencyFactory
    ) {
        $this->_agencyFactory = $agencyFactory;
        parent::__construct($attrOptionCollectionFactory, $attrOptionFactory);
    }

    /**
     * @inheritdoc
     */
    public function getAllOptions($withEmpty = true, $defaultValues = false)
    {
        if (!$this->_options) {
            $this->_options = $this->_createAgencyCollection()->load()->toOptionArray();
        }
        return $this->_options;
    }

    /**
     * @return \SoftwareAgil\StarkenPro\Model\ResourceModel\Agency\Collection
     */
    protected function _createAgencyCollection()
    {
        return $this->_agencyFactory->create();
    }
}
