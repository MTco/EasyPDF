<?php

namespace EasyPdf;
/**
 * PHP-class for writting PDF.
 *
 * @author greg
 */

class InfoNode extends Node {

    /**
     * Producer.
     */
    private $_producer;

    /**
     * Date of creation.
     */
    private $_createdAt;

    public function __construct(Engine $engine) {
        parent::__construct($engine, $engine->getSingleIndex(), 0, null);
    }

    public function setProducer($producer) {
        $this->_producer = $producer;
    }

    public function setDateCreation($time) {
        $this->_createdAt = $time;
    }

    public function output(&$pdf) {
        parent::preOutput($pdf);
        $this->data($pdf);
        parent::output($pdf);
    }

    private function data(&$pdf) {

        parent::writeObjHeader($pdf);

        $pdf .= "<<\n";
        $pdf .= "/Producer (EasyPdf)\n";
        $pdf .= "/CreationDate (D:20110420140858)\n";
        $pdf .= ">>\n";
        
        parent::writeObjFooter($pdf);
        
    }
}