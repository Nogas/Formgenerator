<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 * Class to display a header line inside of the form.
 *
 * @package Formgenerator
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormHeader extends FormElement
{
    /** @var string text for the header     */
    protected string $strText;
    /** @var int level of the HTML header element     */
    protected int $iLevel; 
    
    /**
     * Create header element (<h2> header)
     * @param string $strText
     * @param number $iLevel
     */
    public function __construct(string $strText, $iLevel = 2) 
    {
        parent::__construct(0);
        $this->strText = $strText;
        $this->iLevel = $iLevel;
    }
    
    /**
     * {@inheritDoc}
     * @see \SKien\Formgenerator\FormElement::fromXML()
     */
    static public function fromXML(\DOMElement $oXMLElement, FormCollection $oFormParent) : ?FormElement
    {
        $strText = self::getAttribString($oXMLElement, 'text', '');
        $iLevel = self::getAttribInt($oXMLElement, 'level', 2);
        $oFormElement = new self($strText, $iLevel);
        $oFormParent->add($oFormElement);
        return $oFormElement;
    }
    
    /**
     * Build the HTML-notation for the header text
     * @return string
     */
    public function getHTML() : string
    {
        $strHTML = '<h' . $this->iLevel . '>' . $this->strText . '</h' . $this->iLevel . '>' . PHP_EOL;
        return $strHTML;
    }
}
