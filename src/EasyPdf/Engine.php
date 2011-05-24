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
     * Info node.
     */
    private $_infoNode;

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

    /**
     * Sorted child for cross reference table output.
     */
    private $_sortedChilds;

    /**
     * Template loader
     */
    private $_tplLoader;

    /**
     * Template engine.
     */
    private $_tplEngine;


    public function __construct() {
        // init template engine.
        $this->_tplLoader = new \Twig_Loader_Filesystem(__DIR__ . '/Templates');
        $this->_tplEngine = new \Twig_Environment($this->_tplLoader, array());

        $this->_startIndex = 1;
        $this->_currentIndex = $this->_startIndex;
        $this->_rootNode = new \EasyPdf\RootNode($this, $this->_currentIndex++, 0, $this);
        $this->_sortedChilds[$this->_rootNode->getIndex()] = $this->_rootNode;
        $this->_infoNode = new InfoNode($this);
        $this->_sortedChilds[$this->_infoNode->getIndex()] = $this->_infoNode;
        $this->setUnit('pt');
    }

    public function getTplEngine() {
        return $this->_tplEngine;
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

    public function addSortedChild(Node $child) {
        $this->_sortedChilds[$child->getIndex()] = $child;
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
        $this->_infoNode->output($pdf);
        $this->crossReference($pdf);

        if (!file_put_contents($filename, $pdf)) {
            throw new \Exception('Cannot save PDF file to ' . $filename);
        }
    }

    private function crossReference(&$pdf) {
        $startXref = strlen($pdf);
        $pdf .= "xref\n";

        $startIdx = $this->getStartIndex() - 1;
        $currentIdx = $this->getCurrentIndex() + 1;
        $pdf .= $startIdx . " " . $currentIdx . "\n";
        $pdf .= "0000000000 65535 f \n";

        ksort($this->_sortedChilds);
        foreach ($this->_sortedChilds as $child) {
            $pdf .= sprintf("%010s %05s n \n", $child->getOffset(), $child->getGeneration());
        }

        $pdf .= "\ntrailer\n";
        $pdf .= "<< /Size " . $currentIdx . "\n";
        $pdf .= "/Root " . $this->_rootNode->getIndirectReference() . "\n";
        $pdf .= "/Info " . $this->_infoNode->getIndirectReference() . "\n";
        $pdf .= ">>\n";
        $pdf .= "startxref\n";
        $pdf .= $startXref . "\n";
        $pdf .= "%%EOF\n";
    }

    public function getRootNode() {
        return $this->_rootNode;
    }

    public function addPage(PageNode $page) {
        $this->_rootNode->getPagesNode()->addPage($page);
    }

    public function setProducer($producer) {
        $this->_infoNode->setProducer($producer);
    }

    public function setDateCreation($date) {
        $this->_infoNode->setDateCreation($date);
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

