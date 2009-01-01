<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     lcfirst
 * Purpose:  to use with the tracker field type "User inscription"
 *           if $text="12[13],14[15],16[17]"
 *           then return 48 (=13+1+15+1+17+1)
 * -------------------------------------------------------------
 */
function smarty_modifier_how_many_user_inscriptions( $text ) { 

  $pattern="/\d+\[(\d+)\]/";
  $out=preg_match_all($pattern,$text,$match);
  
  $nb=0;

  foreach($match[1] as $n){
    $nb+=($n+1);
  }
  
  return $nb;
}
?>
