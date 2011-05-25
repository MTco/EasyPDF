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

        $data = $this->getBaseDataForTpl();
        $data['producer'] = "EasyPdf";
        $data['creationDate'] = 'D:'. date('YmdHis');
        $pdf .= $this->_template->render($data);
        
    }
}