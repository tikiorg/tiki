<?php
// Initialization
require_once('tiki-setup.php');

if ($prefs['tiki_p_admin'] != 'y') {
	$smarty->assign('msg', tra("You do not have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}

if ($handle = opendir('tikimovies')) {
	$movies = array();
	while (false !== ($file = readdir($handle))) { 
		if (substr($file,-4,4) == '.swf' and ($file != 'controller.swf')) {
			$movies[] = $file;
		}
	}
	sort($movies);
	closedir($handle);
}

// Get the page from the request var or default it to HomePage
if(isset($_GET["movie"])  and in_array($_GET['movie'],$movies)) {
  $movie = $_GET["movie"]; 
} else {
  $movie = "";
}

$smarty->assign_by_ref('movie',$movie);    
$smarty->assign_by_ref('movies',$movies);    

if ($movie) {
	// Initialize movie size
	$confFile = 'tikimovies/'.substr($movie,0,-4).".xml";
	//trc('confFile', $confFile);
	$fh = @fopen($confFile,'r');
	$config = @fread($fh, 1000);
	@fclose($fh);
	if (isset($config) && $config <>'') {
		$width = preg_replace("/^.*?<MovieWidth>(.*?)<\/MovieWidth>.*$/ms", "$1", $config);
		$height = preg_replace("/^.*?<MovieHeight>(.*?)<\/MovieHeight>.*$/ms", "$1", $config);
		$smarty->assign('movieWidth',$width);    
		$smarty->assign('movieHeight',$height);
	}
}
// Display the template
$smarty->assign('mid','tiki-listmovies.tpl');
$smarty->display("tiki.tpl");
?>
