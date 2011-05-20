<?php

/**
 * Box test.
 */

error_reporting(E_ALL);
ini_set('display_errors','On');

include_once __DIR__.'/../bootstrap.php';

function testBox() {
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

    $nbBox = 10;
    $parent = $box;

    $offsetX = $parent->getWidth() / $nbBox / 2;
    $offsetY = $parent->getHeight() / $nbBox / 2;

    for ($i = 0; $i < $nbBox; ++$i) {
        $child = new EasyPdf\AreaNode($page);
        $child->drawArea(true);
        $child->setGeometricParent($parent);

        $child->setWidth($offsetX * 2 * ($nbBox - $i - 1));
        $child->setHeight($offsetY * 2 * ($nbBox - $i - 1));

        $child->setX($offsetX);
        $child->setY($offsetY);

        $page->addContent($child);
        $parent = $child;
    }

    $pdf->addPage($page);
    $pdf->writePDF();

    $downIn = microtime(true) - $startTime;
    echo "Done in " . $downIn . " secondes.\n";
}


$debug = file_exists("/usr/share/php5/xhprof/header.php") && file_exists("/usr/share/php5/xhprof/footer.php");
if ($debug) {
    include_once '/usr/share/php5/xhprof/header.php';
}

testBox();

if ($debug) {
    include_once '/usr/share/php5/xhprof/footer.php';
}

echo "\nTest End.\n";