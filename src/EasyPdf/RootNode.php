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
        parent::preOutput($pdf);
        $this->data($pdf);
        parent::output($pdf);
    }

    private function data(&$pdf) {
        $pages = $this->_pagesNode->getPages();

        $data = $this->getBaseDataForTpl();
        $data['pagesNode'] = $this->_pagesNode->getIndirectReference();
        $data['openPage'] = $pages[0]->getIndirectReference();
        $pdf .= $this->_template->render($data);
    }
}
