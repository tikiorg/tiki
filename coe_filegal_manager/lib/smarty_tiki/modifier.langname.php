<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_modifier_langname($lang) {
        if ( empty($lang) ) return '';
        include('lang/langmapping.php');
        return empty($langmapping[$lang]) ? $lang : tra($langmapping[$lang][0]);
}
