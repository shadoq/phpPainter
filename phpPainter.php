<?php
/**
 * *****************************************************************************
 * Copyright 2013 See AUTHORS file.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 * ****************************************************************************
 */

/**
 * Helper class to process image with PHP GD2 library and low level pixmap function
 * @author Jaroslaw Czub <http://shad.net.pl>
 * (c) 2007 - 2013
 */
class PhpPainter{

	const SCALE_WIDTH=0;
	const SCALE_HEIGHT=1;
	const SCALE_AUTO=2;
	const SCALE_NO_CHANGE=3;

	protected $version="0.12a";
	protected $dir=NULL;
	protected $thumbnailWidth=100;
	protected $thumbnailHeight=80;
	protected $thumbnailScaleType=SCALE_2;
	protected $imageWidth=480;
	protected $imageHeight=480;
	protected $imagelScaleType=SCALE_2;
	protected $fileName='';
	protected $fileNameWithoutExt='';
	protected $fileExt='';
	protected $filePath='';
	protected $thumbnailName='';
	protected $imageRes=NULL;
	protected $workWidth=NULL;
	protected $workHeight=NULL;
	protected $jpegQuality=80;
	protected $type=array('jpg', 'jpeg', 'png', 'gif');

	/**
	 * 
	 * @return type
	 */
	public function getImageWidth(){
		return $this->imageWidth;
	}

	/**
	 * 
	 * @param type $imageWidth
	 */
	public function setImageWidth($imageWidth){
		$this->imageWidth=$imageWidth;
	}

	/**
	 * 
	 * @return type
	 */
	public function getImageHeight(){
		return $this->imageHeight;
	}

	/**
	 * 
	 * @param type $imageHeight
	 */
	public function setImageHeight($imageHeight){
		$this->imageHeight=$imageHeight;
	}

	/**
	 * 
	 * @return type
	 */
	public function getImagelScaleType(){
		return $this->imagelScaleType;
	}

	/**
	 * 
	 * @param type $imagelScaleType
	 */
	public function setImagelScaleType($imagelScaleType){
		$this->imagelScaleType=$imagelScaleType;
	}

	/**
	 * 
	 * @return type
	 */
	public function getJpegQuality(){
		return $this->jpegQuality;
	}

	/**
	 * 
	 * @param type $jpegQuality
	 */
	public function setJpegQuality($jpegQuality){
		$this->jpegQuality=$jpegQuality;
	}

	/**
	 * 
	 * @return type
	 */
	public function getThumbnailWidth(){
		return $this->thumbnailWidth;
	}

	/**
	 * 
	 * @return type
	 */
	public function getThumbnailHeight(){
		return $this->thumbnailHeight;
	}
	
	/**
	 * 
	 * @param type $thumbnailWidth
	 */
	public function setThumbnailWidth($thumbnailWidth){
		$this->thumbnailWidth=$thumbnailWidth;
	}

	/**
	 * 
	 * @param type $thumbnailHeight
	 */
	public function setThumbnailHeight($thumbnailHeight){
		$this->thumbnailHeight=$thumbnailHeight;
	}

	/**
	 * 
	 * @return type
	 */
	public function getThumbnailScaleType(){
		return $this->thumbnailScaleType;
	}

	/**
	 * 
	 * @param type $thumbnailScaleType
	 */
	public function setThumbnailScaleType($thumbnailScaleType){
		$this->thumbnailScaleType=$thumbnailScaleType;
	}
	
		
	/**
	 * Resample the image to new size
	 * @param type $destWidth
	 * @param type $destHeight
	 * @param type $rescaleType - 
	 */
	public function resampleImage($destWidth='', $destHeight='', $rescaleType=''){

		if ($destWidth == ''){
			$destWidth=$this->imageWidth;
		};
		if ($destHeight == ''){
			$destHeight=$this->imageHeight;
		};
		if ($rescaleType == ''){
			$rescaleType=$this->imagelScaleType;
		};

		$coefficientX=$destWidth / $this->workWidth;
		$coefficientY=$destHeight / $this->workHeight;
		$aspect=$coefficientX / $coefficientY;

		switch ($rescaleType){
			case PhpPainter::SCALE_NO_CHANGE:
				break;
			case PhpPainter::SCALE_AUTO:
				if ($this->workWidth > $this->workHeight){
					//
					// Width
					//
					$coefficientY=$coefficientX;
				} else {
					//
					// Height
					//
					$coefficientX=$coefficientY;
				};
				break;
			case PhpPainter::SCALE_HEIGHT:
				$coefficientX=$coefficientY;
				break;
			case PhpPainter::SCALE_WIDTH:
				$coefficientY=$coefficientX;
				break;
		};
		$destWidth=(int) ($this->workWidth * $coefficientX);
		$destHeight=(int) ($this->workHeight * $coefficientY);

		if ('gd2' == $this->checkGdVersion()){
			$imgRes=imagecreatetruecolor($destWidth, $destHeight);
			imagecopyresampled($imgRes, $this->imageRes, 0, 0, 0, 0, $destWidth + 1, $destHeight + 1, $this->workWidth, $this->workHeight);
		} else {
			$imgRes=imagecreate($destWidth, $destHeight);
			imagecopyresized($imgRes, $this->imageRes, 0, 0, 0, 0, $destWidth + 1, $destHeight + 1, $this->workWidth, $this->workHeight);
		}

		imagedestroy($this->imageRes);
		$this->imageRes=$imgRes;
	}

	/**
	 * Vertical flip image
	 */
	public function flipVertical(){
		if ('gd2' == $this->checkGdVersion()){
			$imgRes=imagecreatetruecolor($this->workWidth, $this->workHeight);
			for ($x=0; $x < $this->workWidth; ++$x){
				for ($y=0; $y < $this->workHeight; ++$y){
					imagecopy($imgRes, $this->imageRes, $this->workWidth - $x - 1, $y, $x, $y, 1, 1);
				}
			}
			imagedestroy($this->imageRes);
			$this->imageRes=$imgRes;
		}
	}

	/**
	 * Horizontal flip image
	 */
	public function flipHorizontal(){
		if ('gd2' == $this->checkGdVersion()){
			$tmp=imagecreatetruecolor($this->workWidth, $this->workHeight);
			for ($x=0; $x < $this->workWidth; ++$x){
				for ($y=0; $y < $this->workHeight; ++$y){
					imagecopy($tmp, $this->imageRes, $x, $this->workHeight - $y - 1, $x, $y, 1, 1);
				}
			}
			imagedestroy($this->imageRes);
			$this->imageRes=$tmp;
		}
	}

	/**
	 * Rotate image on angle 90, 180, 270 degrees
	 * @param int $angle - support angle 90, 180, 270 degrees
	 */
	public function rotate($angle=90){

		if ('gd2' == $this->checkGdVersion()){
			if (!in_array($angle, array(0, 90, 180, 270))){
				$angle=0;
			}
			if ($angle != 0){
				if ($angle == 90 || $angle == 270){
					$tmp=imagecreatetruecolor($this->workHeight, $this->workWidth);
				} else {
					$tmp=imagecreatetruecolor($this->workWidth, $this->workHeight);
				}
				for ($x=0; $x < $this->workWidth; ++$x){
					for ($y=0; $y < $this->workHeight; $y++){
						if ($angle == 90){
							imagecopy($tmp, $this->imageRes, $y, $x, $x, $this->workHeight - $y - 1, 1, 1);
						} elseif ($angle == 180){
							imagecopy($tmp, $this->imageRes, $x, $y, $this->workWidth - $x - 1, $this->workHeight - $y - 1, 1, 1);
						} elseif ($angle == 270){
							imagecopy($tmp, $this->imageRes, $y, $x, $this->workWidth - $x - 1, $y, 1, 1);
						} else {
							imagecopy($tmp, $this->imageRes, $x, $y, $x, $y, 1, 1);
						}
					}
				}

				if ($angle == 90 || $angle == 270){
					$t=$this->workHeight;
					$this->workHeight=$this->workWidth;
					$this->workWidth=$t;
				}
				imagedestroy($this->imageRes);
				$this->imageRes=$tmp;
			}
		}
	}

	/**
	 * Add overlay color to the image
	 * @param type $percent
	 * @param type $red
	 * @param type $green
	 * @param type $blue
	 */
	function overlayColor($percent=50, $red=255, $green=0, $blue=0){
		if ('gd2' == $this->checkGdVersion()){
			$filtrRes=imagecreatetruecolor($this->workWidth, $this->workHeight);
			$color=imagecolorallocate($filtrRes, $red, $green, $blue);
			imagefilledrectangle($filtrRes, 0, 0, $this->workWidth, $this->workHeight, $color);
			imagecopymerge($this->imageRes, $filtrRes, 0, 0, 0, 0, $this->workWidth, $this->workHeight, $percent);
			imagedestroy($filtrRes);
		};
	}

	/**
	 * Change the brightness image
	 * @param type $brightness
	 */
	function brightness($brightness){
		imagefilter($this->imageRes, IMG_FILTER_BRIGHTNESS, $brightness);
	}

	/**
	 * Change the contrast image
	 * @param type $contrast
	 */
	function contrast($contrast){
		imagefilter($this->imageRes, IMG_FILTER_CONTRAST, $contrast);
	}

	/**
	 * Colorize image
	 * @param type $red
	 * @param type $green
	 * @param type $blue
	 */
	function colorize($red, $green, $blue){
		imagefilter($this->imageRes, IMG_FILTER_COLORIZE, $red, $green, $blue);
	}

	/**
	 * Blur the image, using gaussian algorithm
	 * @param type $blur - amount of blur
	 */
	function gaussianBlur($blur){
		for ($i=0; $i < $blur; $i++){
			imagefilter($this->imageRes, IMG_FILTER_GAUSSIAN_BLUR);
		};
	}

	/**
	 * Unsharp mask the image
	 * @param type $unsharp - amount of unsharp
	 */
	function unsharp($unsharp){
		for ($i=0; $i < $unsharp; $i++){
			imagefilter($this->imageRes, IMG_FILTER_MEAN_REMOVAL);
		};
	}

	/**
	 * Blur the image, using smooth algorithm
	 * @param type $blur - amount of blur
	 * @param type $level - blur level
	 */
	function blur($blur, $level){
		for ($i=0; $i < $blur; $i++){
			imagefilter($this->imageRes, IMG_FILTER_SMOOTH, $level);
		};
	}

	/**
	 * Invert the image
	 */
	function invert(){
		imagefilter($this->imageRes, IMG_FILTER_NEGATE);
	}

	/**
	 * Convert image to greyscale
	 */
	function greyscale(){
		imagefilter($this->imageRes, IMG_FILTER_GRAYSCALE);
	}

	/**
	 * Detect edge of the image
	 */
	function edge(){
		imagefilter($this->imageRes, IMG_FILTER_EDGEDETECT);
	}

	/**
	 * Process emboss filter
	 */
	function emboss(){
		imagefilter($this->imageRes, IMG_FILTER_EMBOSS);
	}

	/**
	 * Process emboss filter and detect edge of filter image
	 */
	function eEdge(){
		imagefilter($this->imageRes, IMG_FILTER_EDGEDETECT);
		imagefilter($this->imageRes, IMG_FILTER_EMBOSS);
	}

	/**
	 * Posterize filter, process image to fewer tones
	 * @param type $level
	 */
	function posterize($level=64){
		imagetruecolortopalette($this->imageRes, false, $level);
	}

	/**
	 * Draw horizontal black lines
	 * @param type $step
	 */
	function stripLine($step=2){
		$black=imagecolorallocate($this->imageRes, 0, 0, 0);
		for ($y=1; $y < $this->workHeight; $y += $step){
			imageline($this->imageRes, 0, $y, $this->workWidth, $y, $black);
		}
	}

	/**
	 * Draw black screen lines
	 * @param type $step
	 */
	function screen($step=2){
		$black=imagecolorallocate($this->imageRes, 0, 0, 0);
		for ($x=1; $x <= $this->workWidth; $x += $step){
			imageline($this->imageRes, $x, 0, $x, $this->workHeight, $black);
		}

		for ($y=1; $y <= $this->workHeight; $y += $step){
			imageline($this->imageRes, 0, $y, $this->workWidth, $y, $black);
		}
	}

	/**
	 * Add grayscale noise to the image
	 * @param type $level
	 */
	function noise($level=64){
		for ($x=0; $x < $this->workWidth; ++$x){
			for ($y=0; $y < $this->workHeight; ++$y){
				if (rand(0, 1)){
					$rgb=imagecolorat($this->imageRes, $x, $y);
					$red=($rgb >> 16) & 0xFF;
					$green=($rgb >> 8) & 0xFF;
					$blue=$rgb & 0xFF;
					$modifier=rand(-$level, $level);
					$red += $modifier;
					$green += $modifier;
					$blue += $modifier;

					if ($red > 255) $red=255;
					if ($green > 255) $green=255;
					if ($blue > 255) $blue=255;
					if ($red < 0) $red=0;
					if ($green < 0) $green=0;
					if ($blue < 0) $blue=0;

					$newcol=imagecolorallocate($this->imageRes, $red, $green, $blue);
					imagesetpixel($this->imageRes, $x, $y, $newcol);
				}
			}
		}
	}

	/**
	 * Add fullcolor noise to the image
	 * @param type $level
	 */
	function noiseHSB($level=64){
		for ($x=0; $x < $this->workWidth; ++$x){
			for ($y=0; $y < $this->workHeight; ++$y){
				if (rand(0, 1)){
					$rgb=imagecolorat($this->imageRes, $x, $y);
					$red=($rgb >> 16) & 0xFF;
					$green=($rgb >> 8) & 0xFF;
					$blue=$rgb & 0xFF;
					$red += rand(-$level, $level);
					$green += rand(-$level, $level);
					$blue += rand(-$level, $level);

					if ($red > 255) $red=255;
					if ($green > 255) $green=255;
					if ($blue > 255) $blue=255;
					if ($red < 0) $red=0;
					if ($green < 0) $green=0;
					if ($blue < 0) $blue=0;

					$newcol=imagecolorallocate($this->imageRes, $red, $green, $blue);
					imagesetpixel($this->imageRes, $x, $y, $newcol);
				}
			}
		}
	}

	/**
	 * Detect left edge of the image
	 * @param type $level
	 */
	function edgeLeft($level=10){
		if (function_exists('imageconvolution')){ // PHP >= 5.1
			$matrix=array(array(0.0, 0.0, 0.0),
				array(-$level, $level, 0.0),
				array(0.0, 0.0, 0.0)
			);
			imageconvolution($this->imageRes, $matrix, 8, 0);
		};
	}

	/**
	 * Detect right edge of the image
	 * @param type $level
	 */
	function edgeRight($level=10){
		if (function_exists('imageconvolution')){ // PHP >= 5.1
			$matrix=array(array(0.0, 0.0, 0.0),
				array(0.0, $level, -$level),
				array(0.0, 0.0, 0.0)
			);
			imageconvolution($this->imageRes, $matrix, 8, 0);
		};
	}

	/**
	 * Detect top edge of the image
	 * @param type $level
	 */
	function edgeTop($level=10){
		if (function_exists('imageconvolution')){ // PHP >= 5.1
			$matrix=array(array(0.0, -$level, 0.0),
				array(0.0, $level, 0.0),
				array(0.0, 0.0, 0.0)
			);
			imageconvolution($this->imageRes, $matrix, 8, 0);
		};
	}

	/**
	 * Detect bottom edge of the image
	 * @param type $level
	 */
	function edgeBottom($level=10){
		if (function_exists('imageconvolution')){ // PHP >= 5.1
			$matrix=array(array(0.0, 0.0, 0.0),
				array(0.0, $level, 0.0),
				array(0.0, -$level, 0.0)
			);
			imageconvolution($this->imageRes, $matrix, 8, 0);
		};
	}

	/**
	 * Detect top left edge of the image
	 * @param type $level
	 */
	function edgeTopLeft($level=10){
		if (function_exists('imageconvolution')){ // PHP >= 5.1
			$matrix=array(array(-$level, 0.0, 0.0),
				array(0.0, $level, 0.0),
				array(0.0, 0.0, 0.0)
			);
			imageconvolution($this->imageRes, $matrix, 8, 0);
		};
	}

	/**
	 * Detect top right edge of the image
	 * @param type $level
	 */
	function edgeTopRight($level=10){
		if (function_exists('imageconvolution')){ // PHP >= 5.1
			$matrix=array(array(0.0, 0.0, -$level),
				array(0.0, $level, 0.0),
				array(0.0, 0.0, 0.0)
			);
			imageconvolution($this->imageRes, $matrix, 8, 0);
		};
	}

	/**
	 * Detect bottom left edge of the image
	 * @param type $level
	 */
	function edgeBottomLeft($level=10){
		if (function_exists('imageconvolution')){ // PHP >= 5.1
			$matrix=array(array(0.0, 0.0, 0.0),
				array(0.0, $level, 0.0),
				array(-$level, 0.0, 0.0)
			);
			imageconvolution($this->imageRes, $matrix, 8, 0);
		};
	}

	/**
	 * Detect bottom right edge of the image
	 * @param type $level
	 */
	function edgeBottomRight($level=10){
		if (function_exists('imageconvolution')){ // PHP >= 5.1
			$matrix=array(array(0.0, 0.0, 0.0),
				array(0.0, $level, 0.0),
				array(0.0, 0.0, -$level)
			);
			imageconvolution($this->imageRes, $matrix, 8, 0);
		};
	}

	/**
	 * 
	 */
	function autoLevel(){

		if ('gd2' == $this->checkGdVersion()){

			$redMin=255;
			$redMax=0;
			$greenMin=255;
			$greenMax=0;
			$blueMin=255;
			$blueMax=0;

			//
			// Analize image
			//
			for ($y=0; $y < $this->workHeight; $y++){
				for ($x=0; $x < $this->workWidth; $x++){

					$rgb=imagecolorat($this->imageRes, $x, $y);
					$pixel=imagecolorsforindex($this->imageRes, $rgb);

					$redMin=min($redMin, $pixel['red']);
					$redMax=max($redMax, $pixel['red']);
					$greenMin=min($greenMin, $pixel['green']);
					$greenMax=max($greenMax, $pixel['green']);
					$blueMin=min($blueMin, $pixel['blue']);
					$blueMax=max($blueMax, $pixel['blue']);
				}
			}

			//
			// Calculate level
			//
			$levelRed=255 / ($redMax - $redMin);
			$levelGreen=255 / ($greenMax - $greenMin);
			$levelBlue=255 / ($blueMax - $blueMin);

			//
			// Process image filter
			//
			for ($y=0; $y < $this->workHeight; $y++){
				for ($x=0; $x < $this->workWidth; $x++){
					$rgb=imagecolorat($this->imageRes, $x, $y);
					$pixel=imagecolorsforindex($this->imageRes, $rgb);
					$r=max(min(round(($pixel['red'] - $redMin) * $levelRed), 255), 0);
					$g=max(min(round(($pixel['green'] - $greenMin) * $levelGreen), 255), 0);
					$b=max(min(round(($pixel['blue'] - $blueMin) * $levelBlue), 255), 0);
					$a=$pixel['alpha'];
					$pixelcolor=imagecolorallocatealpha($this->imageRes, $r, $g, $b, $a);
					imagealphablending($this->imageRes, true);
					imagesetpixel($this->imageRes, $x, $y, $pixelcolor);
				}
			}
		}
	}

	/**
	 * Process threshold filter
	 * @param type $level
	 */
	function treshold($level=152){

		imagefilter($this->imageRes, IMG_FILTER_GRAYSCALE);

		for ($y=0; $y < $this->workHeight; $y++){
			for ($x=0; $x < $this->workWidth; $x++){
				$rgb=imagecolorat($this->imageRes, $x, $y);
				$pixel=imagecolorsforindex($this->imageRes, $rgb);
				$px=($pixel['green'] + $pixel['red'] + $pixel['blue']) / 3;

				if ($px > $level){
					$r=255;
					$g=255;
					$b=255;
				} else {
					$r=0;
					$g=0;
					$b=0;
				}
				$a=$pixel['alpha'];
				$pixelcolor=imagecolorallocatealpha($this->imageRes, $r, $g, $b, $a);
				imagealphablending($this->imageRes, true);
				imagesetpixel($this->imageRes, $x, $y, $pixelcolor);
			}
		}
	}

	/**
	 * Process BCT filter of the image
	 * @param type $brightness
	 * @param type $contrast
	 * @param type $colorTint
	 */
	function brightnessContrastTintImage($brightness='', $contrast='', $colorTint=''){

		if ('gd2' == $this->checkGdVersion()){
			if (!empty($colorTint)){
				$red=0;
				$green=0;
				$blue=0;
				sscanf($colorTint, "#%2x%2x%2x", $red, $green, $blue);
			}
			$background=imagecolorallocatealpha($this->imageRes, 255, 255, 255, 0);
			imagefill($this->imageRes, 0, 0, $background);

			for ($y=0; $y < $this->workHeight; $y++){
				for ($x=0; $x < $this->workWidth; $x++){

					//
					// Brightness
					//
					if (is_numeric($brightness)){
						$rgb=imagecolorat($this->imageRes, $x, $y);
						$pixel=imagecolorsforindex($this->imageRes, $rgb);
						$r=max(min(round($pixel['red'] + (($brightness * 2) - 256)), 255), 0);
						$g=max(min(round($pixel['green'] + (($brightness * 2) - 256)), 255), 0);
						$b=max(min(round($pixel['blue'] + (($brightness * 2) - 256)), 255), 0);
						$a=$pixel['alpha'];
						$pixelcolor=imagecolorallocatealpha($this->imageRes, $r, $g, $b, $a);
						imagealphablending($this->imageRes, true);
						imagesetpixel($this->imageRes, $x, $y, $pixelcolor);
					}

					//
					// Contrast
					//
					if (is_numeric($contrast)){
						$rgb=imagecolorat($this->imageRes, $x, $y);
						$pixel=imagecolorsforindex($this->imageRes, $rgb);
						$r=max(min(round($contrast * $pixel['red'] / 128), 255), 0);
						$g=max(min(round($contrast * $pixel['green'] / 128), 255), 0);
						$b=max(min(round($contrast * $pixel['blue'] / 128), 255), 0);
						$a=$pixel['alpha'];
						$pixelcolor=imagecolorallocatealpha($this->imageRes, $r, $g, $b, $a);
						imagealphablending($this->imageRes, true);
						imagesetpixel($this->imageRes, $x, $y, $pixelcolor);
					}

					//
					// ColorTint
					//
					if (!empty($colorTint)){
						$rgb=imagecolorat($this->imageRes, $x, $y);
						$pixel=imagecolorsforindex($this->imageRes, $rgb);
						$r=min(round($red * $pixel['red'] / 169), 255);
						$g=min(round($green * $pixel['green'] / 169), 255);
						$b=min(round($blue * $pixel['blue'] / 169), 255);
						$a=$pixel['alpha'];
						$pixelcolor=imagecolorallocatealpha($this->imageRes, $r, $g, $b, $a);
						imagealphablending($this->imageRes, TRUE);
						imagesetpixel($this->imageRes, $x, $y, $pixelcolor);
					}
				}
			}
		};
	}

	/**
	 * Add text to the image 
	 * @param type $text
	 * @param type $textDirection - v - vertical, h - horizontal
	 * @param type $textColor
	 * @param type $overlayerPercent
	 * @param type $backgroundColor
	 * @param type $backgroundPercent
	 * @param type $textFont
	 * @param type $positionX
	 * @param type $positionY
	 * @param type $textPosition - auto text position - t - top, b - bottom, l - left, r - right
	 * @param int $textPadding
	 * @param int $paddingX
	 * @param type $paddingY
	 */
	function addText($text=NULL, $textDirection=NULL, $textColor='#FFFF00', $overlayerPercent=50, $backgroundColor=NULL, $backgroundPercent=50, $textFont=5, $positionX=NULL, $positionY=NULL, $textPosition=NULL, $textPadding=0, $paddingX=NULL, $paddingY=NULL){

		if (!is_numeric($textPadding)){
			$textPadding=0;
		}
		if (!is_numeric($paddingX)){
			$paddingX=$textPadding;
		}
		if (!is_numeric($paddingY)){
			$paddingY=$textPadding;
		}
		$textPosition=strtolower($textPosition);
		$textDirection=strtolower($textDirection);

		if ($textDirection == 'v'){
			$height=(ImageFontWidth($textFont) * strlen($text)) + (2 * $paddingY);
			$width=ImageFontHeight($textFont) + (2 * $paddingX);
		} else {
			$width=(ImageFontWidth($textFont) * strlen($text)) + (2 * $paddingX);
			$height=ImageFontHeight($textFont) + (2 * $paddingY);
		}

		$textPosX=0;
		$textPosY=0;

		if (is_numeric($positionX)){
			if ($positionX < 0){
				$textPosX=$this->workWidth - $width + $positionX;
			} else {
				$textPosX=$positionX;
			}
		} else {
			if (strpos($textPosition, 'r') !== false){
				$textPosX=$this->workWidth - $width;
			} else if (strpos($textPosition, 'l') !== false){
				$textPosX=0;
			} else {
				$textPosX=($this->workWidth - $width) / 2;
			}
		}

		if (is_numeric($positionY)){
			if ($positionY < 0){
				$textPosY=$this->workHeight - $height + $positionY;
			} else {
				$textPosY=$positionY;
			}
		} else {
			if (strpos($textPosition, 'b') !== false){
				$textPosY=$this->workHeight - $height;
			} else if (strpos($textPosition, 't') !== false){
				$textPosY=0;
			} else {
				$textPosY=($this->workHeight - $height) / 2;
			}
		}

		if (!empty($backgroundColor)){
			sscanf($backgroundColor, "#%2x%2x%2x", $red, $green, $blue);
			if ('gd2' == $this->checkGdVersion() && (is_numeric($backgroundPercent)) && $backgroundPercent >= 0 && $backgroundPercent <= 100){
				$filter=imagecreatetruecolor($width, $height);
				$imageBG=imagecolorallocate($filter, $red, $green, $blue);
				imagefilledrectangle($filter, 0, 0, $width, $height, $imageBG);
				imagecopymerge($this->imageRes, $filter, $textPosX, $textPosY, 0, 0, $width, $height, $backgroundPercent);
				imagedestroy($filter);
			} else {
				$imageBG=imageColorAllocate($this->imageRes, $red, $green, $blue);
				imagefilledrectangle($this->imageRes, $textPosX, $textPosY, $textPosX + $width, $textPosY + $height, $imageBG);
			}
		}
		$textPosX += $paddingX;
		$textPosY += $paddingY;

		sscanf($textColor, "#%2x%2x%2x", $red, $green, $blue);

		if ('gd2' == $this->checkGdVersion() && (is_numeric($overlayerPercent)) && $overlayerPercent >= 0 && $overlayerPercent <= 100){
			$tWidth=$width - (2 * $paddingX);
			$tHeight=$height - (2 * $paddingY);
			if ($tWidth < 0) $tWidth=0;
			if ($tHeight < 0) $tHeight=0;

			$filter=imagecreatetruecolor($tWidth, $tHeight);
			$color=imagecolorallocate($filter, 0, 0, 0);
			$textColor=imageColorAllocate($filter, $red, $green, $blue);
			imagecolortransparent($filter, $color);

			if ($textDirection == 'v'){
				imagestringup($filter, $textFont, 0, $height - (2 * $paddingY), $text, $textColor);
			} else {
				imagestring($filter, $textFont, 0, 0, $text, $textColor);
			}
			imagecopymerge($this->imageRes, $filter, $textPosX, $textPosY, 0, 0, $tWidth, $tHeight, $overlayerPercent);
			imagedestroy($filter);
		} else {
			$textColor=imageColorAllocate($this->imageRes, $red, $green, $blue);
			if ($textDirection == 'v'){
				imagestringup($this->imageRes, $textFont, $textPosX, $textPosY + $height - (2 * $paddingY), $text, $textColor);
			} else {
				imagestring($this->imageRes, $textFont, $textPosX, $textPosY, $text, $textColor);
			}
		}
	}

	/**
	 * Add watermark to the image
	 * @param type $watermark - watermark file name 
	 * @param type $positionX
	 * @param type $positionY
	 * @param type $position - auto position - t - top, b - bottom, l - left, r - right
	 */
	function watermark($watermark, $positionX, $positionY, $position){

		$watermarkFile=$watermark;
		$watermarkThumbals='th_'.$watermark;
		$watermarkName=preg_replace("/(.*)\.([^.]+)$/", "\\1", $watermark);
		$watermarkExt=preg_replace("/.*\.([^.]+)$/", "\\1", $watermark);

		switch ($watermarkExt){
			case 'gif':
				$filter=imagecreatefromgif($watermarkFile);
				break;
			case 'png':
				$filter=imagecreatefrompng($watermarkFile);
				break;
			case 'jpg':
			case 'jpeg':
				$filter=imagecreatefromjpeg($watermarkFile);
				break;
			default:
				throw new UnexpectedValueException('Not support file type'.$this->fileExt);
				break;
		}

		$watermarkWidth=imagesx($filter);
		$watermarkHeight=imagesy($filter);
		$watermarkPosX=0;
		$watermarkPosY=0;

		if (is_numeric($positionX)){
			if ($this->image_watermark_x < 0){
				$watermarkPosX=$this->workWidth - $watermarkWidth + $positionX;
			} else {
				$watermarkPosX=$positionX;
			}
		} else {
			if (strpos($position, 'r') !== false){
				$watermarkPosX=$this->workWidth - $watermarkWidth;
			} else if (strpos($position, 'l') !== false){
				$watermarkPosX=0;
			} else {
				$watermarkPosX=($this->workWidth - $watermarkWidth) / 2;
			}
		}

		if (is_numeric($positionY)){
			if ($this->image_watermark_y < 0){
				$watermarkPosY=$this->workHeight - $watermarkHeight + $positionY;
			} else {
				$watermarkPosY=$positionY;
			}
		} else {
			if (strpos($position, 'b') !== false){
				$watermarkPosY=$this->workHeight - $watermarkHeight;
			} else if (strpos($position, 't') !== false){
				$watermarkPosY=0;
			} else {
				$watermarkPosY=($this->workHeight - $watermarkHeight) / 2;
			}
		}
		imagecopyresampled($this->imageRes, $filter, $watermarkPosX, $watermarkPosY, 0, 0, $watermarkWidth, $watermarkHeight, $watermarkWidth, $watermarkHeight);
	}

	/**
	 * Read the image file and load to the resource data
	 * @param type $filename
	 * @return boolean
	 */
	function read($filename=''){

		if ($filename <> ''){
			$this->fileName=$filename;
		};

		$this->filePath=$this->dir.$this->fileName;
		$this->thumbnailName='th_'.$this->fileName;
		$this->fileNameWithoutExt=preg_replace("/(.*)\.([^.]+)$/", "\\1", $this->fileName);
		$this->fileExt=preg_replace("/.*\.([^.]+)$/", "\\1", $this->fileName);

		switch ($this->fileExt){
			case 'gif':
				$this->imageRes=@imagecreatefromgif($this->filePath);
				break;
			case 'png':
				$this->imageRes=@imagecreatefrompng($this->filePath);
				break;
			case 'jpg':
			case 'jpeg':
				$this->imageRes=@imagecreatefromjpeg($this->filePath);
				break;
			default:
				throw new UnexpectedValueException('Not support file type'.$this->fileExt);
				break;
		}

		$this->workWidth=@imagesx($this->imageRes);
		$this->workHeight=@imagesy($this->imageRes);
		if ($this->workWidth == 0 AND $this->workHeight == 0) return false;
		return true;
	}

	/**
	 * If file correct load return a true value
	 * @return boolean
	 */
	function isLoad(){
		if ($this->workWidth == NULL OR $this->workHeight == NULL){
			return false;
		}
		return true;
	}

	/**
	 * Write the resource image to the file
	 * @param type $filename
	 * @param type $thumbals
	 * @param type $kopia
	 */
	function write($filename='', $thumbals=false, $kopia=false){

		if ($filename <> ''){
			$this->fileName=$filename;
			$this->filePath=$this->dir.$this->fileName;
			$this->thumbnailName='th_'.$this->fileName;
			$this->fileNameWithoutExt=preg_replace("/(.*)\.([^.]+)$/", "\\1", $this->fileName);
			$this->fileExt=preg_replace("/.*\.([^.]+)$/", "\\1", $this->fileName);
		};

		$fullpatch=$this->filePath;

		if ($thumbals == true) $fullpatch=$this->dir.'th_'.$this->fileName;
		if ($kopia == true) $fullpatch=$this->dir.$this->fileNameWithoutExt.'_2'.$this->fileExt;
		switch ($this->fileExt){
			case 'gif':
				imagegif($this->imageRes, $fullpatch);
				break;
			case 'png':
				imagepng($this->imageRes, $fullpatch);
				break;
			case 'jpeg':
			case 'jpg':
				imagejpeg($this->imageRes, $fullpatch, $this->jpegQuality);
				break;
			default:
				throw new UnexpectedValueException('Not support file type'.$this->fileExt);
				break;
		}
	}

	/**
	 * Create thumbnail of the image resource, default thumbnail filename is same with the image name plus "th_" prefix
	 * @param type $sizeX
	 * @param type $sizeY
	 * @param type $scaleType
	 */
	function createThumbnail($sizeX='', $sizeY='', $scaleType=''){

		if ($sizeX == ''){
			$sizeX=$this->thumbnailWidth;
		};
		if ($sizeY == ''){
			$sizeY=$this->thumbnailHeight;
		};
		if ($scaleType == ''){
			$scaleType=$this->thumbnailScaleType;
		};

		$coefficientY=$sizeY / $this->workHeight;
		$coefficientX=$sizeX / $this->workWidth;
		$aspect=$coefficientX / $coefficientY;

		switch ($scaleType){
			default:
				break;
			case PhpPainter::SCALE_AUTO:
				if ($this->workWidth > $this->workHeight){
					# X
					$coefficientY=$coefficientX;
				} else {
					# Y
					$coefficientX=$coefficientY;
				};
				break;
			case PhpPainter::SCALE_HEIGHT:
				$coefficientX=$coefficientY;
				break;
			case PhpPainter::SCALE_WIDTH:
				$coefficientY=$coefficientX;
				break;
		};

		$destWidth=(int) ($this->workWidth * $coefficientX);
		$destHeight=(int) ($this->workHeight * $coefficientY);

		if ('gd2' == $this->checkGdVersion()){
			$imgThRes=imagecreatetruecolor($destWidth, $destHeight);
			imagecopyresampled($imgThRes, $this->imageRes, 0, 0, 0, 0, $destWidth + 1, $destHeight + 1, $this->workWidth, $this->workHeight);
		} else {
			$imgThRes=imagecreate($destWidth, $destHeight);
			imagecopyresized($imgThRes, $this->imageRes, 0, 0, 0, 0, $destWidth + 1, $destHeight + 1, $this->workWidth, $this->workHeight);
		}
		switch ($this->fileExt){
			case 'gif':
				imagegif($imgThRes, $this->dir.$this->thumbnailName);
				break;
			case 'png':
				imagepng($imgThRes, $this->dir.$this->thumbnailName);
				break;
			case 'jpg':
			case 'jpeg':
				imagejpeg($imgThRes, $this->dir.$this->thumbnailName, $this->jpegQuality);
				break;
		};

		imagedestroy($imgThRes);
	}

	/**
	 * Checking GdVersion
	 * @return string
	 */
	protected function checkGdVersion(){
		$gd_content=get_extension_funcs('gd'); // Grab function list
		if (!$gd_content){
			throw new UnexpectedValueException('Can\'t find GD library');
		} else {
			ob_start();
			phpinfo(8);
			$buffer=ob_get_contents();
			ob_end_clean();

			if (strpos($buffer, '2.0')){
				return 'gd2';
			} else {
				return 'gd';
			}
		}
	}

}

?>