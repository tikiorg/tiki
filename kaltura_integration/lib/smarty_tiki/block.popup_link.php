<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_block_popup_link($params, $content, &$smarty, $repeat) {
    global $headerlib, $prefs;
	static $counter = 0;

	$linkId = 'block-popup-link' . ++$counter;
	$block = $params['block'];

    if ( $repeat ) {
		// Do nothing
	} else {
		if ($prefs['feature_jquery'] == 'y') {
			$headerlib->add_js( <<<JS
\$jq(document).ready( function() {

	\$jq('#$block').hide();
	
	\$jq('#$linkId').click( function() {
		var block = \$jq('#$block');
		if( block.css('display') == 'none' ) {
			var coord = \$jq(this).offset();
			block.css( 'position', 'absolute' );
			block.css( 'left', coord.left);
			block.css( 'top', coord.top + \$jq(this).height() );
			show( '$block' );
		} else {
			hide( '$block' );
		}
	});
} );
JS
			);
		}

		window.popup_link = block;
	} );
} );
JS
			);
		}
		$href = '';
		if ($prefs['feature_jquery'] == 'y') {
			$href = " href=\"javascript:void(0)\"";
		} else {
			$href = " href=\"javascript:alert('" . tr('You need JQuery enabled for this feature') . "')\"";
		}
		return '<a id="' . $linkId . '"' . $href . '>' . $content . '</a>';
	}
}
