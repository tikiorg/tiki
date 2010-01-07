<?php
function wikiplugin_kaltura_help() {
        return tra("Displays a KALTURA video on the wiki page").":<br />~np~{KALTURA(id=\"entry_id_of_video\")}{KALTURA}~/np~";
}

function wikiplugin_kaltura_info() {
	return array(
		'name' => tra('Kaltura video'),
		'documentation' => 'PluginKaltura',
		'description' => tra('Displays a KALTURA video on the wiki page'),
		'prefs' => array('wikiplugin_kaltura'),
		'extraparams' => true,
		'params' => array(
			'id' => array(
				'required' => true,
				'name' => tra('Kaltura Entry Id'),
				'description' => tra('Kaltura Entry Id'),
			),
		),
	);
}

function wikiplugin_kaltura($data, $params) {
     extract ($params, EXTR_SKIP);
     
     $code ='<object name="kaltura_player" id="kaltura_player" type="application/x-shockwave-flash" allowScriptAccess="always" allowNetworking="all" allowFullScreen="true" height="365" width="400" data="http://www.kaltura.com/index.php/kwidget/wid/_23929/uiconf_id/48411/entry_id/'.$id.'">
			 <param name="allowScriptAccess" value="always" />
			 <param name="allowNetworking" value="all" />
			 <param name="allowFullScreen" value="true" />
			 <param name="movie" value="http://www.kaltura.com/index.php/kwidget/wid/_23929/uiconf_id/48411/entry_id/'.$id.'"/>
			 <param name="flashVars" value="entry_id='.$id.'"/>
			 <param name="wmode" value="opaque"/>
			 </object>';
     return '~np~'.$code.'~/np~';
}