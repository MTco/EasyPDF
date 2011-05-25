<?php

namespace EasyPdf;
/**
 * PHP-class for writting PDF.
 *
 * @author greg
 */

abstract class ADrawableNode extends Node implements IDrawable {

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
    protected $_width;

    /**
     * Height box.
     */
    protected $_height;

    /**
     * Absolute X position.
     */
    protected $_absoluteX;

    /**
     * Absolute Y position.
     */
    protected $_absoluteY;

    /**
     * The geometric parent.
     */
    protected $_geometricParent;

    public function __construct($parent) {
        $engine = $parent->getEngine();
        parent::__construct($engine, $engine->getSingleIndex(), $parent->getGeneration(), $parent);
        $this->_x = null;
        $this->_y = null;
    }

    public function setX($x) {
        $this->_x = $x;
    }

    public function getX() {
        if ($this->_geometricParent && $this->_x !== null) {
            return $this->_geometricParent->getX() + $this->_x;
        }
        return $this->_x;
    }

    public function getY() {
        if ($this->_geometricParent && $this->_x !== null) {
            return $this->_geometricParent->getY() + $this->_y;
        }
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

    public function getGeometricParent() {
        return $this->_geometricParent;
    }

    public function setGeometricParent(IDrawable $parent) {
        $this->_geometricParent = $parent;
    }

    public function getAbsoluteX() {
        return $this->_absoluteX;
    }

    public function getAbsoluteY() {
        return $this->_absoluteY;
    }

    public function setAbsoluteX($x) {
        $this->_absoluteX = $x;
    }

    public function setAbsoluteY($y) {
        $this->_absoluteY = $y;
    }
}