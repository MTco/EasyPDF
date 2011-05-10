<?php

/**
 * Basic test for debug
 */

error_reporting(E_ALL);
ini_set('display_errors','On');


include_once '../srcs/EPDFEngine.class.php';

function test1() {
    $pdf = new EPDFEngine();
    $pdf->setUnit('mm');
    $pdf->addFont('../tests/Chicken Butt.ttf', 'ma font de ouf');
    
    $page = new EPDFPageNode($pdf);
    $page->addFontResource('ma font de ouf');
    $page->setFormat(array(0, 0, 210, 297));
    $page->addText("Hello World!");
    
    $pdf->addPage($page);
    $pdf->writePDF();
}
test1();

?>
