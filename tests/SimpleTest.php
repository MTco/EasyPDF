<?php

/**
 * Basic test for debug
 */

error_reporting(E_ALL);
ini_set('display_errors','On');


include_once '../src/EPDFEngine.class.php';

function test1() {
    $startTime = microtime(true);
    
    $pdf = new EPDFEngine();
    $pdf->setUnit('mm');
    $fontDeOuf = $pdf->addFont('../tests/Chicken Butt.ttf', 'TrueType', 'ma font de ouf');
    
    $page = new EPDFPageNode($pdf);
    $page->addFontResource($fontDeOuf);
    $page->setFormat(array(0, 0, 210, 297));
    $page->addText("Hello World!");
    
    $pdf->addPage($page);
    $pdf->writePDF();
    
    $downIn = microtime(true) - $startTime;
    
    echo "Done in " . $downIn . " secondes.\n";
    
}

test1();

?>
