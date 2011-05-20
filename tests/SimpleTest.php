<?php

/**
 * Basic test for debug
 */

error_reporting(E_ALL);
ini_set('display_errors','On');

include_once __DIR__.'/../bootstrap.php';



function test1() {
    $startTime = microtime(true);
    

    $pdf = new EasyPdf\Engine();
    $pdf->setUnit('mm');
    //$fontDeOuf = $pdf->addFont('../tests/arial.ttf', 'TrueType', 'ma font de ouf');

    $page = new EasyPdf\PageNode($pdf);
    //$page->addFontResource($fontDeOuf);
    $page->setFormat(array(0, 0, 210, 297));

    $box = new EasyPdf\AreaNode($page);
    $box->setX(0);
    $box->setY(0);
    $box->setWidth(210);
    $box->setHeight(297);
    $box->drawArea(true);
    $page->addContent($box);

    $nbBox = 3;
    $parent = $box;
    $offsetX = $parent->getWidth() / ($nbBox) / 2;
    $offsetY = $parent->getHeight() / ($nbBox) / 2;
    for ($i = 0; $i < $nbBox; ++$i) {
        $child = new EasyPdf\AreaNode($page);
        $child->drawArea(true);
        $child->setGeometricParent($parent);
        $child->setX($parent->getX() + $offsetX);
        $child->setY($parent->getY() + $offsetY);
        $child->setWidth($parent->getWidth() - ($offsetX * 2));
        $child->setHeight($parent->getHeight() - ($offsetY * 2));
        echo $parent->getHeight() . "\n";
        $page->addContent($child);
        $parent = $child;
    }

    $pdf->addPage($page);

    /*$textArea = new EasyPdf\TextAreaNode($page, file_get_contents("text"));
    $textArea->setWidth(210);
    $textArea->setSize(11);
    $textArea->setFont($fontDeOuf);
    $textArea->setX(0);
    $textArea->setY(10);
    $page->addContent($textArea);

    $stressValue = 20;
    for ($i = 0; $i < $stressValue; ++$i) {
        $page2 = new EasyPdf\PageNode($pdf);
        $page2->addFontResource($fontDeOuf); // wont be duplicate
        $page2->setFormat(array(0, 0, 210, 297));


        $textArea = new EasyPdf\TextAreaNode($page, file_get_contents("text"));
        $textArea->setWidth(210);
        $textArea->setSize(11);
        $textArea->setFont($fontDeOuf);
        $textArea->setX(0);
        $textArea->setY(10);
        $page2->addContent($textArea);
        $pdf->addPage($page2);
    }*/
    $pdf->writePDF();
    
    $downIn = microtime(true) - $startTime;
    echo "Done in " . $downIn . " secondes.\n";
    
}

$debug = file_exists("/usr/share/php5/xhprof/header.php") && file_exists("/usr/share/php5/xhprof/footer.php");
if ($debug) {
    include_once '/usr/share/php5/xhprof/header.php';
}
test1();
if ($debug) {
    include_once '/usr/share/php5/xhprof/footer.php';
}

echo "\nTest End.\n";