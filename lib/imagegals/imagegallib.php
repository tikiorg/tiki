<?php
class ImageGalsLib extends TikiLib {

  function ImageGalsLib($db) 
  {
    # this is probably uneeded now
    if(!$db) {
      die("Invalid db object passed to ImagegalLib constructor");  
    }
    $this->db = $db;  
  }
  
   // Batch image uploads ////
  // Batch image uploads ////
  // Fixed by FLO
  function process_batch_image_upload($galleryId,$file,$user)
  {
    global $gal_match_regex;
    global $gal_nmatch_regex;
    global $gal_use_db;
    global $gal_use_dir;
    global $tmpDir;
    $numimages=0;
    include_once('lib/pclzip.lib.php');
    $archive = new PclZip($file);
    // Read Archive contents
    $ziplist=$archive->listContent();
    if (!$ziplist) return(false); // Archive invalid
    for ($i=0; $i<sizeof($ziplist); $i++) {
      $file=$ziplist["$i"]["filename"];
      if (!$ziplist["$i"]["folder"]) {
        //copied
        $gal_info = $this->get_gallery($galleryId);
        $upl=1;
        if(!empty($gal_match_regex)) {
          if(!preg_match("/$gal_match_regex/",$file,$reqs)) $upl=0;
        }
        if(!empty($gal_nmatch_regex)) {
          if(preg_match("/$gal_nmatch_regex/",$file,$reqs)) $upl=0;
        }
        //extract file

        $archive->extractByIndex($ziplist["$i"]["index"],$tmpDir,dirname($file)); //extract and remove (dangerous) pathname
        $file=basename($file);
        //determine filetype and dimensions
        $imageinfo=getimagesize($tmpDir."/".$file);
        if ($imageinfo["0"] > 0 && $imageinfo["1"] > 0 && $imageinfo["2"] > 0 ) {
          if (chkgd2()) {
            $type = $imageinfo["mime"];
          } else {
            $mimetypes=array("1" => "gif", "2" => "jpg", "3" => "png",
                             "4" => "swf", "5" => "psd", "6" => "bmp",
                             "7" => "tiff", "8" => "tiff", "9" => "jpc",
                             "10" => "jp2", "11" => "jpx", "12" => "jb2",
                             "13" => "swc", "14" => "iff");
            $type="image/".$mimetypes[$imageinfo["2"]];
          }
          
          $exp=substr($file,strlen($file)-3,3);
          $fp = fopen($tmpDir."/".$file,"rb");
          $size=filesize($tmpDir."/".$file);
          $data = fread($fp,$size);
          fclose($fp);
          if(function_exists("ImageCreateFromString")&&(!strstr($type,"gif"))) {
            $img = imagecreatefromstring($data);
            $size_x = imagesx($img);
            $size_y = imagesy($img);
            if ($size_x > $size_y)
              $tscale = ((int)$size_x / $gal_info["thumbSizeX"]);
            else
              $tscale = ((int)$size_y / $gal_info["thumbSizeY"]);
            $tw = ((int)($size_x / $tscale));
            $ty = ((int)($size_y / $tscale));
            if (chkgd2()) {
              $t = imagecreatetruecolor($tw,$ty);
              imagecopyresampled($t, $img, 0,0,0,0, $tw,$ty, $size_x, $size_y);
            } else {
              $t = imagecreate($tw,$ty);
              $this->ImageCopyResampleBicubic( $t, $img, 0,0,0,0, $tw,$ty, $size_x, $size_y);
            }
            // CHECK IF THIS TEMP IS WRITEABLE OR CHANGE THE PATH TO A WRITEABLE DIRECTORY
            //$tmpfname = 'temp.jpg';
            $tmpfname = tempnam ($tmpDir , "FOO").'.jpg';
            imagejpeg($t,$tmpfname);
            // Now read the information
            $fp = fopen($tmpfname,"rb");
            $t_data = fread($fp, filesize($tmpfname));
            fclose($fp);
            unlink($tmpfname);
            $t_pinfo = pathinfo($tmpfname);
            $t_type = $t_pinfo["extension"];
            $t_type='image/'.$t_type;
            $imageId = $this->insert_image($galleryId,$file,'',$file, $type, $data, $size, $size_x, $size_y, $user,$t_data,$t_type);
            $numimages++;
            unlink($tmpDir."/".$file);
          } else {
            $tmpfname='';
            $imageId = $this->insert_image($galleryId,$file,'',$file, $type, $data, $size, 0, 0, $user,'','');
            $numimages++;
            unlink($tmpDir."/".$file);
          }
        }
      }
    }
  return $numimages;
  }

  
}

$imagegallib= new ImageGalsLib($dbTiki);
?>