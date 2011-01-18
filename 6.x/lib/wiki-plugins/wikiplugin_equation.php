<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/*
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
 */

function wikiplugin_equation_help() {
    $help  = tra("Renders an equation written in LaTeX syntax as a png.  Optional parameter size defaults to 100 and is the percentage of the normal size, i.e., size=200 indicates an equation 2x the normal size").":\n";
    $help .= tra("Example").":<br />~np~{EQUATION(size=&lt;size&gt;)}".tra("equation")."{EQUATION}~/np~";
    return $help;
}

function wikiplugin_equation_info() {
	return array(
		'name' => tra('Equation'),
		'documentation' => 'PluginEquation',
		'description' => tra('Render an equation written in LaTeX syntax as an image.'),
		'prefs' => array('wikiplugin_equation'),
		'body' => tra('equation'),
		'params' => array(
			'size' => array(
				'required' => false,
				'name' => tra('Size'),
				'description' => tra('Size expressed as a percentage of the normal size. 100 produces the default size. 200 produces an image twice as large.'),
				'default' => 100,
				'filter' => 'digits',
			),
		),
	);
}

function wikiplugin_equation($data, $params) {
	if (empty($data)) return '';
    extract ($params, EXTR_SKIP);
    if (empty($size)) $size = 100;

    $latexrender_path = getcwd() . "/lib/equation"; 
    include_once($latexrender_path . "/class.latexrender.php");
    $latexrender_path_http = "lib/equation";
    $latex = new LatexRender($latexrender_path."/pictures",$latexrender_path_http."/pictures",$latexrender_path."/tmp");
    $latex->_formula_density = 120 * $size / 100;

	extract ($params, EXTR_SKIP);

	$data=html_entity_decode(trim($data), ENT_QUOTES);

    $url = $latex->getFormulaURL($data);
    $alt = "~np~" . $data . "~/np~";

    if ($url != false) {
        $html = "<img src=\"$url\" alt=\"$alt\" style=\"vertical-align:middle\">";
    } else {
        $html = "__~~#FF0000:Unparseable or potentially dangerous latex formula. Error {$latex->_errorcode} {$latex->_errorextra}~~__";
    }
	return $html;
}
