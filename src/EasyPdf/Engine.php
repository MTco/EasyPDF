<?php

namespace EasyPdf;
/**
 * PHP-class for writting PDF.
 *
 * @author greg
 */

class Engine {

    /**
     * Root node containing all pdf.
     */
    private $_rootNode;

    /**
     * Current index of pdf objects.
     */
    private $_currentIndex;

    /**
     * Start index, first object created.
     */
    private $_startIndex;

    /**
     * Unit of document.
     */
    private $_unit;

    /**
     * Factor to convert unit into point.
     */
    private $_unitFactor;
    
    /**
     * Used font in PDF Document.
     */
    private $_fonts;

    public function __construct() {
        $this->_startIndex = 1;
        $this->_currentIndex = $this->_startIndex;
        $this->_rootNode = new \EasyPdf\RootNode($this, $this->_currentIndex++, 0, $this);
        $this->setUnit('pt');
    }
    
    public function addFont($filename, $type = "TrueType", $singleReference = null) {
        if (!$singleReference) {
            $singleReference = $filename;
        }
        if (isset($this->_fonts[$singleReference])) {
            return $this->_fonts[$singleReference];
        }
        $this->_fonts[$singleReference] = new FontNode($this, $filename, $type);
        return $this->_fonts[$singleReference];
    }
    
    public function getUnit() {
        return $this->_unit;
    }

    public function getUnitFactor() {
        return $this->_unitFactor;
    }
    
    public function setUnit($unit) {
        switch ($unit) {
            case 'pt':
                $this->_unitFactor = 1;
                break;
            case 'mm':
                $this->_unitFactor = 72 / 25.4;
                break;
            case 'cm':
                $this->_unitFactor = 720 / 25.4;
                break;
            default:
                $this->generateFatalError("Unit " . $unit. " not supported.\n");
        }
        $this->_unit = $unit;
    }

    public function getStartIndex() {
        return $this->_startIndex;
    }

    public function getCurrentIndex() {
        return $this->_currentIndex;
    }

    public function getSingleIndex() {
        return ++$this->_currentIndex;
    }

    public function writePDF($filename = 'output.pdf') {
        $pdf;
        $this->_rootNode->output($pdf);
        
        if (!file_put_contents($filename, $pdf)) {
            throw new \Exception('Cannot save PDF file to ' . $filename);
        }
    }

    public function getRootNode() {
        return $this->_rootNode;
    }

    public function addPage(PageNode $page) {
        $this->_rootNode->getPagesNode()->addPage($page);
    }

    private function generateFatalError($error) {
        die($error);
    }
    
    public function getFonts() {
        return $this->_fonts;
    }
    
    public function attachFontToPage($fontnameReference, PageNode &$pageReference) {
        $pageReference->addFontResource($fontnameReference);
    }
}

