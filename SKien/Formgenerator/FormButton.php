<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 * HTML form button element (inpt type="button")
 *
 * @package Formgenerator
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormButton extends FormInput
{
    /** @var string button text     */
    protected string $strBtnText;

    /**
     * Create button element.
     * Note: <br/>
     * Alignment self::ALIGN_CENTER / self::ALIGN_RIGHT set through the $wFlags dont affect the
     * alignment of the text within the button but the alignment of the button within the col! 
     * @param string $strId        button id
     * @param string $strBtnText   button text (value)
     * @param string $strOnClick   onClick() handler
     * @param int $wFlags       
     * @param string $strStyle     CSS style(s) (default: '')
     */
    public function __construct(string $strId, string $strBtnText, string $strOnClick, int $wFlags = 0, string $strStyle = '') 
    {
        $this->oFlags = new FormFlags($wFlags);
        $this->strBtnText = $strBtnText;
        $this->strID = $strId;
        $this->addAttribute('onclick', $strOnClick);
        
        if (strlen($strStyle) > 0) {
            $this->parseStyle($strStyle);
        }
    }
    
    /**
     * {@inheritDoc}
     * @see \SKien\Formgenerator\FormElement::fromXML()
     */
    static public function fromXML(\DOMElement $oXMLElement, FormCollection $oFormParent) : ?FormElement
    {
        $strId = self::getAttribString($oXMLElement, 'id', '');
        $strText = self::getAttribString($oXMLElement, 'text', '');
        $wFlags = self::getAttribFlags($oXMLElement);
        $oFormElement = new self($strId, $strText, '', $wFlags);
        $oFormParent->add($oFormElement);
        $oFormElement->readAdditionalXML($oXMLElement);
        return $oFormElement;
    }
    
    /**
     * Build the HTML-notation for the button.
     * @return string
     */
    public function getHTML() : string
    {
        $strStyle = '';
        if ($this->oFlags->isSet(FormFlags::ALIGN_CENTER)) {
            $strStyle = 'text-align: center;';
        } else if ($this->oFlags->isSet(FormFlags::ALIGN_RIGHT)) {
            $strStyle = 'text-align: right;';
        }
        $strHTML  = $this->buildContainerDiv($strStyle);

        $strHTML .= '<input type="button" ';
        $strHTML .= $this->buildID();
        $strHTML .= $this->buildStyle();
        $strHTML .= $this->buildAttributes();
        $strHTML .= ' value="' . $this->strBtnText . '"></div>' . PHP_EOL;

        return $strHTML;
    }
}
