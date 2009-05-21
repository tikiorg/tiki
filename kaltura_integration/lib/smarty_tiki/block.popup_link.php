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
		} else if ($prefs['feature_mootools'] == 'y') {
			$headerlib->add_js( <<<JS
window.addEvent( 'domready', function( event ) {
	var link = $('$linkId');
	var block = $('$block');

	block.setStyle( 'display', 'none' );
	link.addEvent( 'click', function( event ) {

		if( block.getStyle( 'display' ) == 'none' ) {
			if( window.popup_link ) {
				window.popup_link.setStyle( 'display', 'none' );
			}

			var coord = link.getCoordinates();
			block.setStyle( 'position', 'absolute' );
			block.setStyle( 'left', coord.left + 'px' );
			block.setStyle( 'top', coord.bottom + 'px' );
			block.setStyle( 'display', 'block' );
		} else {
			block.setStyle( 'display', 'none' );
		}

		window.popup_link = block;
	} );
} );
JS
			);
		}
		$href = '';
		if ($prefs['feature_mootools'] == 'y' || $prefs['feature_jquery'] == 'y') {
			$href = " href=\"javascript:void(0)\"";
		} else {
			$href = " href=\"javascript:alert('" . tr('You need either JQuery or MooTools enabled for this feature') . "')\"";
		}
		return '<a id="' . $linkId . '"' . $href . '>' . $content . '</a>';
	}
}



?>
