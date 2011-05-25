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
        $data = $this->getBaseDataForTpl();
        $data['fonts'] = $this->_fonts;
        $pdf .= $this->_template->render($data);
    }
    
    public function addFont(FontNode $font) {
        $this->_fonts[] = $font;
        $this->addChild($font);
    }
    
}