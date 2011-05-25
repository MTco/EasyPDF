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

    public function __construct(Engine $pdf, FontDescriptorNode $fontD) {
        parent::__construct($pdf, $pdf->getSingleIndex(), $fontD->getGeneration(), $fontD);

        $this->_data = \file_get_contents($fontD->getFontNode()->getFilename());
        $this->_compressedData = \gzcompress($this->_data);
    }

    public function output(&$pdf) {
        parent::preOutput($pdf);
        $this->data($pdf);
        parent::output($pdf);
    }

    private function data(&$pdf) {
        $data = $this->getBaseDataForTpl();
        $data['length'] = strlen($this->_compressedData);
        $data['filter'] = "FlateDecode";
        $data['length1'] = strlen($this->_data);
        $data['stream'] = $this->_compressedData;
        $pdf .= $this->_template->render($data);
    }
}