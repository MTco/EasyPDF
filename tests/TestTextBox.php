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
    $textNode->setText(file_get_contents("text"));

    $box = new EasyPdf\AreaNode($page);
    $box->setX(10);
    $box->setY(10);
    $box->setWidth(190);
    $box->setHeight(277);
    $box->drawArea(true);

    $textAreaNode = new EasyPdf\TextAreaNode($page);
    $textAreaNode->setTextNode($textNode);
    $textAreaNode->setAreaNode($box);

    $page->addContent($textAreaNode);

    $pdf->addPage($page);
    $pdf->writePDF('testTextBox.pdf');

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