<?php

namespace EasyPdf;
/**
 * PHP-class for writting PDF.
 *
 * @author greg
 */

class PagesNode extends Node {

    /**
     * Array containing pages child.
     */
    private $_pageNodes;

    public function __construct(Engine &$engine, $index, $generation = 0, $parent = null) {
        parent::__construct($engine, $index, $generation, $parent);

        $this->_pageNodes = array();
    }

    public function output(&$pdf) {
        parent::preOutput($pdf);
        $this->data($pdf);
        parent::output($pdf);
    }

    public function getPages() {
        return $this->_pageNodes;
    }

    public function addPage(PageNode $page) {
        $this->_pageNodes[] = $page;
        $this->addChild($page);
        $page->setParent($this);
    }

    private function data(&$pdf) {
        if (!count($this->_pageNodes)) {
            $this->generateFatalError("Cannot generate PDF without page.\n");
        }

        parent::writeObjHeader($pdf);

        $pdf .= "<< /Type /Pages\n";
        $pdf .= "/Kids [";
        foreach ($this->_pageNodes as $page) {
            $pdf .= $page->getIndirectReference() . " ";
        }
        $pdf .= "]\n";
        $pdf .= "/Count " . count($this->_pageNodes) . "\n";
        $pdf .= ">>\n";

        parent::writeObjFooter($pdf);
    }
    
}

?>