<?php
// Initialization
require_once('tiki-setup.php');

if ($handle = opendir('tikimovies')) {
	while (false !== ($file = readdir($handle))) { 
		if (substr($file,-4,4) == '.swf' and ($file != 'controller.swf')) {
			$movies[] = $file;
		}
	}
	sort($movies);
	closedir($handle);
}

// Get the page from the request var or default it to HomePage
if(isset($_GET["movie"])) {
  $movie = $_GET["movie"]; 
} else {
  $movie = "";
}

$smarty->assign_by_ref('movie',$movie);    
$smarty->assign_by_ref('movies',$movies);    

// Display the template
$smarty->assign('mid','tiki-listmovies.tpl');
$smarty->display("tiki.tpl");
?>
