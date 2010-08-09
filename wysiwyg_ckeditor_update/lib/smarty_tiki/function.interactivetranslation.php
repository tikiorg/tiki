<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

// Param: 'id' or 'label'
function smarty_function_interactivetranslation($params, &$smarty) {
	global $headerlib;

	$strings = get_collected_strings();
	if( count( $strings ) == 0 ) {
		return;
	}

	usort( $strings, 'sort_strings_by_length' );

	$strings = json_encode( $strings );

	$text = tra('Interactive Translation');
	$help = tra('Once checked, click on any string to translate it.');
	$save = tra('Save translations');
	$note = tra('Changes will be applied on next page load only.');
	$cancel = tra('Cancel');
	$jq = <<<JS
	var data = $strings;

	\$('.intertrans').find('*').addClass('intertrans');
	\$('#intertrans-form :reset').click( function() {
		\$('#intertrans-form').hide();
		return false;
	} );
	\$('body').css('padding-top', 50 );

	\$('#intertrans-form form').submit( function( e ) {
		e.preventDefault();

		\$('#intertrans-form').hide();
		\$.post( \$(this).attr('action'), \$(this).serialize() );

		return false;
	} );
	
	\$(document).find('body *:not(.intertrans)').click( function( e ) {
		if( \$('#intertrans-active:checked').length == 0 ) {
			return;
		}

		var text = \$(this).text();
		var val = \$(this).val();
		var alt = \$(this).attr('alt');
		var title = \$(this).attr('title');
		var applicable = \$(data).filter( function( k ) {
			return ( text && text.length && text.indexOf( this[1] ) != -1 )
				|| ( val && val.length && val.indexOf( this[1] ) != -1 )
				|| ( alt && alt.length && alt.indexOf( this[1] ) != -1 )
				|| ( title && title.length && title.indexOf( this[1] ) != -1 );
		} );

		\$('#intertrans-form table')
			.empty()
			.append( applicable.map( function() {
				var r = \$('<tr><td class="original"></td><td><input type="text" name="trans[]"/><input type="hidden" name="source[]"/></td></tr>');
				r.find('td.original').html( this[0] );
				r.find(':hidden').val( this[0] );
				r.find(':text').val( this[1] );
				return r[0];
			} ) );
		
		\$('#intertrans-form').show();
		return false;
	} );
JS;
	$headerlib->add_jq_onready($jq);

	return <<<HTML
<div class="intertrans" id="intertrans-indicator">
	<input type="checkbox" id="intertrans-active"/>
	<label for="intertrans-active">$text</label>
	<div>$help</div>
</div>
<div class="intertrans" id="intertrans-form">
	<form method="post" action="tiki-interactive_trans.php">
		<table>
		</table>
		<p>
			<input type="submit" value="$save"/>
			<input type="reset" value="$cancel"/>
		</p>
		<p>$note</p>
	</form>
</div>
HTML;
}

function sort_strings_by_length( $a, $b ) {
	$a = strlen( $a[1] );
	$b = strlen( $b[1] );

	if( $a == $b ) {
		return 0;
	} elseif( $a > $b ) {
		return -1;
	} else {
		return 1;
	}
}
