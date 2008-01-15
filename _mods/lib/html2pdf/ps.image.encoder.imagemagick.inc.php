<?php

class PSImageEncoderImageMagick {
  var $last_image_id;

  function PSImageEncoderImageMagick() {
    $this->last_image_id = 0;
  }

  // Generates new unique image identifier
  // 
  // @return generated identifier
  //
  function generate_id() {
    $this->last_image_id ++;
    return $this->last_image_id;
  }

  // Encodes image, automatically detecting if this image is transparent or not
  // and calling the appropriate function
  //
  // @param $psdata (in) PS date file wrapper
  // @param $src_img (in) PHP image resource to be processed
  // @param $size_x (out) actual horizontal size of image (pixels)
  // @param $size_y (out) actual vertical size of image (pixels)
  // @param $tcolor (out) 
  // @param $timage (out) name of postscript image data source 
  // @param $tmask (out) name of postscipt image mask data source
  function auto($psdata, $src_img, &$size_x, &$size_y, &$tcolor, &$image, &$mask) {
    if (imagecolortransparent($src_img) == -1) {
      $id = $this->solid($psdata, $src_img, $size_x, $size_y, $image, $mask);
      $tcolor = 0;
      return $id;
    } else {
      $id = $this->transparent($psdata, $src_img, $size_x, $size_y, $image, $mask);
      $tcolor = 1;
      return $id;
    };
  }

  // Encodes "solid" image without any transparent parts
  // 
  // @param $psdata (in) Postscript file "writer" object
  // @param $src_img (in) PHP image resource
  // @param $size_x (out) size of image in pixels
  // @param $size_y (out) size of image in pixels
  // @returns identifier if encoded image to use in postscript file
  // 
  function solid(&$psdata, $src_img, &$size_x, &$size_y, &$image, &$mask) {
    // Generate an unique image id
    $id = $this->generate_id();

    // Generate the unique temporary file name for this image; 
    // we'll use it for imagemagick temporary files
    $tempfile = $psdata->mk_filename();

    // Save image as PNG for further processing
    imagepng($src_img, $tempfile.'.png');

    // Call image magick - convert to raw RGB samples (binary)
    safe_exec('"'.IMAGE_MAGICK_CONVERT_EXECUTABLE.'"'." ${tempfile}.png ${tempfile}.rgb", $output);

    // read raw rgb samples
    $samples = file_get_contents($tempfile.'.rgb');

    // Determine image size 
    $size_x      = imagesx($src_img); 
    $size_y      = imagesy($src_img);

    // write stread header to the postscript file
    $psdata->write("/image-{$id}-init { image-{$id}-data 0 setfileposition } def\n");
    $psdata->write("/image-{$id}-data currentfile << /Filter /ASCIIHexDecode >> /ReusableStreamDecode filter\n");

    // initialize line length counter
    $ctr = 0;
    
    for ($i = 0; $i < strlen($samples); $i += 3) {
      // Save image pixel to the stream data
      $r = ord($samples{$i});
      $g = ord($samples{$i+1});
      $b = ord($samples{$i+2});
      $psdata->write(sprintf("%02X%02X%02X\n",$r,$g,$b));

      // Increate the line length counter; check if stream line needs to be terminated
      $ctr += 6;
      if ($ctr > MAX_LINE_LENGTH) { 
        $psdata->write("\n");
        $ctr = 0;
      };
    };

    // terminate the stream data
    $psdata->write(">\ndef\n");

    // return image and mask data references
    $image = "image-{$id}-data";
    $mask  = "";

    // Delete temporary files 
    unlink($tempfile.'.png');
    unlink($tempfile.'.rgb');

    return $id;
  }

  // Encodes image containing 100% transparent color (1-bit alpha channel)
  // 
  // @param $psdata (in) Postscript file "writer" object
  // @param $src_img (in) PHP image resource
  // @param $size_x (out) size of image in pixels
  // @param $size_y (out) size of image in pixels
  // @returns identifier if encoded image to use in postscript file
  // 
  function transparent($psdata, $src_img, &$size_x, &$size_y, &$image, &$mask) {
    // Generate an unique image id
    $id = $this->generate_id();

    // Generate the unique temporary file name for this image; 
    // we'll use it for imagemagick temporary files
    $tempfile = $psdata->mk_filename();

    // Save image as PNG for further processing
    imagepng($src_img, $tempfile.'.png');

    // Call image magick - convert to raw RGBA samples (binary)
    safe_exec('"'.IMAGE_MAGICK_CONVERT_EXECUTABLE.'"'." ${tempfile}.png ${tempfile}.rgba", $output);

    // read raw RGBA samples
    $samples = file_get_contents($tempfile.'.rgba');

    // Determine image size and create a truecolor copy of this image 
    // (as we don't want to work with palette-based images manually)
    $size_x      = imagesx($src_img); 
    $size_y      = imagesy($src_img);
    
    // write stream header to the postscript file
    $psdata->write("/image-{$id}-init { image-{$id}-data 0 setfileposition mask-{$id}-data 0 setfileposition } def\n");

    // Create IMAGE data stream
    $psdata->write("/image-{$id}-data currentfile << /Filter /ASCIIHexDecode >> /ReusableStreamDecode filter\n");

    // initialize line length counter
    $ctr = 0;
    
    for ($i = 0; $i < strlen($samples); $i += 4) {
      // Save image pixel to the stream data
      $r = ord($samples{$i});
      $g = ord($samples{$i+1});
      $b = ord($samples{$i+2});
      $psdata->write(sprintf("%02X%02X%02X",$r,$g,$b));

      // Increate the line length counter; check if stream line needs to be terminated
      $ctr += 6;
      if ($ctr > MAX_LINE_LENGTH) { 
        $psdata->write("\n");
        $ctr = 0;
      }
    };

    // terminate the stream data
    $psdata->write(">\ndef\n");

    // Create MASK data stream
    $psdata->write("/mask-{$id}-data currentfile << /Filter /ASCIIHexDecode >> /ReusableStreamDecode filter\n");

    // initialize line length counter
    $ctr = 0;

    // initialize mask bit counter
    $bit_ctr = 0;
    $mask_data = 0xff;

    for ($y = 0; $y < $size_y; $y++) {
      for ($x = 0; $x < $size_x; $x++) {
        // Check if this pixel should be transparent
        $a = ord($samples{($y*$size_x + $x)*4+3});
      
        if ($a < 255) {
          $mask_data = ($mask_data << 1) | 0x1;
        } else {
          $mask_data = ($mask_data << 1);
        };
        $bit_ctr ++;
      
        // If we've filled the whole byte,  write it into the mask data stream
        if ($bit_ctr >= 8 || $x + 1 == $size_x) { 
          // Pad mask data, in case we have completed the image row
          while ($bit_ctr < 8) {
            $mask_data = ($mask_data << 1) | 0x01;
            $bit_ctr ++;
          };
          
          $psdata->write(sprintf("%02X", $mask_data & 0xff)); 

          // Clear mask data after writing 
          $mask_data = 0xff;
          $bit_ctr = 0;

          // Increate the line length counter; check if stream line needs to be terminated
          $ctr += 1;
          if ($ctr > MAX_LINE_LENGTH) { 
            $psdata->write("\n");
            $ctr = 0;
          }
        };
      };
    };

    // terminate the stream data
    // Write any incomplete mask byte to the mask data stream
    if ($bit_ctr != 0) {
      while ($bit_ctr < 8) {
        $mask_data <<= 1;
        $mask_data |= 1;
        $bit_ctr ++;
      }
      $psdata->write(sprintf("%02X", $mask_data));
    };
    $psdata->write(">\ndef\n");

    // return image and mask data references
    $image = "image-{$id}-data";
    $mask  = "mask-{$id}-data";

    // Delete temporary files 
    unlink($tempfile.'.png');
    unlink($tempfile.'.rgba');

    return $id;
  }

  function alpha($psdata, $src_img, &$size_x, &$size_y, &$image, &$mask) {
    // Generate an unique image id
    $id = $this->generate_id();

    // Generate the unique temporary file name for this image; 
    // we'll use it for imagemagick temporary files
    $tempfile = $psdata->mk_filename();

    // Save image as PNG for further processing
    imagepng($src_img, $tempfile.'.png');

    // Call image magick - convert to raw RGB samples (binary)
    safe_exec('"'.IMAGE_MAGICK_CONVERT_EXECUTABLE.'"'." ${tempfile}.png ${tempfile}.rgba", $output);

    // read raw rgba samples
    $samples = file_get_contents($tempfile.'.rgba');

    // Determine image size
    $size_x      = imagesx($src_img); 
    $size_y      = imagesy($src_img);
    
    // write stread header to the postscript file
    $psdata->write("/image-{$id}-init { image-{$id}-data 0 setfileposition } def\n");
    $psdata->write("/image-{$id}-data currentfile << /Filter /ASCIIHexDecode >> /ReusableStreamDecode filter\n");

    // initialize line length counter
    $ctr = 0;

    // Save visible background color
    $handler =& get_css_handler('background-color');
    $bg = $handler->get_visible_background_color();

    for ($i = 0; $i < strlen($samples); $i += 4) {
      // Save image pixel to the stream data
      $r = ord($samples{$i});
      $g = ord($samples{$i+1});
      $b = ord($samples{$i+2});
      $a = 255-ord($samples{$i+3});

      // Calculate approximate color 
      $r = (int)($r + ($bg[0] - $r)*$a/255);
      $g = (int)($g + ($bg[1] - $g)*$a/255);
      $b = (int)($b + ($bg[2] - $b)*$a/255);

      // Save image pixel to the stream data
      $psdata->write(sprintf("%02X%02X%02X",
                             min(max($r,0),255),
                             min(max($g,0),255),
                             min(max($b,0),255)));

      // Increate the line length counter; check if stream line needs to be terminated
      $ctr += 6;
      if ($ctr > MAX_LINE_LENGTH) { 
        $psdata->write("\n");
        $ctr = 0;
      }
    };

    // terminate the stream data
    $psdata->write(">\ndef\n");

    // return image and mask data references
    $image = "image-{$id}-data";
    $mask  = "";

    // Delete temporary files 
    unlink($tempfile.'.png');
    unlink($tempfile.'.rgba');

    return $id;
  }

}
?>