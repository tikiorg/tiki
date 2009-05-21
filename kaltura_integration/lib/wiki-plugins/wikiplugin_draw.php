<?php

function wikiplugin_draw_info()
{
	return array(
		'name' => tra('Draw'),
		'documentation' => 'PluginDraw',
		'description' => tra('Displays drawings in the wiki page'),
		'prefs' => array( 'feature_drawings', 'wikiplugin_draw' ),
		'params' => array(
			'name' => array(
				'name' => tra('Name'),
				'description' => tra('Name of the drawing to display.'),
				'required' => true,
			),
		),
	);
}

function wikiplugin_draw( $data, $params )
{
	global $tiki_p_edit_drawings, $tiki_p_admin_drawings, $page, $tikidomain;
	$pars = parse_url($_SERVER["REQUEST_URI"]);

	$pars_parts = split('/', $pars["path"]);
	$pars = array();

	$temp_max = count($pars_parts) - 1;
	for ($i = 0; $i < $temp_max; $i++) {
		$pars[] = $pars_parts[$i];
	}

	$pars = join('/', $pars);

	if( ! isset($params['name'] ) )
		return '^' . tra("Name parameter missing") . '^';

	$id = $params['name'];

	$repl = '';
	if ($tikidomain) {
		$name = $tikidomain.'/'.$id . '.gif';
	} else {
		$name = $id . '.gif';
	}

	if (file_exists("img/wiki/$name")) {
		if ($tiki_p_edit_drawings == 'y' || $tiki_p_admin_drawings == 'y') {
			$repl = "<a href='#' onclick=\"javascript:window.open('tiki-editdrawing.php?page=" . urlencode($page). "&amp;path=$pars&amp;drawing={$id}','','menubar=no,width=252,height=25');\"><img border='0' src='img/wiki/$name' alt='click to edit' /></a>";
		} else {
			$repl = "<img border='0' src='img/wiki/$name' alt='a drawing' />";
		}
	} else {
		if ($tiki_p_edit_drawings == 'y' || $tiki_p_admin_drawings == 'y') {
			$repl = "<a class='wiki' href='#' onclick=\"javascript:window.open('tiki-editdrawing.php?page=" . urlencode($page). "&amp;path=$pars&amp;drawing={$id}','','menubar=no,width=252,height=25');\">click here to create draw $id</a>";
		} else {
			$repl = tra('drawing not found');
		}
	}

	return $repl;
}

?>
