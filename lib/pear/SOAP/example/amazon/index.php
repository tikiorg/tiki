<?php
#ini_set('include_path','.;d:/src/pear;c:/php/pear;');
require_once 'amazon.php';
require_once 'config.php';

$amazon = new Amazon('soap',$amazon_id);
$amazon->SearchForm($_REQUEST); 
if (count($_REQUEST)) {
    if ($amazon->Search($_REQUEST)) {
        // display a second search form at the bottom of page
        $amazon->SearchForm($_REQUEST); 
    }
}
?>