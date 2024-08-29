<?php
/**
 * Customer Address Edit Form Base block
 *
 * @category     SoftwareAgil
 * @package      SoftwareAgil_StarkenPro
 * @author       SoftwareAgil (info@softwareagil.com)
 * @copyright    Copyright (c) 2020 SoftwareAgil (www.softwareagil.com)
 */
namespace SoftwareAgil\StarkenPro\Block\Form\Renderer\Element\Html;

/**
 * Date element block
 */
class Date extends \Magento\Framework\View\Element\Template
{
    /**
     * Render block HTML
     *
     * @return string
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _toHtml()
    {
        $saHtml = '<input type="text" name="' . $this->getName() . '" id="' . $this->getId() . '" ';
        $saHtml .= 'value="' . $this->_scaper->escapeHtml($this->getValue()) . '" ';
        $saHtml .= 'class="' . $this->getClass() . '" ' . $this->getExtraParams() . '/> ';
        $saCalendarYearsRange = $this->getYearsRange();
        $saChangeMonth = $this->getChangeMonth();
        $saChangeYear = $this->getChangeYear();
        $saMaxDate = $this->getMaxDate();
        $showOn = $this->getShowOn();
        $firstDay = $this->getFirstDay();

        $saHtml .= '<script type="text/javascript">
            require(["jquery", "mage/calendar"], function($){
                    $("#' .
            $this->getId() .
            '").calendar({
                        showsTime: ' .
            ($this->getTimeFormat() ? 'true' : 'false') .
            ',
                        ' .
            ($this->getTimeFormat() ? 'timeFormat: "' .
            $this->getTimeFormat() .
            '",' : '') .
            '
                        dateFormat: "' .
            $this->getDateFormat() .
            '",
                        buttonImage: "' .
            $this->getImage() .
            '",
                        ' .
            ($saCalendarYearsRange ? 'yearRange: "' .
            $saCalendarYearsRange .
            '",' : '') .
            '
                        buttonText: "' .
            (string)new \Magento\Framework\Phrase(
                'Select Date'
            ) .
            '"' . ($saMaxDate ? ', maxDate: "' . $saMaxDate . '"' : '') .
            ($saChangeMonth === null ? '' : ', changeMonth: ' . $saChangeMonth) .
            ($saChangeYear === null ? '' : ', changeYear: ' . $saChangeYear) .
            ($showOn ? ', showOn: "' . $showOn . '"' : '') .
            ($firstDay ? ', firstDay: ' . $firstDay : '') .
            '})
            });
            </script>';

        return $saHtml;
    }

    /**
     * Convert special characters to HTML entities
     *
     * @return string
     */
    public function getEscapedValue()
    {
        if ($this->getFormat() && $this->getValue()) {
            return strftime($this->getFormat(), strtotime($this->getValue()));
        }
        return $this->_scaper->escapeHtml($this->getValue());
    }

    /**
     * Produce and return block's html output
     *
     * {@inheritdoc}
     *
     * @return string
     */
    public function getHtml()
    {
        return $this->toHtml();
    }
}
