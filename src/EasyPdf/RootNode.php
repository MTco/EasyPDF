<?php

namespace EasyPdf;
/**
 * PHP-class for writting PDF.
 *
 * @author greg
 */

class RootNode extends Node {

    /**
     * Reference to pages node.
     */
    private $_pagesNode;

    public function __construct(Engine &$engine, $index, $generation = 0, $parent = null) {
        parent::__construct($engine, $index, $generation, $parent);
        
        $this->_pagesNode = new PagesNode($engine, $index + 1, $generation, $this);
        $this->addChild($this->_pagesNode);
    }

    public function getPagesNode() {
        return $this->_pagesNode;
    }

    public function output(&$pdf) {
        $this->header($pdf);
        parent::output($pdf);
    }

    private function header(&$pdf) {
        $pdf = "%PDF-1.4\n";
        parent::preOutput($pdf);
        parent::writeObjHeader($pdf);

        $pdf .= "<< /Type /Catalog\n";
        $pdf .= "/Pages " . $this->_pagesNode->getIndirectReference() . "\n";
        $pdf .= ">>\n";

        parent::writeObjFooter($pdf);
    }

}
