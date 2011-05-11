<?php

/**
 * PHP-class for writting PDF.
 *
 * @author greg
 */

class EPDFResourcesNode extends EPDFNode {
    
    /**
     * Font resources.
     */
    private $_fonts;
    
    public function EPDFResourcesNode(EPDFEngine &$pdf, EPDFPageNode &$page) {
        parent::EPDFNode($pdf, $pdf->getSingleIndex(), 0, $page);
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
    
    public function addFont(EPDFFontNode $font) {
        $this->_fonts[] = $font;
        $this->_childs[] = $font;
    }
    
}