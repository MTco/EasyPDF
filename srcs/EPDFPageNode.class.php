<?php

/**
 * PHP-class for writting PDF.
 *
 * @author greg
 */

include_once 'EPDFNode.class.php';
include_once 'EPDFPagesNode.class.php';
include_once 'EPDFTextNode.class.php';
include_once 'EPDFResourcesNode.class.php';

class EPDFPageNode extends EPDFNode {

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
    
    /**
     * Page fonts name.
     */
    private $_fonts;
    
    public function EPDFPageNode(EPDFEngine &$pdf, $mediaBox = null) {
        $parent = $pdf->getRootNode()->getPagesNode();
        parent::EPDFNode($pdf, $pdf->getSingleIndex(), $parent->getGeneration(), $parent);

        $this->_content = array();
        $this->setFormat($mediaBox);
        $this->_resourceNode = new EPDFResourcesNode($pdf, $this);
    }
    
    public function addFontResource($fontname) {
        $this->_fonts[$fontname] = true;
    }

    public function addText($textUser) {
        $text = new EPDFTextNode($this, $textUser);
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
        $this->putResources($pdf);
        $this->putContent($pdf);
        $pdf .= ">>\n";

        parent::writeObjFooter($pdf);
    }
    
    private function putResources(&$pdf) {
        $pdf .= "/Resources " . $this->_resourceNode->getIndirectReference() . "\n";
    }
    
    private function putContent(&$pdf) {
        $content = $this->_content[0]; //tmp
        $pdf .= "/Contents " . $content->getIndirectReference() . "\n";
    }

}

?>