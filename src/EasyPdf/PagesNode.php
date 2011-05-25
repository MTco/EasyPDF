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
        $page->setParent($this);
        $this->addChild($page);
    }

    private function data(&$pdf) {
        if (empty($this->_pageNodes)) {
            $this->generateFatalError("Cannot generate PDF without page.\n");
        }

        $data = $this->getBaseDataForTpl();
        $data['kids'] = $this->_pageNodes;
        $data['numberPage'] = count($this->_pageNodes);
        $pdf .= $this->_template->render($data);
    }
     
}

?>