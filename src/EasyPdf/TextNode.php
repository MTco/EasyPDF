<?php

namespace EasyPdf;
/**
 * PHP-class for writting PDF.
 *
 * @author greg
 */

class TextNode extends Node implements IDrawable {
    
    /**
     * Text content.
     */
    protected $_text;

    /**
     * X position.
     */
    protected $_x;

    /**
     * Y position.
     */
    protected $_y;

    /**
     * Font of text.
     */
    protected $_font;

    /**
     * Size of text.
     */
    protected $_size;
    
    public function __construct(PageNode &$page, $text = '') {
        $engine = $page->getEngine();
        parent::__construct($engine, $engine->getSingleIndex(), $page->getGeneration(), $page);
        
        $this->_text = $text;
        $this->_size = 12;
    }

    public function setX($x) {
        $this->_x = $x;
    }

    public function setFont(FontNode $font) {
        $this->_font = $font;
    }

    public function getX() {
        return $this->_x;
    }

    public function getY() {
        return $this->_y;
    }

    public function setSize($size) {
        $this->_size = $size;
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

        $stream = $this->streamText($this->_text);
        $this->writeStream($pdf, $stream);
        
        parent::writeObjFooter($pdf);

    }

    protected function writeStream(&$pdf, $stream) {
        $compressed = \gzcompress($stream);

        $pdf .= "<< /Length " . strlen($compressed) . "\n";
        $pdf .= "/Filter /FlateDecode\n";
        $pdf .= "/Length1 " . strlen($stream) . " >>\n";
        $pdf .= "stream\n";
        $pdf .= $compressed;
        $pdf .= "\nendstream\n";
    }

    protected function streamText($text) {
        $x = $this->_x * $this->_engine->getUnitFactor();
        $y = ($this->_parent->getHeight() - ($this->_y * $this->_engine->getUnitFactor()));// * $this->_engine->getUnitFactor();

        $stream = "BT\n";
        $stream .= "/F" . $this->_font->getIndex() . " " . $this->_size . " Tf\n";
        $stream .= "$x $y Td\n";
        $stream .= "(" . $text . ") Tj\n";
        $stream .= "ET\n";

        return $stream;
    }
    
}
