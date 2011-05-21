<?php

/**
 * Test for TextAreaBox.
 */

error_reporting(E_ALL);
ini_set('display_errors','On');

include_once __DIR__.'/../bootstrap.php';

function testTextBox() {
    $startTime = microtime(true);

    $pdf = new EasyPdf\Engine();
    $pdf->setUnit('mm');

    $font = $pdf->addFont("arial.ttf");

    $page = new EasyPdf\PageNode($pdf);
    $page->setFormat(array(0, 0, 210, 297));
    $page->addFontResource($font);

    $textNode = new EasyPdf\TextNode($page);
    $textNode->setFont($font);
    $textNode->setText("Hello World!\nLOL");

    $textAreaNode = new EasyPdf\TextAreaNode($page);
    $textAreaNode->setTextNode($textNode);
    $textAreaNode->setX(10);
    $textAreaNode->setY(10);
    $textAreaNode->setWidth(100);
    $textAreaNode->setHeight(100);
    $textAreaNode->drawArea(true);

    $page->addContent($textAreaNode);

    $pdf->addPage($page);
    $pdf->writePDF('textTextBox.pdf');

    $downIn = microtime(true) - $startTime;
    echo "Done in " . $downIn . " secondes.\n";
}


$debug = file_exists("/usr/share/php5/xhprof/header.php") && file_exists("/usr/share/php5/xhprof/footer.php");
if ($debug) {
    include_once '/usr/share/php5/xhprof/header.php';
}

testTextBox();

if ($debug) {
    include_once '/usr/share/php5/xhprof/footer.php';
}

echo "\nTest End.\n";