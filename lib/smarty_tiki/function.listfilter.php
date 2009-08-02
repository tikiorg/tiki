<?php
// $Id: $

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
 * @id			id of the input field
 * @size		size of the input field
 * @maxlength	max length of the input field in characters
 * @prefix		prefix text to be put before the input field
 * @selectors	CSS selector(s)
 * 
 */



function smarty_function_listfilter($params, &$smarty) {
	global $headerlib, $prefs, $listfilter_id;
	if ($prefs['feature_jquery'] != 'y' || $prefs['javascript_enabled'] != 'y') {
		return '';
	} else {
		extract($params);

		if (!isset($prefix)) {
			$input = tra("Filter:");
		} else {
			$input = tra($prefix);
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
		$input .= " />";
		
		if (!isset($selectors)) $selectors = ".$id table tr";
			
		$content = "\$jq('#$id').keyup( function() {
		var criterias = this.value.toLowerCase().split( /\s+/ );

		\$jq('$selectors').each( function() {
			var text = \$jq(this).text().toLowerCase();
			for( i = 0; criterias.length > i; ++i ) {
				word = criterias[i];
				if( word.length > 0 && text.indexOf( word ) == -1 ) {
					\$jq(this).hide();
					return;
				}
			}

			\$jq(this).show();
		} );
	} );";
	
		$headerlib->add_jq_onready($content);
		return $input;
	}
}
