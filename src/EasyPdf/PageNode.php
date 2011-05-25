<?php

namespace EasyPdf;
/**
 * PHP-class for writting PDF.
 *
 * @author greg
 */


class PageNode extends Node {

    /**
     * Media box of the page.
     */
    private $_mediaBox;

    /**
     * Content of the page.
     */
    private $_contents;

    /**
     * Node resources.
     */
    private $_resourceNode;

    /**
     * Position X of current content.
     */
    private $_x;

    /**
     * Position Y of current content.
     */
    private $_y;
    
    public function __construct(Engine &$pdf, $mediaBox = null) {
        $parent = $pdf->getRootNode()->getPagesNode();
        parent::__construct($pdf, $pdf->getSingleIndex(), $parent->getGeneration(), $parent);

        $this->_contents = array();
        $this->_x = 0;
        $this->_y = 0;
        $this->setFormat($mediaBox);
        $this->_resourceNode = new ResourcesNode($pdf, $this);
        $this->addChild($this->_resourceNode);
    }
    
    public function addFontResource(FontNode $font) {
        $this->_resourceNode->addFont($font);
    }

    public function addContent($content) {
        $this->_contents[] = $content;
        $content->_added = true;
        $content->onAdd();
        $this->addChild($content);
        $content->setParent($this);
    }
    
    /**
     * Set page format.
     * $size must contains startx, starty, endx, endy values.
     * If $size is null, default format is set (A4).
     */
    public function setFormat($size) {
        if (!$size || count($size) != 4) {
            $size = array(0, 0, 595.27, 841.89);
        }
        for ($i = 0; $i < 4; ++$i) {
            $size[$i] *= $this->_engine->getUnitFactor();
        }
        $this->_mediaBox = $size;
    }

    public function getHeight() {
        return $this->_mediaBox[3] - $this->_mediaBox[0];
    }

    public function getWidth() {
        return $this->_mediaBox[2] - $this->_mediaBox[1];
    }

    public function output(&$pdf) {
        parent::preOutput($pdf);
        $this->data($pdf);
        parent::output($pdf);
    }

    private function data(&$pdf) {
        $data = $this->getBaseDataForTpl();
        $data['parent'] = $this->_parent->getIndirectReference();
        $data['mediaBox'] = $this->_mediaBox;
        $data['resources'] = $this->_resourceNode->getIndirectReference();
        $data['contents'] = $this->_contents;
        $pdf .= $this->_template->render($data);
    }

    public function setX($x) {
        $this->_x = $x;
    }

    public function getX() {
        return $this->_x;
    }

    public function getY() {
        return $this->_y;
    }

    public function setY($y) {
        $this->_y = $y;
    }
}

?>