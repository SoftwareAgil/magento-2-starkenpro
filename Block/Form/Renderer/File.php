<?php
/**
 * Customer address form render block
 *
 * @api
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @since 100.0.2
 */
namespace SoftwareAgil\StarkenPro\Block\Form\Renderer;

use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Template;

/**
 * EAV Entity Attribute Form Renderer Block for File
 *
 * @api
 * @author      Magento Core Team <core@magentocommerce.com>
 * @since 100.0.2
 */
class File extends \SoftwareAgil\StarkenPro\Block\Form\Renderer\AbstractRenderer
{
    /**
     * @var \Magento\Framework\Url\EncoderInterface
     */
    protected $urlEncoder;

    /**
     * @param \Magento\Framework\Url\EncoderInterface $urlEncoder
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        array $data = []
    ) {
        $this->urlEncoder = $urlEncoder;
        parent::__construct($context, $data);
    }

    /**
     * Return escaped value
     *
     * @return string
     */
    public function getEscapedValue()
    {
        if ($this->getValue()) {
            return $this->_escaper->escapeHtml($this->urlEncoder->encode($this->getValue()));
        }
        return '';
    }
}
