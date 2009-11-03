<?php

require_once("tiki-setup.php");
require_once("lib/cache/cachelib.php");

$page = $_POST['page'];
$locale = $_POST['lang'];

if ( $prefs['feature_wiki_screencasts'] == 'y' && (isset($tiki_p_upload_screencast)) && ($tiki_p_upload_screencast == 'y')) {

  require_once("lib/screencasts/screencastlib.php");
  
  // Get a page hash identical to what images are assigned
  $pageHash = md5( $locale . '/' . ( (strpos($page,'*')===0) ? substr($page,1) : $page) );
  $hashedFileName = join('-', array($pageHash, time(), rand(1,1000)));
  
  $screencastErrors = array();
  
  if ( isset($_FILES['flash_screencast']) ) {
    $cachelib->invalidate($pageHash);
    
    $xml = '<html><body>' . "\n" . '<response>';
    $status = "201";
    $msg = tra("Your screencast has been uploaded successfully!");
    
    if ( count($_FILES['flash_screencast']['tmp_name']) > 1 )
      $msg = tra("Your screencasts have been uploaded successfully!");

    for ( $i = 0; $i <= count($_FILES['flash_screencast']['name']); $i++ ) {
      
      if ( $_FILES['flash_screencast']['size'][$i] > $prefs['feature_wiki_screencasts_max_size'] ||
          $_FILES['flash_screencast']['error'][$i] == 1 || $_FILES['flash_screencast']['error'][$i] == 2 ) {
          $status = '400';
          $msg = tra("The file you selected is too large to upload") . ' (' . htmlentities($_FILES['flash_screencast']['name'][$i], ENT_QUOTES) . ')';
          continue;
      }
      
      if ( is_uploaded_file($_FILES['flash_screencast']['tmp_name'][$i]) ) {
        
        if ( preg_match("/\.((swf)|(flv))$/", $_FILES['flash_screencast']['name'][$i], $ext) ) {
          if ( !$screencastlib->add($_FILES['flash_screencast']['tmp_name'][$i], $hashedFileName . "-" . $i . "." . $ext[1] ) ) {
            $status = "409";
            $msg = tra("An unexpected error occurred while uploading your flash screencast!");
          }
        } else {
          $status = "400";
          $msg = tra("Incorrect file extension was used for your flash screencast, expecting .swf or .flv");     
        }
    
        if ( isset($_FILES['ogg_screencast']) && $_FILES['ogg_screencast']['name'][$i]) {
          
          if ( $_FILES['ogg_screencast']['size'][$i] >= $prefs['feature_wiki_screencasts_max_size'] ||
            $_FILES['ogg_screencast']['error'][$i] == 1 || $_FILES['ogg_screencast']['error'][$i] == 2 ) {
            $status = "400";
            $msg = tra("The file you selected is too large to upload") . ' (' . htmlentities($_FILES['ogg_screencast']['name'][$i], ENT_QUOTES) . ')';
            continue;
          }  
          
          if ( is_uploaded_file($_FILES['flash_screencast']['tmp_name'][$i]) ) {
          
            if ( preg_match("/\.(ogg)$/", $_FILES['ogg_screencast']['name'][$i], $ext) ) {
              if ( !$screencastlib->add($_FILES['ogg_screencast']['tmp_name'][$i], $hashedFileName . "-" . $i . "." .  $ext[1])) {
                $status = "409";
                $msg = tra("An unexpected error occurred while uploading your Ogg screencast!");
              }
            } else {
              $status = "400";
              $msg = tra("Incorrect file extension was used for your 0gg screencast, expecting .ogg");
            }
          }
        }
      }
    }
    
    $xml .= '<status>' . $status . '</status>' . "\n";
    $xml .= '<message>' . $msg . '</message>' . "\n";
  }
  
  if ( $cachelib->isCached($pageHash) ) {
    $videos = unserialize($cachelib->getCached($pageHash));
  } else {
    $videos = $screencastlib->find($pageHash, true);
    $cachelib->cacheItem($pageHash, serialize($videos));
  }
  
  if ( $videos ) {
    $xml .= '<videos>' . "\n";
    foreach ( $videos as $v ) {
      $xml .= '<video>' . $v . '</video>' . "\n";
    }
    
    $xml .= '</videos>';
  }
    
  $xml .= '</response></body></html>';
  echo $xml;
  
}
