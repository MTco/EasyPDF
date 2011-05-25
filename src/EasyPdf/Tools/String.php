<?php

namespace EasyPdf\Tools;
/**
 * PHP-class for writting PDF.
 *
 * @author greg
 */

class String {

    static public function getWidthString($str, $size, $charsW, $missingW) {
        $len = strlen($str);
        $ret = 0;
        $size /= 1000;
        for ($i = 0; $i < $len; ++$i) {
            if (!isset($charsW[ord($str[$i])])) {
                $ret += $missingW * $size;
            } else {
                $ret += $charsW[ord($str[$i])] * $size;
            }
        }
        return $ret;
    }
}