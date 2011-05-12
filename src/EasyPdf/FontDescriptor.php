<?php

namespace EasyPdf;
/**
 * PHP-class for writting PDF.
 *
 * @author greg
 */

class FontDescriptor extends \EasyPdf\Node {
    
    private $_fontNode;
    
    public function __construct(Engine &$pdf, FontNode $font) {
        parent::__construct($pdf, $pdf->getSingleIndex(), $font->getGeneration(), $font);
        $this->_fontNode = $font;
    }
    
    public function setFont(FontNode $font) {
        $this->_fontNode = $font;
    }
    
    public function output(&$pdf) {
        parent::preOutput($pdf);
        $this->data($pdf);
        parent::output($pdf);
    }
    
    private function data(&$pdf) {
        parent::writeObjHeader($pdf);
        $properties = $this->_fontNode->getProperties();
        
        $pdf .= "<< /Type /FontDescriptor\n";
        $pdf .= "/FontName /" . $properties['FontName']['value'] . "\n";
        $pdf .= "/Ascent " . $properties['Ascender']['value'] . "\n";
        $pdf .= "/Descent " . $properties['Descender']['value'] . "\n";
        $pdf .= "/CapHeight " . $properties['CapHeight']['value'] . "\n";
        $pdf .= "/Flags " . $properties['Flags']['value'] . "\n";
        $pdf .= "/FontBBox [" . $properties['FontBBox']['value'][0] . " " . $properties['FontBBox']['value'][1] . " " . $properties['FontBBox']['value'][2] . " " . (int)$properties['FontBBox']['value'][3] . "]\n";
        $pdf .= "/ItalicAngle " . $properties['ItalicAngle']['value'] . "\n";
        $pdf .= "/StemV " . $properties['StdVW']['value'] . "\n";
        $pdf .= "/MissingWidth " . $properties['MissingWidth']['value'] . "\n"; 
        $pdf .= ">>\n";
        
        
        parent::writeObjFooter($pdf);
    }
    
}
