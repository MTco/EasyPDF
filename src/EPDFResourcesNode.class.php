<?php

/**
 * PHP-class for writting PDF.
 *
 * @author greg
 */

class EPDFResourcesNode extends EPDFNode {
    
    public function EPDFResourcesNode(EPDFEngine &$pdf, EPDFPageNode &$page) {
        parent::EPDFNode($pdf, $pdf->getSingleIndex(), 0, $page);
    }
    
}