<?php
/*
 * PHP QR Code encoder
 *
 * Config file, feel free to modify
 */
     
define('QR_CACHEABLE', true);                                                               // use cache - more disk reads but less CPU power, masks and format templates are stored there
define('QR_CACHE_DIR',__DIR__ . '/../cache/');  // used when QR_CACHEABLE === true
define('QR_LOG_DIR',__DIR__ . '/../');                                // default error logs dir   

define('QR_FIND_BEST_MASK', true);                                                          // if true, estimates best mask (spec. default, but extremally slow; set to false to significant performance boost but (propably) worst quality code
define('QR_FIND_FROM_RANDOM', false);                                                       // if false, checks all masks available, otherwise value tells count of masks need to be checked, mask id are got randomly
define('QR_DEFAULT_MASK', 2);                                                               // when QR_FIND_BEST_MASK === false
                                                
define('QR_PNG_MAXIMUM_SIZE',  1024);                                                       // maximum allowed png image width (in pixels), tune to make sure GD and PHP can handle such big images

// Levels of error correction.

define('QR_ECLEVEL_L', 0);
define('QR_ECLEVEL_M', 1);
define('QR_ECLEVEL_Q', 2);
define('QR_ECLEVEL_H', 3);

// Encoding modes
define('QR_MODE_NUL', -1);
define('QR_MODE_NUM', 0);
define('QR_MODE_AN', 1);
define('QR_MODE_8', 2);
define('QR_MODE_KANJI', 3);
define('QR_MODE_STRUCTURE', 4);


// Supported output formats

define('QR_FORMAT_TEXT', 0);
define('QR_FORMAT_PNG',  1);

// QR Spec
define('QRSPEC_VERSION_MAX', 40);
define('QRSPEC_WIDTH_MAX',   177);

define('QRCAP_WIDTH',        0);
define('QRCAP_WORDS',        1);
define('QRCAP_REMINDER',     2);
define('QRCAP_EC',           3);