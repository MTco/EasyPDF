<?php

namespace EasyPdf;
/**
 * PHP-class for writting PDF.
 *
 * @author greg
 */

class FontDescriptorNode extends \EasyPdf\Node {

    /**
     * Font Node.
     */
    private $_fontNode;

    /**
     * Font file data.
     */
    private $_fontFile;
    
    public function __construct(Engine &$pdf, FontNode $font) {
        parent::__construct($pdf, $pdf->getSingleIndex(), $font->getGeneration(), $font);
        $this->_fontNode = $font;

        if ($font->getType() == "TrueType") {
            $this->_fontFile = new FontFileTTNode($pdf, $this);
            $this->addChild($this->_fontFile);
        } else {
            $this->generateFatalError("Only TrueType font is supported for the moment.");
        }
    }

    public function getFontNode() {
        return $this->_fontNode;
    }
    
    public function setFont(FontNode $font) {
        $this->_fontNode = $font;
    }
    
    public function output(&$pdf) {
        parent::preOutput($pdf);
        $this->data($pdf);
        parent::output($pdf);
    }
    
    private function data(&$pdf) {
        $properties = $this->_fontNode->getProperties();

        $data = $this->getBaseDataForTpl();
        $data['fontName'] = $properties['FontName']['value'];
        $data['ascent'] = $properties['Ascender']['value'];
        $data['descent'] = $properties['Descender']['value'];
        $data['capHeight'] = $properties['CapHeight']['value'];
        $data['flags'] = $properties['Flags']['value'];
        $data['fontBBox'] = $properties['FontBBox']['value'];
        $data['italicAngle'] = $properties['ItalicAngle']['value'];
        $data['stemV'] = $properties['StdVW']['value'];
        $data['missingWidth'] = $properties['MissingWidth']['value'];
        $data['fontFile'] = $this->_fontFile->getIndirectReference();

        $pdf .= $this->_template->render($data);
    }
    
}
