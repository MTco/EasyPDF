<?php

/**
 * PHP-class for writting PDF.
 *
 * @author greg
 */

include_once 'EPDFNode.class.php';

class EPDFTextNode extends EPDFNode {
    
    /**
     * Text content.
     */
    private $_text;
    
    public function EPDFTextNode(EPDFPageNode &$page, $text = '') {
        $engine = $page->getEngine();
        parent::EPDFNode($engine, $engine->getSingleIndex(), $page->getGeneration(), $page);
        
        $this->_text = gzdeflate($text);
    }
    
    public function output(&$pdf) {
        parent::preOutput($pdf);
        $this->data($pdf);
        parent::output($pdf);
    }
    
    private function data(&$pdf) {
        parent::writeObjHeader($pdf);
     
        $encoded = $this->_text;
        $pdf .= "<< /Filter /FlateDecode /Length " . strlen($encoded) . " >>\n";
        $pdf .= "stream\n";
        $pdf .= $encoded;
        $pdf .= "\nendstream\n";

        parent::writeObjFooter($pdf);
    }
    
}
