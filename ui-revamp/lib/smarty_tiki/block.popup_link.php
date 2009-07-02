<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_block_popup_link($params, $content, &$smarty, $repeat) {
    global $headerlib;
	static $counter = 0;

	$linkId = 'block-popup-link' . ++$counter;
	$block = $params['block'];

    if ( $repeat ) {
		// Do nothing
	} else {
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
		return '<a id="' . $linkId . '" href="javascript:void(0)">' . $content . '</a>';
	}
}



?>
