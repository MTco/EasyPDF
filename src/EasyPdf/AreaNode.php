<?php

namespace EasyPdf;
/**
 * PHP-class for writting PDF.
 *
 * @author greg
 */

class AreaNode extends Node implements IDrawable {
    
    /**
     * X position.
     */
    protected $_x;

    /**
     * Y position.
     */
    protected $_y;
    /**
     * Width box.
     */
    private $_width;

    /**
     * Height box.
     */
    private $_height;

    /**
     * Draw bounding box.
     */
    private $_drawArea;

    /**
     * The geometric parent.
     */
    private $_geometricParent;

    public function __construct(PageNode $page) {
        $engine = $page->getEngine();
        parent::__construct($engine, $engine->getSingleIndex(), $page->getGeneration(), $page);

        $this->_geometricParent = null;
    }

    public function getGeometricParent() {
        return $this->_geometricParent;
    }

    public function setGeometricParent(IDrawable $parent) {
        $this->_geometricParent = $parent;
    }

    public function output(&$pdf) {
        parent::preOutput($pdf);
        $this->data($pdf);
        parent::output($pdf);
    }

    private function data(&$pdf) {
        parent::writeObjHeader($pdf);
        
        if ($this->_drawArea) {
            $addX = 0;
            $addY = 0;
            if ($this->_geometricParent) {
                $addX = $this->_geometricParent->getX();
                $addY = $this->_geometricParent->getY();
            }
            $unitFactor = $this->_engine->getUnitFactor();
            $pageHeight = $this->_parent->getHeight();
            $toWrite = sprintf("%.2F %.2F %.2F %.2F re %s",
                    ($this->_x + $addX) * $unitFactor,
                    $pageHeight - ($this->_y + $addY),
                    $this->_width * $unitFactor,
                    -$this->_height * $unitFactor,
                    'S');

            $pdf .= "<< /Length " . strlen($toWrite) . " >>\n";
            $pdf .= "stream\n";
            $pdf .= $toWrite;
            $pdf .= "\nendstream\n";
        }

        parent::writeObjFooter($pdf);
    }

    public function drawArea($value) {
        $this->_drawArea = $value;
    }

    public function setX($x) {
        $this->_x = $x;
    }

    public function getX() {
        return $this->_x;
    }

    public function getY() {
        return $this->_y;
    }

    public function setY($y) {
        $this->_y = $y;
    }

    public function getWidth() {
        return $this->_width;
    }

    public function getHeight() {
        return $this->_height;
    }

    public function setWidth($width) {
        $this->_width = $width;
    }

    public function setHeight($height) {
        $this->_height = $height;
    }
}