<?php

namespace EasyPdf;
/**
 * PHP-class for writting PDF.
 *
 * @author greg
 */

class FontNode extends Node {

    /**
     * Path to font file.
     */
    private $_filename;

    /**
     * Font properties.
     */
    private $_properties;

    /**
     * Type of font (ttf...)
     */
    private $_type;
    
    /**
     * Widths info.
     */
    private $_widths;

    /**
     * Font descriptor.
     */
    private $_fontDescriptor;
    
    public function __construct(Engine &$pdf, $filename, $type) {
        $parent = $pdf->getRootNode();
        parent::__construct($pdf, $pdf->getSingleIndex(), $parent->getGeneration(), $parent);

        $this->_filename = $filename;
        $this->_type = $type;
        $this->_widths = new \EasyPdf\FontWidthsNode($pdf, $this);
        $this->_childs[] = $this->_widths;
        $this->_fontDescriptor = new \EasyPdf\FontDescriptor($pdf, $this);
        $this->_childs[] = $this->_fontDescriptor;
        $this->populateMetricsData();
        $this->parseMetricsFile();
    }
    
    public function output(&$pdf) {
        parent::preOutput($pdf);
        $this->data($pdf);
        parent::output($pdf);
    }
    
    private function data(&$pdf) {
        parent::writeObjHeader($pdf);
/*
        <</Type /Font
        /BaseFont /Calligrapher-Regular
        /Subtype /TrueType
        /FirstChar 32 /LastChar 255
        /Widths 7 0 R
        /FontDescriptor 8 0 R
        /Encoding /WinAnsiEncoding
        >>
*/
        $pdf .= "<< /Type /Font\n";
        $pdf .= "/BaseFont /" . $this->_properties['FontName']['value'] . "\n";
        $pdf .= "/Subtype /" . $this->_type . "\n";
        $pdf .= "/FirstChar " . $this->_properties['FirstChar']['value'] . " " . "/LastChar " . $this->_properties['LastChar']['value'] . "\n";
        $pdf .= "/Widths " . $this->_widths->getIndirectReference() . "\n";
        $pdf .= "/FontDescriptor " . $this->_fontDescriptor->getIndirectReference() . "\n";
        $pdf .= "/Encoding /WinAnsiEncoding\n";
        $pdf .= ">>\n";
        
        parent::writeObjFooter($pdf);
    }
    
    public function getProperties() {
        return $this->_properties;
    }
    
    private function populateMetricsData() {
        $this->generateMetricsFile();
    }
    
    private function generateMetricsFile() {
        if (file_exists("../utils/ttf2pt1_" . PHP_OS . ".exe")) {
            $olddir = getcwd();
            chdir(__DIR__ . "/../../utils");
            $hack = preg_match("#WIN#", PHP_OS) ? '' : './';
            $cmd = $hack . "ttf2pt1_" . PHP_OS . ".exe -a \"" . $this->_filename . "\" \"../tmp/" . basename($this->_filename) . "\" 2> ../tmp/log";
            shell_exec($cmd);
            chdir($olddir);
        }
        else {
            $error = PHP_OS . " cannot generate valide metrics file for font.\n";
            $error .= "You need to place the executable in utils/ folder and name it: ttf2pt1_os\n";
            $error .= "Where os must be replace by os name.(see PHP_OS)\n";
            $this->generateFatalError($error);
        }
    }

    private function resetProperties() {
        $this->_properties = array();

        $this->_properties['FontName']['value'] = null;      $this->_properties['FontName']['parsing'] = 'defaultExtraction';       $this->_properties['FontName']['fallback'] = 'defaultFallback';
        $this->_properties['Ascender']['value'] = null;      $this->_properties['Ascender']['parsing'] = 'defaultExtraction';       $this->_properties['Ascender']['fallback'] = 'defaultFallback';
        $this->_properties['Descender']['value'] = null;     $this->_properties['Descender']['parsing'] = 'defaultExtraction';      $this->_properties['Descender']['fallback'] = 'defaultFallback';
        $this->_properties['CapHeight']['value'] = null;     $this->_properties['CapHeight']['parsing'] = 'defaultExtraction';      $this->_properties['CapHeight']['fallback'] = 'capHeightFallback';
        $this->_properties['FontBBox']['value'] = null;      $this->_properties['FontBBox']['parsing'] = 'fontBBoxExtraction';      $this->_properties['FontBBox']['fallback'] = 'defaultFallback';
        $this->_properties['ItalicAngle']['value'] = null;   $this->_properties['ItalicAngle']['parsing'] = 'defaultExtraction';    $this->_properties['ItalicAngle']['fallback'] = 'defaultFallback';
        $this->_properties['Weight']['value'] = null;        $this->_properties['Weight']['parsing'] = 'defaultExtraction';         $this->_properties['Weight']['fallback'] = 'defaultFallback';
        $this->_properties['StdVW']['value'] = null;         $this->_properties['StdVW']['parsing'] = 'defaultExtraction';          $this->_properties['StdVW']['fallback'] = 'stdVWFallback';
        $this->_properties['MissingWidth']['value'] = null;  $this->_properties['MissingWidth']['parsing'] = 'defaultExtraction';   $this->_properties['MissingWidth']['fallback'] = 'missingWidthFallback';
        $this->_properties['FirstChar']['value'] = null;     $this->_properties['FirstChar']['parsing'] = 'defaultExtraction';      $this->_properties['FirstChar']['fallback'] = 'defaultFallback';
        $this->_properties['LastChar']['value'] = null;      $this->_properties['LastChar']['parsing'] = 'defaultExtraction';       $this->_properties['LastChar']['fallback'] = 'defaultFallback';
        $this->_properties['Widths']['value'] = null;        $this->_properties['Widths']['parsing'] = 'defaultExtraction';         $this->_properties['Widths']['fallback'] = 'defaultFallback';
        $this->_properties['IsFixedPitch']['value'] = null;  $this->_properties['IsFixedPitch']['parsing'] = 'defaultExtraction';   $this->_properties['IsFixedPitch']['fallback'] = 'defaultFallback';
        $this->_properties['Flags']['value'] = null;         $this->_properties['Flags']['parsing'] = 'defaultExtraction';          $this->_properties['Flags']['fallback'] = 'flagFallback';

    }

    private function parseMetricsFile() {
        $data = file_get_contents("../tmp/" . basename($this->_filename) . ".afm");
        $this->resetProperties();
        
        $lines = explode("\n", $data);
        $cc = count($lines);
        for ($i = 0; $i < $cc; ++$i) {
            $words = explode(" ", $lines[$i]);
            if (array_key_exists($words[0], $this->_properties)) {
                $this->_properties[$words[0]]['value'] = call_user_func(__NAMESPACE__ .'\FontNode::' . $this->_properties[$words[0]]['parsing'], $words);
            }
        }
        
        $this->extractWidths($lines);
        //fallback call
        foreach ($this->_properties as $key => $value) {
            if (!$value['value']) {
                $this->_properties[$key]['value'] = call_user_func(__NAMESPACE__ .'\FontNode::' . $value['fallback'], $this->_properties, $key);
            }
        }
    }

    private function extractWidths($lines) {
        $firstChar = null;
        $lastChar = null;
        foreach ($lines as $line) {
            if (preg_match("; N .notdef ;", $line)) {
                continue;
            }
            //C 6 ; WX 500 ; N .notdef ; B 49 0 451 700 ; ---------> pattern sample
            if (preg_match("/C [0-9]+ ; WX [0-9]+ ;.*/", $line))
            {
                $words = explode(" ", $line);
                $lastChar = $words[1];
                if (!$firstChar) {
                    $firstChar = $words[1];
                }
                $this->_properties['Widths']['value'][$lastChar] = $words[4];
            }
        }
        $this->_properties['FirstChar']['value'] = $firstChar;
        $this->_properties['LastChar']['value'] = $lastChar;
        //set widths object
        $this->_widths->setData($this->_properties['Widths']['value']);
    }
    
    static public function defaultExtraction($line) {
        return $line[1];
    }
    
    static public function fontBBoxExtraction($line) {
        return array($line[1], $line[2], $line[3], $line[4]);
    }

    static public function defaultFallback($properties, $property) {
        Node::generateFatalError("Font parsing: No fallback appropriate function to compute " . $property . " value.\n");
    }

    static public function capHeightFallback($properties, $property) {
        return $properties['Ascender']['value'];
    }

    static public function stdVWFallback($properties, $property) {
        if(isset($properties['Weight']['value']) && preg_match('/bold|black/i', $properties['Weight']['value']))
            return 120;
	else
            return 140;
    }

    static public function missingWidthFallback($properties, $property) {
        return 0;
    }

    /**
     * Todo: upgrade flag computation.
     */
    static public function flagFallback($properties, $property) {
        $flags = 0;
	if ($properties['IsFixedPitch']) {
            $flags += 1 << 0;
        }
        // not symbolic
        $flags += 1 << 5;
	if ($properties['ItalicAngle']) {
		$flags += 1 << 6;
        }
        return $flags;
    }
}

?>