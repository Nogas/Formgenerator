<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 * Class to create a line starting with label.
 * - create as child of a FormField using FormFieldSet::addLine().
 * - create standalone as direct child of the form
 *
 * @package Formgenerator
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormLine extends FormCollection
{
    /** line contains only HR - no further childs! */
    const HR = '<hr>';

    /** @var string text for the line label     */
    protected string $strLabel;
    /** @var int col count     */
    protected int $iCols = 0;

    /**
     * Create new line.
     * Label is allways the first child / col of the line.
     * With `$strLabel = self::HR` a horizontal line is created.
     * @param string $strLabel  text for label
     */
    public function __construct(string $strLabel)
    {
        parent::__construct(0);
        $this->strLabel = $strLabel;
        $this->iCol = 0;
        $this->strID = '';
    }

    /**
     * {@inheritDoc}
     * @see \SKien\Formgenerator\FormElement::fromXML()
     * @internal
     */
    static public function fromXML(\DOMElement $oXMLElement, FormCollection $oFormParent) : ?FormElement
    {
        if (self::getAttribString($oXMLElement, 'horzline') !== null) {
            $strLabel = self::HR;
        } else {
            $strLabel = self::getAttribString($oXMLElement, 'label', '&nbsp;');
        }
        $oFormElement = new self($strLabel);
        $oFormParent->add($oFormElement);
        $oFormElement->readAdditionalXML($oXMLElement);

        return $oFormElement;
    }

    /**
     * Add a child to the line.
     * Some properties of the element to add (parent, tabindex, ...) are changed/set
     * with the call of this method.
     * next col index is passed to the element and the col count is inkremented with
     * each element added to this line.
     * @param FormElementInterface $oElement element to add
     * @return FormElementInterface added element
     */
    public function add(FormElementInterface $oElement) : FormElementInterface
    {
        parent::add($oElement);
        $oElement->setCol(++$this->iCols);

        return $oElement;
    }

    /**
     * Build the HTML code for the element.
     * The line is 'abstract' and representet by a div. <br/>
     * All direct child elements are generated inside this div.
     * @return string
     * @internal
     */
    public function getHTML() : string
    {
        $strWidth = $this->getColWidth();
        if (!empty($strWidth)) {
            $this->addStyle('width', $strWidth);
        }
        if (!isset($this->aStyle['float'])) {
            $this->addStyle('float', 'left');
        }

        $strHTML  = '';
        $strHTML .= '   <div';
        $strHTML .= $this->buildID();
        $strHTML .= ">" . PHP_EOL;
        if (strtolower($this->strLabel) == self::HR) {
            $strHTML .= '<hr>';
        } else {
            if (strlen($this->strLabel) > 0 && $strWidth != '0%') {
                $strHTML .= '       <label';
                $strHTML .= $this->buildStyle();
                $strHTML .= '>' . $this->strLabel . '</label>' . PHP_EOL;
            }
            $iCnt = count($this->aChild);
            for ($i = 0; $i < $iCnt; $i++) {
                $strHTML .= $this->aChild[$i]->getHTML();
            }
            $strHTML .= '       <br style="clear:both;" />' . PHP_EOL;
        }
        $strHTML .= '   </div>' . PHP_EOL;
        return $strHTML;
    }
}
