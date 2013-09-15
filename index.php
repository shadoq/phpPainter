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

include('phpPainter.php');
$painter=new PhpPainter();

if (empty($_GET['img'])){
	$_GET['img']=1;
}
$_GET['img']=$_GET['img'] * 1;

?>

<!DOCTYPE html>
<html>
	<head>
		<title>phpPainter</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		
		<style>
			html, body, div, span, object, iframe,
			h1, h2, h3, h4, h5, h6, p, blockquote, pre,
			abbr, address, cite, code,
			del, dfn, em, img, ins, kbd, q, samp,
			small, strong, sub, sup, var,
			b, i,
			dl, dt, dd, ol, 
			fieldset, form, label, legend,
			table, caption, tbody, tfoot, thead, tr, th, td,
			article, aside, canvas, details, figcaption, figure, 
			footer, header, hgroup, menu, nav, section, summary,
			time, mark, audio, video
			{
				margin: 0;
				padding: 0;
				font-size: 100.00%;
				text-align: inherit;
				border: 0;
				outline: 0;
				background: transparent;
			}

			body {
				line-height: 120%;
				font-family: Tahoma, Arial, Helvetica, san-serif;
				background: #FFFFFF;
				color: #333333;
				font-size: 12px;
				margin: 8px;
				margin-top: 0px;
				text-align: left;
			}
			img
			{
				border: 0;
				margin: auto;
			}

			h1, h2, h3, h4, h5, h6, p, pre, blockquote, table, ol, ul, fieldset, form, menu, dir
			{
				margin-bottom: 0.2em
			}

			h1, h2, h3, h4, h5, h6 
			{
				font-size: 100%;
				font-weight: normal;
				margin-top: 1em;
			}			
			h1 
			{
				font-size: 22px;
			}
			h2 
			{
				font-size: 20px;
			}
			h3 
			{
				font-size: 18px;
			}
			h4
			{
				font-size: 16px;
			}
			h5 
			{
				font-size: 14px;
			}			
		</style>
		
	</head>
	<body>

		<h1 style="text-align: center;">PhpPainter</h1>
		<div>
			<a href="index.php?sample=image&img=<?php echo $_GET['img'] ?>">Image color process</a> | 
			<a href="index.php?sample=scale&img=<?php echo $_GET['img'] ?>">Scale and flip</a> | 
			<a href="index.php?sample=edge&img=<?php echo $_GET['img'] ?>">Edge Detec</a> | 
			<a href="index.php?sample=filter&img=<?php echo $_GET['img'] ?>">Image filter</a> | 
			<a href="index.php?sample=blur&img=<?php echo $_GET['img'] ?>">Blur and sharpen filter</a>
		</div>
		<div style="padding: 20px;">
			<a href="index.php?img=1&sample=<?php echo strip_tags($_GET['sample']) ?>"><img src="test_image/1.jpg" width="128" height="96"></a>
			<a href="index.php?img=2&sample=<?php echo strip_tags($_GET['sample']) ?>"><img src="test_image/2.jpg" width="128" height="96"></a>
			<a href="index.php?img=3&sample=<?php echo strip_tags($_GET['sample']) ?>"><img src="test_image/3.jpg" width="128" height="96"></a>
		</div>
		
		<div style="text-align: center; float: left; padding: 5px;">
			<h3>Original image</h3>
			<img src="test_image/<?php echo $_GET['img'].'.jpg' ?>" />
		</div>

		<?php
		switch ($_GET['sample']){
			default :
				?>

				<h2 style="text-align: center; clear: both;">Image color process</h2>
				<?php
				//
				// Auto Level
				//
				$painter->read('test_image/'.$_GET['img'].'.jpg');
				$painter->autoLevel();
				$painter->write('temp/auto_level.jpg');
				?>
				<div style="text-align: center; float: left;  padding: 5px;">
					<h3>Auto Level</h3>
					<img src="temp/auto_level.jpg">
				</div>


				<?php
				//
				// Greyscale
				//
				$painter->read('test_image/'.$_GET['img'].'.jpg');
				$painter->greyscale();
				$painter->write('temp/greyscale.jpg');
				?>
				<div style="text-align: center; float: left;  padding: 5px;">
					<h3>Greyscale</h3>
					<img src="temp/greyscale.jpg">
				</div>

				<?php
				//
				// Invert
				//
				$painter->read('test_image/'.$_GET['img'].'.jpg');
				$painter->invert();
				$painter->write('temp/invert.jpg');
				?>
				<div style="text-align: center; float: left;  padding: 5px;">
					<h3>Invert</h3>
					<img src="temp/invert.jpg">
				</div>

				<?php
				//
				// Overlay Color
				//
					for ($i=0; $i < 101; $i=$i + 20){
					$painter->read('test_image/'.$_GET['img'].'.jpg');
					$painter->overlayColor($i);
					$painter->write('temp/overlay_color'.$i.'.jpg');
					?>
					<div style="text-align: center; float: left;  padding: 5px;">
						<h3>Overlay Color <?php echo $i ?>%</h3>
						<img src="temp/overlay_color<?php echo $i ?>.jpg">
					</div>
					<?php
				};
				?>


				<?php
				//
				// Brightness
				//
					for ($i=-100; $i < 101; $i=$i + 40){
					$painter->read('test_image/'.$_GET['img'].'.jpg');
					$painter->brightness($i);
					$painter->write('temp/brightness'.$i.'.jpg');
					?>
					<div style="text-align: center; float: left;  padding: 5px;">
						<h3>Brightness <?php echo $i ?></h3>
						<img src="temp/brightness<?php echo $i ?>.jpg">
					</div>
					<?php
				};
				?>


				<?php
				//
				// Contrast
				//
					for ($i=-100; $i < 101; $i=$i + 40){
					$painter->read('test_image/'.$_GET['img'].'.jpg');
					$painter->contrast($i);
					$painter->write('temp/contrast'.$i.'.jpg');
					?>
					<div style="text-align: center; float: left;  padding: 5px;">
						<h3>Contrast <?php echo $i ?></h3>
						<img src="temp/contrast<?php echo $i ?>.jpg">
					</div>
					<?php
				};
				?>


				<?php
				//
				// Colorize RED
				//
					for ($i=0; $i < 255; $i=$i + 50){
					$painter->read('test_image/'.$_GET['img'].'.jpg');
					$painter->colorize($i, 0, 0);
					$painter->write('temp/colorize_r'.$i.'.jpg');
					?>
					<div style="text-align: center; float: left;  padding: 5px;">
						<h3>Colorize R: <?php echo $i ?> G: 0 B: 0</h3>
						<img src="temp/colorize_r<?php echo $i ?>.jpg">
					</div>
					<?php
				};
				?>

				<?php
				//
				// Colorize GREEN
				//
					for ($i=0; $i < 255; $i=$i + 50){
					$painter->read('test_image/'.$_GET['img'].'.jpg');
					$painter->colorize(0, $i, 0);
					$painter->write('temp/colorize_g'.$i.'.jpg');
					?>
					<div style="text-align: center; float: left;  padding: 5px;">
						<h3>Colorize R: 0 G: <?php echo $i ?> B: 0</h3>
						<img src="temp/colorize_g<?php echo $i ?>.jpg">
					</div>
					<?php
				};
				?>

				<?php
				//
				// Colorize BLUE
				//
							for ($i=0; $i < 255; $i=$i + 50){
					$painter->read('test_image/'.$_GET['img'].'.jpg');
					$painter->colorize(0, 0, $i);
					$painter->write('temp/colorize_b'.$i.'.jpg');
					?>
					<div style="text-align: center; float: left;  padding: 5px;">
						<h3>Colorize R: 0 G: B: <?php echo $i ?> </h3>
						<img src="temp/colorize_b<?php echo $i ?>.jpg">
					</div>
					<?php
				};
				?>

				<?php
				break;


			case 'scale':
				?>
				<h2 style="text-align: center; clear: both;">Scale and flip</h2>
				<?php
				//
				// Resample Image
				//
				$painter->read('test_image/'.$_GET['img'].'.jpg');
				$painter->resampleImage(320, 200, PhpPainter::SCALE_WIDTH);
				$painter->write('temp/rescale_0.jpg');
				?>
				<div style="text-align: center; float: left;  padding: 5px;">
					<h3>Resample Image Mode: SCALE_WIDTH</h3>
					<img src="temp/rescale_0.jpg">
				</div>

				<?php
				$painter->read('test_image/'.$_GET['img'].'.jpg');
				$painter->resampleImage(320, 200, PhpPainter::SCALE_HEIGHT);
				$painter->write('temp/rescale_1.jpg');
				?>
				<div style="text-align: center; float: left;  padding: 5px;">
					<h3>Resample Image Mode: SCALE_HEIGHT</h3>
					<img src="temp/rescale_1.jpg">
				</div>

				<?php
				$painter->read('test_image/'.$_GET['img'].'.jpg');
				$painter->resampleImage(320, 200, PhpPainter::SCALE_AUTO);
				$painter->write('temp/rescale_2.jpg');
				?>
				<div style="text-align: center; float: left;  padding: 5px;">
					<h3>Resample Image Mode: SCALE_AUTO</h3>
					<img src="temp/rescale_2.jpg">
				</div>

				<?php
				$painter->read('test_image/'.$_GET['img'].'.jpg');
				$painter->resampleImage(320, 200, PhpPainter::SCALE_NO_CHANGE);
				$painter->write('temp/rescale_3.jpg');
				?>
				<div style="text-align: center; float: left;  padding: 5px;">
					<h3>Resample Image Mode: SCALE_NO_CHANGE</h3>
					<img src="temp/rescale_3.jpg">
				</div>

				<?php
				//
				// Flip Vertical
				//
				$painter->read('test_image/'.$_GET['img'].'.jpg');
				$painter->flipVertical();
				$painter->write('temp/flipv.jpg');
				?>
				<div style="clear: both"></div>
				<div style="text-align: center; float: left;  padding: 5px;">
					<h3>Flip Vertical</h3>
					<img src="temp/flipv.jpg">
				</div>


				<?php
				//
				// Flip Horizontal
				//
				$painter->read('test_image/'.$_GET['img'].'.jpg');
				$painter->flipHorizontal();
				$painter->write('temp/fliph.jpg');
				?>
				<div style="text-align: center; float: left;  padding: 5px;">
					<h3>Flip Horizontal</h3>
					<img src="temp/fliph.jpg">
				</div>

				<?php
				//
				// Rotate Image
				//
						$painter->read('test_image/'.$_GET['img'].'.jpg');
				$painter->rotate(90);
				$painter->write('temp/rotate90.jpg');
				?>
						<div style="text-align: center; float: left;  padding: 5px;">
							<h3>Rotate 90 degrees</h3>
							<img src="temp/rotate90.jpg">
						</div>

				<?php
				$painter->read('test_image/'.$_GET['img'].'.jpg');
				$painter->rotate(180);
				$painter->write('temp/rotate180.jpg');
				?>
						<div style="text-align: center; float: left;  padding: 5px;">
							<h3>Rotate 180 degrees</h3>
							<img src="temp/rotate180.jpg">
						</div>

				<?php
				$painter->read('test_image/'.$_GET['img'].'.jpg');
				$painter->rotate(270);
				$painter->write('temp/rotate270.jpg');
				?>
						<div style="text-align: center; float: left;  padding: 5px;">
							<h3>Rotate 270 degrees</h3>
							<img src="temp/rotate270.jpg">
						</div>
				<?php
				break;

			case 'edge':
				
				?>
				<h2 style="text-align: center; clear: both;">Edge Detec</h2>
				
				<?php
				//
				// Treshold
				//
				$painter->read('test_image/'.$_GET['img'].'.jpg');
				$painter->treshold();
				$painter->write('temp/treshold.jpg');
				?>
				<div style="text-align: center; float: left;  padding: 5px;">
					<h3>Treshold</h3>
					<img src="temp/treshold.jpg">
				</div>
				
				<?php
				//
				// Edge
				//
				$painter->read('test_image/'.$_GET['img'].'.jpg');
				$painter->edge();
				$painter->write('temp/edge.jpg');
				?>
				<div style="text-align: center; float: left;  padding: 5px;">
					<h3>Edge</h3>
					<img src="temp/edge.jpg">
				</div>

				<?php
				//
				// Emboss
				//
				$painter->read('test_image/'.$_GET['img'].'.jpg');
				$painter->emboss();
				$painter->write('temp/emboss.jpg');
				?>
				<div style="text-align: center; float: left;  padding: 5px;">
					<h3>Emboss</h3>
					<img src="temp/emboss.jpg">
				</div>

				<?php
				//
				// Edge Emboss
				//
				$painter->read('test_image/'.$_GET['img'].'.jpg');
				$painter->eEdge();
				$painter->write('temp/eedge.jpg');
				?>
				<div style="text-align: center; float: left;  padding: 5px;">
					<h3>Edge Emboss</h3>
					<img src="temp/eedge.jpg">
				</div>
				
				
				<?php
				//
				// Left Edge
				//
				$painter->read('test_image/'.$_GET['img'].'.jpg');
				$painter->edgeLeft();
				$painter->write('temp/edgel.jpg');
				?>
				<div style="text-align: center; float: left;  padding: 5px;">
					<h3>Left Edge</h3>
					<img src="temp/edgel.jpg">
				</div>
				

				<?php
				//
				// Right Edge
				//
				$painter->read('test_image/'.$_GET['img'].'.jpg');
				$painter->edgeRight();
				$painter->write('temp/edger.jpg');
				?>
				<div style="text-align: center; float: left;  padding: 5px;">
					<h3>Right Edge</h3>
					<img src="temp/edger.jpg">
				</div>

				<?php
				//
				// Top Edge
				//
				$painter->read('test_image/'.$_GET['img'].'.jpg');
				$painter->edgeTop();
				$painter->write('temp/edget.jpg');
				?>
				<div style="text-align: center; float: left;  padding: 5px;">
					<h3>Top Edge</h3>
					<img src="temp/edget.jpg">
				</div>

				<?php
				//
				// Bottom Edge
				//
				$painter->read('test_image/'.$_GET['img'].'.jpg');
				$painter->edgeBottom();
				$painter->write('temp/edgeb.jpg');
				?>
				<div style="text-align: center; float: left;  padding: 5px;">
					<h3>Bottom Edge</h3>
					<img src="temp/edgeb.jpg">
				</div>

				
				<?php
				//
				// Top Left Edge
				//
				$painter->read('test_image/'.$_GET['img'].'.jpg');
				$painter->edgeTopLeft();
				$painter->write('temp/edgetl.jpg');
				?>
				<div style="text-align: center; float: left;  padding: 5px;">
					<h3>Top Left Edge</h3>
					<img src="temp/edgetl.jpg">
				</div>

				<?php
				//
				// Bottom Left Edge
				//
				$painter->read('test_image/'.$_GET['img'].'.jpg');
				$painter->edgeBottomLeft();
				$painter->write('temp/edgebl.jpg');
				?>
				<div style="text-align: center; float: left;  padding: 5px;">
					<h3>Bottom Left Edge</h3>
					<img src="temp/edgebl.jpg">
				</div>

				
				<?php
				//
				// Top Right Edge
				//
				$painter->read('test_image/'.$_GET['img'].'.jpg');
				$painter->edgeTopRight();
				$painter->write('temp/edgetr.jpg');
				?>
				<div style="text-align: center; float: left;  padding: 5px;">
					<h3>Top Right Edge</h3>
					<img src="temp/edgetr.jpg">
				</div>

				<?php
				//
				// Bottom Right Edge
				//
				$painter->read('test_image/'.$_GET['img'].'.jpg');
				$painter->edgeBottomRight();
				$painter->write('temp/edgebr.jpg');
				?>
				<div style="text-align: center; float: left;  padding: 5px;">
					<h3>Bottom Right Edge</h3>
					<img src="temp/edgebr.jpg">
				</div>
				
		<?php				
				break;
			case 'filter':
				
				?>
				<h2 style="text-align: center; clear: both;">Image filter</h2>
				
				<?php
				//
				// Strip line
				//
				$painter->read('test_image/'.$_GET['img'].'.jpg');
				$painter->stripLine();
				$painter->write('temp/stripline.jpg');
				?>
				<div style="text-align: center; float: left;  padding: 5px;">
					<h3>Strip line (2 px)</h3>
					<img src="temp/stripline.jpg">
				</div>
				
				<?php
				//
				// Strip line
				//
				$painter->read('test_image/'.$_GET['img'].'.jpg');
				$painter->stripLine(4);
				$painter->write('temp/stripline4.jpg');
				?>
				<div style="text-align: center; float: left;  padding: 5px;">
					<h3>Strip line (4 px)</h3>
					<img src="temp/stripline4.jpg">
				</div>
				
				<?php
				//
				// Strip line
				//
				$painter->read('test_image/'.$_GET['img'].'.jpg');
				$painter->screen();
				$painter->write('temp/screen.jpg');
				?>
				<div style="text-align: center; float: left;  padding: 5px;">
					<h3>Screen (2 px)</h3>
					<img src="temp/screen.jpg">
				</div>
				
				<?php
				//
				// Strip line
				//
				$painter->read('test_image/'.$_GET['img'].'.jpg');
				$painter->screen(4);
				$painter->write('temp/screen4.jpg');
				?>
				<div style="text-align: center; float: left;  padding: 5px;">
					<h3>Screen (4 px)</h3>
					<img src="temp/screen4.jpg">
				</div>
				
				<?php
				//
				// Noise
				//
				$painter->read('test_image/'.$_GET['img'].'.jpg');
				$painter->noise();
				$painter->write('temp/noise.jpg');
				?>
				<div style="text-align: center; float: left;  padding: 5px;">
					<h3>Noise level: 64</h3>
					<img src="temp/noise.jpg">
				</div>
				

				<?php
				//
				// Noise
				//
				$painter->read('test_image/'.$_GET['img'].'.jpg');
				$painter->noise(128);
				$painter->write('temp/noise128.jpg');
				?>
				<div style="text-align: center; float: left;  padding: 5px;">
					<h3>Noise level: 128</h3>
					<img src="temp/noise128.jpg">
				</div>


				<?php
				//
				// Noise HSB
				//
				$painter->read('test_image/'.$_GET['img'].'.jpg');
				$painter->noiseHSB();
				$painter->write('temp/noisehsb.jpg');
				?>
				<div style="text-align: center; float: left;  padding: 5px;">
					<h3>Noise HSB level: 64</h3>
					<img src="temp/noisehsb.jpg">
				</div>
				

				<?php
				//
				// Noise HSB
				//
				$painter->read('test_image/'.$_GET['img'].'.jpg');
				$painter->noiseHSB(128);
				$painter->write('temp/noisehsb128.jpg');
				?>
				<div style="text-align: center; float: left;  padding: 5px;">
					<h3>Noise HSB level: 128</h3>
					<img src="temp/noisehsb128.jpg">
				</div>
				
				
				<?php
				//
				// Posterize
				//
				$painter->read('test_image/'.$_GET['img'].'.jpg');
				$painter->posterize();
				$painter->write('temp/posterize.jpg');
				?>
				<div style="text-align: center; float: left;  padding: 5px;">
					<h3>Posterize level: 64</h3>
					<img src="temp/posterize.jpg">
				</div>
				

				<?php
				//
				// Posterize 
				//
				$painter->read('test_image/'.$_GET['img'].'.jpg');
				$painter->posterize(32);
				$painter->write('temp/posterize32.jpg');
				?>
				<div style="text-align: center; float: left;  padding: 5px;">
					<h3>Posterize level: 32</h3>
					<img src="temp/posterize32.jpg">
				</div>
				
				
		<?php				
				break;
			case 'blur':
				?>
				<h2 style="text-align: center; clear: both;">Blur and sharpen filter</h2>
				
				<?php
				//
				// Gaussian blur
				//
				for ($i=-0; $i < 51; $i=$i + 10){
					$painter->read('test_image/'.$_GET['img'].'.jpg');
					$painter->gaussianBlur($i);
					$painter->write('temp/gaussian_blur'.$i.'.jpg');
					?>
					<div style="text-align: center; float: left;  padding: 5px;">
						<h3>Gaussian blur level: <?php echo $i ?></h3>
						<img src="temp/gaussian_blur<?php echo $i ?>.jpg">
					</div>
					<?php
				};
				?>

				<?php
				//
				// Blur
				//
				for ($j=1; $j < 7; $j+=3){
					for ($i=-6; $i < 8; $i+=2){
						$painter->read('test_image/'.$_GET['img'].'.jpg');
						$painter->blur($i, 1);
						$painter->write('temp/blur'.$i.$j.'.jpg');
						?>
						<div style="text-align: center; float: left;  padding: 5px;">
							<h3>Blur size: <?php echo $i ?> level: <?php echo $j ?></h3>
							<img src="temp/blur<?php echo $i.$j ?>.jpg">
						</div>
						<?php
					};
				};
				?>

				<?php
				//
				// Unsharp
				//
				for ($i=-0; $i < 6; $i++){
					$painter->read('test_image/'.$_GET['img'].'.jpg');
					$painter->unsharp($i);
					$painter->write('temp/unsharp'.$i.'.jpg');
					?>
					<div style="text-align: center; float: left;  padding: 5px;">
						<h3>Unsharp level: <?php echo $i ?></h3>
						<img src="temp/unsharp<?php echo $i ?>.jpg">
					</div>
					<?php
				};
				?>
				
				
		<?php
				break;
		}
?>
	</body>
</html>
