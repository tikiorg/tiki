<?php
/* Insert the Bookmark button from ShareThis (www.sharethis.com). Sharethis account is optional.
// Developed by Andrew Hafferman for Tiki CMS
// Usage:
// {SHARETHIS(<options="values">) /}
// If Tabs are not specified, all tabs will be displayed by default.
// If only ONE TAB is specified, only ONE TAB will be displayed.
//
// 2008-11-25 SEWilco
//   Convert comments to WikiSyntax comments.
*/
function wikiplugin_sharethis_help() {
	return tra("Insert a ShareThis Button from www.sharethis.com").":<br />~np~{SHARETHIS(publisher='varchar',webtab=y|n,posttab=y|n,emailtab=y|n,rotateimage=y|n,inactivebg='varchar',inactivefg='varchar',headerbg='varchar',linkfg='varchar',offsetTop='int',offsetLeft='int',popup=true|false,embed=true|false)}{SHARETHIS}<br />A ShareThis account is optional and is to be passed via the 'publisher' parameter or hardcoded into the wiki-plugin.~/np~";
}

function wikiplugin_sharethis($data, $params) {

	extract ($params,EXTR_SKIP);
	$sharethis_options = array();
	$sep = '&amp;';
	$comma = '%2C';
	$lb = '%23';

	// The following is the array that holds the default options for the plugin.
	// Add your publisher to the variable below to be used by default.
	$sharethis_options['publisher'] = '';
	$sharethis_options['style'] = 'default';
	$sharethis_options['charset'] = 'utf-8';
	$sharethis_options['services'] = '';
	$sharethis_options['headerbg'] = '333333';
	$sharethis_options['inactivebg'] = 'cccccc';
	$sharethis_options['inactivefg'] = '000000';
	$sharethis_options['linkfg'] = 'CF000D';
	$sharethis_options['offsetLeft'] = '0';
	$sharethis_options['offsetTop'] = '0';
	$sharethis_options['popup'] = '';
	$sharethis_options['embed'] = 'true';

	// load setting options from $params

	// set plugin services
	if($services)
	{
		$sharethis_options['services'] = str_replace(',',$comma,$services);
	}
	// set plugin publisher (sharethis account identifier)
	if($publisher)
	{
		$sharethis_options['publisher'] = $publisher;
	}
	// set icon style
	if($rotateimage)
	{
		if($rotateimage==y){
			$sharethis_options['style'] = 'rotate';
		}
	}
	// set headerbg
	if($headerbg)
	{
		$sharethis_options['headerbg'] = $headerbg;
	}
	// set link link color
	if($linkfg)
	{
		$sharethis_options['linkfg'] = $linkfg;
	}
	// set inactive bg
	if($inactivebg)
	{
		$sharethis_options['inactivebg'] = $inactivebg;
	}
	// set inactive link color
	if($inactivefg)
	{
		$sharethis_options['inactivefg'] = $inactivefg;
	}
	// set offset left
	if($offsetleft)
	{
		$sharethis_options['offsetLeft'] = $offsetleft;
	}
	// set offset top
	if($offsettop)
	{
		$sharethis_options['offsetTop'] = $offsettop;
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
	// set charset
	if($charset)
	{
		$sharethis_options['charset'] = $charset;
	}
	// compile tab selection
	if(!$tabs) {
		if(isset($webtab)==true || isset($emailtab)==true || isset($posttab)==true) {
			$tabs = '';
			if(isset($webtab)) {
				if($webtab === 'y') {
					if($tabs == '') {
						$tabs = "web";	
					} else {
						$tabs .= $comma."web";
					}
				}
			}
			if(isset($posttab)) {
				if($posttab === 'y') {
					if($tabs == '') {
						$tabs = "post";	
					} else {
						$tabs .= $comma."post";
					}
				}
			}
			if(isset($emailtab)) {
				if($emailtab === 'y') {
					if($tabs == '') {
						$tabs = "email";	
					} else {
						$tabs .= $comma."email";
					}
				}
			}
		} else {
			$tabs = "web".$comma."post".$comma."email";
		}
	}
	
	// put all the options together

	$sharethiscode = "~hc~ ShareThis Bookmark Button BEGIN ~/hc~";
	$sharethiscode .= '<script type="text/javascript" src="http://w.sharethis.com/widget/?';

	$sharethiscode .= "tabs=".$tabs;
	if(!empty($sharethis_options['headerbg'])) $sharethiscode .= $sep."headerbg=".$lb.$sharethis_options['headerbg'];
	if(!empty($sharethis_options['linkfg'])) $sharethiscode .= $sep."linkfg=".$lb.$sharethis_options['linkfg'];
	if(!empty($sharethis_options['inactivefg'])) $sharethiscode .= $sep."inactivefg=".$lb.$sharethis_options['inactivefg'];
	if(!empty($sharethis_options['inactivebg'])) $sharethiscode .= $sep."inactivebg=".$lb.$sharethis_options['inactivebg'];
	if(!empty($sharethis_options['offsetLeft'])) $sharethiscode .= $sep."offsetLeft=".$sharethis_options['offsetLeft'];
	if(!empty($sharethis_options['offsetTop'])) $sharethiscode .= $sep."offsetTop=".$sharethis_options['offsetTop'];
	if(!empty($sharethis_options['charset'])) $sharethiscode .= $sep."charset=".$sharethis_options['charset'];
	if(!empty($sharethis_options['publisher'])) $sharethiscode .= $sep."publisher=".$sharethis_options['publisher'];
	if(!empty($sharethis_options['services'])) $sharethiscode .= $sep."services=".$sharethis_options['services'];
	if(!empty($sharethis_options['popup'])) $sharethiscode .= $sep."popup=".$sharethis_options['popup'];
	if(!empty($sharethis_options['embed'])) $sharethiscode .= $sep."embed=".$sharethis_options['embed'];

	if($rotateimage) $sharethiscode .= $sep."style=".$sharethis_options['style'];

	$sharethiscode .= "\"></script>";
	$sharethiscode .= "~hc~ ShareThis Bookmark Button END ~/hc~";

$result = $sharethiscode;

return $result;

}

?>
