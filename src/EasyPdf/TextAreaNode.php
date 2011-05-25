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

    /**
     * Line splitted in array.
     */
    private $_splittedLines;

    public function __construct(PageNode &$page) {
        $engine = $page->getEngine();
        parent::__construct($engine, $engine->getSingleIndex(), $page->getGeneration(), $page);

        $this->_lineHeight = null;
    }

    public function setTextNode(TextNode $text) {
        $this->_textNode = $text;
        $this->addChild($text);
        $this->onAdd();
    }

    public function setAreaNode(AreaNode $area) {
        $this->_areaNode = $area;
        $this->_parent->addContent($area);
        $this->onAdd();
    }

    public function output(&$pdf) {
        Node::preOutput($pdf);
        $this->data($pdf);
        Node::output($pdf);
    }

    private function data(&$pdf) {
        $totalBreak = $this->_splittedLines;

        $x = $this->_textNode->getAbsoluteX();
        $y = $this->_textNode->getAbsoluteY();
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

        $compressed = \gzcompress($stream);

        $data = $this->getBaseDataForTpl();
        $data['length'] = strlen($compressed);
        $data['filter'] = "FlateDecode";
        $data['length1'] = strlen($stream);
        $data['stream'] = $compressed;

        $pdf .= $this->_template->render($data);
        //$this->_textNode->writeStream($pdf, $stream);
        //parent::writeObjFooter($pdf);
    }

    /**
     * Temporary tools, split text.
     * TODO, refacto width string to accept utf8 char.
     */
    private function splitLine(&$s) {
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

    protected function onAdd() {
        if (!$this->_textNode || !$this->_textNode->getText() || !$this->_added
                || !$this->_areaNode) {
            return;
        }

        $this->_textNode->setGeometricParent($this->_areaNode);

        if (!$this->_lineHeight) {
            $prop = $this->_textNode->getFont()->getProperties();
            $this->_lineHeight = $prop['Ascender']['value'];
            $this->_lineHeight *= $this->_textNode->getSize() / 1000;
        }

        $this->_splittedLines = $this->splitLine($this->_textNode->getText());

        $cw = $this->_textNode->getFont()->getWidths()->getData();
        $mw = $this->_textNode->getFont()->getProperties();
        $mw = $mw['MissingWidth']['value'];
        $ll = count($this->_splittedLines) - 1;
        $addedX = Tools\String::getWidthString($this->_splittedLines[$ll], $this->_textNode->getSize(), $cw, $mw);
        $addedY = $this->_lineHeight * ($ll + 1);

        if ($this->_textNode->getX() === null) {
            $x = $this->_areaNode->getX() * $this->_engine->getUnitFactor();
        } else {
            $x = $this->_textNode->getX() * $this->_engine->getUnitFactor();
        }
        if ($this->_textNode->getY() === null) {
            $y = ($this->_parent->getHeight() - ($this->_areaNode->getY() * $this->_engine->getUnitFactor()));
        } else {
            $y = ($this->_parent->getHeight() - ($this->_textNode->getY() * $this->_engine->getUnitFactor()));
        }
        $this->_textNode->setAbsoluteX($x);
        $this->_textNode->setAbsoluteY($y);
        $this->_parent->setX($addedX + $x);
        $this->_parent->setY($y - $addedY);
    }

}