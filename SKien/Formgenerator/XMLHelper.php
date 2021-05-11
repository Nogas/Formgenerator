<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 * Trait containing some Helper to generate the Form from XML-File  
 * @package Formgenerator
 * @author Stefanius <s.kien@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
trait XMLHelper
{
    /**
     * Get the string value of named attrib or return default value, if attrib not exist
     * @param \DOMElement $oXMLElement
     * @param string $strName
     * @param string $strDefault
     * @return string|null
     */
    static protected function getAttribString(\DOMElement $oXMLElement, string $strName, ?string $strDefault = null) : ?string
    {
        if (!$oXMLElement->hasAttribute($strName)) {
            return $strDefault;
        }
        return $oXMLElement->getAttribute($strName);
    }
    
    /**
     * Get the integer value of named attrib or return default value, if attrib not exist
     * @param \DOMElement $oXMLElement
     * @param string $strName
     * @param int $iDefault
     * @return int|null
     */
    static protected function getAttribInt(\DOMElement $oXMLElement, string $strName, ?int $iDefault = null) : ?int
    {
        if (!$oXMLElement->hasAttribute($strName)) {
            return $iDefault;
        }
        return intval($oXMLElement->getAttribute($strName));
    }
    
    /**
     * Get an array of string values of the named attrib.
     * The attrib must contain a list spearated by comma.
     * @param \DOMElement $oXMLElement
     * @param string $strName
     * @return array|null
     */
    static protected function getAttribStringArray(\DOMElement $oXMLElement, string $strName) : ?array
    {
        $aValues = null;
        $strArray = $oXMLElement->getAttribute($strName);
        if (strlen($strArray) > 0) {
            $aValues = array_map('trim', explode(',', $strArray));
        }
        return $aValues;
    }
    
    /**
     * Get an array of integer values of the named attrib.
     * The attrib must contain a list spearated by comma.
     * @param \DOMElement $oXMLElement
     * @param string $strName
     * @return array|null
     */
    static protected function getAttribIntArray(\DOMElement $oXMLElement, string $strName) : ?array
    {
        $aValues = null;
        $strArray = $oXMLElement->getAttribute($strName);
        if (strlen($strArray) > 0) {
            $aValues = array_map('intval', explode(',', $strArray));
        }
        return $aValues;
    }
    
    /**
     * Get the flags specified by the named attrib.
     * The attrib must contain the textlist of FormFlag - constants spearated by comma. 
     * @param \DOMElement $oXMLElement
     * @return int
     */
    static protected function getAttribFlags(\DOMElement $oXMLElement) : int
    {
        $wFlags = 0;
        $strFlags = $oXMLElement->getAttribute('flags');
        if (strlen($strFlags) > 0) {
            $aFlags = array_map('trim', explode(',', $strFlags));
            foreach ($aFlags as $strFlag) {
                $strConstName = __NAMESPACE__ . '\FormFlags::' . strtoupper($strFlag);
                if (defined($strConstName)) {
                    $wFlags += constant($strConstName);
                } else {
                    trigger_error('Unknown Constant [' . $strConstName . '] for the FormFlag property!', E_USER_WARNING);
                }
            }
        }
        return $wFlags;
    }
    
    /**
     * Read all known attributes that don't need any further processing. 
     * @param \DOMElement $oXMLElement
     */
    public function readElementAttributes(\DOMElement $oXMLElement, ?array $aAttributes) : array
    {
        $aAttributesToRead = [
            'onclick', 
            'ondblclick', 
            'onchange', 
            'oninput', 
            'onfocus', 
            'onblur', 
            'onkeydown', 
            'onkeypress',
            'onkeyup',
            'title', 
            'placeholder', 
            'maxlength', 
        ];
        if ($aAttributes == null) {
            $aAttributes = array();
        }
        foreach ($aAttributesToRead as $strAttribute) {
            if (($strValue = self::getAttribString($oXMLElement, $strAttribute)) !== null) {
                $aAttributes[$strAttribute] = $strValue;
            }
        }
        return $aAttributes;
    }
}