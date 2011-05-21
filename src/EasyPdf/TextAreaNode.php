<?php

namespace EasyPdf;
/**
 * PHP-class for writting PDF.
 *
 * @author greg
 */

class TextAreaNode extends AreaNode {

    /**
     * Line height.
     */
    private $_lineHeight;

    /**
     * Text node.
     */
    private $_textNode;

    public function __construct(PageNode &$page) {
        parent::__construct($page);

        $this->_lineHeight = null;
    }

    public function setTextNode(TextNode $text) {
        $this->_textNode = $text;
    }

    public function output(&$pdf) {
        Node::preOutput($pdf);
        $this->data($pdf);
        Node::output($pdf);
    }

    private function data(&$pdf) {
        parent::writeObjHeader($pdf);

        if (!$this->_lineHeight) {
            $prop = $this->_textNode->getFont()->getProperties();
            $this->_lineHeight = $prop['Ascender']['value'];
            $this->_lineHeight *= $this->_textNode->getSize() / 1000;
        }
        $totalBreak = $this->splitLine($this->_text);

        $x = $this->getX() * $this->_engine->getUnitFactor();
        $y = ($this->_parent->getHeight() - ($this->getY() * $this->_engine->getUnitFactor()));

        $stream = "";
        foreach ($totalBreak as $text) {
            $stream .= "BT\n";
            $stream .= "/F" . $this->_textNode->getFont()->getIndex() . " " . $this->_textNode->getSize() . " Tf\n";
            $stream .= "$x $y Td\n";
            $stream .= "(" . $text . ") Tj\n";
            $y -= $this->_lineHeight;
            $stream .= "ET\n";
        }

        $this->_textNode->writeStream($pdf, $stream);
        parent::writeObjFooter($pdf);
    }

    /**
     * Temporary tools, return string width.
     * TODO, refacto width string to accept utf8 char.
     */
    private function splitLine(&$s)
    {
	//Get width of a string in the current font
        $ret = array();
	$s = (string)$s;
	$cw = $this->_textNode->getFont()->getWidths()->getData();
        $missingWidth = $this->_textNode->getFont()->getProperties();
        $missingWidth = $missingWidth['MissingWidth']['value'];
	$w = 0;
	$l = strlen($s);
        $tmp = "";
        $maxWidth = $this->_width * $this->_engine->getUnitFactor();
	for($i = 0; $i < $l; $i++) {
            $last = 0;
            if (!isset($cw[ord($s[$i])])) {
                $last = $missingWidth * $this->_size / 1000;
                $w += $last;
            } else {
                $last = ($cw[ord($s[$i])] * $this->_size / 1000);
                $w += $last;
            }
            if ($w >=  $maxWidth || $s[$i] == "\n") {
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