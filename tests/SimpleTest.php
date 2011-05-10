<?php

/**
 * Basic test for debug
 */

ini_set('error_reporting', E_ALL);
error_reporting(E_ALL);


include_once '../src/EPDFEngine.class.php';

function test1() {
    $pdf = new EPDFEngine();
    $pdf->setUnit('mm');
    $pdf->addFont('../tests/Chicken Butt.ttf');
    
    $page = new EPDFPageNode($pdf);
    $page->setFormat(array(0, 0, 210, 297));
    $page->addText("Hello World!");
    
    $pdf->addPage($page);
    $pdf->writePDF();
}

test1();

