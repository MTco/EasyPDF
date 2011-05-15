<?php

namespace EasyPdf;
/**
 * PHP-class for writting PDF.
 *
 * @author greg
 */

class TextAreaNode extends TextNode {

    /**
     * Width box.
     */
    private $_width;

    /**
     * Height box.
     */
    private $_height;

    /**
     * Line height.
     */
    private $_lineHeight;

    public function __construct(PageNode &$page, $text = '') {
        parent::__construct($page, $text);
        $this->_lineHeight = null;
    }

    /*
     * TODO, fallback if no width specified.
     */
    public function setWidth($width) {
        $this->_width = $width;
    }

    /*
     * TODO, fallback if no height specified.
     */
    public function setHeight($height) {
        $this->_height = $height;
    }

    public function output(&$pdf) {
        parent::preOutput($pdf);
        $this->data($pdf);
        Node::output($pdf);
    }

    private function data(&$pdf) {
        parent::writeObjHeader($pdf);

        if (!$this->_lineHeight) {
            $prop = $this->_font->getProperties();
            $this->_lineHeight = $prop['Ascender']['value'];
            $this->_lineHeight *= $this->_size / 1000;
        }
        $totalBreak = $this->splitLine($this->_text);

        $x = $this->_x * $this->_engine->getUnitFactor();
        $y = ($this->_parent->getHeight() - ($this->_y * $this->_engine->getUnitFactor()));

        $stream = "";
        foreach ($totalBreak as $text) {
            $stream .= "BT\n";
            $stream .= "/F" . $this->_font->getIndex() . " " . $this->_size . " Tf\n";
            $stream .= "$x $y Td\n";
            $stream .= "(" . $text . ") Tj\n";
            $y -= $this->_lineHeight;
            $stream .= "ET\n";
        }

        parent::writeStream($pdf, $stream);
        parent::writeObjFooter($pdf);
    }

    /**
     * Temporary tools, return string width.
     * TODO, refacto width string to accept utf8 char.
     */
    private function splitLine($s)
    {
	//Get width of a string in the current font
        $ret = array();
	$s = (string)$s;
	$cw = $this->_font->getWidths()->getData();
        $missingWidth = $this->_font->getProperties();
        $missingWidth = $missingWidth['MissingWidth']['value'];
	$w = 0;
	$l = strlen($s);
        $tmp = "";
	for($i = 0; $i < $l; $i++) {
            $last = 0;
            if (!isset($cw[ord($s[$i])])) {
                $w += $missingWidth * $this->_size / 1000;
                $last = $missingWidth * $this->_size / 1000;;
            } else {
                $w += ($cw[ord($s[$i])] * $this->_size / 1000);
                $last = ($cw[ord($s[$i])] * $this->_size / 1000);
            }
            if ($w >= $this->_width * $this->_engine->getUnitFactor() || $s[$i] == "\n") {
                $ret[] = $tmp;
                $tmp = "";
                $w = $last;
            }
            if ($s[$i] != "\n")
                $tmp .= $s[$i];
        }
        if (!empty($tmp)) {
            $ret[] = $tmp;
        }
	return $ret;
    }

}