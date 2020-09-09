<?php
namespace PhpQrCode\Encode;

/*
 * PHP QR Code encoder
 *
 * Main encoder classes.
 *
 * Based on libqrencode C library distributed under LGPL 2.1
 * Copyright (C) 2006, 2007, 2008, 2009 Kentaro Fukuchi <fukuchi@megaui.net>
 *
 * PHP QR Code is distributed under LGPL 3
 * Copyright (C) 2010 Dominik Dzienia <deltalab at poczta dot fm>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 */

use PhpQrCode\QrCode;
use PhpQrCode\QrTools;

class QrEncode {

    public $casesensitive = true;
    public $eightbit = false;
    
    public $version = 0;
    public $size = 3;
    public $margin = 4;
    public $driver = 'Imagick';

    public $structured = 0; // not supported yet
    
    public $level = QR_ECLEVEL_L;
    public $hint = QR_MODE_8;
    
    public static function factory($level = QR_ECLEVEL_L, $size = 3, $margin = 4, $driver = 'Imagick') {
        $enc = new QrEncode();
        $enc->size = $size;
        $enc->margin = $margin;
        $enc->driver = $driver;
        
        switch ($level.'') {
            case '0':
            case '1':
            case '2':
            case '3':
                    $enc->level = $level;
                break;
            case 'l':
            case 'L':
                    $enc->level = QR_ECLEVEL_L;
                break;
            case 'm':
            case 'M':
                    $enc->level = QR_ECLEVEL_M;
                break;
            case 'q':
            case 'Q':
                    $enc->level = QR_ECLEVEL_Q;
                break;
            case 'h':
            case 'H':
                    $enc->level = QR_ECLEVEL_H;
                break;
        }
        
        return $enc;
    }
    
    public function encodeRAW($intext, $outfile = false) {
        $code = new QrCode();

        if($this->eightbit) {
            $code->encodeString8bit($intext, $this->version, $this->level);
        } else {
            $code->encodeString($intext, $this->version, $this->level, $this->hint, $this->casesensitive);
        }
        
        return $code->data;
    }

    public function encode($intext, $outfile = false) {
        $code = new QRcode();

        if($this->eightbit) {
            $code->encodeString8bit($intext, $this->version, $this->level);
        } else {
            $code->encodeString($intext, $this->version, $this->level, $this->hint, $this->casesensitive);
        }
        
        QrTools::markTime('after_encode');
        
        if ($outfile!== false) {
            file_put_contents($outfile, join("\n", QrTools::binarize($code->data)));
        } else {
            return QrTools::binarize($code->data);
        }
    }
    
    public function encodePNG($intext, $outfile = false, $saveandprint=false) {
        try {
        
            ob_start();
            $tab = $this->encode($intext);
            $err = ob_get_contents();
            ob_end_clean();
            
            if ($err != '') {
                QrTools::log($outfile, $err);
            }
            
            $maxSize = (int)(QR_PNG_MAXIMUM_SIZE / (count($tab)+2*$this->margin));
            $class = '\\PhpQrCode\\' . $this->driver . '\\QrImage';
            return $class::png($tab, $outfile, min(max(1, $this->size), $maxSize), $this->margin,$saveandprint);
        
        } catch (\Exception $e) {
            echo $e->getMessage();
            QrTools::log($outfile, $e->getMessage());
            return null;
        }
    }

    public function encodeJPEG($intext, $outfile = false, $saveandprint=false) {
        try {
            ob_start();
            $tab = $this->encode($intext);
            $err = ob_get_contents();
            ob_end_clean();
            
            if ($err != '') {
                QrTools::log($outfile, $err);
            }
            
            $maxSize = (int)(QR_PNG_MAXIMUM_SIZE / (count($tab)+2*$this->margin));
            $class = '\\PhpQrCode\\' . $this->driver . '\\QrImage';
            return $class::jpg($tab, $outfile, min(max(1, $this->size), $maxSize), $this->margin,$saveandprint);
        
        } catch (\Exception $e) {
            echo $e->getMessage();
            QrTools::log($outfile, $e->getMessage());
            return null;
        }
    }
}
