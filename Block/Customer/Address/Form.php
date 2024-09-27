<?php
/**
 * Customer Address Edit Form block
 *
 * @category     SoftwareAgil
 * @package      SoftwareAgil_StarkenPro
 * @author       SoftwareAgil (info@softwareagil.com)
 * @copyright    Copyright (c) 2020 SoftwareAgil (www.softwareagil.com)
 */
namespace SoftwareAgil\StarkenPro\Block\Customer\Address;

use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Customer\Api\AddressMetadataInterface;
use Magento\Customer\Model\Address;
use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Entity\Type as EntityType;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Session;

/**
 * Customer Dynamic attributes Form Block
 *
 * @api
 * @author      Magento Core Team <core@magentocommerce.com>
 * @since 100.0.2
 */
class Form extends \SoftwareAgil\StarkenPro\Block\Customer\Address\FormBase
{
    /**
     * @var \Magento\Customer\Model\Metadata\Form
     */
    protected $_metadataForm;

    /**
     * @var \Magento\Customer\Model\Metadata\FormFactory
     */
    protected $_metadataFormFactory;

    /**
     * @var Session
     */
    protected $_customerSession;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \SoftwareAgil\StarkenPro\Model\Data\Collection\ModelFactory $modelFactory
     * @param \Magento\Eav\Model\Form\Factory $formFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Customer\Model\Metadata\FormFactory $metadataFormFactory
     * @param Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \SoftwareAgil\StarkenPro\Model\Data\Collection\ModelFactory $modelFactory,
        \Magento\Eav\Model\Form\Factory $formFactory,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Customer\Model\Metadata\FormFactory $metadataFormFactory,
        Session $customerSession,
        array $data = []
    ) {
        parent::__construct($context, $modelFactory, $formFactory, $eavConfig, $data);
        $this->_metadataFormFactory = $metadataFormFactory;
        $this->_customerSession = $customerSession;
        $this->_isScopePrivate = true;
    }

    /**
     * Name of the block in layout update xml file
     *
     * @var string
     */
    protected $_xmlBlockName = 'customer_form_template';

    /**
     * Class path of Form Model
     *
     * @var string
     */
    protected $_formModelPath = 'Magento\Customer\Model\Form';

    /**
     * Returns (and initiates) metadata form.
     *
     * @return \Magento\Customer\Model\Metadata\Form
     */
    public function getMetadataForm()
    {
        if ($this->_metadataForm === null) {
            $this->_metadataForm = $this->_metadataFormFactory->create(
                $this->_entityType->getEntityTypeCode(),
                $this->_formCode
            );
            // @todo initialize default values  MAGETWO-17600
        }
        return $this->_metadataForm;
    }

    /**
     * Return Entity object
     *
     * @return \Magento\Framework\Model\AbstractModel
     */
    public function getEntity()
    {
        if ($this->_entity === null && $this->_entityModelClass) {
            $entity = $this->_modelFactory->create($this->_entityModelClass);
            /** @var EntityType $entityType */
            $entityType = $entity->getData('entity_type');
            $entityId = isset($entityType) ? $this->getCurrentEntityId($entityType) : false;
            if ($entityId) {
                $entity->load($entityId);
                if ($entityType->getEntityTypeCode() === AddressMetadataInterface::ENTITY_TYPE_ADDRESS) {
                    if ($entity->getData('customer_id') != $this->_customerSession->getCustomerId()) {
                        $entity = $this->_modelFactory->create(
                            $this->_entityModelClass
                        );
                    }
                }
            } elseif ($this->getObject()) {
                $entity->setData($this->getObject()->getData());
            }
            $this->_entity = $entity;
        }

        return $this->_entity;
    }

    /**
     * Retrieve current entity type
     *
     * @param EntityType $entityType
     * @return int|null
     *
     * @SuppressWarnings(PHPMD.RequestAwareBlockMethod)
     */
    protected function getCurrentEntityId(EntityType $entityType)
    {
        switch ($entityType->getEntityTypeCode()) {
            case CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER:
                return $this->_customerSession->getCustomerId();
                break;
            case AddressMetadataInterface::ENTITY_TYPE_ADDRESS:
                return (int)$this->getRequest()->getParam('id');
                break;
            default:
                return null;
        }
    }
}
