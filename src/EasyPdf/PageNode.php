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
    private $_content;

    /**
     * Node resources.
     */
    private $_resourceNode;
    
    public function __construct(Engine &$pdf, $mediaBox = null) {
        $parent = $pdf->getRootNode()->getPagesNode();
        parent::__construct($pdf, $pdf->getSingleIndex(), $parent->getGeneration(), $parent);

        $this->_content = array();
        $this->setFormat($mediaBox);
        $this->_resourceNode = new ResourcesNode($pdf, $this);
        $this->addChild($this->_resourceNode);
    }
    
    public function addFontResource(FontNode $font) {
        $this->_resourceNode->addFont($font);
    }

    public function addText($textUser, FontNode $font, $size, $x = 0, $y = 0) {
        $text = new TextNode($this, $textUser);
        $text->setX($x);
        $text->setY($y);
        $text->setFont($font);
        $text->setSize($size);
        $this->_content[] = $text;
        $this->addChild($text);
        $text->setParent($this);
    }

    /*public function addTextArea(TextAreaNode $textArea) {
        $this->_content[] = $textArea;
        $this->addChild($textArea);
        $textArea->setParent($this);
    }*/


    public function addContent($textArea) {
        $this->_content[] = $textArea;
        $this->addChild($textArea);
        $textArea->setParent($this);
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
        $this->header($pdf);
        parent::output($pdf);
    }

    private function header(&$pdf) {
        parent::writeObjHeader($pdf);

        $pdf .= "<< /Type /Page\n";
        $pdf .= "/Parent " . $this->_parent->getIndirectReference() . "\n";
        $pdf .= "/MediaBox [" . $this->_mediaBox[0] . " " . $this->_mediaBox[1] . " " . $this->_mediaBox[2] . " " . $this->_mediaBox[3] . "]\n";
        $this->putResources($pdf);
        $this->putContent($pdf);
        $pdf .= ">>\n";

        parent::writeObjFooter($pdf);
    }
    
    private function putResources(&$pdf) {
        $pdf .= "/Resources " . $this->_resourceNode->getIndirectReference() . "\n";
    }
    
    private function putContent(&$pdf) {
        $pdf .= "/Contents [";
        foreach ($this->_content as $c) {
            $pdf .= $c->getIndirectReference() . " ";
        }
        $pdf .= "]\n";
        //$content = $this->_content[0]; //tmp
        //$pdf .= "/Contents " . $content->getIndirectReference() . "\n";
    }
    
}

?>