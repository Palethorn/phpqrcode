<?php
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

namespace PhpQrCode;

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
            // $base_image = ImageCreate($imgW, $imgH);

            // $col[0] = ImageColorAllocate($base_image,255,255,255);
            // $col[1] = ImageColorAllocate($base_image,0,0,0);

            // imagefill($base_image, 0, 0, $col[0]);

            for($y=0; $y<$h; $y++) {
                for($x=0; $x<$w; $x++) {
                    if ($frame[$y][$x] == '1') {
                        $draw  = new \ImagickDraw();
                        $color = new \ImagickPixel('black');
                        $draw->setFillColor($color);
                        $draw->point($x + $outerFrame, $y + $outerFrame);
                        $base_image->drawImage($draw);

                        // $pixel = $base_image->getImagePixelColor($x+$outerFrame, $y+$outerFrame);
                        // ImageSetPixel($base_image,$x+$outerFrame,$y+$outerFrame,$col[1]); 
                        // $pixel->setColor("black");
                    }
                }
            }
            
            // $target_image = $imagick->newImage($imgW * $pixelPerPoint, $imgH * $pixelPerPoint);
            // $target_image =ImageCreate($imgW * $pixelPerPoint, $imgH * $pixelPerPoint);
            // ImageCopyResized($target_image, $base_image, 0, 0, 0, 0, $imgW * $pixelPerPoint, $imgH * $pixelPerPoint, $imgW, $imgH);
            // $base_image->destroy();
            // ImageDestroy($base_image);
            $base_image->scaleImage($imgW * $pixelPerPoint, $imgH * $pixelPerPoint);
            
            return $base_image;
            // return $target_image;
        }
}