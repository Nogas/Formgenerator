<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 * Button-Box with standrad buttons to control the form.
 * 
 * Supports the most used 'control' buttons for a form like
 * [OK] [Save] [Cancel] ...
 * Custom defined buttons can also be added.
 * Language can be configured through the config file.
 * 
 * #### History
 * - *2021-01-22*   initial version
 *
 * @package Formgenerator
 * @version 1.1.0
 * @author Stefanius <s.kien@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormButtonBox extends FormElement
{
    public const NONE       = 0;
    public const FIRST      = 0;
    public const LAST       = -1;
    public const OK	        = 0x0001;   // An "OK" button for submit.
    public const OPEN	    = 0x0002;   // An "Open" button for submit.
    public const SAVE       = 0x0004;   // A "Save" button for submit.
    public const YES	    = 0x0008;   // A "Yes" button for submit.
    public const NO         = 0x0010;   // A "No" button
    public const CANCEL     = 0x0020;   // A "Cancel" button
    public const CLOSE      = 0x0040;   // A "Close" button
    public const DISCARD    = 0x0080;   // A "Discard" button
    public const APPLY      = 0x0100;   // An "Apply" button for submit.
    public const RESET      = 0x0200;   // A "Reset" button
    public const RETRY      = 0x0400;   // A "Retry" button for submit.
    public const IGNORE     = 0x0800;   // An "Ignore" button
    public const BACK       = 0x1000;   // A "Back" button
    
    public const YES_NO_CANCEL = self::YES | self::NO | self::CANCEL;
    public const SAVE_CANCEL = self::SAVE | self::CANCEL;
    
    /** @var integer Buttons, the box containing     */
    protected int $iBtns = 0;
    /** @var array user defined button(s)     */
    protected array $aCustomButtons = [];

    /**
     * @param int $iBtns
     */
    public function __construct(int $iBtns, int $wFlags = 0)
    {
        $this->iBtns = $iBtns;
        parent::__construct($wFlags);
    }
    
    /**
     * Add custom button to the buttonbox.
     * Position of the button inside the box can be specified with the param $iAfterBtn: <ul>
     * <li> self::FIRST </li>
     * <li> self::LAST </li>
     * <li> other valid Button: the custom Button appears after this button </li></ul>
     * @param string $strText
     * @param string $strID
     * @param int $iAfterBtn
     * @param bool $bSubmit
     */
    public function addButton(string $strText, string $strID, int $iAfterBtn = self::LAST, bool $bSubmit = false) : void
    {
        $this->aCustomButtons[$iAfterBtn] = ['text' => $strText, 'id' => $strID, 'type' => ($bSubmit ? 'submit' : 'button') ]; 
    }

    /**
     * (non-PHPdoc)
     *
     * @see \SKien\Formgenerator\FormElement::getHTML()
     */
    public function getHTML() : string
    {
        if ($this->iBtns === 0) {
            return '';
        }
        if ($this->oFlags->isSet(FormFlags::ALIGN_CENTER)) {
            $this->addStyle('text-align', 'center');
        } else if ($this->oFlags->isSet(FormFlags::ALIGN_RIGHT)) {
            $this->addStyle('text-align', 'right');
        }
        
        $aButtonDef = $this->loadButtonDef();
        
        $strHTML = '<div id=buttonbox' . $this->buildStyle() . '>' . PHP_EOL;
        $iBtn = 0x0001;
        $strHTML .= $this->getCustomButton(self::FIRST);
        while ($iBtn < 0xffff) {
            if (($this->iBtns & $iBtn) != 0) {
                $strHTML .= $this->getButton($aButtonDef[$iBtn]);
            }
            $strHTML .= $this->getCustomButton($iBtn);
            $iBtn = $iBtn << 1;
        }
        $strHTML .= $this->getCustomButton(self::LAST);
        $strHTML .= '</div>' . PHP_EOL;
        
        return $strHTML;
    }
    
    /**
     * Set the tab index of first button.
     * Method is called from the PageGenerator after an element is added to the form.
     * @param int $iTabindex
     * @return int the count of buttons (-> number tabindexes 'needed')
     */
    public function setTabindex(int $iTabindex) : int
    {
        $this->iTabindex = $iTabindex;
        return $this->getButtonCount();
    }
    
    /**
     * Build the markup for the button.
     * @param array $aBtn
     * @return string
     */
    protected function getButton(array $aBtn) : string
    {
        $strHTML = '  <input id="' . $aBtn['id'] . '"';
        $strHTML .= ' type="' . $aBtn['type'] . '"';
        $strHTML .= ' tabindex="' . $this->iTabindex++ . '"';
        if ($this->oFlags->isSet(FormFlags::READ_ONLY | FormFlags::DISABLED)) {
            if ($aBtn['type'] == 'submit') {
                $strHTML .= ' disabled';
            }
        } else {
            $strHTML .= ' onclick="' . $aBtn['id'] . 'Clicked();"';
        }
        $strHTML .= ' value="' . $aBtn['text'] . '"';
        $strHTML .= '>' . PHP_EOL;
        
        return $strHTML;
    }

    /**
     * Build custom button, if defined for the requested position.
     * @param int $iAfterBtn
     * @return string
     */
    protected function getCustomButton(int $iAfterBtn) : string
    {
        if (!isset($this->aCustomButtons[$iAfterBtn])) {
            return '';
        }
        return $this->getButton($this->aCustomButtons[$iAfterBtn]);
    }

    /**
     * Get the number of buttons the box contains.
     * @return int
     */
    protected function getButtonCount() : int
    {
        $iCount = 0;
        $iBtns = $this->iBtns;
        while($iBtns) {
            $iCount += ($iBtns & 1);
            $iBtns >>= 1;
        }
        return $iCount + count($this->aCustomButtons);
    }
    
    /**
     * Get Textlabels for all buttons.
     * Default they are initialized with the englisch Text.
     * Configuration can contain localization.
     * @return array
     */
    protected function loadButtonDef() : array
    {
        $aButtonDef = [
            self::OK => ['text' => 'OK', 'id' => 'btnOK', 'type' => 'submit' ],
            self::OPEN => ['text' => 'Open', 'id' => 'btnOpen', 'type' => 'button' ],
            self::SAVE => ['text' => 'Save', 'id' => 'btnSave', 'type' => 'submit' ],
            self::YES => ['text' => 'Yes', 'id' => 'btnYes', 'type' => 'submit' ],
            self::NO => ['text' => 'No', 'id' => 'btnNo', 'type' => 'button' ],
            self::CANCEL => ['text' => 'Cancel', 'id' => 'btnCancel', 'type' => 'button' ],
            self::CLOSE => ['text' => 'Close', 'id' => 'btnClose', 'type' => 'button' ],
            self::DISCARD => ['text' => 'Discard', 'id' => 'btnDiscard', 'type' => 'button' ],
            self::APPLY => ['text' => 'Apply', 'id' => 'btnApply', 'type' => 'submit' ],
            self::RESET => ['text' => 'Reset', 'id' => 'btnReset', 'type' => 'button' ],
            self::RETRY => ['text' => 'Retry', 'id' => 'btnRetry', 'type' => 'submit' ],
            self::IGNORE => ['text' => 'Ignore', 'id' => 'btnIgnore', 'type' => 'button' ],
            self::BACK => ['text' => 'Back', 'id' => 'btnBack', 'type' => 'button' ],
        ];
        
        $aConfig = $this->oFG->getConfig()->getArray('ButtonBox.ButtonText');
        // To make it easier to read, the configuration contains the names of the constants 
        // as keys. So we have to convert the names into the values and assign the texts 
        // accordingly.
        foreach ($aConfig as $strName => $strText) {
            $iBtn = constant('self::' . $strName);
            $aButtonDef[$iBtn]['text'] = $strText;
        }
        return $aButtonDef;
    }
}

