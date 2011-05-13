<?php

namespace EasyPdf;
/**
 * PHP-class for writting PDF.
 *
 * @author greg
 */

class FontFileTTNode extends Node {

    /**
     * Font data.
     */
    private $_data;

    /**
     * Font Compressed.
     */
    private $_compressedData;

    public function __construct(Engine $pdf, FontDescriptor $fontD) {
        parent::__construct($pdf, $pdf->getSingleIndex(), $fontD->getGeneration(), $fontD);

        $this->_data = \file_get_contents($fontD->getFontNode()->getFilename());
        $this->_compressedData = \gzdeflate($this->_data);
    }

    public function output(&$pdf) {
        parent::preOutput($pdf);
        $this->data($pdf);
        parent::output($pdf);
    }

    private function data(&$pdf) {
        parent::writeObjHeader($pdf);

        $pdf .= "<< /Length " . strlen($this->_compressedData) . "\n";
        $pdf .= "/Filter FlateDecode\n";
        $pdf .= "/Length1 " . strlen($this->_data) . "\n";
        $pdf .= ">>\n";
        $pdf .= "stream\n";
        $pdf .= $this->_compressedData;
        $pdf .= "\nendstream\n";

        parent::writeObjFooter($pdf);
    }
}