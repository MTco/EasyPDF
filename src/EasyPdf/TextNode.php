<?php

namespace EasyPdf;
/**
 * PHP-class for writting PDF.
 *
 * @author greg
 */

class TextNode extends ADrawableNode {
    
    /**
     * Text content.
     */
    protected $_text;

    /**
     * Font of text.
     */
    protected $_font;

    /**
     * Size of text.
     */
    protected $_size;

    
    public function __construct(PageNode &$page) {
        parent::__construct($page);
        $this->_size = 12;
    }

    public function setText($text) {
        $this->_text = $text;
    }

    public function getText() {
        return $this->_text;
    }

    public function getSize() {
        return $this->_size;
    }

    public function getFont() {
        return $this->_font;
    }

    public function setFont(FontNode $font) {
        $this->_font = $font;
    }

    public function setSize($size) {
        $this->_size = $size;
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

    public function writeStream(&$pdf, $stream) {
        $compressed = \gzcompress($stream);

        $pdf .= "<< /Length " . strlen($compressed) . "\n";
        $pdf .= "/Filter /FlateDecode\n";
        $pdf .= "/Length1 " . strlen($stream) . " >>\n";
        $pdf .= "stream\n";
        $pdf .= $compressed;
        $pdf .= "\nendstream\n";
    }

    protected function streamText($text) {
        $x = $this->getX() * $this->_engine->getUnitFactor();
        $y = ($this->_parent->getHeight() - ($this->getY() * $this->_engine->getUnitFactor()));

        $stream = "BT\n";
        $stream .= "/F" . $this->_font->getIndex() . " " . $this->_size . " Tf\n";
        $stream .= "$x $y Td\n";
        $stream .= "(" . $text . ") Tj\n";
        $stream .= "ET\n";

        return $stream;
    }
    
}
