<?php
/*
 * $Header: /cvsroot/tikiwiki/_mods/wiki-plugins/formula/wiki-plugins/wikiplugin_formula.php,v 1.3 2005-09-08 17:22:15 damosoft Exp $
 * Tiki-Wiki plugin formula
 *  
 * This plugin will try to render a formula written with LaTeX syntax
 * For simple formulas (polynoms) it will try to render it directly in html
 * else will try to call latex to render the formula in a graph and 
 * display it.
 *
 * Mostly copied from a Jens Frank message in
 * http://mail.wikipedia.org/pipermail/wikitech-l/2002-November/001460.html
 * related to rendering formulas for wikipedia
 *
 * Of course, this requires latex installed or at least the commands that are called
 * by tex2png
 * On RedHat 8, these commands are provided by the tetex-dvips, tetex-fonts, tetex-latex
 *   and tetex rpms.
 * Not sure how multiplataform it is, the script i call in the end seem to be bash, maybe
 * an alternative .bat should be done
 *
 * Installation instructions:
 *   move this file to lib/wiki-plugins
 *   move tex2png to lib
 *
 */

function wikiplugin_formula_help() {
	return tra("Example").":<br />~np~{FORMULA()}".tra("formula")."{FORMULA}~/np~";
}

function wikiplugin_formula($data, $params) {
	# This function is disabled as shipped.
  # You must edit this file to enable Tiki's FORMULA Plugin.
  # The problem is that, for example, {FORMULA()}\input{/etc/passwd}{FORMULA} shows the contents of /etc/passwd.
  # TeX also has "\write18", "\include", and "\usepackage" functions which might cause similar problems.
  # If you want to rewrite this function to be more secure, you might start by looking at MediaWiki's TeX pluggin.
  # If your Linux system is physically secure, not on a network, and used only by you, it is probably safe to run this pluggin as-is.
  # Comment out the following four lines to enable this function.
	$html= "<i>";
	$html .= "The Tiki formula plugin is disabled due to security issues.  You must edit lib/wiki-plugins/wikiplugin_formula.php to enable the formula plugin.";
	$html .= "</i>";
	return $html;

	extract ($params, EXTR_SKIP);

	$data=trim($data);
	if ( ereg ("^[-a-zA-Z+*/=,0-9 <>^_{}()]*$", $data ))  {
 		#
	 	# Simple LaTeX, only polynom markup
 		# handle in HTML
 		#
 		$state= array();
 		$letters= preg_split("//", $data);
 		$html= "<i>";
 		foreach($letters as $letter) {
 			if ( ereg ( "[-a-zA-Z+*/=,0-9 <>()]", $letter ) ) {
 				$html .= htmlentities($letter);
 				if (end($state) == "SUP") {
 					array_pop($state);
 					$html .= "</sup>";
 				} else if (end($state) == "SUB") {
 						array_pop($state);
 						$html .= "</sub>";
 				}
 			} else if ( $letter == "^" ) {
 					$html .= "<sup>";
 					$state[]= "SUP"; /* Push to end of array */
 				} else if ( $letter == "_" ) {
 					$html .= "<sub>";
 					$state[]= "SUB";
 				} else if ( $letter == "{" ) {
 					$state[]= "{";
 				} else if ( $letter == "}" ) {
 					array_pop($state);
 					if (end($state) == "SUP") {
 						array_pop($state);
 						$html .= "</sup>";
 					} else if (end($state) == "SUB") {
 						array_pop($state);
 						$html .= "</sub>";
 					}
 				} 
 		}
 		$html .= "</i>";
 	} else {
 		#
 		# Complex HTML, use LaTeX and PNG image
 		#
		# generated only if no formula with the same md5 was generated already
 		$tmpf = md5($data);
		if (! file_exists("temp/$tmpf.png")) { 
	 		$fp = fopen ("temp/".$tmpf, "w+");
		
 			fputs ($fp, "\documentclass{article}"
 			   	."\pagestyle{empty}"
 		   		."\begin{document}\n" 
	 		   	."$$".$data."$$\n"
 			   	."\end{document}" );
 			fclose ($fp);
	 		exec ("lib/tex2png ".$tmpf);
		}
 		$file = $tmpf . ".png";
 		$html = "<img src=\"temp/" .$file.
 			"\" alt=\"" .$data. "\" ".
 			"align=\"middle\">";
 	}
	return $html;

}

?>
