<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//Converts a SimpleXMLElement object to a multi-dimensional array
//$sxmle is a SimpleXMLElement object
function xml2array ($sxmle, &$parent=null, $namespace='', $recursive=true) {
	$namespaces = $sxmle->getNameSpaces(true);
	$content = "$sxmle";

	$r['name'] = $sxmle->getName();
	if (!$recursive) {
		$tmp = array_keys($sxmle->getNameSpaces(false));
		$r['namespace'] = $tmp[0];
		$r['namespaces'] = $namespaces;
	}
	if ($namespace) $r['namespace']=$namespace;
	if ($content) $r['content']=$content;

	foreach ($namespaces as $pre=>$ns) {
		foreach ($sxmle->children($ns) as $k=>$v) {
			xml2array($v, $r['children'], $pre, true);
		}
		foreach ($sxmle->attributes($ns) as $k=>$v) {
			$r['attributes'][$k]="$pre:$v";
		}
	}
	
	foreach ($sxmle->children() as $k=>$v) {
		xml2array($v, $r['children'], '', true);
	}
	foreach ($sxmle->attributes() as $k=>$v) {
		$r['attributes'][$k]="$v";
	}
	
	$parent[]=&$r;
	return $parent[0];
}
