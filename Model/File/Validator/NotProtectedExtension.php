<?php
/**
 * @category     SoftwareAgil
 * @package      SoftwareAgil_StarkenPro
 * @author       SoftwareAgil (info@softwareagil.com)
 * @copyright    Copyright (c) 2020 SoftwareAgil (www.softwareagil.com)
 */
namespace SoftwareAgil\StarkenPro\Model\File\Validator;

use Laminas\Validator\AbstractValidator;

/**
 * Validator for check not protected file extensions
 */
class NotProtectedExtension extends AbstractValidator
{
    /**
     * Protected extension message key
     */
    public const SA_PROTECTED_EXTENSION = 'protectedExtension';

    /**
     * Protected files config path
     */
    public const XML_PATH_SA_PROTECTED_FILE_EXTENSIONS = 'general/file/protected_extensions';

    /**
     * The file extension
     *
     * @var string
     */
    protected $saValue;

    /**
     * Protected file types
     *
     * @var string[]
     */
    protected $_saProtectedFileExtensions = [];

    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;
    /**
     * @var array
     */
    protected $messageTemplates;

    /**
     * Init validator
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->_scopeConfig = $scopeConfig;
        $this->_saInitMessageTemplates();
        $this->_saInitProtectedFileExtensions();
        parent::__construct();
    }

    /**
     * Initialize message templates with translating
     *
     * @return $this
     */
    protected function _saInitMessageTemplates()
    {
        if (!$this->messageTemplates) {
            $this->messageTemplates = [
                self::SA_PROTECTED_EXTENSION => __('File with an extension "%value%" is protected and cannot be uploaded'),
            ];
        }
        return $this;
    }

    /**
     * Initialize protected file extensions
     *
     * @return $this
     */
    protected function _saInitProtectedFileExtensions()
    {
        if (!$this->_saProtectedFileExtensions) {
            $extensions = $this->getProtectedFileExtensions();
            if (is_string($extensions)) {
                $extensions = explode(',', $extensions);
            }
            foreach ($extensions as &$ext) {
                $ext = strtolower(trim($ext));
            }
            $this->_saProtectedFileExtensions = (array)$extensions;
        }
        return $this;
    }

    /**
     * Return list with protected file extensions
     *
     * @param \Magento\Store\Model\Store|string|int $store
     * @return string|string[]
     */
    public function getProtectedFileExtensions($store = null)
    {
        return $this->_scopeConfig->getValue(
            self::XML_PATH_SA_PROTECTED_FILE_EXTENSIONS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Returns true if and only if $saValue meets the validation requirements
     *
     * If $saValue fails validation, then this method returns false, and
     * getMessages() will return an array of messages that explain why the
     * validation failed.
     *
     * @param string $saValue         Extension of file
     * @return bool
     */
    public function isValid($saValue)
    {
        $saValue = strtolower(trim($saValue));
        $this->setValue($saValue);

        if (in_array($this->value, $this->_saProtectedFileExtensions)) {
            $this->error(self::SA_PROTECTED_EXTENSION, $this->value);
            return false;
        }

        return true;
    }
}
