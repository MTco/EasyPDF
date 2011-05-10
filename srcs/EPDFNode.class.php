<?php

/**
 * PHP-class for writting PDF.
 *
 * @author greg
 */

class EPDFNode {

    /**
     * Index number.
     */
    protected $_index;

    /**
     * Generation number.
     */
     protected $_generation;

    /**
     * Parent EPDF Node relative to this node instance.
     */
    protected $_parent;

    /**
     * Childs nodes.
     */
    protected $_childs;

    /**
     * Current byte offset in PDF.
     */
    protected $_offset;

    /**
     * Reference to EPDFEngine instance.
     */
    protected $_engine;

    /**
     * Default constructor, initialize default members states.
     */
    public function EPDFNode(EPDFEngine $engine, $index, $generation = 0, $parent = null) {
        $this->_engine = $engine;
        $this->_index = $index;
        $this->_generation = $generation;
        $this->_parent = $parent;
        $this->_childs = array();
    }
    
    static public function  createEmptyNode(EPDFEngine $engine, $parent = null) {
        return new EPDFNode($engine, $engine->getSingleIndex(), 0, $parent);
    }

    public function getIndex() {
        return $this->_index;
    }
    
    public function getEngine() {
        return $this->_engine;
    }

    public function setParent($parent) {
        $this->_parent = $parent;
    }

    public function getGeneration() {
        return $this->_generation;
    }

    protected function writeObjHeader(&$pdf) {
        $pdf .= $this->_index . " " . $this->_generation . " obj\n";
    }

    protected function writeObjFooter(&$pdf) {
        $pdf .= "endobj\n";
    }

    public function getIndirectReference() {
        return $this->_index . " " . $this->_generation . " R";
    }

    public function preOutput(&$pdf) {
        $this->_offset = strlen($pdf);
    }

    public function output(&$pdf) {
        foreach ($this->_childs as $child) {
            $child->output($pdf);
        }
    }

    public function crossReferenceMe(&$pdf) {
        $pdf .= sprintf("%010s %05s n\n", $this->_offset, $this->_generation);
        foreach ($this->_childs as $c) {
            $c->crossReferenceMe($pdf);
        }
    }

    protected function generateFatalError($error) {
        EPDFNode::staticGenerateFatalError($error);
    }

    static protected function staticGenerateFatalError($error) {
        die($error);
    }

}


?>
