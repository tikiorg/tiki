<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: $
function wikiplugin_author_help() {
	return tra("Shows which author created/deleted which text.").":<br />~np~{AUTHOR(author=username,deleted_by=username)}".tra('text')."{AUTHOR}~/np~";
}
function wikiplugin_author_info() {
	return array(
		'name' => tra('Author'),
		'documentation' => 'PluginAuthor',
		'description' => tra("Color codes parts of the page like 'Track changes' in Office programs."),
		'prefs' => array('wikiplugin_author'),
		'body' => tra('text'),
		'params' => array(
			'author' => array(
				'required' => true,
				'name' => tra('User name'),
				'description' => tra('User name of the user who wrote the text.'),
			),
			'deleted_by' => array(
				'required' => false,
				'name' => tra('Deleted by User'),
				'description' => tra('User name of the user who deleted the text.'),
			),
			'visible'	=> array(
				'required'	=> false,
				'name'		=> tra('Make visible'),
				'description' => tra("Should this author's contribution be visible (default: no)."),
				'options' => array(
					array('text' => tra('No'), 'value' => '0'), 
					array('text' => tra('Yes'), 'value' => '1'), 
				),
			),
			'popup'	=> array(
				'required'	=> false,
				'name'		=> tra('Show popup with author/deleted by'),
				'description' => tra('Generate a popup with names of author(s) (default: no).'),
				'options' => array(
					array('text' => tra('No'), 'value' => '0'), 
					array('text' => tra('Yes'), 'value' => '1'), 
				),
			),
		), // params
	);
}

function wikiplugin_author($data, $params) {
	global $smarty, $tikilib, $headerlib;
	
	static $authors;
	static $color=0;
	static $id=0;
	
	$blocktags='/(<+\/?address.*?>|<+\/?blockcode.*?>|<+\/?blockquote.*?>|<+\/?div.*?>|<+\/?h1.*?>|<+\/?h2.*?>|<+\/?h3.*?>|<+\/?h4.*?>|<+\/?h5.*?>|<+\/?h6.*?>|<+\/?hr.*?>|<+\/?h.*?>|<+\/?li.*?>|<+\/?ol.*?>|<+\/?pre.*?>|<+\/?p.*?>|<+\/?section.*?>|<+\/?table.*?>|<+\/?td.*?>|<+\/?th.*?>|<+\/?tr.*?>|<+\/?ul.*?>)/';
	$default = array('popup' => 0);
	$params = array_merge($default, $params);
	if(!is_array($authors)) $authors=array();
//	if (!isset($color)) $color=0;
	
	$author=$params['author'];
	if (!isset($authors[$author])) {
		$colors =      array('black', 'blue',  'red',   'green', 'maroon', 'yellow', 'aqua', 'fuchsia', 'teal',  'purple', 'white', 'olive', 'gray',  'navy',  'silver', 'lime');
		$backgrounds = array('white', 'white', 'white', 'white', 'white',  'gray',   'gray', 'gray',    'white', 'white',  'blue',  'white', 'white', 'white', 'navy',   'gray');
		$authors[$author]=array(
			'color' => $colors[$color %16],
			'background' => $backgrounds[$color %16],
		);
		$color++;
	}
	
	$content=preg_split($blocktags, $data, -1, PREG_SPLIT_DELIM_CAPTURE);
	$html='';
	foreach ($content as $data) {
		if ($data!='') {
			if (preg_match($blocktags,$data)>0) {
				$html.=$data;

			} else {
				if ($params['visible']==1 or $params['popup']==1) {
					$html.='<span id="author'.$id.'-link" ';
				}
				if ($params['visible']==1) {
					$html.='style="color: ' . $authors[$author]['color'] . '; background-color: ' . $authors[$author]['background'] .';';
					if (isset($params['deleted_by'])) {
						$html.=' text-decoration: line-through;';
					} else {
						$html.=' text-decoration: none;';
					}
					$html.='"';
				}
				if($params['popup']==1) {
					$html.=' onclick="javascript:void()"';
				}
				if ($params['visible']==1 or $params['popup']==1) {
					$html.=">$data</span>";
				} else {
					$html.=$data;
				}
				
				if($params['popup']==1) {
					//Mouseover for detailed info
					$js = "\$jq('#author$id-link').mouseover(function(event) {
						\$jq('#author$id').css('left', event.pageX).css('top', event.pageY);
						showJQ('#author$id', '', '');
						1000
					});";
					$js .= "\$jq('#author$id-link').mouseout(function(event) { setTimeout(function() {hideJQ('#author$id', '', '')}, 1000); });";
					$headerlib->add_jq_onready($js);
					$html.="<span id=\"author$id\" class=\"plugin-mouseover\" style=\"width: 200px; height: 80px; padding: 2px \">" . 
						tra('Author') . ": $author" . (isset($params['deleted_by'])?"<br />" . tra('deleted by') . ': '.$params['deleted_by']:'') . "</span>";
				}
				$id++;
			} // content is not a block tag
		} // content <>""
	} // foreach
	return $html;
}
