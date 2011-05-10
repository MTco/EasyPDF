<?php

/**
 * PHP-class for writting PDF.
 *
 * @author greg
 */

include_once 'EPDFNode.class.php';
include_once 'EPDFPageNode.class.php';

class EPDFPagesNode extends EPDFNode {

    /**
     * Array containing pages child.
     */
    private $_pageNodes;

    public function EPDFPagesNode(EPDFEngine &$engine, $index, $generation = 0, $parent = null) {
        parent::EPDFNode($engine, $index, $generation, $parent);

        $this->_pageNodes = array();
    }

    public function output(&$pdf) {
        parent::preOutput($pdf);
        $this->header($pdf);
        parent::output($pdf);
    }

    public function addPage(EPDFPageNode $page) {
        $this->_pageNodes[] = $page;
        $this->_childs[] = $page;
        $page->setParent($this);
    }

    private function header(&$pdf) {
        if (!count($this->_pageNodes)) {
            $this->generateFatalError("Cannot generate PDF without page.\n");
        }

        parent::writeObjHeader($pdf);

        $pdf .= "<< /Type /Pages\n";
        $pdf .= "/Kids [";
        foreach ($this->_pageNodes as $page) {
            $pdf .= $page->getIndirectReference() . "\n";
        }
        $pdf .= "]\n";
        $pdf .= "/Count " . count($this->_pageNodes) . "\n";
        $pdf .= ">>\n";

        parent::writeObjFooter($pdf);
    }
    
}
