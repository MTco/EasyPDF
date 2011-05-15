<?php

namespace EasyPdf;
/**
 * PHP-class for writting PDF.
 *
 * @author greg
 */

class TextNode extends Node {
    
    /**
     * Text content.
     */
    private $_text;
    
    public function __construct(PageNode &$page, $text = '') {
        $engine = $page->getEngine();
        parent::__construct($engine, $engine->getSingleIndex(), $page->getGeneration(), $page);
        
        $this->_text = $text;
    }
    
    public function output(&$pdf) {
        parent::preOutput($pdf);
        $this->data($pdf);
        parent::output($pdf);
    }
    
    private function data(&$pdf) {

        /*5 0 obj
<< /Length 73 >>
stream
BT
/F1 24 Tf
100 100 Td
(Hello World) Tj
ET
endstream
endobj*/
        parent::writeObjHeader($pdf);



        $stream = "BT\n";
        $stream .= "/F1 24 Tf\n";
        $stream .= "100 400 Td\n";
        $stream .= "(" . $this->_text . ") Tj\n";
        $stream .= "ET\n";

        $compressed = \gzcompress($stream);
        
        $pdf .= "<< /Length " . strlen($compressed) . "\n";
        $pdf .= "/Filter /FlateDecode\n";
        $pdf .= "/Length1 " . strlen($stream) . " >>\n";
        $pdf .= "stream\n";
        $pdf .= $compressed;
        $pdf .= "\nendstream\n";
        
        parent::writeObjFooter($pdf);

    }
    
}
