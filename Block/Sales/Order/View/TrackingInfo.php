<?php
/**
 * Tracking Info block
 *
 * @category     SoftwareAgil
 * @package      SoftwareAgil_StarkenPro
 * @author       SoftwareAgil (info@softwareagil.com)
 * @copyright    Copyright (c) 2020 SoftwareAgil (www.softwareagil.com)
 */
namespace SoftwareAgil\StarkenPro\Block\Sales\Order\View;

use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\ShipmentRepositoryInterface;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\InputException;

class TrackingInfo extends \Magento\Framework\View\Element\Template
{
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

    protected $_current_order;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Serialize\Serializer\Json $json,
        protected OrderRepositoryInterface $orderRepository,
        protected ShipmentRepositoryInterface $orderShipmentRepository,
        protected MessageManagerInterface $messageManager,
        array $data = []
    ) {
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
        if (is_null($this->_current_order)) {
            $this->_current_order = $this->_initOrder();
            if (!$this->_current_order) {
                $shipment = $this->_initOrderShipment();
                $this->_current_order = $shipment->getOrder();
            }
        }

        return $this->_current_order;
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

    /**
     * Initialize order model instance
     *
     * @return OrderInterface|false
     */
    protected function _initOrder()
    {
        $id = $this->getRequest()->getParam('order_id');
        try {
            $order = $this->orderRepository->get($id);
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('This order no longer exists.'));
            return false;
        } catch (InputException $e) {
            $this->messageManager->addErrorMessage(__('This order no longer exists.'));
            return false;
        }

        return $order;
    }

    /**
     * Initialize order shipment model instance
     *
     * @return OrderInterface|false
     */
    protected function _initOrderShipment()
    {
        $id = $this->getRequest()->getParam('shipment_id');
        try {
            $orderShipment = $this->orderShipmentRepository->get($id);
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('This shipment no longer exists.'));
            return false;
        } catch (InputException $e) {
            $this->messageManager->addErrorMessage(__('This shipment no longer exists.'));
            return false;
        }

        return $orderShipment;
    }
}
