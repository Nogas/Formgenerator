<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 * Input element for integer value.
 *
 * @package Formgenerator
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormInt extends FormInput
{
    /** @var bool empty entries allowed. If false, empty input is set to '0' */
    protected bool $bEmptyAllowed = false;
    
    /**
     * @param string $strName   name of input
     * @param int|string $iSize     size in digits
     * @param int $wFlags    flags (default = 0)
     */
    public function __construct(string $strName, $iSize, int $wFlags = 0) 
    {
        parent::__construct($strName, $iSize, $wFlags);
    }
    
    /**
     * {@inheritDoc}
     * @see \SKien\Formgenerator\FormElement::fromXML()
     */
    static public function fromXML(\DOMElement $oXMLElement, FormCollection $oFormParent) : ?FormElement
    {
        $strName = self::getAttribString($oXMLElement, 'name', '');
        $strSize = self::getAttribString($oXMLElement, 'size', '');
        $wFlags = self::getAttribFlags($oXMLElement);
        $oFormElement = new self($strName, $strSize, $wFlags);
        $oFormParent->add($oFormElement);
        $oFormElement->readAdditionalXML($oXMLElement);
        return $oFormElement;
    }
    
    /**
     * {@inheritDoc}
     * @see \SKien\Formgenerator\FormElement::readAdditionalXML()
     */
    public function readAdditionalXML(\DOMElement $oXMLElement) : void
    {
        parent::readAdditionalXML($oXMLElement);
        $this->setMinMax(self::getAttribInt($oXMLElement, 'min'), self::getAttribInt($oXMLElement, 'max'));
    }
    
    /**
     * We 'ask' the configuration for alignment.
     * $this->oFG is not available until the parent is set!
     */
    protected function onParentSet() : void
    {
        if ($this->oFG->getConfig()->getBool('Integer.RightAlign', true)) {
            $this->addFlags(FormFlags::ALIGN_RIGHT);
        }
        
        // if readonly, don't set the 'number' type...
        if (!$this->oFlags->isSet(FormFlags::READ_ONLY)) {
            $this->strType = 'number';
            $this->addStyle('width', $this->size . 'em');
        }
        $this->addAttribute('data-validation', 'integer:' . ($this->bEmptyAllowed ? 'e' : 'x'));
    }
    
    /**
     * Set min/max value accepted by input field. 
     * @param int $iMin - set to null, if no limitation wanted
     * @param int $iMax - set to null, if no limitation wanted
     */
    public function setMinMax(?int $iMin, ?int $iMax) : void 
    {
        if ($iMin !== null) {
            $this->addAttribute('min', (string)$iMin);
        }
        if ($iMax !== null) {
            $this->addAttribute('max', (string)$iMax);
        }
    }
    
    /**
     * {@inheritDoc}
     * @see \SKien\Formgenerator\FormElement::buildValue()
     */
    protected function buildValue() : string
    {
        $iValue = intval($this->oFG->getData()->getValue($this->strName));
        if ($this->oFlags->isSet(FormFlags::NO_ZERO) && $iValue == 0) {
            return '';
        }
        $strHTML = ' value="' . $iValue . '"';
        
        return $strHTML;
    }
}
