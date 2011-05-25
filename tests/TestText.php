<?php

/**
 * Box test.
 */

error_reporting(E_ALL);
ini_set('display_errors','On');

include_once __DIR__.'/../bootstrap.php';

function testText() {
    $startTime = microtime(true);

    $pdf = new EasyPdf\Engine();
    $pdf->setUnit('mm');

    $font = $pdf->addFont("arial.ttf");

    $page = new EasyPdf\PageNode($pdf);
    $page->setFormat(array(0, 0, 210, 297));
    $page->addFontResource($font);

    $text = new EasyPdf\TextNode($page);
    $text->setFont($font);
    $text->setText("Hello World!");
    $text->setY(10);
    $text->setX(0);

    echo $page->getX() . "\n";
    $page->addContent($text);
    echo $page->getX() . "\n";
    $text = $text->giveMeAnotherLife();
    $text->setX(null);
    $page->addContent($text);
    echo $page->getX() . "\n";

    $pdf->addPage($page);
    $pdf->writePDF('testText.pdf');

    $downIn = microtime(true) - $startTime;
    echo "Done in " . $downIn . " secondes.\n";
}


$debug = file_exists("/usr/share/php5/xhprof/header.php") && file_exists("/usr/share/php5/xhprof/footer.php");
if ($debug) {
    include_once '/usr/share/php5/xhprof/header.php';
}

testText();

if ($debug) {
    include_once '/usr/share/php5/xhprof/footer.php';
}

echo "\nTest End.\n";