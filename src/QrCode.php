<?php
namespace PhpQrCode;

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

use PhpQrCode\Encode\FrameFiller;
use PhpQrCode\Encode\QrEncode;
use PhpQrCode\Encode\QrRawCode;
use PhpQrCode\Input\QrInput;
use PhpQrCode\QrMask;
use PhpQrCode\QrSpec;
use PhpQrCode\QrSplit;
use PhpQrCode\QrTools;

class QrCode {

    public $version;
    public $width;
    public $data; 
    
    public function encodeMask(QrInput $input, $mask) {
        if($input->getVersion() < 0 || $input->getVersion() > QRSPEC_VERSION_MAX) {
            throw new \Exception('wrong version');
        }
        if($input->getErrorCorrectionLevel() > QR_ECLEVEL_H) {
            throw new \Exception('wrong level');
        }

        $raw = new QrRawCode($input);
        
        QrTools::markTime('after_raw');
        
        $version = $raw->version;
        $width = QrSpec::getWidth($version);
        $frame = QrSpec::newFrame($version);
        
        $filler = new FrameFiller($width, $frame);
        if(is_null($filler)) {
            return NULL;
        }

        // inteleaved data and ecc codes
        for($i=0; $i<$raw->dataLength + $raw->eccLength; $i++) {
            $code = $raw->getCode();
            $bit = 0x80;
            for($j=0; $j<8; $j++) {
                $addr = $filler->next();
                $filler->setFrameAt($addr, 0x02 | (($bit & $code) != 0));
                $bit = $bit >> 1;
            }
        }
        
        QrTools::markTime('after_filler');
        
        unset($raw);
        
        // remainder bits
        $j = QrSpec::getRemainder($version);
        for($i=0; $i<$j; $i++) {
            $addr = $filler->next();
            $filler->setFrameAt($addr, 0x02);
        }
        
        $frame = $filler->frame;
        unset($filler);
        
        
        // masking
        $maskObj = new QrMask();
        if($mask < 0) {
        
            if (QR_FIND_BEST_MASK) {
                $masked = $maskObj->mask($width, $frame, $input->getErrorCorrectionLevel());
            } else {
                $masked = $maskObj->makeMask($width, $frame, (intval(QR_DEFAULT_MASK) % 8), $input->getErrorCorrectionLevel());
            }
        } else {
            $masked = $maskObj->makeMask($width, $frame, $mask, $input->getErrorCorrectionLevel());
        }
        
        if($masked == NULL) {
            return NULL;
        }
        
        QrTools::markTime('after_mask');
        
        $this->version = $version;
        $this->width = $width;
        $this->data = $masked;
        
        return $this;
    }

    public function encodeInput(QrInput $input) {
        return $this->encodeMask($input, -1);
    }
    
    public function encodeString8bit($string, $version, $level) {
        if($string == NULL) {
            throw new \Exception('empty string!');
            return NULL;
        }

        $input = new QrInput($version, $level);
        if($input == NULL) return NULL;

        $ret = $input->append($input, QR_MODE_8, strlen($string), str_split($string));
        if($ret < 0) {
            unset($input);
            return NULL;
        }
        return $this->encodeInput($input);
    }

    public function encodeString($string, $version, $level, $hint, $casesensitive) {

        if($hint != QR_MODE_8 && $hint != QR_MODE_KANJI) {
            throw new \Exception('bad hint');
            return NULL;
        }

        $input = new QrInput($version, $level);
        if($input == NULL) return NULL;

        $ret = QrSplit::splitStringToQRinput($string, $input, $hint, $casesensitive);
        if($ret < 0) {
            return NULL;
        }

        return $this->encodeInput($input);
    }
    
    public static function png($text, $driver = 'Imagick', $outfile = false, $level = QR_ECLEVEL_L, $size = 3, $margin = 4, $saveandprint=false) {
        $enc = QrEncode::factory($level, $size, $margin, $driver);
        return $enc->encodePNG($text, $outfile, $saveandprint = false);
    }

    public static function jpg($text, $driver = 'Imagick', $outfile = false, $level = QR_ECLEVEL_L, $size = 3, $margin = 4, $saveandprint=false) {
        $enc = QrEncode::factory($level, $size, $margin, $driver);
        return $enc->encodeJPEG($text, $outfile, $saveandprint = false);
    }

    public static function text($text, $outfile = false, $level = QR_ECLEVEL_L, $size = 3, $margin = 4) {
        $enc = QrEncode::factory($level, $size, $margin);
        return $enc->encode($text, $outfile);
    }

    public static function raw($text, $outfile = false, $level = QR_ECLEVEL_L, $size = 3, $margin = 4) {
        $enc = QrEncode::factory($level, $size, $margin);
        return $enc->encodeRAW($text, $outfile);
    }
}