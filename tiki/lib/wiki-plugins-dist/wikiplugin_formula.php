<?php
/* Tiki-Wiki plugin formula
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
 * Not sure how multiplataform it is, the script i call in the end seem to be bash, maybe
 * an alternative .bat should be done
 */

function wikiplugin_formula_help() {
	return tra("Example").":<br />~np~{FORMULA()}".tra("formula")."{FORMULA}~/np~";
}

function wikiplugin_formula($data, $params) {
	extract ($params);

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
