<?php

namespace EasyPdf;
/**
 * PHP-class for writting PDF.
 *
 * @author greg
 */

class AreaNode extends ADrawableNode {

    public function __construct(PageNode $page) {
        parent::__construct($page);
    }

    public function output(&$pdf) {
        parent::preOutput($pdf);
        $this->data($pdf);
        parent::output($pdf);
    }

    private function data(&$pdf) {
        parent::writeObjHeader($pdf);
        
        if ($this->_drawArea) {

            $unitFactor = $this->_engine->getUnitFactor();
            $pageHeight = $this->_parent->getHeight();
            $toWrite = sprintf("%.2F %.2F %.2F %.2F re %s",
                    ($this->getX()) * $unitFactor,
                    $pageHeight - ($this->getY()* $unitFactor),
                    $this->_width * $unitFactor,
                    -$this->_height * $unitFactor,
                    'S');

            $pdf .= "<< /Length " . strlen($toWrite) . " >>\n";
            $pdf .= "stream\n";
            $pdf .= $toWrite;
            $pdf .= "\nendstream\n";
        } else {
            $pdf .= "<< /Length 0 >>\n";
            $pdf .= "stream\n";
            $pdf .= "\nendstream\n";
        }

        parent::writeObjFooter($pdf);
    }

    public function drawArea($value) {
        $this->_drawArea = $value;
    }
}