<?php
/**
 * Tracking Info block
 *
 * @category     SoftwareAgil
 * @package      SoftwareAgil_StarkenPro
 * @author       SoftwareAgil (info@softwareagil.com)
 * @copyright    Copyright (c) 2020 SoftwareAgil (www.softwareagil.com)
 */
namespace SoftwareAgil\StarkenPro\Block\Adminhtml\Shipment\View;

class TrackingInfo extends \Magento\Backend\Block\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Columns
     *
     * @var array
     */
    protected $_columns = [];

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $_json;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Serialize\Serializer\Json $json,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_json = $json;
        $this->_columns = [
            'status' => __('Status'),
            'note' => __('Note'),
            'created_at' => __('Created At'),
            'updated_at' => __('Updated At')
        ];
        parent::__construct($context, $data);
    }

    /**
     * Retrieve order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        $order = $this->_coreRegistry->registry('current_order');
        if (!$order) {
            $shipment = $this->_coreRegistry->registry('current_shipment');
            $order = $shipment->getOrder();
        }
        return $order;
    }

    /**
     * Retrieve shipment model instance
     *
     * @return \Magento\Sales\Model\Order\Shipment
     */
    public function getShipment()
    {
        $currentShipment = new \Magento\Framework\DataObject();
        if ($this->getOrder()->getShipmentsCollection()->getSize()) {
            $currentShipment = $this->getOrder()->getShipmentsCollection()->getFirstItem();
        }

        return $currentShipment;
    }

    /**
     * Retrieve Shipment Tracking Content
     *
     * @return \Magento\Sales\Model\Order\Shipment
     */
    public function getSaSpTrackingContent()
    {
        return $this->getShipment()->getData('sa_sp_tracking_content');
    }

    /**
     * Is a StarkenPro shipping method?
     *
     * @return \Magento\Sales\Model\Order\Shipment
     */
    public function isStarkenPro()
    {
        if (strpos($this->getOrder()->getShippingMethod(), "starkenpro_") === false) {
            return false;
        }
        return true;
    }

    /**
     * Get items
     *
     * @return \Magento\Sales\Model\Order\Shipment
     */
    public function getItems()
    {
        $items = [];
        if ($this->getShipment()->getData('sa_sp_tracking_content')) {
            $arrayTemp = $this->_json->unserialize($this->getShipment()->getData('sa_sp_tracking_content'));
            if (isset($arrayTemp["history"])) {
                foreach ($arrayTemp["history"] as $event) {
                    $items[] = [
                        'status' => $event['status'],
                        'note' => $event['note'],
                        'created_at' => $event['created_at'],
                        'updated_at' => $event['updated_at']
                    ];
                }
            }
        }
        return $items;
    }

    /**
     * Get columns
     *
     * @return \Magento\Sales\Model\Order\Shipment
     */
    public function getColumns()
    {
        return $this->_columns;
    }
}
