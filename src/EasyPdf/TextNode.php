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

    /**
     * Stream computed on onAdd method.
     */
    protected $_precomputedStream;

    
    public function __construct(PageNode &$page) {
        parent::__construct($page);
        $this->_size = 12;
    }

    public function setText($text) {
        $this->_text = $text;
        $this->onAdd();
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
        $x = $this->_absoluteX;
        $y = $this->_absoluteY;

        echo "ici" . $x . "\n";

        $stream = "BT\n";
        $stream .= "/F" . $this->_font->getIndex() . " " . $this->_size . " Tf\n";
        $stream .= "$x $y Td\n";
        $stream .= "(" . $text . ") Tj\n";
        $stream .= "ET\n";

        return $stream;
    }

    protected function onAdd() {
        if (!$this->getText() || !$this->_added) {
            return;
        }
        
        if ($this->_x === null) {
            $x = $this->_parent->getX();
        } else {
            $x = $this->getX() * $this->_engine->getUnitFactor();
        }
        if ($this->_y === null) {
            $y = $this->_parent->getY();
        } else {
            $y = ($this->_parent->getHeight() - ($this->getY() * $this->_engine->getUnitFactor()));
        }

        $cw = $this->getFont()->getWidths()->getData();
        $mw = $this->getFont()->getProperties();
        $mw = $mw['MissingWidth']['value'];

        $this->_absoluteX = $x;
        $this->_absoluteY = $y;
        $this->_parent->setX($x + Tools\String::getWidthString($this->getText(), $this->getSize(), $cw, $mw));
    }


}
