<?php
class ImageGalsLib extends TikiLib {

  function ImageGalsLib($db) 
  {
    # this is probably uneeded now
    if(!$db) {
      die("Invalid db object passed to ImagegalLib constructor");  
    }
    $this->db = $db;

    // Which GD Version do we have?
    if (function_exists("imagecreatefromstring")) {
      $this->havegd = true;
      if (function_exists("gd_info")) {
        $gdinfo=gd_info();
	preg_match("/[0-9]+\.[0-9]+/",$gdinfo["GD Version"],$gdversiontmp);
	$this->gdversion=$gdversiontmp[0];
      } else {
        // I have no experience ... maybe someone knows better
        $this->gdversion="1.0";
      }
    } else {
      $this->havegd = false;
    }

    // Do we have the imagick PEAR module?
    // Module can be downloaded at http://pear.php.net/package-info.php?pacid=76
    if (function_exists("imagick_read")) {
      $this->haveimagick = true;
    } else {
      $this->haveimagick = false;
    }
   
   //what shall we use?
   //will be configurable in admin menu soon

   $this->uselib = "gd";
   //$this->uselib = "imagick";

  }
  
  //
  // Wrappers
  //

  function readimagefromstring()
  { 
    if ($this->uselib == "imagick") {
      $this->imagehandle=imagick_blob2image($this->image);
    } else if ($this->uselib == "gd") {
      $this->imagehandle=imagecreatefromstring($this->image);
    }
  }

  function writeimagetostring()
  {
    if ($this->uselib == "imagick") {
      $this->image=imagick_image2blob($this->imagehandle);
    } else if ($this->uselib == "gd") {
      ob_start();
      imagejpeg($this->imagehandle);
      $this->image= ob_get_contents();
      ob_end_clean();
    }
  }

  function readimagefromfile($fname)
  {
    if ($this->uselib == "imagick") {
      $this->image=imagick_readimage($fname);
    } else if ($this->uselib == "gd") {
      // read image
      $fp = fopen($fname,"rb");
      $size=filesize($fname);
      $data = fread($fp,$size);
      fclose($fp);
      $this->image=imagecreatefromstring($data);
    }
  }

  // for piping out a image
  function pipeimage($fname)
  {
    $fp = fopen($fname,"rb");
    $size=filesize($fname);
    $data = fread($fp,$size);
    echo $data;
    fclose($fp);
  }

  //Get sizes. Image must be loaded before
  function getimageinfo()
  {
    if ($this->uselib == "imagick") {
      $this->mime=imagick_getmimetype($this->imagehandle);
      $this->sizex=imagick_getwidth($this->imagehandle);
      $this->sizey=imagick_getheight($this->imagehandle);
    } else if ($this->uselib == "gd") {
      $this->sizex=imagesx($this->imagehandle);
      $this->sizey=imagesy($this->imagehandle);
    }
  }

  // GD can only get the mimetype from the file
  function getfileinfo($fname)
  {
    $this->filesize=filesize($fname);

    if ($this->uselib == "imagick") {

    } else if ($this->uselib == "gd") {
      $imageinfo=getimagesize($fname);
      if ($imageinfo["0"] > 0 && $imageinfo["1"] > 0 && $imageinfo["2"] > 0 ) {
        if ($this->gdversion >= 2.0) {
          $this->mime = $imageinfo["mime"];
        } else {
          $mimetypes=array("1" => "gif", "2" => "jpg", "3" => "png",
                           "4" => "swf", "5" => "psd", "6" => "bmp",
                           "7" => "tiff", "8" => "tiff", "9" => "jpc",
                           "10" => "jp2", "11" => "jpx", "12" => "jb2",
                           "13" => "swc", "14" => "iff");
          $this->mime="image/".$mimetypes[$imageinfo["2"]];
        }
      }
    }
  }
  
  // resize Image
  function resizeImage($newx,$newy)
  {

    if (!isset($this->imagehandle)) {$this->readimagefromstring();}
    if (!isset($this->sizex)) {$this->getimageinfo();}
    if ($this->uselib == "imagick") {
      //  For 4th parameter, use any of the following:
      //
      //      IMAGICK_FILTER_UNDEFINED
      //      IMAGICK_FILTER_POINT
      //      IMAGICK_FILTER_BOX
      //      IMAGICK_FILTER_TRIANGLE
      //      IMAGICK_FILTER_HERMITE
      //      IMAGICK_FILTER_HANNING
      //      IMAGICK_FILTER_HAMMING
      //      IMAGICK_FILTER_BLACKMAN
      //      IMAGICK_FILTER_GAUSSIAN
      //      IMAGICK_FILTER_QUADRATIC
      //      IMAGICK_FILTER_CUBIC
      //      IMAGICK_FILTER_CATROM
      //      IMAGICK_FILTER_MITCHELL
      //      IMAGICK_FILTER_LANCZOS
      //      IMAGICK_FILTER_BESSEL
      //      IMAGICK_FILTER_SINC
      //      IMAGICK_FILTER_UNKNOWN
      //
      //  Use IMAGICK_FILTER_UNKNOWN to defer to whatever filter is defined
      //  in the image.
      imagick_resize( $this->imagehandle,$this->sizex,$this->sizey,
			IMAGICK_FILTER_UNKNOWN, 0, $newx."+".$newy."!" );

    } else if ($this->uselib == "gd") {
    
      if($this->gdversion>=2.0){
        $t = imagecreatetruecolor($newx,$newy);
        imagecopyresampled($t, $this->imagehandle, 0,0,0,0, $newx,$newy, 
				$this->sizex, $this->sizey);
       } else {
         $t = imagecreate($newx,$newy);
         $this->ImageCopyResampleBicubic( $t, $this->imagehandle, 0,0,0,0, 
			$newx,$newy, $this->sizex, $this->sizey);
       }
       $this->imagehandle=$t;

    }
    // fill $this->image for writing or output
    $this->writeimagetostring();
    // reget sizes
    $this->getimageinfo();
    return true;
  }

  // rescale Image, almost the same as resize, but keeps apect ratio
  // bbx and bby give the boundary box
  function rescaleImage($bbx,$bby)
  {

  }

  // Batch image uploads ////
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

  function add_image_hit($id)
  {
    $query = "update tiki_images set hits=hits+1 where imageId=$id";
    $result = $this->query($query);
    return true;
  }

  function add_gallery_hit($id)
  {
    $query = "update tiki_galleries set hits=hits+1 where galleryId=$id";
    $result = $this->query($query);
    return true;
  }

  function ImageCopyResampleBicubic (&$dst_img, &$src_img, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h)
// port to PHP by John Jensen July 10 2001 (updated 4/21/02) -- original code (in C, for the PHP GD Module) by jernberg@fairytale.se////
{
$palsize = ImageColorsTotal ($src_img);
for ($i = 0; $i < $palsize; $i++) { // get palette.
$colors = ImageColorsForIndex ($src_img, $i);
ImageColorAllocate ($dst_img, $colors['red'], $colors['green'], $colors['blue']);
}

$scaleX = ($src_w - 1) / $dst_w;
$scaleY = ($src_h - 1) / $dst_h;
$scaleX2 = (int) ($scaleX / 2);
$scaleY2 = (int) ($scaleY / 2);
for ($j = $src_y; $j < $dst_h; $j++) {
$sY = (int) ($j * $scaleY);
$y13 = $sY + $scaleY2;
for ($i = $src_x; $i < $dst_w; $i++) {
$sX = (int) ($i * $scaleX);
$x34 = $sX + $scaleX2;
$color1 = ImageColorsForIndex ($src_img, ImageColorAt ($src_img, $sX, $y13));
$color2 = ImageColorsForIndex ($src_img, ImageColorAt ($src_img, $sX, $sY));
$color3 = ImageColorsForIndex ($src_img, ImageColorAt ($src_img, $x34, $y13));
$color4 = ImageColorsForIndex ($src_img, ImageColorAt ($src_img, $x34, $sY));
$red = ($color1['red'] + $color2['red'] + $color3['red'] + $color4['red']) / 4;
$green = ($color1['green'] + $color2['green'] + $color3['green'] + $color4['green']) / 4;
$blue = ($color1['blue'] + $color2['blue'] + $color3['blue'] + $color4['blue']) / 4;
ImageSetPixel ($dst_img, $i + $dst_x - $src_x, $j + $dst_y - $src_y, ImageColorClosest ($dst_img, $red, $green, $blue));
}
}
}

  function store_image_data()
  {

    global $gal_use_dir;
    global $gal_use_db;

    $size=sizeof($this->image);
    $fhash="";

    if($gal_use_db == 'y') {
      // Prepare to store data in database
      $data = addslashes($this->image);
    } else {
      // Store data in directory
      switch ($this->type) {
        case 't':
          $ext=".thumb";
          break;
        case 's':
          $ext=".scaled_".$this->xsize."x".$this->ysize;
          break;
        case 'b':
          // for future use
          $ext=".backup";
          break;
        default:
          $ext='';
        }
      $fhash = md5($this->filename).$ext; //Path+extension
      @$fw = fopen($gal_use_dir.$fhash,"wb");
      if(!$fw) {
        return false;
      }
      fwrite($fw,$data);
      fclose($fw);
      $data='';
    }
    $this->filename=$this->xsize."x".$this->ysize."_".$this->filename; // rebuild filename for downloading images
    // insert data
    $query = "insert into tiki_images_data(imageId,xsize,ysize,
                                type,filesize,filetype,filename,data)
                        values ($this->imageId,$this->xsize,$this->ysize,'$this->type',$size,
                                '$this->filetype','$this->filename','$data')";
    $result = $this->query($query);
    return true;
  }


  function rebuild_image($imageid,$itype,$xsize,$ysize)
  {
    $galid=$this->get_gallery_from_image($imageid);
    //we don't rebuild original images
    if ($itype == 'o') return false;

    //if it is a scaled image, test the gallery settings
    if ($itype == 's')
    {
      $scaleinfo=$this->get_gallery_scale_info($galid);
      $hasscale=false;
      while (list ($num, $sci) = each ($scaleinfo)) {
        if ($sci["xsize"] == $xsize && $sci["ysize"] == $ysize) {
          $hasscale=true;
          $newx=$sci["xsize"];
          $newy=$sci["ysize"];
        }
      }
      if (!$hasscale) return false;
    }

    // now we can start rebuilding the image
    global $gal_use_dir;
    global $gal_use_db;
    //if(!function_exists("ImageCreateFromString")) return false;
    #get image and other infos
    $this->get_image($imageid);
    $galinfo=$this->get_gallery_info($galid);

    // determine new size
    if ($itype == 't') {
      $newx=$galinfo["thumbSizeX"];
      $newy=$galinfo["thumbSizeY"];
    }

    if($this->xsize > $this->ysize)
    {
      $tscale = ((int)$this->xsize / $newx);
    } else {
      $tscale = ((int)$this->ysize / $newy);
    }
    $xsize=((int)($this->xsize / $tscale));
    $ysize=((int)($this->ysize / $tscale));

    if(!$this->resizeImage($xsize,$ysize))
    {
      die;
    }
    $this->xsize=$xsize;
    $this->ysize=$ysize;

    // we always rescale to jpegs.
    $t_type='image/jpeg';

    // some more infos
    $filename=$this->filename; // filename of original image
    $this->type=$itype;
    
    //store
    $this->store_image_data();

    //return new size
    $newsize["xsize"]=$xsize;
    $newsize["ysize"]=$ysize;
    return $newsize;
  }

  function rebuild_thumbnails($galleryId)
  {
    global $gal_use_dir;
    global $gal_use_db;

    // rewritten by flo
    $query = "select imageId from tiki_images where galleryId=$galleryId";
    $result = $this->query($query);
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $query2="delete from tiki_images_data where imageId=".$res["imageId"]." and type='t'";
      $result2 = $this->query($query2);
    }
    return true;
  }

  function edit_image($id,$name,$description) {
   $name = addslashes(strip_tags($name));
   $description = addslashes(strip_tags($description));
    $query = "update tiki_images set name='$name', description='$description' where imageId = $id";
    $result = $this->query($query);
    return true;
  }

  function get_random_image($galleryId = -1)
  {
    $whgal = "";
    if (((int)$galleryId) != -1) { $whgal = " where galleryId = " . $galleryId; }
    $query = "select count(*) from tiki_images" . $whgal;
    $cant = $this->getOne($query);
    $pick = rand(0,$cant-1);
    $ret = Array();
    $query = "select imageId,galleryId,name from tiki_images" . $whgal . " limit $pick,1";
    $result=$this->query($query);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    $ret["galleryId"] = $res["galleryId"];
    $ret["imageId"] = $res["imageId"];
    $ret["name"] = $res["name"];
    $query = "select name from tiki_galleries where galleryId = " . $res["galleryId"];
    $ret["gallery"] = $this->getOne($query);
    return($ret);
  }


  function insert_image($galleryId,$name,$description,$filename, $filetype, $data, $size, $xsize, $ysize, $user,$t_data,$t_type)
  {
    global $gal_use_db;
    global $gal_use_dir;
    $name = addslashes(strip_tags($name));
    $description = addslashes(strip_tags($description));
    $now = date("U");
    $path='';
    if($gal_use_db == 'y') {
      // Prepare to store data in database
      $data = addslashes($data);
      $t_data = addslashes($t_data);
    } else {
      // Store data in directory
      $fhash = md5(uniqid($filename));
      @$fw = fopen($gal_use_dir.$fhash,"wb");
      if(!$fw) {
        return false;
      }
      fwrite($fw,$data);
      fclose($fw);
      @$fw = fopen($gal_use_dir.$fhash.'.thumb',"wb");
      if(!$fw) {
        return false;
      }
      fwrite($fw,$t_data);
      fclose($fw);
      $t_data='';
      $data='';
      $path=$fhash;
    }
    $query = "insert into tiki_images(galleryId,name,description,user,created,hits,path)
                          values($galleryId,'$name','$description','$user',$now,0,'$path')";
    $result = $this->query($query);
    $query = "select max(imageId) from tiki_images where created=$now";
    $imageId = $this->getOne($query);
    // insert data
    $query = "insert into tiki_images_data(imageId,xsize,ysize,
                                type,filesize,filetype,filename,data)
                        values ($imageId,$xsize,$ysize,'o',$size,
                                '$filetype','$filename','$data')";
    $result = $this->query($query);
    // insert thumb
    if (sizeof($t_data) >0)
    {
      $query = "insert into tiki_images_data(imageId,xsize,ysize,
                                type,filesize,filetype,filename,data)
                        values ($imageId,$xsize,$ysize,'t',$size,
                                '$t_type','$filename','$t_data')";
      $result = $this->query($query);
    }

    $query = "update tiki_galleries set lastModif=$now where galleryId=$galleryId";
    $result = $this->query($query);
    return $imageId;
  }

  function rotate_image($id,$angle)
  {
    //get image
    global $gal_use_dir;
    global $gal_use_db;
    $data=$this->getOne("select data from tiki_images_data where imageId=$id and type='o'");
    //$data = $this->get_image($id);
    $data = imagecreatefromstring($data);

    $sx=imagesx($data);
    $sy=imagesy($data);
    $data=imagerotate($data,$angle,0);
    ob_start();
    imagejpeg($data);
    $data = ob_get_contents();
    ob_end_clean();
    // Prepare to store data in database
    $data= addslashes($data);
    $query = "update tiki_images_data set data='$data' where imageId=$id and type='o'";
    $result = $this->query($query);
    if (DB::isError($result)) $this->sql_error($query,$result);
    // delete all scaled images. Will be rebuild when requested
    $query = "delete from tiki_images_data where imageId=$id and type !='o'";
    $result = $this->query($query);
  }

  function rotate_right_image($id)
  {
    $this->rotate_image($id,270);
  }

  function rotate_left_image($id)
  {
    $this->rotate_image($id,90);
  }


  function remove_image($id)
  {
    global $gal_use_dir;
    $path = $this->getOne("select path from tiki_images where imageId=$id");
    if($path) {
      @unlink($gal_use_dir.$path);
      @unlink($gal_use_dir.$path.'.thumb');
      //todo: remove scaled images
    }
    $query = "delete from tiki_images where imageId=$id";
    $result = $this->query($query);
    $query = "delete from tiki_images_data where imageId=$id";
    $result = $this->query($query);
    return true;
  }

  function get_images($offset,$maxRecords,$sort_mode,$find,$galleryId=-1)
  {

    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" and (name like '%".$find."%' or description like '%".$find."%')";
    } else {
      $mid="";
    }

    $midcant="";
    if ($galleryId != -1 && is_numeric($galleryId))
    {
      $mid .= " and i.galleryId=$galleryId ";
      $midcant = "where galleryId=$galleryId ";
    }

    $query = "select i.path ,i.imageId,i.name,i.description,i.created,
                d.filename,d.filesize,d.xsize,d.ysize,
                i.user,i.hits
                from tiki_images i , tiki_images_data d
                 where i.imageID=d.imageID
                 and d.type='o'
                $mid
                order by $sort_mode limit $offset,$maxRecords";
    $result = $this->query($query);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $query_cant = "select count(*) from tiki_images $midcant";
    $cant = $this->getOne($query_cant);
    $retval["cant"] = $cant;
    return $retval;
  }

  function list_images($offset,$maxRecords,$sort_mode,$find)
  {
    return $this->get_images($offset,$maxRecords,$sort_mode,$find);
  }

  function get_gallery_owner($galleryId)
  {
    $query = "select user from tiki_galleries where galleryId=$galleryId";
    $user = $this->getOne($query);
    return $user;
  }

  function get_gallery_from_image($imageid)
  {
    $query = "select galleryId from tiki_images where imageId=$imageid";
    $galid=$this->getOne($query);
    return $galid;

  }

  function get_gallery($id)
  {
    $query = "select * from tiki_galleries where galleryId='$id'";
    $result = $this->query($query);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }

  function move_image($imgId,$galId)
  {
    $query = "update tiki_images set galleryId=$galId where imageId=$imgId";
    $result = $this->query($query);
    return true;
  }

  function get_image_info($id,$itype='o',$xsize=0,$ysize=0)
  {
    // code may be merged with get_image
    $mid="";
    if ($xsize!=0) {$mid="and d.xsize=$xsize ";}
    if ($ysize!=0) {$mid.="and d.ysize=$ysize ";}
    if ($xsize!=0 && $ysize==$xsize)
      {
        // we don't know yet.
        $mid="and greatest(d.xsize,d.ysize) = greatest($xsize,$ysize) ";
      }
    $query = "select i.imageId, i.galleryId, i.name,
                     i.description, i.created, i.user,
                     i.hits, i.path,
                     d.xsize,d.ysize,d.type,d.filesize,
                     d.filetype,d.filename
                 from tiki_images i, tiki_images_data d where
                     i.imageId='$id' and d.imageId=i.imageId
                     and d.type='$itype'
                     $mid";
    $result = $this->query($query);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }

  // Add an option to stablish Image size (x,y)
  function get_image($id,$itype='o',$xsize=0,$ysize=0)
  {
    global $gal_use_db;
    global $gal_use_dir;
    $mid="";
    if ($xsize!=0) {$mid="and d.xsize=$xsize ";}
    if ($ysize!=0) {$mid.="and d.ysize=$ysize ";}
    if ($xsize!=0 && $ysize==$xsize)
      {
        // we don't know yet.
        $mid="and greatest(d.xsize,d.ysize) = greatest($xsize,$ysize) ";
      }
    $query = "select i.imageId, i.galleryId, i.name,
                     i.description, i.created, i.user,
                     i.hits, i.path,
                     d.xsize,d.ysize,d.type,d.filesize,
                     d.filetype,d.filename,d.data
                 from tiki_images i, tiki_images_data d where
                     i.imageId='$id' and d.imageId=i.imageId
                     and d.type='$itype'
                     $mid";
    $result = $this->query($query);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);

    $this->imageId=$res["imageId"];
    $this->galleryId=$res["galleryId"];
    $this->name=$res["name"];
    $this->description=$res["description"];
    $this->created=$res["created"];
    $this->user=$res["user"];
    $this->hits=$res["hits"];
    $this->path=$res["path"];
    $this->xsize=$res["xsize"];
    $this->ysize=$res["ysize"];
    $this->type=$res["type"];
    $this->filesize=$res["filesize"];
    $this->filetype=$res["filetype"];
    $this->filename=$res["filename"];

    # build scaled images or thumb if not availible
    if ($itype != 'o' && !isset($this->imageId))
      {
        if($newsize=$this->rebuild_image($id,$itype,$xsize,$ysize)) {
          return $this->get_image($id,$itype,$newsize["xsize"],$newsize["ysize"]);
        }
      }
    // get image data from fs

    if ($res["data"]=='' )
    {
      switch ($itype) {
        case 't':
          $ext=".thumb";
          break;
        case 's':
          $ext=".scaled_".$res["xsize"]."x".$res["ysize"];
          break;
        case 'b':
          // for future use
          $ext=".backup";
          break;
        default:
          $ext='';
        }
      // If the image was a .gif then the thumbnail has 0 bytes if the thumbnail
      // is empty then use the full image as thumbnail
      if($ext==".thumb" && filesize($gal_use_dir.$res["path"].$ext)==0 ) {
        $ext='';
      }
      //@$fp = fopen($gal_use_dir.$res["path"].$ext,'rb');
      //if(!$fp) {die;}
      //while(!feof($fp)) {
      //  $res["data"].=fread($fp,8192*16);
      //}
      //fclose($fp);
      $this->readimagefromfile($gal_use_dir.$res["path"].$ext);
    } else {
      $this->image=$res["data"];
      $this->readimagefromstring($res["data"]);
    }
    return $res;
  }

  function get_image_thumb($id)
  {
    return $this->get_image($id,'t');
  }


  function replace_gallery($galleryId, $name, $description, $theme, $user,$maxRows,$rowImages,$thumbSizeX,$thumbSizeY,$public,$visible='y')
  {
    // if the user is admin or the user is the same user and the gallery exists then replace if not then
    // create the gallary if the name is unused.
    $name = addslashes(strip_tags($name));
    $description = addslashes(strip_tags($description));
    $now = date("U");
    if($galleryId>0) {
      //$res = $result->fetchRow(DB_FETCHMODE_ASSOC);
      //if( ($user == 'admin') || ($res["user"]==$user) ) {
      $query = "update tiki_galleries set name='$name',visible='$visible', maxRows=$maxRows, rowImages=$rowImages, 
                thumbSizeX=$thumbSizeX, thumbSizeY=$thumbSizeY, description='$description', theme='$theme', 
                lastModif=$now, public='$public' where galleryId=$galleryId";
      $result = $this->query($query);
    } else {
      // Create a new record
      $query =  "insert into tiki_galleries(name,description,theme,created,user,lastModif,maxRows,rowImages,thumbSizeX,thumbSizeY,public,hits,visible)
                                    values ('$name','$description','$theme',$now,'$user',$now,$maxRows,$rowImages,$thumbSizeX,$thumbSizeY,'$public',0,'$visible')";
      $result = $this->query($query);
      $galleryId = $this->getOne("select max(galleryId) from tiki_galleries where name='$name' and created=$now");
    }
    return $galleryId;
  }

  function add_gallery_scale($galleryId,$xsize,$ysize)
  {
    $query="insert into tiki_galleries_scales(galleryId,xsize,ysize)
            values($galleryId,$xsize,$ysize)";
    $result = $this->query($query);
  }

  function remove_gallery_scale($galleryId,$xsize=0,$ysize=0)
  {
    $mid="";
    if ($xsize!=0) $mid=" and xsize=$xsize ";
    if ($ysize!=0) $mid.=" and ysize=$ysize";
    $query="delete from tiki_galleries_scales where
            galleryId=$galleryId $mid";
    $result = $this->query($query);
  }

  function remove_gallery($id)
  {
    global $gal_use_dir;
    $query = "select path from tiki_images where galleryId='$id'";
    $result = $this->query($query);
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $path = $res["path"];
      if($path) {
        @unlink($gal_use_dir.$path);
        @unlink($gal_use_dir.$path.'.thumb');
      }
    }
    $query = "delete from tiki_galleries where galleryId='$id'";
    $result = $this->query($query);
    $query = "delete from tiki_images where galleryId='$id'";
    $result = $this->query($query);
    $this->remove_gallery_scale($id);
    $this->remove_object('image gallery',$id);
    return true;
  }

  function get_gallery_info($id)
  {
    $query = "select * from tiki_galleries where galleryId='$id'";
    $result = $this->query($query);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }

  function get_gallery_scale_info($id)
  {
    $query = "select * from tiki_galleries_scales where galleryId='$id'
              order by xsize*ysize asc";
    $result = $this->query($query);
    $resa=Array();
    while ($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $resa[]=$res;
    }
    return $resa;
  }

  function get_gallery_next_scale($id,$xsize=0,$ysize=0)
  {
    $xy=$xsize*$ysize;
    $query = "select * from tiki_galleries_scales where galleryId='$id'
              and xsize*ysize > $xy order by xsize*ysize asc";
    $result = $this->query($query);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }

  //Capture Images from wiki, blogs, ....

  function capture_images($data)
  {
    $cacheimages = $this->get_preference("cacheimages",'y');
    if($cacheimages != 'y') return $data;
    preg_match_all("/src=\"([^\"]+)\"/",$data,$reqs1);
    preg_match_all("/src=\'([^\']+)\'/",$data,$reqs2);
    preg_match_all("/src=([A-Za-z0-9\:\?\=\/\\\.\-\_]+)\}/",$data,$reqs3);
    preg_match_all("/src=([A-Za-z0-9\:\?\=\/\\\.\-\_]+) /",$data,$reqs4);
    $merge = array_merge($reqs1[1],$reqs2[1],$reqs3[1],$reqs4[1]);
    $merge = array_unique($merge);
    //print_r($merge);
    // Now for each element in the array capture the image and
    // if the capture was succesful then change the reference to the
    // internal image
    $page_data = $data;
    foreach($merge as $img) {
      // This prevents caching images
      if(!strstr($img,"show_image.php") && !strstr($img,"nocache")) {
      //print("Procesando: $img<br/>");
      @$fp = fopen($img,"r");
      if($fp) {
        $data = '';
        while(!feof($fp)) {
          $data .= fread($fp,4096);
        }
        //print("Imagen leida:".strlen($data)." bytes<br/>");
        fclose($fp);
        if(strlen($data)>0) {
          $url_info = parse_url($img);
          $pinfo = pathinfo($url_info["path"]);
          $type = "image/".$pinfo["extension"];
          $name = $pinfo["basename"];
          $size = strlen($data);
          $url = $img;

          if(function_exists("ImageCreateFromString")&&(!strstr($type,"gif"))) {

            $img = imagecreatefromstring($data);
            $size_x = imagesx($img);
            $size_y = imagesy($img);
      // Fix the ratio values for system gallery
      $gal_info["thumbSizeX"]=90;
      $gal_info["thumbSizeY"]=90;
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
            $tmpfname = tempnam ("/tmp", "FOO").'.jpg';
            imagejpeg($t,$tmpfname);
            // Now read the information
            $fp = fopen($tmpfname,"rb");
            $t_data = fread($fp, filesize($tmpfname));
            fclose($fp);
            unlink($tmpfname);
            $t_pinfo = pathinfo($tmpfname);
            $t_type = $t_pinfo["extension"];
            $t_type='image/'.$t_type;

            $imageId = $this->insert_image(0,'','',$name, $type, $data, $size, $size_x, $size_y, 'admin',$t_data,$t_type);
            //print("Imagen generada en $imageId<br/>");
          } else {
            //print("No GD detected generating image without thumbnail<br/>");
            $imageId = $this->insert_image(0,'','',$name, $type, $data, $size, 100, 100, 'admin','','');
      //print("Imagen en $imageId<br/>");
          }
          // Now change it!
          //print("Changing $url to imageId: $imageId");
          $uri = parse_url($_SERVER["REQUEST_URI"]);
          $path=str_replace("tiki-editpage","show_image",$uri["path"]);
          $page_data = str_replace($url,httpPrefix().$path.'?id='.$imageId,$page_data);
        } // if strlen
      } // if $fp
      }
    } // foreach
    return $page_data;
  }

}



$imagegallib= new ImageGalsLib($dbTiki);
?>
