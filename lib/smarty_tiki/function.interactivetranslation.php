<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
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
	global $headerlib, $smarty;

	$strings = get_collected_strings();
	if( count( $strings ) == 0 ) {
		return;
	}

	usort( $strings, 'sort_strings_by_length' );

	$strings = json_encode( $strings );

	$text = tra('Interactive Translation');
	$help = tra('Once checked, click on any string to translate it.') . ' ';
	
	// add wrench icon link
	require_once $smarty->_get_plugin_filepath('block', 'self_link');
	$help .= smarty_block_self_link(
		array( '_icon'=>'wrench',
			   '_script'=>'tiki-edit_languages.php',
			   '_title'=>tra('Click here to go to Edit Languages')),
		'', $smarty);
	
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
	\$('body').css('padding-top', 64);

	var interTransDone = false;
	\$('#intertrans-form form').submit( function( e ) {
		e.preventDefault();

		\$('#intertrans-form').hide();
		\$.post( \$(this).attr('action'), \$(this).serialize() );
		interTransDone = true;
		
		return false;
	} );
	
	var canTranslateIt = function( e ) {
		if( \$('#intertrans-active:checked').length == 0 ||
				e.currentTarget.id.indexOf('intertrans-') === 0 ||
				\$(e.currentTarget).parents("form.intertrans, #intertrans-form").length > 0 ) {
			return false;
		} else {
			return true;
		}
	}
	
	var interTransDeepestElement = -1;
	
	\$("#intertrans-active").click( function( e ) {
		if (interTransDone && !\$(this).attr("checked")) {
			history.go(0);
		}
	});
	
	\$(document).find('body *').click( function( e ) {
		if( !canTranslateIt( e ) ) { return; }
		
		e.preventDefault();
		var text = \$(this).text();
		var val = \$(this).val();
		var alt = \$(this).attr('alt');
		var title = \$(this).attr('title');
		var applicable = \$(data).filter( function( k ) {
			var textToSearchFor = $('<span>' + this[1] + '</span>').text(); // The spans just make sure this calls jQuery( html ) instead of another jQuery constructor. text() will strip them.
			return textToSearchFor.length && (( text && text.length && text.indexOf( textToSearchFor ) != -1 )
				|| ( val && val.length && val.indexOf( textToSearchFor ) != -1 )
				|| ( alt && alt.length && alt.indexOf( textToSearchFor ) != -1 )
				|| ( title && title.length && title.indexOf( textToSearchFor ) != -1 ));
		} );
		if (applicable.length === 0) {
			applicable = \$([[ text, "", true ]]);
		}

		\$('#intertrans-form table')
			.empty()
			.append( applicable.map( function() {
				var r = \$('<tr><td class="original"></td><td><input type="text" name="trans[]"/><input type="hidden" name="source[]"/></td></tr>');
				r.find('td.original').text( this[0] );
				if (this[2]) {	// new ones in italic
					r.find('td.original').css("font-style", 'italic');
				}
				r.find(':hidden').val( this[0] );
				r.find(':text').val( this[1] );
				return r[0];
			} ) );
		
		\$('#intertrans-form').show().keydown(function (e) {
                if (e.keyCode === 27) {
                    e.preventDefault();
                    \$(this).hide();
                }
            }).find("input:first").focus();
		return false;
	} ).mouseover(function( e ) {
		if( !canTranslateIt( e ) ) { return; }
		var myparents = \$(this).parents();
		if ( myparents.length > interTransDeepestElement ) {	// trying to only highlight one element at a time
			var shad = "black 0 0 5px";
			\$(this).css({"box-shadow":shad, "-moz-box-shadow":shad, "-webkit-box-shadow":shad});
			\$(myparents[interTransDeepestElement]).css({"box-shadow":"", "-moz-box-shadow":"", "-webkit-box-shadow":""});
			interTransDeepestElement = myparents.length;
		}
	}).mouseout(function( e ) {
		if( !canTranslateIt( e ) ) { return; }
		\$(this).css({"box-shadow":"", "-moz-box-shadow":"", "-webkit-box-shadow":""});
		interTransDeepestElement = -1;
	});
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
		<p class="center">
			<input type="submit" value="$save"/>
			<input type="reset" value="$cancel"/>
		</p>
		<p class="description center">$note</p>
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
