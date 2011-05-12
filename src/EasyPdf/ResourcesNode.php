<?php

namespace EasyPdf;
/**
 * PHP-class for writting PDF.
 *
 * @author greg
 */

class ResourcesNode extends Node {
    
    /**
     * Font resources.
     */
    private $_fonts;
    
    public function __construct(Engine &$pdf, PageNode &$page) {
        parent::__construct($pdf, $pdf->getSingleIndex(), 0, $page);
    }
    
    public function output(&$pdf) {
        parent::preOutput($pdf);
        $this->data($pdf);
        parent::output($pdf);
    }
    
    private function data(&$pdf) {
        parent::writeObjHeader($pdf);
        
        $pdf .= "<<\n";
        $pdf .= "/Font <<\n";
        
        for ($i = 0; $i < count($this->_fonts); ++$i) {
            $pdf .= "/F" . ($i + 1) . " " . $this->_fonts[$i]->getIndirectReference() . "\n";
        }
        
        $pdf .= ">>\n>>\n";
        
        parent::writeObjFooter($pdf);
    }
    
    public function addFont(FontNode $font) {
        $this->_fonts[] = $font;
        $this->_childs[] = $font;
    }
    
}