<?php
/*
 * $Id: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_equation.php,v 1.1.2.1 2008-03-04 16:25:35 chriscramer Exp $
 * Tiki-Wiki plugin equation
 *  
 * This plugin will try to render a formula written with LaTeX syntax
 * into a png and will include a reference to that png in your wiki page.
 *
 * Uses Benjamin Zeiss's Latex Rendering Class in order to minimize the 
 * chances of someone inserting malicious LaTeX syntax into your page 
 * (e.g., \input{/etc/passwd} )
 *
 * The plugin requires that latex and the amsfonts package be installed
 * on your server
 *
 */

function wikiplugin_equation_help() {
    $help  = tra("Renders an equation written in LaTeX syntax as a png.  Optional parameter size defaults to 100 and is the percentage of the normal size, i.e., size=200 indicates an equation 2x the normal size").":\n";
    $help .= tra("Example").":<br />~np~{EQUATION(size=<size>)}".tra("equation")."{EQUATION}~/np~";
    return $help;
}

function wikiplugin_equation($data, $params) {
    extract ($params, EXTR_SKIP);
    if (empty($size)) $size = 100;

    include_once("equation/class.latexrender.php");
    $latexrender_path = getcwd() . "/lib/equation"; 
    $latexrender_path_http = "lib/equation";
    $latex = new LatexRender($latexrender_path."/pictures",$latexrender_path_http."/pictures",$latexrender_path."/tmp");
    $latex->_formula_density = 120 * $size / 100;

	extract ($params, EXTR_SKIP);

	$data=html_entity_decode(trim($data), ENT_QUOTES);

    $url = $latex->getFormulaURL($data);
    $alt = "~np~" . $data . "~/np~";

    if ($url != false) {
        $html = "<img src=\"$url\" alt=\"$alt\" align=\"absmiddle\">";
    } else {
        $html = "__~~#FF0000:Unparseable or potentially dangerous latex formula. Error {$latex->_errorcode} {$latex->_errorextra}~~__";
    }
	return $html;
}

?>
