<?php
// Initialization
require_once('tiki-setup.php');

if($feature_games != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}


if($tiki_p_play_games != 'y') {
    $smarty->assign('msg',tra("You dont have permission to use this feature"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
}

$smarty->assign('uploadform','n');
if(isset($_REQUEST["uploadform"])&&$tiki_p_admin_games=='y') {
  $smarty->assign('uploadform','y');
}

if(isset($_REQUEST["upload"])) {
  if(isset($_FILES['userfile1']) && is_uploaded_file($_FILES['userfile1']['tmp_name']) && isset($_FILES['userfile2']) && is_uploaded_file($_FILES['userfile2']['tmp_name'])) {
  
    $name1 = $_FILES['userfile1']['name'];
    $name2 = $_FILES['userfile1']['name'];
    $parts=explode('.',$name2);
    $namec=implode('.',Array($parts[0],$parts[1]));
    if($namec!=$name1) {
        $smarty->assign('msg',                                                                                                          tra("The thumbnail name must be").
        " $name1.gif ".
        tra("or").
        " $name1.jpg ".
        tra("or").
        " $name1.png");

      $smarty->display("styles/$style_base/error.tpl");
      die;  
    }
    
    
  
    @$fp = fopen($_FILES['userfile1']['tmp_name'],"rb");
    $name = $_FILES['userfile1']['name'];
    @$fw = fopen("games/flash/$name","wb");
    if($fp && $fw) {
      while(!feof($fp)) {
        $data=fread($fp,8192);
        fwrite($fw,$data);
      }
      fclose($fp);
      fclose($fw);
    }
    
    @$fp = fopen($_FILES['userfile2']['tmp_name'],"rb");
    $name = $_FILES['userfile2']['name'];
    @$fw = fopen("games/thumbs/$name","wb");
    if($fp && $fw) {
      while(!feof($fp)) {
        $data=fread($fp,8192);
        fwrite($fw,$data);
      }
      fclose($fp);
      fclose($fw);
    }
    
    @$fw = fopen("games/thumbs/$name".".txt","wb");    
    if($fw) {
      fwrite($fw,$description);
      fclose($fw);
    }
  }
}



$smarty->assign('editgame','n');
if(isset($_REQUEST["edit"]) && $tiki_p_admin_games=='y') {
  $smarty->assign('editgame','y'); 
  $smarty->assign('editable', $_REQUEST["edit"]);
  $file=$_REQUEST["edit"];
  $data = '';
  @$fp = fopen("games/thumbs/$file".'.txt',"rb");
  if($fp) {
    $data = fread($fp,filesize("games/thumbs/$file".'.txt'));
    fclose($fp);
  }
  $smarty->assign('data',$data);
}

if(isset($_REQUEST["save"]) && $tiki_p_admin_games=='y') {
  $file = $_REQUEST["editable"];
  @$fp = fopen("games/thumbs/$file".'.txt',"w");
  if($fp) {
    fwrite($fp,$_REQUEST["description"]);
    fclose($fp);
  }
}

if(isset($_REQUEST["remove"]) && $tiki_p_admin_games=='y') {
  $game = $_REQUEST["remove"];
  $parts=explode('.',$game);
  $source='games/flash/'.implode('.',Array($parts[0],$parts[1]));
  $desc = "games/thumbs/$game".'.txt';
  $game = "games/thumbs/$game";
  @unlink($source);
  @unlink($desc);
  @unlink($game);
}



$games=Array();
$h=opendir("games/thumbs");
while($file=readdir($h)) {
  $game=Array();
  if($file!='.' && $file!='..' && !strstr($file,'txt') ) {
    if(file_exists("games/thumbs/$file".'.txt')) {
      $fp = fopen("games/thumbs/$file".'.txt',"rb");
      $data = fread($fp,filesize("games/thumbs/$file".'.txt'));
      fclose($fp);
      $desc=nl2br($data);
    } else {
      $desc='';
    }
    $game["hits"]=$tikilib->get_game_hits($file);
    $game["desc"]=$desc;
    $game["game"]=$file;
    $games[]=$game;
  }
  
}
closedir($h);

function compare($ar1,$ar2)
{
  return $ar2["hits"]-$ar1["hits"];
}
usort($games,'compare');  

$smarty->assign_by_ref('games',$games);


$smarty->assign('play','n');

if(isset($_REQUEST["game"])) {
 $tikilib->add_game_hit($_REQUEST["game"]);
 $game = $_REQUEST["game"];
 $parts=explode('.',$game);
 $source='games/flash/'.implode('.',Array($parts[0],$parts[1]));
 $smarty->assign('source',$source);
 $smarty->assign('play','y');
}

$section='games';
include_once('tiki-section_options.php');

// Display the template
$smarty->assign('mid','tiki-list_games.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>