<?php
namespace PhpQrCode\Imagick;

/*
 * PHP QR Code encoder
 *
 * Image output of code using GD2
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

define('QR_IMAGE', true);

class QrImage {

    //----------------------------------------------------------------------
    public static function png($frame, $filename = false, $pixelPerPoint = 4, $outerFrame = 4,$saveandprint=FALSE) 
    {
        return self::image($frame, $pixelPerPoint, $outerFrame);
    }

    //----------------------------------------------------------------------
    public static function jpg($frame, $filename = false, $pixelPerPoint = 8, $outerFrame = 4, $q = 85) 
    {
        return self::image($frame, $pixelPerPoint, $outerFrame);
    }

    //----------------------------------------------------------------------
        private static function image($frame, $pixelPerPoint = 4, $outerFrame = 4) {
            $h = count($frame);
            $w = strlen($frame[0]);
            
            $imgW = $w + 2 * $outerFrame;
            $imgH = $h + 2 * $outerFrame;
            
            $base_image = new \Imagick();
            $base_image->newImage($imgW, $imgH, new \ImagickPixel('white'));
            $base_image->setImageFormat('PNG');

            for($y=0; $y<$h; $y++) {
                for($x=0; $x<$w; $x++) {
                    if ($frame[$y][$x] == '1') {
                        $draw  = new \ImagickDraw();
                        $color = new \ImagickPixel('black');
                        $draw->setFillColor($color);
                        $draw->point($x + $outerFrame, $y + $outerFrame);
                        $base_image->drawImage($draw);
                    }
                }
            }
            
            $base_image->scaleImage($imgW * $pixelPerPoint, $imgH * $pixelPerPoint);
            
            return $base_image;
        }
}