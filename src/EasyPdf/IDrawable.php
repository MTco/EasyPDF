<?php

namespace EasyPdf;
/**
 * PHP-class for writting PDF.
 *
 * @author greg
 */

interface IDrawable {

    /**
     * Return x position.
     */
    public function getX();

    /**
     * Return y position.
     */
    public function getY();

    /**
     * Set x position.
     */
    public function setX($v);

    /**
     * Set y position.
     */
    public function setY($v);

    /**
     * Geometric parent must extends IDrawable.
     */
    public function getGeometricParent();

    /**
     * Set the geometric parent.
     */
    public function setGeometricParent(IDrawable $parent);

}
?>
