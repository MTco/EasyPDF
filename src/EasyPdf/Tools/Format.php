<?php

namespace EasyPdf\Tools;
/**
 * PHP-class for writting PDF.
 *
 * @author greg
 */

class Format {

    /**
     * Read int on filedescriptor
     * and consume bytes on filestream
     * @param <filedescriptor> $fd
     */
    static public function readInt($fd) {
        $a = unpack('Ni', Format::readStream($fd, 4));
        return $a['i'];

    }

    /**
     * Read and consume stream.
     * @param <filedescriptor> $fd
     * @param <int> $n
     */
    static public function readStream($fd, $n) {
        $ret = fread($fd, $n);
    }
}