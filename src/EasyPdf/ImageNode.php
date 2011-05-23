<?php

namespace EasyPdf;
/**
 * PHP-class for writting PDF.
 *
 * @author greg
 */

class ImageNode extends ADrawableNode {

    /**
     * Filename of picture.
     */
    private $_filename;

    public function __construct(PageNode &$page) {
        parent::__construct($page);
    }

    public function output(&$pdf) {
        parent::preOutput($pdf);
        $this->data($pdf);
        parent::output($pdf);
    }

    private function data(&$pdf) {
        parent::writeObjHeader($pdf);

        

        parent::writeObjFooter($pdf);
    }

    public function setFilename($filename) {
        $this->_filename = $filename;
    }

}