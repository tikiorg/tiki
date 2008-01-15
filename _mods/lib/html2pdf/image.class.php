<?php

// Note that REAL cache should check for Last-Modified HTTP header at least;
// As I don't like idea of implementing the full-scaled HTTP protocol library
// and curl extension is very rare, this implementation of cache is very simple.
// Cache is cleared after the script finishes it work!

// Also, it can have problems with simultaneous access to the images.

// The class responsible for downloading and caching images
// as PHP does not support the static variables, we'll use a global variable
// containing all cached objects; note that cache consumes memory!
//
$g_image_cache = array();

class Image {
  // Static funcion; checks if given URL is already cached and either returns 
  // cached object or downloads the requested image
  //
  function get($url) {
    global $g_image_cache;

    // Check if this URL have been cached
    //
    if (isset($g_image_cache[$url])) {
      //      return do_image_open($g_image_cache[$url]);
      return $g_image_cache[$url]['handle'];
    };

    // Download image; we should do it before we call do_image_open,
    // as it tries to open image file twice: first to determine image type 
    // and second to actually create the image - PHP url wrappers do no caching 
    // at all
    //
    $filename = Image::make_cache_filename($url);

    // REQUIRES: PHP 4.3.0+
    // we suppress warning messages, as missing image files will cause 'copy' to print 
    // several warnings
    // 
    if (!@copy($url, $filename)) { 
      return null; 
    };
    
    // register it in the cached objects array
    //
    // $g_image_cache[$url] = $filename;
    $g_image_cache[$url] = array('filename' => $filename,
                                 'handle' => do_image_open($filename));
    
    // return image
    //
    // return do_image_open($filename);
    return $g_image_cache[$url]['handle'];
  }

  // Makes the filename to contain the cached version of URL
  // 
  function make_cache_filename($url) {
    // We cannot use the $url as an cache image name as it could be longer than 
    // allowed file name length (especially after escaping specialy symbols)
    // thus, we generate long almost random 32-character name using the md5 hash function
    //
    return CACHE_DIR.md5(time() + $url + rand());
  }

  // Checks if cache directory is available
  //
  function check_cache_dir() {
    // TODO: some cool easily understandable error message for the case 
    // image cache directory cannot be created or accessed
    
    // Check if CACHE_DIR exists
    //
    if (!is_dir(CACHE_DIR)) { 
      // Cache directory does not exists; try to create it (with read/write rightss for the owner only)
      //
      if (!mkdir(CACHE_DIR, 0700)) { die("Cache directory cannot be created"); }
    };

    // Check if we can read and write to the CACHE_DIR
    //
    if (!is_readable(CACHE_DIR) || !is_writeable(CACHE_DIR)) {
      // omg. Cache directory exists, but useless
      //
      die("Check cache directory permissions; cannot either read or write to directory cache");
    };
    
    return;
  }

  // Clears the image cache (as we're neither implemented checking of Last-Modified HTTP header nor 
  // provided the means of limiting the cache size
  //
  // TODO: Will cause problems with simultaneous access to the same images  
  //
  function clear_cache() {
    global $g_image_cache;

    foreach ($g_image_cache as $key => $value) {
      unlink($value['filename']);
    };
    $g_image_cache = array();
  }
}
?>