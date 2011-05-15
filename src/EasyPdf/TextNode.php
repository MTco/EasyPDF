<?php

namespace EasyPdf;
/**
 * PHP-class for writting PDF.
 *
 * @author greg
 */

class TextNode extends Node {
    
    /**
     * Text content.
     */
    private $_text;

    /**
     * X position.
     */
    private $_x;

    /**
     * Y position.
     */
    private $_y;
    
    public function __construct(PageNode &$page, $text = '') {
        $engine = $page->getEngine();
        parent::__construct($engine, $engine->getSingleIndex(), $page->getGeneration(), $page);
        
        $this->_text = $text;
    }

    public function setX($x) {
        $this->_x = $x;
    }

    public function setY($y) {
        $this->_y = $y;
    }

    public function output(&$pdf) {
        parent::preOutput($pdf);
        $this->data($pdf);
        parent::output($pdf);
    }
    
    private function data(&$pdf) {
        parent::writeObjHeader($pdf);

        $x = $this->_x * $this->_engine->getUnitFactor();
        $y = ($this->_parent->getHeight() - $this->_y);// * $this->_engine->getUnitFactor();

        $stream = "BT\n";
        $stream .= "/F1 24 Tf\n";
        $stream .= "$x $y Td\n";
        $stream .= "(" . $this->_text . ") Tj\n";
        $stream .= "ET\n";

        $compressed = \gzcompress($stream);
        
        $pdf .= "<< /Length " . strlen($compressed) . "\n";
        $pdf .= "/Filter /FlateDecode\n";
        $pdf .= "/Length1 " . strlen($stream) . " >>\n";
        $pdf .= "stream\n";
        $pdf .= $compressed;
        $pdf .= "\nendstream\n";
        
        parent::writeObjFooter($pdf);

    }
    
}
