<?php
//
// config
//
$img_sz_type   = 0; 
$img_sz_width  = 115; 
$img_sz_height = 25; 

$im_bg      = 0; 
$im_bg_type = 1; 
$im_bg_url  = 'captchabg/1.gif'; 

$fontUsed  = 0; 
$font_url  = 'captchafonts/font4.ttf';   // 1,16,31
$fonts_dir = 'captchafonts';

$min_font_size = 13; $max_font_size = 17;

$min_angle = -13; $max_angle = 13;

$col_txt_type = 4;  $col_txt_r = 206; $col_txt_g = 227; $col_txt_b = 253; 

$char_padding = 2;

# $output_type='jpeg';
$output_type='png';

//
// dont't change anything below this comment
//
session_start();

$no     = ($_REQUEST['ts']<>'')?$_REQUEST['ts']:'';
$turing = $_SESSION['turing_string_'.$no];

if ($fontUsed == 1 ) {
	$fontno = mt_rand(1,34);
	$font = $fonts_dir . '/font' . $fontno . '.ttf';
	}
	else $font = $font_url;
                 
/* initialize variables */

$length = strlen($turing);
$data = array();
$image_width = $image_height = 0;


/* build the data array of the characters, size, placement, etc. */

for($i=0; $i<$length; $i++) {

  $char = substr($turing, $i, 1);

  $size = mt_rand($min_font_size, $max_font_size);
  $angle = mt_rand($min_angle, $max_angle);

  $bbox = ImageTTFBBox( $size, $angle, $font, $char );

  $char_width = max($bbox[2], $bbox[4]) - min($bbox[0], $bbox[6]);
  $char_height = max($bbox[1], $bbox[3]) - min($bbox[7], $bbox[5]);

  $image_width += $char_width + $char_padding;
  $image_height = max($image_height, $char_height);

  $data[] = array(
    'char'    => $char,
    'size'    => $size,
    'angle'    => $angle,
    'height'  => $char_height,
    'width'    => $char_width,
  );
}

/* calculate the final image size, adding some padding */

$x_padding = 12;

if ( $img_sz_type == 1 )
	{
	$image_width += ($x_padding * 2);
	$image_height = ($image_height * 1.5) + 2;
	}
   else {
	$image_width = $img_sz_width;
	$image_height = $img_sz_height;
	}

/* build the image, and allocte the colors */

$im = ImageCreate($image_width, $image_height);
$cs = mt_rand(1,3);

$d1 = $d2 = $d3 = 0;
while ( ($d1<50) AND ($d2<50) AND ($d3<50) )
	{
	$r = mt_rand(200,255);	$g = mt_rand(200,255);	$b = mt_rand(200,255);
	$d1 = abs($r-$g);	$d2 = abs($r-$b);	$d3 = abs($g-$b);
	}

$color_bg       = ImageColorAllocate($im, $r, $g, $b );
$color_border   = ImageColorAllocate($im, round($r/2), round($g/2), round($b/2));
$color_line0    = ImageColorAllocate($im, round($r*0.85), round($g*0.85), round($b*0.85) );
$color_elipse0  = ImageColorAllocate($im, round($r*0.95), round($g*0.95), round($b*0.95) );
$color_elipse1  = ImageColorAllocate($im, round($r*0.90), round($g*0.90), round($b*0.90) );

$d1 = mt_rand(0,50); $d2 = mt_rand(0,50); $d3 = mt_rand(0,50);

$color_line1  = ImageColorAllocate($im, $r-$d1, $g-$d2, $b-$d3 );

$d1 = $d2 = $d3 = 0;
while ( ($d1<100) AND ($d2<100) AND ($d3<100) )
	{
	$r = mt_rand(0,150); $g = mt_rand(0,150); $b = mt_rand(0,150);
	$d1 = abs($r-$g); $d2 = abs($r-$b); $d3 = abs($g-$b);
	}

switch ( $col_txt_type ) 
	{
	case 1 : $col_txt    = ImageColorAllocate($im, $r, $g, $b ); break;
	case 2 : $col_txt    = ImageColorAllocate($im, 0, 0, 0 ); break;
	case 3 : $col_txt    = ImageColorAllocate($im, 255, 255, 255 );	break;
	case 4 : $col_txt    = ImageColorAllocate($im, $col_txt_r, $col_txt_g, $col_txt_b ); break;
	}

$noiset = mt_rand(1,2);

if ( $im_bg == 1 )
	{
		switch ($noiset) {
			case '1' :
					/* make the random background elipses */
				for($l=0; $l<10; $l++) {
			
			  		$c = 'color_elipse' . ($l%2);
			
					$cx = mt_rand(0, $image_width);
			  		$cy = mt_rand(0, $image_width);
			  		$rx = mt_rand(10, $image_width);
			  		$ry = mt_rand(10, $image_width);
			
					ImageFilledEllipse($im, $cx, $cy, $rx, $ry, $$c );
			  		}; break;
			case '2' :
					for($l=0; $l<10; $l++) {
			
							$c = 'color_line' . ($l%2);
					
					 	  	$lx = mt_rand(0, $image_width+$image_height);
					  		$lw = mt_rand(0,3);
					  		if ($lx > $image_width) {
					    		  $lx -= $image_width;
					    		  ImageFilledRectangle($im, 0, $lx, $image_width-1, $lx+$lw, $$c );
					  		   } else ImageFilledRectangle($im, $lx, 0, $lx+$lw, $image_height-1, $$c );
			  		}; break;
			} 
	}

if ( $im_bg == 0 )
	{
	  	$image_data=getimagesize($im_bg_url);
	
	  	$image_type=$image_data[2];
	
	  	if($image_type==1) $img_src=imagecreatefromgif($im_bg_url);
	  	elseif($image_type==2) $img_src=imagecreatefromjpeg($im_bg_url);
	  	elseif($image_type==3) $img_src=imagecreatefrompng($im_bg_url);
	
			if ( $im_bg_type == 1 ) {
						  imagesettile($im,$img_src);
						  imagefill($im,0,0,IMG_COLOR_TILED);
						}
			else imagecopyresampled($im,$img_src,0,0,0,0,$image_width,$image_height,$image_data[0],$image_data[1]);
	
	}

$pos_x = $x_padding + ($char_padding / 2);
foreach($data as $d) {

	  $pos_y = ( ( $image_height + $d['height'] ) / 2 );
	  ImageTTFText($im, $d['size'], $d['angle'], $pos_x, $pos_y, $col_txt, $font, $d['char'] );
	
	  $pos_x += $d['width'] + $char_padding;

}


/* a nice border */
ImageRectangle($im, 0, 0, $image_width-1, $image_height-1, $color_border);

	/* display it */
  
	switch ($output_type) {
			 case 'jpeg':
					  Header('Content-type: image/jpeg');
					  ImageJPEG($im,NULL,100);
					  break;
			
			 case 'png':
			 default:
					  Header('Content-type: image/png');
					  ImagePNG($im);
					  break;
	 }

ImageDestroy($im);

session_write_close();
?>
