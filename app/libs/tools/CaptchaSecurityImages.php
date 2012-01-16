<?php
/*
* File: CaptchaSecurityImages.php
* Author: Simon Jarvis
* Copyright: 2006 Simon Jarvis
* Date: 03/08/06
* Updated: 07/02/07
* Requirements: PHP 4/5 with GD and FreeType libraries
* Link: http://www.white-hat-web-design.co.uk/articles/php-captcha.php
* 
* This program is free software; you can redistribute it and/or 
* modify it under the terms of the GNU General Public License 
* as published by the Free Software Foundation; either version 2 
* of the License, or (at your option) any later version.
* 
* This program is distributed in the hope that it will be useful, 
* but WITHOUT ANY WARRANTY; without even the implied warranty of 
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the 
* GNU General Public License for more details: 
* http://www.gnu.org/licenses/gpl.html
*
*/

class CaptchaSecurityImages {

	private $font;
        
        private static $filename = NULL;
        

	public function __construct($width='120',$height='40',$characters='6', $bgcol = array(255, 255, 255), $textcol = array(20, 40, 100), $noisecol = array(100, 120, 180)) {
                $this->font = __DIR__ . '/monofont.ttf';
		$code = $this->generateCode($characters);
		/* font size will be 75% of the image height */
		$font_size = $height * 0.75;
		$image = @imagecreate($width, $height) or die('Cannot initialize new GD image stream');
		/* set the colours */
                list($bgR, $bgG, $bgB) = $bgcol;
                list($txtR, $txtG, $txtB) = $textcol;
                list($nsR, $nsG, $nsB) = $noisecol;
                
		$background_color = imagecolorallocate($image, $bgR, $bgG, $bgB);
		$text_color = imagecolorallocate($image, $txtR, $txtG, $txtB);
		$noise_color = imagecolorallocate($image, $nsR, $nsG, $nsB);
		/* generate random dots in background */
		for( $i=0; $i<($width*$height)/3; $i++ ) {
			imagefilledellipse($image, mt_rand(0,$width), mt_rand(0,$height), 1, 1, $noise_color);
		}
		/* generate random lines in background */
		for( $i=0; $i<($width*$height)/150; $i++ ) {
			imageline($image, mt_rand(0,$width), mt_rand(0,$height), mt_rand(0,$width), mt_rand(0,$height), $noise_color);
		}
		/* create textbox and add text */
		$textbox = imagettfbbox($font_size, 0, $this->font, $code) or die('Error in imagettfbbox function');
		$x = ($width - $textbox[4])/2;
		$y = ($height - $textbox[5])/2;
		imagettftext($image, $font_size, 0, $x, $y, $text_color, $this->font , $code) or die('Error in imagettftext function');
		/* output captcha image to browser */
		//header('Content-Type: image/jpeg');
                
                /* self cleaning routine - erases old captcha images*/
		$imagesDir = opendir(WWW_DIR.'/tmp/');
		while($files = readdir($imagesDir)) {
			$images[] = $files;
		}
		closedir($imagesDir);

		$imagesDir = WWW_DIR.'/tmp/';
                
                // must prevent from browser repeated requests
                $timestamp = date("U");
                $timeFromLastRequest = 100;
                if ( isset($_SESSION[md5( baseUrl() )]['BZ_FORM']['timestamp']) ) {
                    $timeFromLastRequest = $timestamp - $_SESSION[md5( baseUrl() )]['BZ_FORM']['timestamp'];
                }
                
                // if request is more then 7 seconds create new captcha
                if( $timeFromLastRequest > 7 ) {
                    
                    foreach($images as $img){		
			if(substr($img, 0, 1) != '.'){
			  if(substr($img, 0 , 7) == 'captcha') unlink($imagesDir . $img);
			}
                    }
                    
                    self::$filename = 'captcha-'.date("U").'.jpg';
                    // U can change the path of the file manually. The images directore must have write rights.
                    $captchafile = WWW_DIR.'/tmp/'.  self::$filename;

                    imagejpeg($image, $captchafile);
                    imagedestroy($image);
                    
                    $_SESSION[md5( baseUrl() )]['BZ_FORM']['timestamp'] = $timestamp;
                    $_SESSION[md5( baseUrl() )]['BZ_FORM']['security_code'] = $code;
                }
                // else show the same captcha
                else {
                    foreach($images as $img){		
			if(substr($img, 0, 1) != '.'){
			  if(substr($img, 0 , 7) == 'captcha') self::$filename = $img;
			}
                    }
                }
                
	}
        
        public function getFilename()
        {
            return self::$filename;
        }
        
        private function generateCode($characters) {
		/* list all possible characters, similar looking characters and vowels have been removed */
		$possible = '23456789bcdfghjkmnpqrstvwxyz';
		$code = '';
		$i = 0;
		while ($i < $characters) { 
			$code .= substr($possible, mt_rand(0, strlen($possible)-1), 1);
			$i++;
		}
		return $code;
	}

}
