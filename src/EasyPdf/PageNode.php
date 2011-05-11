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

    public function PageNode(Engine &$pdf, $mediaBox = null) {
        $parent = $pdf->getRootNode()->getPagesNode();
        parent::Node($pdf, $pdf->getSingleIndex(), $parent->getGeneration(), $parent);

        $this->_content = array();
        $this->setFormat($mediaBox);
    }

    public function addText($textUser) {
        $text = new TextNode($this, $textUser);
        $this->_content[] = $text;
        $this->_childs[] = $text;
        $text->setParent($this);
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
        $this->putContent($pdf);
        $pdf .= ">>\n";

        parent::writeObjFooter($pdf);
    }
    
    private function putContent(&$pdf) {
        $content = $this->_content[0]; //tmp
        $pdf .= "/Contents " . $content->getIndirectReference() . "\n";
    }

}
