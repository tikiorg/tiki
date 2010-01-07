<?php
// $Id$

// this script may only be included - so it's better to die if called directly
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  die;
}

/**
 * \brief JQuery Smarty function to filter list of results (by default table)
 * 
 * Params
 * 
 * @id				id of the input field
 * @size			size of the input field
 * @maxlength		max length of the input field in characters
 * @prefix			prefix text to be put before the input field
 * @selectors		CSS (jQuery) selector(s) for what to filter
 * @exclude			selector(s) for what to exclude from the text filter
 * 						(but still hide when parent is empty)
 * 
 * Mainly for treetable lists...
 * @parentSelector	CSS (jQuery) selector(s) for parent nodes of what to filter
 * @childPrefix = 'child-of-'	prefix for child class (to hide parent if all children are hidden by the filter)
 */



function smarty_function_listfilter($params, &$smarty) {
	global $headerlib, $prefs, $listfilter_id;
	if ($prefs['feature_jquery'] != 'y' || $prefs['javascript_enabled'] != 'y') {
		return '';
	} else {
		extract($params);
		$childPrefix = isset($childPrefix) ? $childPrefix : 'child-of-';
		$exclude = isset($exclude) ? $exclude : '';

		$input = "<label>";
		
		if (!isset($prefix)) {
			$input .= tra("Filter:");
		} else {
			$input .= tra($prefix);
		}
		$input .= "&nbsp;<input type='text'";
		if (!isset($id)) {
			if (isset($listfilter_id)) {
				$listfilter_id++;
			} else {
				$listfilter_id = 1;
			}
			$id = "listfilter_$listfilter_id";
			$input .= " id='$id'";
		} else {
			$input .= " id='$id'";
		}
		if (isset($size)) $input .= " size='$size'";
		if (isset($maxlength)) $input .= " maxlength='$maxlength'";
		$input .= " /></label>";
		
		if (!isset($selectors)) $selectors = ".$id table tr";
			
		$content = "
\$jq('#$id').keyup( function() {
	var criterias = this.value.toLowerCase().split( /\s+/ );
	
	\$jq('$selectors').each( function() {
		var text = \$jq(this).text().toLowerCase();
		for( i = 0; criterias.length > i; ++i ) {
			word = criterias[i];
			if( word.length > 0 && text.indexOf( word ) == -1 ) {
				\$jq(this).not('$exclude').hide();	// don't search within excluded elements
				return;
			}
		}
		\$jq(this).show();
	} );
";
		if (!empty($parentSelector)) {
			$content .= "
	\$jq('$parentSelector').show().each( function() {
		var cl = '.$childPrefix' + \$jq(this).attr('id');
		if (\$jq(cl + ':visible:not(\"$exclude\")').length == 0) {	// excluded things don't count
			\$jq(this).hide();
			\$jq(cl + '$exclude').hide();							// but need hiding if the parent is 'empty'
		}
	});
";
		}
		$content .= "
} );	// end keyup
		";
	
		$js = $headerlib->add_jq_onready($content);
		if ($js) {
			$input .= $js;
		}
		return $input;
	}
}
