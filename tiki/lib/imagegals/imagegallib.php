<?php

class ImageGalsLib extends TikiLib {
	function ImageGalsLib($db) {
		# this is probably uneeded now
		if (!$db) {
			die ("Invalid db object passed to ImagegalLib constructor");
		}

		$this->db = $db;

		// Which GD Version do we have?
		if (function_exists("imagecreatefromstring")) {
			$this->havegd = true;

			if (function_exists("gd_info")) {
				$this->gdinfo = gd_info();

				preg_match("/[0-9]+\.[0-9]+/", $this->gdinfo["GD Version"], $gdversiontmp);
				$this->gdversion = $gdversiontmp[0];
			} else {
				//next try
				ob_start();

				phpinfo (INFO_MODULES);

				if (preg_match('/GD Version.*2.0/', ob_get_contents())) {
					$this->gdversion = "2.0";
				} else {
					// I have no experience ... maybe someone knows better
					$this->gdversion = "1.0";
				}

				$this->gdinfo["JPG Support"] = preg_match('/JPG Support.*enabled/', ob_get_contents());
				$this->gdinfo["PNG Support"] = preg_match('/PNG Support.*enabled/', ob_get_contents());
				$this->gdinfo["GIF Create Support"] = preg_match('/GIF Create Support.*enabled/', ob_get_contents());
				$this->gdinfo["WBMP Support"] = preg_match('/WBMP Support.*enabled/', ob_get_contents());
				$this->gdinfo["XBM Support"] = preg_match('/XBM Support.*enabled/', ob_get_contents());
				ob_end_clean();
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

		//$this->uselib = "gd";
		$this->uselib = $this->get_preference('gal_use_lib', 'gd');

		if ($this->uselib == "imagick") {
			$this->canrotate = true;
		} else {
			$this->canrotate = false;
		}

		//Fallback to GD
		if ($this->uselib == "imagick" && $this->haveimagick == false) {
			$this->uselib = "gd";

			$this->set_preference('gal_use_lib', 'gd');
		}
	}
	// Features
	function canrotate() {
		return $this->canrotate;
	}
	//
	// Wrappers
	//
	function validhandle() {
		if (isset($this->imagehandle)) {
			if ($this->uselib == "imagick") {
				// seems not to work in imagick 0.95 aahrgh
				if (imagick_iserror($this->imagehandle))
					return false;

				return true;
			} else if ($this->uselib == "gd") {
				if (imagesx($this->imagehandle) == 0)
					return false;

				return true;
			}
		}

		return false;
	}

	function readimagefromstring() {
		if (!isset($this->image)) {
			return false;
		}

		//avoid error messages
		if (isset($this->filetype)) {
			if (!$this->issupported($this->filetype)) {
				return false;
			}
		}

		if ($this->uselib == "imagick") {
			$this->imagehandle = imagick_blob2image($this->image);
		} else if ($this->uselib == "gd") {
			$this->imagehandle = imagecreatefromstring($this->image);
		}
	}

	function writeimagetostring() {
		if ($this->uselib == "imagick") {
			$this->image = imagick_image2blob($this->imagehandle);
		} else if ($this->uselib == "gd") {
			ob_start();

			imagejpeg ($this->imagehandle);
			$this->image = ob_get_contents();
			ob_end_clean();
		}
	}

	function readimagefromfile($fname) {
		@$fp = fopen($fname, "rb");

		if ($fp) {
			$size = filesize($fname);

			$this->image = fread($fp, $size);
			fclose ($fp);
			return true;
		} else {
			return false;
		}
	}

	// for piping out a image
	function pipeimage($fname) {
		$fp = fopen($fname, "rb");

		$size = filesize($fname);
		$data = fread($fp, $size);
		echo $data;
		fclose ($fp);
	}

	//Get sizes. Image must be loaded before
	function getimageinfo() {
		if ($this->uselib == "imagick") {
			$this->filetype = imagick_getmimetype($this->imagehandle);

			$this->xsize = imagick_getwidth($this->imagehandle);
			$this->ysize = imagick_getheight($this->imagehandle);
		} else if ($this->uselib == "gd") {
			$this->xsize = imagesx($this->imagehandle);

			$this->ysize = imagesy($this->imagehandle);
		}
	}

	//Repair Image info. Is called if someone inserts unsupported images (like gif)
	//and switches to imagick
	function repairimageinfo() {
		if (!isset($this->imageId))
			die();

		if (!$this->issupported($this->filetype))
			die();

		$this->getimageinfo();
		//update
		$query = "update `tiki_images_data` set `xsize`=? , `ysize`=?
    		where `imageId`=? and `type`=?";
		$this->query($query,array($this->xsize,$this->ysize,$this->imageId,$this->type));
	}

	// GD can only get the mimetype from the file
	function getfileinfo($fname) {
		$this->filesize = filesize($fname);

		if ($this->uselib == "imagick") {
		} else if ($this->uselib == "gd") {
			unset ($this->filetype);

			$imageinfo = getimagesize($fname);

			if ($imageinfo["0"] > 0 && $imageinfo["1"] > 0 && $imageinfo["2"] > 0) {
				if ($this->gdversion >= 2.0) {
					$this->filetype = $imageinfo["mime"];
				} else {
					$mimetypes = array(
						"1" => "gif",
						"2" => "jpg",
						"3" => "png",
						"4" => "swf",
						"5" => "psd",
						"6" => "bmp",
						"7" => "tiff",
						"8" => "tiff",
						"9" => "jpc",
						"10" => "jp2",
						"11" => "jpx",
						"12" => "jb2",
						"13" => "swc",
						"14" => "iff"
					);

					$this->filetype = "image/" . $mimetypes[$imageinfo["2"]];
				}
			}
		}
	}

	// resize Image
	function resizeImage($newx, $newy) {
		if (!isset($this->imagehandle)) {
			$this->readimagefromstring();
		}

		if (!isset($this->xsize)) {
			$this->getimageinfo();
		}

		if ($this->xsize * $this->ysize == 0)
			$this->repairimageinfo();

		if ($this->uselib == "imagick") {
			if (!imagick_scale($this->imagehandle, $newx, $newy, "!")) {
				$reason = imagick_failedreason($handle);

				$description = imagick_faileddescription($handle);
				// todo: Build in error handler in imagegallib
				exit;
			}
		} else if ($this->uselib == "gd") {
			if ($this->gdversion >= 2.0) {
				$t = imagecreatetruecolor($newx, $newy);

				imagecopyresampled($t, $this->imagehandle, 0, 0, 0, 0, $newx, $newy, $this->xsize, $this->ysize);
			} else {
				$t = imagecreate($newx, $newy);

				$this->ImageCopyResampleBicubic($t, $this->imagehandle, 0, 0, 0, 0, $newx, $newy, $this->xsize, $this->ysize);
			}

			$this->imagehandle = $t;
		}

		// fill $this->image for writing or output
		$this->writeimagetostring();
		// reget sizes
		$this->getimageinfo();
		//set new sizes
		$this->xsize = $newx;
		$this->ysize = $newy;
		return true;
	}

	// rescale Image, almost the same as resize, but keeps apect ratio
	// bbx and bby give the boundary box
	function rescaleImage($bbx, $bby) {
		if (!isset($this->imagehandle)) {
			$this->readimagefromstring();
		}

		if (!isset($this->xsize)) {
			$this->getimageinfo();
		}

		if ($this->xsize * $this->ysize == 0)
			$this->repairimageinfo();

		if ($this->xsize > $this->ysize) {
			$tscale = ((int)$this->xsize / $bbx);
		} else {
			$tscale = ((int)$this->ysize / $bby);
		}

		$newx = round($this->xsize / $tscale);
		$newy = round($this->ysize / $tscale);
		return $this->resizeImage($newx, $newy);
	}

	function rotateimage($angle) {
		if ($this->uselib == "imagick") {
			//Imagick and GD have different opinion what is 90 degree. right or left?
			imagick_rotate($this->imagehandle, -$angle);
		} else if ($this->uselib == "gd") {
			if ($this->gdversion > 2.0) { //I know, it's PHP <= 4.3.0. It destroys images if u try to rotate them
				$this->imagehandle = imagerotate($this->imagehandle, $angle, 0);
			}
		}

		// update the $this->image
		$this->writeimagetostring();
		//get new sizex,sizey
		$this->oldxsize = $this->xsize;
		$this->oldysize = $this->ysize;
		$this->getimageinfo();
	}

	// function to determine supported image types
	// imagick has no function to get the supported image types
	function issupported($imagetype) {
		if ($this->uselib == "imagick") {
			//imagick can read everything ... we assume
			return true;
		} else if ($this->uselib == "gd") {
			switch ($imagetype) {
			case 'jpeg':
			case 'pjpeg':
			case 'jpg':
			case 'image/jpeg':
			case 'image/pjpeg':
			case 'image/jpg':
				return ($this->gdinfo["JPG Support"]);

				break;

			case 'png':
			case 'image/png':
				return ($this->gdinfo["PNG Support"]);

				break;

			case 'gif':
			case 'image/gif':
				return ($this->gdinfo["GIF Create Support"]);

				break;

			case 'bmp':
			case 'image/bmp':
				return ($this->gdinfo["WBMP Support"]);

				break;

			case 'xbm':
			case 'image/xbm':
				return ($this->gdinfo["XBM Support"]);

				break;

			default:
				return false;

				break;
			}
		}
	}

	// Batch image uploads todo
	function process_batch_image_upload($galleryId, $file, $user) {
		global $gal_match_regex;

		global $gal_nmatch_regex;
		global $gal_use_db;
		global $gal_use_dir;
		global $tmpDir;
		$numimages = 0;
		include_once ('lib/pclzip.lib.php');
		$archive = new PclZip($file);
		// Read Archive contents
		$ziplist = $archive->listContent();

		if (!$ziplist)
			return (false); // Archive invalid

		for ($i = 0; $i < sizeof($ziplist); $i++) {
			$file = $ziplist["$i"]["filename"];

			if (!$ziplist["$i"]["folder"]) {
				//copied
				$gal_info = $this->get_gallery($galleryId);

				$upl = 1;

				if (!empty($gal_match_regex)) {
					if (!preg_match("/$gal_match_regex/", $file, $reqs))
						$upl = 0;
				}

				if (!empty($gal_nmatch_regex)) {
					if (preg_match("/$gal_nmatch_regex/", $file, $reqs))
						$upl = 0;
				}
				//extract file
				$archive->extractByIndex($ziplist["$i"]["index"],
					$tmpDir, dirname($file)); //extract and remove (dangerous) pathname
				$file = basename($file);

				//unset variables
				unset ($this->filetype);
				unset ($this->xsize);
				unset ($this->ysize);

				//determine filetype and dimensions
				$this->getfileinfo($tmpDir . "/" . $file);

				$exp = substr($file, strlen($file) - 3, 3);
				// read image and delete it after
				$this->readimagefromfile($tmpDir . "/" . $file);
				unlink ($tmpDir . "/" . $file);

				if ($this->issupported($exp)) {
					// convert to handle
					$this->readimagefromstring();

					if ($this->validhandle()) {
						$this->getimageinfo();
					}
				}

				//if there is no mimetype, we don't got a image
				if (isset($this->filetype)) {
					if (!isset($this->xsize)) {
						$this->xsize = $this->ysize = 0;
					}

					$imageId = $this->insert_image($galleryId, $file,
						'', $file, $this->filetype, $this->image, $this->filesize, $this->xsize, $this->ysize, $user, '', '');

					$numimages++;
				}
			}
		}

		return $numimages;
	}

	function add_image_hit($id) {
		global $count_admin_pvs;

		global $user;

		if ($count_admin_pvs == 'y' || $user != 'admin') {
			$query = "update `tiki_images` set `hits`=`hits`+1 where `imageId`=?";

			$result = $this->query($query,array($id));
		}

		return true;
	}

	function add_gallery_hit($id) {
		global $count_admin_pvs;

		global $user;

		if ($count_admin_pvs == 'y' || $user != 'admin') {
			$query = "update `tiki_galleries` set `hits`=`hits`+1 where `galleryId`=?";

			$result = $this->query($query,array($id));
		}

		return true;
	}

	function ImageCopyResampleBicubic(&$dst_img, &$src_img, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h)
	// port to PHP by John Jensen July 10 2001 (updated 4/21/02) -- original code (in C, for the PHP GD Module) by jernberg@fairytale.se////
		{
		$palsize = ImageColorsTotal($src_img);

		for ($i = 0; $i < $palsize; $i++) { // get palette.
			$colors = ImageColorsForIndex($src_img, $i);

			ImageColorAllocate($dst_img, $colors['red'], $colors['green'], $colors['blue']);
		}

		$scaleX = ($src_w - 1) / $dst_w;
		$scaleY = ($src_h - 1) / $dst_h;
		$scaleX2 = (int)($scaleX / 2);
		$scaleY2 = (int)($scaleY / 2);

		for ($j = $src_y; $j < $dst_h; $j++) {
			$sY = (int)($j * $scaleY);

			$y13 = $sY + $scaleY2;

			for ($i = $src_x; $i < $dst_w; $i++) {
				$sX = (int)($i * $scaleX);

				$x34 = $sX + $scaleX2;
				$color1 = ImageColorsForIndex($src_img, ImageColorAt($src_img, $sX, $y13));
				$color2 = ImageColorsForIndex($src_img, ImageColorAt($src_img, $sX, $sY));
				$color3 = ImageColorsForIndex($src_img, ImageColorAt($src_img, $x34, $y13));
				$color4 = ImageColorsForIndex($src_img, ImageColorAt($src_img, $x34, $sY));
				$red = ($color1['red'] + $color2['red'] + $color3['red'] + $color4['red']) / 4;
				$green = ($color1['green'] + $color2['green'] + $color3['green'] + $color4['green']) / 4;
				$blue = ($color1['blue'] + $color2['blue'] + $color3['blue'] + $color4['blue']) / 4;
				ImageSetPixel($dst_img, $i + $dst_x - $src_x, $j + $dst_y - $src_y, ImageColorClosest($dst_img, $red, $green, $blue));
			}
		}
	}

	function store_image_data($overwrite = false) {
		global $gal_use_dir;

		global $gal_use_db;

		$size = strlen($this->image);
		$fhash = "";

		if ($gal_use_db == 'y') {
			// Prepare to store data in database
			$data = addslashes($this->image);
		} else {
			// Store data in directory
			switch ($this->type) {
			case 't':
				$ext = ".thumb";

				break;

			case 's':
				$ext = ".scaled_" . $this->xsize . "x" . $this->ysize;

				break;

			case 'b':
				// for future use
				$ext = ".backup";

				break;

			default:
				$ext = '';
			}

			$fhash = $this->path . $ext; //Path+extension
			@$fw = fopen($gal_use_dir . $fhash, "wb");

			if (!$fw) {
				return false;
			}

			fwrite($fw, $this->image);
			fclose ($fw);
			$data = '';
		}

		$this->filename = $this->xsize . "x" . $this->ysize . "_" . $this->name; // rebuild filename for downloading images
		// insert data
		$fn = addslashes($this->filename);

		if ($overwrite) {
			//overwrites all except the colums of the primary key
			//if there is no oldxsize, we use xsize
			if (!isset($this->oldxsize)) {
				$this->oldxsize = $this->xsize;

				$this->oldysize = $this->ysize;
			}

			$query = "update `tiki_images_data` set `filetype`=?,
				filename=?,data=?,
				filesize=? ,xsize=?, 
				ysize=?
			where
			imageId=? and type=? and
			xsize=? and ysize=?";
			$bindvars=array($this->filetype,$this->filename,$data,$size,$this->xsize,$this->ysize,$this->imageId,$this->type,$this->oldxsize,$this->oldysize);
		} else {
			$query = "insert into `tiki_images_data`(imageId,xsize,ysize,
                                type,filesize,filetype,filename,data)
                        values (?,?,?,?,?,?,?,?)";
			$bindvars=array($this->imageId,$this->xsize,$this->ysize,$this->type,$size,$this->filetype,$this->filename,$data);
		}

		$result = $this->query($query,$bindvars);
		return true;
	}

	function rebuild_image($imageid, $itype, $xsize, $ysize) {
		$galid = $this->get_gallery_from_image($imageid);

		//we don't rebuild original images
		if ($itype == 'o')
			return false;

		//if it is a scaled image, test the gallery settings
		if ($itype == 's') {
			$scaleinfo = $this->get_gallery_scale_info($galid);

			$hasscale = false;

			while (list($num, $sci) = each($scaleinfo)) {
				if ($sci["xsize"] == $xsize && $sci["ysize"] == $ysize) {
					$hasscale = true;

					$newx = $sci["xsize"];
					$newy = $sci["ysize"];
				}
			}

			if (!$hasscale)
				return false;
		}

		// now we can start rebuilding the image
		global $gal_use_dir;
		global $gal_use_db;
		//if(!function_exists("ImageCreateFromString")) return false;
		#get image and other infos
		$this->get_image($imageid);
		$galinfo = $this->get_gallery_info($galid);

		// determine new size
		if ($itype == 't') {
			$newx = $galinfo["thumbSizeX"];

			$newy = $galinfo["thumbSizeY"];
		}

		// do it
		if ($this->issupported($this->filetype)) {
			if (!$this->rescaleImage($newx, $newy)) {
				die;
			}
		} else {
			// filetype is not supported, but we store the data
			// assiming newx,newy as new size
			$this->xsize = $newx;

			$this->ysize = $newy;
		}

		// we always rescale to jpegs.
		$t_type = 'image/jpeg';

		// some more infos
		$filename = $this->filename; // filename of original image
		$this->type = $itype;

		//store
		$this->store_image_data();

		//return new size
		$newsize["xsize"] = $this->xsize;
		$newsize["ysize"] = $this->ysize;
		return $newsize;
	}

	function rebuild_thumbnails($galleryId) {
		global $gal_use_dir;

		global $gal_use_db;

		// rewritten by flo
		$query = "select `imageId`, `path` from `tiki_images` where `galleryId`=?";
		$result = $this->query($query,array($galleryId));

		while ($res = $result->fetchRow()) {
			$query2 = "delete from `tiki_images_data` where `imageId`=? and `type`=?";

			$result2 = $this->query($query2,array($res["imageId"],'t'));

			if (strlen($res["path"]) > 0) {
				$ftn = $gal_use_dir . $res["path"] . ".thumb";

				if (file_exists($ftn)) {
					unlink ($ftn);
				}
			}
		}

		return true;
	}

	function rebuild_scales($galleryId, $imageId = -1) {
		// doesn't really rebuild, it deletes the schales and thumbs for 
		// automatic rebuild
		// give either a galleryId for rebuild complete gallery or
		// a imageId for a image rebuild
		if ($imageId == -1) {
			//gallery mode
			//mysql does'nt have subqueries. Bad.
			$query1 = "select `imageId` from `tiki_images` where `galleryId`=?";

			$result1 = $this->query($query1,array($galleryId));

			while ($res = $result->fetchRow()) {
				$query2 = "delete from `tiki_images_data` where `ImageId`=? and not `type`=?";

				$result2 = $this->query($query2,array($res["imageId"],'o'));
			}
		} else {
			//image mode
			$query = "delete from `tiki_images_data` where `ImageId`=? and not `type`=?";

			$result = $this->query($query,array($ImageId,'o'));
		}
	}

	function edit_image($id, $name, $description) {
		$name = strip_tags($name);

		$description = strip_tags($description);
		$query = "update `tiki_images` set `name`=?, description=? where `imageId` = ?";
		$result = $this->query($query,array($name,$description,$id));
		return true;
	}

	function insert_image($galleryId, $name, $description, $filename, $filetype, &$data, $size, $xsize, $ysize, $user, $t_data, $t_type) {
		global $gal_use_db;

		global $gal_use_dir;
		$name = addslashes(strip_tags($name));
		$filename = addslashes($filename);
		$description = addslashes(strip_tags($description));
		$now = date("U");
		$path = '';

		if ($gal_use_db == 'y') {
			// Prepare to store data in database
			$data = addslashes($data);

			$t_data = addslashes($t_data);
		} else {
			// Store data in directory
			$fhash = md5(uniqid($filename));

			@$fw = fopen($gal_use_dir . $fhash, "wb");

			if (!$fw) {
				return false;
			}

			fwrite($fw, $data);
			fclose ($fw);

			if (sizeof($t_data) > 0) {
				@$fw = fopen($gal_use_dir . $fhash . '.thumb', "wb");

				if (!$fw) {
					return false;
				}

				fwrite($fw, $t_data);
				fclose ($fw);
				$t_data = '';
			}

			$data = '';
			$path = $fhash;
		}

		$query = "insert into `tiki_images`(`galleryId`,`name`,`description`,`user`,`created`,`hits`,`path`)
                          values(?,?,?,?,?,?,?)";
		$result = $this->query($query,array($galleryId,$name,$description,$user,$now,0,$path));
		$query = "select max(`imageId`) from `tiki_images` where `created`=?";
		$imageId = $this->getOne($query,array($now));
		// insert data
		$query = "insert into `tiki_images_data`(`imageId`,`xsize`,`ysize`,
                                `type`,`filesize`,`filetype`,`filename`,`data`)
                        values (?,?,?,?,?,?,?,?)";
		$result = $this->query($query,array($imageId,$xsize,$ysize,'o',$size,$filetype,$filename,$data));

		// insert thumb
		if (sizeof($t_data) > 1) {
			$query = "insert into `tiki_images_data`(`imageId`,`xsize`,`ysize`,
                                `type`,`filesize`,`filetype`,`filename`,`data`)
                        values (?,?,?,?,?,?,?,?)";

			$result = $this->query($query,array($imageId,$xsize,$ysize,'t',$size,$t_type,$filename,$t_data));
		}

		$query = "update `tiki_galleries` set `lastModif`=? where `galleryId`=?";
		$result = $this->query($query,array($now,$galleryId));
		return $imageId;
	}

	function rotate_image($id, $angle) {
		//get image
		global $gal_use_dir;

		global $gal_use_db;
		$this->get_image($id);

		$this->rotateimage($angle);
		$this->store_image_data(true);
		// delete all scaled images. Will be rebuild when requested
		$query = "delete from `tiki_images_data` where `imageId`=? and `type` !=?";
		$result = $this->query($query,array($id,'o'));
	}

	function rotate_right_image($id) {
		$this->rotate_image($id, 270);
	}

	function rotate_left_image($id) {
		$this->rotate_image($id, 90);
	}

	function remove_image($id) {
		global $gal_use_dir;

		$path = $this->getOne("select `path` from `tiki_images` where `imageId`=?",array($id));

		if ($path) {
			@unlink ($gal_use_dir . $path);

			@unlink ($gal_use_dir . $path . '.thumb');
		//todo: remove scaled images
		}

		$query = "delete from `tiki_images` where `imageId`=?";
		$result = $this->query($query,array($id));
		$query = "delete from `tiki_images_data` where `imageId`=?";
		$result = $this->query($query,array($id));
		return true;
	}

	function get_images($offset, $maxRecords, $sort_mode, $find, $galleryId = -1) {

		if ($find) {
			$findesc = '%' . $find . '%';

			$mid = " and (`name` like ? or `description` like ?)";
			$bindvars=array('o',$findesc,$findesc);
		} else {
			$mid = "";
			$bindvars=array('o');
		}

		$midcant = "";
		$cantvars=array();
		if ($galleryId != -1 && is_numeric($galleryId)) {
			$mid .= " and i.`galleryId`=? ";
			$bindvars[]=$galleryId;
			$midcant = "where `galleryId`=? ";
			$cantvars[]=$galleryId;
		}

		$query = "select i.`path` ,i.`imageId`,i.`name`,i.`description`,i.`created`,
                d.`filename`,d.`filesize`,d.`xsize`,d.`ysize`,
                i.`user`,i.`hits`
                from `tiki_images` i , `tiki_images_data` d
                 where i.`imageId`=d.`imageId`
                 and d.`type`=?
                $mid
                order by ".$this->convert_sortmode($sort_mode);
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$query_cant = "select count(*) from `tiki_images` $midcant";
		$cant = $this->getOne($query_cant,$cantvars);
		$retval["cant"] = $cant;
		return $retval;
	}

	function get_first_image($sort_mode, $find, $galleryId = -1) {

		if ($find) {
			$findesc = '%' . $find . '%';

			$mid = " and (`name` like ? or `description` like ?)";
			$bindvars=array('o',$findesc,$findesc);
		} else {
			$mid = "";
			$bindvars=array('o');
		}

		$midcant = "";
		$cantvars=array();

		if ($galleryId != -1 && is_numeric($galleryId)) {
			$mid .= " and i.`galleryId`=? ";
			$bindvars[]=$galleryId;
			$midcant = "where `galleryId`=? ";
			$cantvars[]=$galleryId;
		}

		$query = "select i.`imageId`
                from `tiki_images` i , `tiki_images_data` d
                 where i.`imageId`=d.`imageId`
                 and d.`type`=?
                $mid
                order by ".$this->convert_sortmode($sort_mode);
		$result = $this->query($query,$bindvars,1,0);
		$res = $result->fetchRow();
		return $res['imageId'];
	}

	function get_last_image($sort_mode, $find, $galleryId = -1) {
		if (strstr($sort_mode, 'asc')) {
			$sort_mode = str_replace('asc', 'desc', $sort_mode);
		} else {
			$sort_mode = str_replace('desc', 'asc', $sort_mode);
		}

		if ($find) {
			$findesc = '%' . $find . '%';

			$mid = " and (`name` like ? or `description` like ?)";
			$bindvars=array('o',$findesc,$findesc);
		} else {
			$mid = "";
			$bindvars=array('o');
		}

		$midcant = "";
		$cantvars=array();

		if ($galleryId != -1 && is_numeric($galleryId)) {
                        $mid .= " and i.`galleryId`=? ";
                        $bindvars[]=$galleryId;
                        $midcant = "where `galleryId`=? ";
                        $cantvars[]=$galleryId;
		}

		$query = "select i.`imageId`
                from `tiki_images` i , `tiki_images_data` d
                 where i.`imageId`=d.`imageId`
                 and d.`type`=?
                $mid
                order by ".$this->convert_sortmode($sort_mode);
		$result = $this->query($query,$bindvars,1,0);
		$res = $result->fetchRow();
		return $res['imageId'];
	}

	function list_images($offset, $maxRecords, $sort_mode, $find) {
		return $this->get_images($offset, $maxRecords, $sort_mode, $find);
	}

	function get_gallery_owner($galleryId) {
		$query = "select `user` from `tiki_galleries` where `galleryId`=?";

		$user = $this->getOne($query,array($galleryId));
		return $user;
	}

	function get_gallery_from_image($imageid) {
		$query = "select `galleryId` from `tiki_images` where `imageId`=?";

		$galid = $this->getOne($query,array($imageid));
		return $galid;
	}

	function move_image($imgId, $galId) {
		$query = "update `tiki_images` set `galleryId`=? where `imageId`=?";

		$result = $this->query($query,array($galId,$imgId));
		return true;
	}

	function get_image_info($id, $itype = 'o', $xsize = 0, $ysize = 0) {
		// code may be merged with get_image
		$mid = "";

		if ($xsize != 0) {
			$mid = "and d.`xsize`=? ";
			$bindvars=array($id,$itype,$xsize);
		} elseif ($ysize != 0) {
			$mid .= "and d.`ysize`=? ";
			$bindvars=array($id,$itype,$ysize);
		} elseif ($xsize != 0 && $ysize == $xsize) {
			// we don't know yet.
			$mid = "and greatest(d.`xsize`,d.`ysize`) = greatest(?,?) ";
			$bindvars=array($id,$itype,$xsize,$ysize);
		} else {
			$bindvars=array($id,$itype);
		}

		$query = "select i.`imageId`, i.`galleryId`, i.`name`,
                     i.`description`, i.`created`, i.`user`,
                     i.`hits`, i.`path`,
                     d.`xsize`,d.`ysize`,d.`type`,d.`filesize`,
                     d.`filetype`,d.`filename`
                 from `tiki_images` i, `tiki_images_data` d where
                     i.`imageId`=? and d.`imageId`=i.`imageId`
                     and d.`type`=?
                     $mid";
		$result = $this->query($query,$bindvars);
		$res = $result->fetchRow();
		return $res;
	}

	// Add an option to stablish Image size (x,y)
	function get_image($id, $itype = 'o', $xsize = 0, $ysize = 0) {
		global $gal_use_db;

		global $gal_use_dir;
		$mid = "";

		if ($itype == 't') {
			$galid = $this->get_gallery_from_image($id);

			$galinfo = $this->get_gallery_info($galid);
			$xsize = $galinfo["thumbSizeX"];
			$ysize = $galinfo["thumbSizeY"];
		}

		if ($xsize != 0) {
			$mid = "and d.`xsize`=? ";
			$bindvars=array($id,$itype,$xsize);
		} elseif ($ysize != 0) {
			$mid .= "and d.`ysize`=? ";
			$bindvars=array($id,$itype,$ysize);
		} elseif ($xsize != 0 && $ysize == $xsize) {
			// we don't know yet.
			$mid = "and greatest(d.`xsize`,d.`ysize`) = greatest(?,?) ";
			$bindvars=array($id,$itype,$xsize,$ysize);
		} else {
			$bindvars=array($id,$itype);
		}

		$query = "select i.`imageId`, i.`galleryId`, i.`name`,
                     i.`description`, i.`created`, i.`user`,
                     i.`hits`, i.`path`,
                     d.`xsize`,d.`ysize`,d.`type`,d.`filesize`,
                     d.`filetype`,d.`filename`,d.`data`
                 from `tiki_images` i, `tiki_images_data` d where
                     i.`imageId`=? and d.`imageId`=i.`imageId`
                     and d.`type`=?
                     $mid";

		$result = $this->query($query,$bindvars);
		$res = $result->fetchRow();

		$this->imageId = $res["imageId"];
		$this->galleryId = $res["galleryId"];
		$this->name = $res["name"];
		$this->description = $res["description"];
		$this->created = $res["created"];
		$this->user = $res["user"];
		$this->hits = $res["hits"];
		$this->path = $res["path"];
		$this->xsize = $res["xsize"];
		$this->ysize = $res["ysize"];
		$this->type = $res["type"];
		$this->filesize = $res["filesize"];
		$this->filetype = $res["filetype"];
		$this->filename = $res["filename"];

		# build scaled images or thumb if not available
		if ($itype != 'o' && !isset($this->imageId)) {
			if ($newsize = $this->rebuild_image($id, $itype, $xsize, $ysize)) {
				return $this->get_image($id, $itype, $newsize["xsize"], $newsize["ysize"]);
			}
		}

		// get image data from fs
		if ($res["data"] == '') {
			switch ($itype) {
			case 't':
				$ext = ".thumb";

				break;

			case 's':
				$ext = ".scaled_" . $res["xsize"] . "x" . $res["ysize"];

				break;

			case 'b':
				// for future use
				$ext = ".backup";

				break;

			default:
				$ext = '';
			}

			// If the image was a .gif then the thumbnail has 0 bytes if the thumbnail
			// is empty then use the full image as thumbnail
			//  if($ext==".thumb" && filesize($gal_use_dir.$res["path"].$ext)==0 ) {
			//   $ext='';
			//}
			$this->readimagefromfile($gal_use_dir . $res["path"] . $ext);
		} else {
			$this->image = $res["data"];
		}

		if (!isset($this->imagehandle))
			$this->readimagefromstring();

		return $res;
	}

	function get_image_thumb($id) {
		return $this->get_image($id, 't');
	}

	function replace_gallery($galleryId, $name, $description, $theme, $user, $maxRows, $rowImages, $thumbSizeX, $thumbSizeY, $public, $visible = 'y') {
		// if the user is admin or the user is the same user and the gallery exists then replace if not then
		// create the gallary if the name is unused.
		$name = strip_tags($name);

		$description = strip_tags($description);
		$now = date("U");

		if ($galleryId > 0) {
			//$res = $result->fetchRow();
			//if( ($user == 'admin') || ($res["user"]==$user) ) {
			$query = "update `tiki_galleries` set `name`=?,`visible`=?, `maxRows`=? , `rowImages`=?, 
                `thumbSizeX`=?, `thumbSizeY`=?, `description`=?, `theme`=?, 
                `lastModif`=?, `public`=? where `galleryId`=?";

			$result = $this->query($query,array($name,$visible,$maxRows,$rowImages,$thumbSizeX,$thumbSizeY,$description,$theme,$now,$public,$galleryId));
		} else {
			// Create a new record
			$query = "insert into `tiki_galleries`(`name`,`description`,`theme`,`created`,`user`,`lastModif`,`maxRows`,`rowImages`,`thumbSizeX`,`thumbSizeY`,`public`,`hits`,`visible`)
                                    values (?,?,?,?,?,?,?,?,?,?,?,?,?)";
			$bindvars=array($name,$description,$theme,(int) $now,$user,(int) $now,(int) $maxRows,(int) $rowImages,(int) $thumbSizeX,(int) $thumbSizeY,$public,0,$visible);
			$result = $this->query($query,$bindvars);
			$galleryId = $this->getOne("select max(`galleryId`) from `tiki_galleries` where `name`=? and `created`=?",array($name,(int) $now));
		}

		return $galleryId;
	}

	function add_gallery_scale($galleryId, $xsize, $ysize) {
		$query = "insert into `tiki_galleries_scales`(`galleryId`,`xsize`,`ysize`)
            values(?,?,?)";

		$result = $this->query($query,array($galleryId,$xsize,$ysize));
	}

	function remove_gallery_scale($galleryId, $xsize = 0, $ysize = 0) {
		$mid = "";
		$bindvars=array((int) $galleryId);
		if ($xsize != 0) {
			$mid = " and `xsize`=? ";
			$bindvars=array((int) $galleryId,(int) $xsize);
		}
		if ($ysize != 0) {
			$mid .= " and `ysize`=? ";
			$bindvars=array((int) $galleryId,(int) $ysize);
		}
		$query = "delete from `tiki_galleries_scales` where
            `galleryId`=? $mid";
		$result = $this->query($query,$bindvars);
	}

	function remove_gallery($id) {
		global $gal_use_dir;

		$query = "select `imageId`,path from `tiki_images` where `galleryId`=?";
		$result = $this->query($query,array((int) $id));

		while ($res = $result->fetchRow()) {
			$path = $res["path"];

			$query2 = "select `xsize`,`ysize`,`type` from `tiki_images_data` where `imageId`=?";
			$result2 = $this->query($query2,array($res["imageId"]));

			while ($res2 = $result2->fetchRow()) {
				switch ($res2["type"]) {
				case 't':
					$ext = ".thumb";

					break;

				case 's':
					$ext = ".scaled_" . $res2["xsize"] . "x" . $res2["ysize"];

					break;

				case 'b':
					// for future use
					$ext = ".backup";

					break;

				default:
					$ext = '';
				}

				if ($path) {
					@unlink ($gal_use_dir . $path . $ext);
				}
			}

			$query3 = "delete from `tiki_images_data` where `imageId`=?";
			$result3 = $this->query($query3,array($res["imageId"]));
		}

		$query = "delete from `tiki_galleries` where `galleryId`=?";
		$result = $this->query($query,array((int) $id));
		$query = "delete from `tiki_images` where `galleryId`=?";
		$result = $this->query($query,array((int) $id));
		$this->remove_gallery_scale($id);
		$this->remove_object('image gallery', $id);
		return true;
	}

	function get_gallery_info($id) {
		$query = "select * from `tiki_galleries` where `galleryId`=?";

		$result = $this->query($query,array($id));
		$res = $result->fetchRow();
		return $res;
	}

	function get_gallery_scale_info($id) {
		$query = "select * from `tiki_galleries_scales` where `galleryId`=?
              order by `xsize`*`ysize` asc";

		$result = $this->query($query,array((int) $id));
		$resa = array();

		while ($res = $result->fetchRow()) {
			$resa[] = $res;
		}

		return $resa;
	}

	function get_gallery_next_scale($id, $xsize = 0, $ysize = 0) {
		$xy = $xsize * $ysize;

		$query = "select * from `tiki_galleries_scales` where `galleryId`=?
              and `xsize`*`ysize` > ? order by `xsize`*`ysize` asc";
		$result = $this->query($query,array($id,$xy));
		$res = $result->fetchRow();
		return $res;
	}

	//Capture Images from wiki, blogs, ....
	function capture_images($data) {
		$cacheimages = $this->get_preference("cacheimages", 'y');

		if ($cacheimages != 'y')
			return $data;

		preg_match_all("/src=\"([^\"]+)\"/", $data, $reqs1);
		preg_match_all("/src=\'([^\']+)\'/", $data, $reqs2);
		preg_match_all("/src=([A-Za-z0-9\:\?\=\/\\\.\-\_]+)\}/", $data, $reqs3);
		preg_match_all("/src=([A-Za-z0-9\:\?\=\/\\\.\-\_]+) /", $data, $reqs4);
		$merge = array_merge($reqs1[1], $reqs2[1], $reqs3[1], $reqs4[1]);
		$merge = array_unique($merge);
		//print_r($merge);
		// Now for each element in the array capture the image and
		// if the capture was successful then change the reference to the
		// internal image
		$page_data = $data;

		foreach ($merge as $img) {
			// This prevents caching images
			if (!strstr($img, "show_image.php") && !strstr($img, "nocache")) {
				//print("Procesando: $img<br/>");
				@$fp = fopen($img, "r");

				if ($fp) {
					$data = '';

					while (!feof($fp)) {
						$data .= fread($fp, 4096);
					}

					//print("Imagen leida:".strlen($data)." bytes<br/>");
					fclose ($fp);

					if (strlen($data) > 0) {
						$url_info = parse_url($img);

						$pinfo = pathinfo($url_info["path"]);
						$type = "image/" . $pinfo["extension"];
						$name = $pinfo["basename"];
						$size = strlen($data);
						$url = $img;

						if (function_exists("ImageCreateFromString") && (!strstr($type, "gif"))) {
							$img = imagecreatefromstring($data);

							$size_x = imagesx($img);
							$size_y = imagesy($img);
							// Fix the ratio values for system gallery
							$gal_info["thumbSizeX"] = 90;
							$gal_info["thumbSizeY"] = 90;

							if ($size_x > $size_y)
								$tscale = ((int)$size_x / $gal_info["thumbSizeX"]);
							else
								$tscale = ((int)$size_y / $gal_info["thumbSizeY"]);

							$tw = ((int)($size_x / $tscale));
							$ty = ((int)($size_y / $tscale));

							if (chkgd2()) {
								$t = imagecreatetruecolor($tw, $ty);

								imagecopyresampled($t, $img, 0, 0, 0, 0, $tw, $ty, $size_x, $size_y);
							} else {
								$t = imagecreate($tw, $ty);

								$this->ImageCopyResampleBicubic($t, $img, 0, 0, 0, 0, $tw, $ty, $size_x, $size_y);
							}

							// CHECK IF THIS TEMP IS WRITEABLE OR CHANGE THE PATH TO A WRITEABLE DIRECTORY
							//$tmpfname = 'temp.jpg';
							$tmpfname = tempnam("/tmp", "FOO"). '.jpg';
							imagejpeg($t, $tmpfname);
							// Now read the information
							$fp = fopen($tmpfname, "rb");
							$t_data = fread($fp, filesize($tmpfname));
							fclose ($fp);
							unlink ($tmpfname);
							$t_pinfo = pathinfo($tmpfname);
							$t_type = $t_pinfo["extension"];
							$t_type = 'image/' . $t_type;

							$imageId = $this->insert_image(0, '', '', $name, $type, $data, $size, $size_x, $size_y, 'admin', $t_data, $t_type);
						//print("Imagen generada en $imageId<br/>");
						} else {
							//print("No GD detected generating image without thumbnail<br/>");
							$imageId = $this->insert_image(0, '', '', $name, $type, $data, $size, 100, 100, 'admin', '', '');
						//print("Imagen en $imageId<br/>");
						}

						// Now change it!
						//print("Changing $url to imageId: $imageId");
						$uri = parse_url($_SERVER["REQUEST_URI"]);
						$path = str_replace("tiki-editpage", "show_image", $uri["path"]);
						$page_data = str_replace($url, httpPrefix(). $path . '?id=' . $imageId, $page_data);
					} // if strlen
				} // if $fp
			}
		} // foreach

		return $page_data;
	}
}

global $imagegallib;
$imagegallib = new ImageGalsLib($dbTiki);

?>
