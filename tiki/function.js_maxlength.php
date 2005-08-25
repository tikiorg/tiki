<?php
// $Header: /cvsroot/tikiwiki/tiki/function.js_maxlength.php,v 1.8 2005-08-25 20:50:04 michael_davey Exp $
/**
 * \brief Smarty {js_maxlength} function handler
 *
 * Creates javascript to add 'maxlength' functionality to a <textbox>
 * Usage:
 * {js_maxlength textarea=[string] maxlength=[int]}
 *
 * TODO would be great if it worked with array arguments
 *
 */

//this script may only be included - so its better to die if called directly.
//smarty is not there - we need setup
require_once('tiki-setup.php');  
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

function smarty_function_js_maxlength($params, &$smarty) {
	extract($params); // textarea=string maxlength=num

    echo "\n<script type=\"text/javascript\" language=\"Javascript1.2\">\n";
    echo "<!--\n";
    //
    // TODO this is the best I can get a countdown to work but it is still not
    // good enough. Improve or Remove permanently
    // function textCounter(f, countf, maxl) {
    //  if ( f.value.length > maxl-1 ) {
    //    f.value = f.value.substring(0, maxl-1);
    //    alert( 'This text can only be ' + maxl + ' characters long.' );
    //    return false;
    //  } else {
    //    countf.value = maxl - 1 - f.value.length;
    //  }
    //}
    echo "function verifyForm(f){\n";
    echo " var rtn=true;\n";
    echo "  if ( f.$textarea.value.length > $maxlength ) {\n";
    echo "    f.$textarea.style.color='red';\n";
    echo "    alert('" . tra("The text in RED is") . " ' + (f.$textarea.value.length - $maxlength) + ' " . tra("character(s) too long - please edit it.") . "');\n";
    echo "    rtn = false;\n";
    echo "  }\n";
    echo "  return rtn;\n";
    echo "}\n";
    echo "//-->\n";
    echo "</script>\n";
}
?>
