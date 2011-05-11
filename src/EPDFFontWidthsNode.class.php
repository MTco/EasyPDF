<?php

/**
 * PHP-class for writting PDF.
 *
 * @author greg
 */

class EPDFFontWidthsNode extends EPDFNode {
    
    private $_data;
    
    public function EPDFFontWidthsNode(EPDFEngine &$pdf, EPDFFontNode &$font) {
        parent::EPDFNode($pdf, $pdf->getSingleIndex(), 0, $font);
    }
    
    public function setData($data) {
        $this->_data = $data;
    }
    
    public function output(&$pdf) {
        parent::preOutput($pdf);
        $this->data($pdf);
        parent::output($pdf);
    }
    
    private function data(&$pdf) {
        parent::writeObjHeader($pdf);
        
        $pdf .= "[";
        foreach ($this->_data as $v) {
            $pdf .= $v . " ";
        }
        $pdf .= "]\n";
        parent::writeObjFooter($pdf);
    }
    
}

?>