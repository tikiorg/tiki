<?php
/* Insert the bookmark button from ShareThis (www.sharethis.com). ShareThis account is not necessary.
// Developed by Andrew Hafferman for Tiki CMS
//
// 2008-11-25 SEWilco
//   Convert comments to WikiSyntax comments.
// 2009-07-11 lindon
//   Update for changes in ShareThis and fix bugs
//
*/
function wikiplugin_sharethis_help() {
	return tra('Insert a ShareThis button from www.sharethis.com').":<br />~np~{SHARETHIS(sendsvcs=> , postfirst=> ,  rotateimage=> y|n, buttontext=> , headertitle=> , headerbg=> , headertxtcolor=> , linkfg=> , popup=> true|false, embed=> true|false)}{SHARETHIS} ~/np~ <br /> ";
}
function wikiplugin_sharethis_info() {
	return array(
		'name' => tra('sharethis'),
		'documentation' => 'PluginSharethis',
		'description' => tra('Display a social networking tool.'),
		'prefs' => array( 'wikiplugin_sharethis' ),
		'params' => array(
			'sendsvcs' => array(
				'required' => false,
				'name' => tra('Send services'),
				'description' => tra('By default, email, aim and sms are available. Input one or two of the services separated by a | to limit the choice of send services.'),
			),
			'postfirst' => array(
				'required' => false,
				'name' => tra('First post services shown'),
				'description' => tra('Input a list of post services (like facebook, myspace, digg, etc.) separated by a | to customize the services that are shown in the opening panel of the widget.'),
			),
			'rotateimage' => array(
				'required' => false,
				'name' => tra('Rotate image'),
				'description' => tra('A value of y will cause the button icon to rotate every 3 seconds between a few icons, cycling through twice before stopping.'),
			),
			'buttontext' => array(
				'required' => false,
				'name' => tra('Button text'),
				'description' => tra('Custom link text for the button.'),
			),
			'headertitle' => array(
				'required' => false,
				'name' => tra('Header title'),
				'description' => tra('Optional header title text for the widget.'),
			),
			'headerbg' => array(
				'required' => false,
				'name' => tra('Header background'),
				'description' => tra('HTML color code (not color name) for the background color for the header if an optional header title is used.'),
			),
			'headertxtcolor' => array(
				'required' => false,
				'name' => tra('Header text color'),
				'description' => tra('HTML color code (not color name) for the header text if an optional header title is used.'),
			),
			'linkfg' => array(
				'required' => false,
				'name' => tra('Link text color for services'),
				'description' => tra('HTML color code (not color name) for the link text for all send and post services shown in the widget'),
			),
			'popup' => array(
				'required' => false,
				'name' => tra('Pop-up'),
				'description' => tra('A value of true will cause the widget to show in a pop-up window.'),
			),
			'embed' => array(
				'required' => false,
				'name' => tra('Embedded elements'),
				'description' => tra('A value of true will allow embedded elements (like flash) to be seen while iframe is loading.'),
			),		)
	);
}
function wikiplugin_sharethis($data, $params) {

	extract ($params,EXTR_SKIP);
	$sharethis_options = array();
	$sep = '&amp;';
	$comma = '%2C';
	$lb = '%23';
	$sp = '%20';

	// The following is the array that holds the default options for the plugin.
	$sharethis_options['type'] = 'website';
	$sharethis_options['sendsvcs'] = '';
	$sharethis_options['style'] = '';
	$sharethis_options['buttontext'] = '';
	$sharethis_options['postfirst'] = '';
	$sharethis_options['headertitle'] = '';
	$sharethis_options['headerbg'] = '';
	$sharethis_options['headertxtcolor'] = '';
	$sharethis_options['linkfg'] = '';
	$sharethis_options['popup'] = '';
	$sharethis_options['embed'] = '';

	// load setting options from $params

	// set post services that appear upon widget opening
	if($postfirst)
	{
		$sharethis_options['postfirst'] = str_replace('|',$comma,$postfirst);
	}
	// limit send services that will appear
	if($sendsvcs)
	{
		$sharethis_options['sendsvcs'] = str_replace('|',$comma,$sendsvcs);
	}
	// set icon style
	if($rotateimage)
	{
		if($rotateimage == 'y'){
			$sharethis_options['style'] = 'rotate';
		}
	}
	// set button text
	if($buttontext)
	{
		$sharethis_options['buttontext'] = $buttontext;
	}
	// set header title text. If header title is set by user, then set background color and text color
	if($headertitle)
	{
		$sharethis_options['headertitle'] = str_replace(' ',$sp,$headertitle);
			if($headerbg) {
			$sharethis_options['headerbg'] = $headerbg;
			}
			if($headertxtcolor) {
			$sharethis_options['headertxtcolor'] = $headertxtcolor;
			}
		} else {
			$sharethis_options['headerbg'] = '';
			$sharethis_options['headertxtcolor'] = '';
		}
	// set link text color for services shown in popup
	if($linkfg)
	{
		$sharethis_options['linkfg'] = $linkfg;
	}
	// set popup
	if($popup)
	{
		$sharethis_options['popup'] = $popup;
	}
	// set embed
	if($embed)
	{
		$sharethis_options['embed'] = $embed;
	}

	// put all the options together

	$sharethiscode = "~hc~ ))ShareThis(( Bookmark Button BEGIN ~/hc~";
	$sharethiscode .= '<script type="text/javascript" src="http://w.sharethis.com/button/sharethis.js#';
	$sharethiscode .= "type=".$sharethis_options['type'];
	
	if(!empty($sharethis_options['buttontext'])) $sharethiscode .= $sep."buttonText=".$sharethis_options['buttontext'];
	if(!empty($sharethis_options['popup'])) $sharethiscode .= $sep."popup=".$sharethis_options['popup'];
	if(!empty($sharethis_options['embed'])) $sharethiscode .= $sep."embeds=".$sharethis_options['embed'];
	if(!empty($sharethis_options['style'])) $sharethiscode .= $sep."style=".$sharethis_options['style'];
	if(!empty($sharethis_options['sendsvcs'])) $sharethiscode .= $sep."send_services=".$sharethis_options['sendsvcs'];
	if(!empty($sharethis_options['postfirst'])) $sharethiscode .= $sep."post_services=".$sharethis_options['postfirst'];
	if(!empty($sharethis_options['headertxtcolor'])) $sharethiscode .= $sep."headerfg=".$lb.$sharethis_options['headertxtcolor'];	
	if(!empty($sharethis_options['headerbg'])) $sharethiscode .= $sep."headerbg=".$lb.$sharethis_options['headerbg'];	
	if(!empty($sharethis_options['linkfg'])) $sharethiscode .= $sep."linkfg=".$lb.$sharethis_options['linkfg'];
	if(!empty($sharethis_options['headertitle'])) $sharethiscode .= $sep."headerTitle=".$sharethis_options['headertitle'];

	$sharethiscode .= "\"></script>";
	$sharethiscode .= "~hc~ ))ShareThis(( Bookmark Button END ~/hc~";

$result = $sharethiscode;

return $result;

}
