<?php
/**
 * $Header: /cvsroot/tikiwiki/tiki/tiki-doxyview.php,v 1.1 2003-10-07 23:26:16 zaufi Exp $
 *
 * Doxygened files viewer (wrapper)
 *
 * The URL to use should look like this:
 *  http://tikisite/tiki/tiki-doxyview.php?doxpath=<repository>&doxfile=index.html
 *
 * Where:
 *  <repository> = path from web root to directory with generated docs
 *  index.html   = actualy can be any html file from repository
 *
 * Example:
 *  http://tikisite/tiki/tiki-doxyview.php?doxpath=megalib-dox&doxfile=namaspaces.html
 * this example will display namespaces.html file from 'repository' named "megalib-dox"
 * located at www root (usual /var/www/html -- i.e. full path will be
 * /var/www/html/megalib-dox/namespaces.html :)
 * 
 * The best (currently I found) way to use all of above is to add
 * index URL to my 'User Menu' :) -- to have short path to my project documentation
 *
 * This is first implemantation was designed for doxygen only.
 * Possible future versions will support more features to allow
 * easy 'integrate' other docs/apps into Tiki natively. :)
 *
 * Hint: to make all graphs in generaterd docs with transparent background use
 *       the following command:
 *
 *       find <repository> -name '*.png' -exec convert -transparent '#FFFFFF' '{}' '{}' ';'
 *
 * Note: U need ImageMagick to do convert...
 *
 */

require_once('tiki-setup.php');

// I'm have not enough time to make separate permissions for view doxygened docs
// so use 'wiki view' for now... 'till smbd (maybe I :) fix this or phpGACL will come... :)
if($tiki_p_view != 'y')
{
  $smarty->assign('msg',tra("Permission denied to use this feature"));
  $smarty->display("styles/$style_base/error.tpl");
  die;
}

if (!isset($_REQUEST["doxpath"]) && !isset($_REQUEST["doxfile"]))
{
    $smarty->assign('msg',tra("No doxygened repository or file given"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
}
$doxpath=$_REQUEST["doxpath"];
$doxfile=$_REQUEST["doxfile"];

// Check if given file present at given location
$f=$_SERVER['DOCUMENT_ROOT'].'/'.$doxpath.'/'.$doxfile;
if (!file_exists($f))
{
    $smarty->assign('msg',tra("File not found"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
}

// Now we need to hack this file...
$data=file_get_contents($f);
// Fix references
$data = str_replace('href="', 'href="tiki-doxyview.php?doxpath='.$doxpath.'&doxfile=', $data);
// Fix path to pictures
$data = str_replace('img src="', 'img src="/'.$doxpath.'/', $data);
// Remove head before <body>
$data = substr($data, strpos($data, '<body>') + 6);
// Remove tail after </body>
$data = substr($data, 0, strpos($data, '</body>'));

// Display the template
$smarty->assign_by_ref('data', $data);
$smarty->assign('mid','tiki-doxyview.tpl');
$smarty->display("styles/$style_base/tiki.tpl");

?>