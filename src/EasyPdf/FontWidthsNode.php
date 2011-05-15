<?php

namespace EasyPdf;
/**
 * PHP-class for writting PDF.
 *
 * @author greg
 */

class FontWidthsNode extends Node {
    
    private $_data;
    
    public function __construct(Engine &$pdf, FontNode &$font) {
        parent::__construct($pdf, $pdf->getSingleIndex(), 0, $font);
    }
    
    public function setData($data) {
        $this->_data = $data;
    }

    public function getData() {
        return $this->_data;
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