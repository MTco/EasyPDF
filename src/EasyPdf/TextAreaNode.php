<?php

namespace EasyPdf;
/**
 * PHP-class for writting PDF.
 *
 * @author greg
 */

class TextAreaNode extends Node {

    /**
     * Line height.
     */
    private $_lineHeight;

    /**
     * Text node.
     */
    private $_textNode;

    /**
     * Box node.
     */
    private $_areaNode;

    public function __construct(PageNode &$page) {
        $engine = $page->getEngine();
        parent::__construct($engine, $engine->getSingleIndex(), $page->getGeneration(), $page);

        $this->_lineHeight = null;
    }

    public function setTextNode(TextNode $text) {
        $this->_textNode = $text;
    }

    public function setAreaNode(AreaNode $area) {
        $this->_areaNode = $area;
        $this->addChild($area);
        $this->_parent->addContent($area);
    }

    public function output(&$pdf) {
        Node::preOutput($pdf);
        $this->data($pdf);
        Node::output($pdf);
    }

    private function data(&$pdf) {
        parent::writeObjHeader($pdf);

        if ($this->_textNode && $this->_areaNode) {
            $this->_textNode->setGeometricParent($this->_areaNode);
        }

        if (!$this->_lineHeight) {
            $prop = $this->_textNode->getFont()->getProperties();
            $this->_lineHeight = $prop['Ascender']['value'];
            $this->_lineHeight *= $this->_textNode->getSize() / 1000;
        }
        
        $totalBreak = $this->splitLine($this->_textNode->getText());

        $x = $this->_textNode->getX() * $this->_engine->getUnitFactor();
        $y = ($this->_parent->getHeight() - ($this->_textNode->getY() * $this->_engine->getUnitFactor()));
        $y -= $this->_lineHeight;

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
     * Temporary tools, split text.
     * TODO, refacto width string to accept utf8 char.
     */
    private function splitLine(&$s)
    {
	//Get width of a string in the current font
        $ret = array();
	$cw = $this->_textNode->getFont()->getWidths()->getData();
        $missingWidth = $this->_textNode->getFont()->getProperties();
        $missingWidth = $missingWidth['MissingWidth']['value'];
	$w = 0;
	$l = strlen($s);
        $tmp = "";
        $maxWidth = $this->_areaNode->getWidth() * $this->_engine->getUnitFactor();
	for($i = 0; $i < $l; $i++) {
            $last = 0;
            if (!isset($cw[ord($s[$i])])) {
                $last = $missingWidth * $this->_textNode->getSize() / 1000;
                $w += $last;
            } else {
                $last = ($cw[ord($s[$i])] * $this->_textNode->getSize() / 1000);
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