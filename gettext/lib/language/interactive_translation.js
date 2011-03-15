$('.intertrans').find('*').addClass('intertrans');
$('#intertrans-form :reset').click( function() {
	$('#intertrans-form').hide();
	return false;
} );
$('body').css('padding-top', 64);

var interTransDone = false;
$('#intertrans-form form').submit( function( e ) {
	e.preventDefault();
	$('body, input[type="submit"]').css('cursor', 'wait');
	
	$.ajax({
		url: $(this).attr('action'),
		data: $(this).serialize(),
		success: function() {
			$('body').css('cursor', 'default');
			$('input[type="submit"]').css('cursor', 'pointer');
			$('#intertrans-form').hide();
			interTransDone = true;
		}
	});
	
	return false;
} );

var canTranslateIt = function( e ) {
	if( $('#intertrans-active:checked').length == 0 ||
			e.currentTarget.id.indexOf('intertrans-') === 0 ||
			$(e.currentTarget).parents("form.intertrans, #intertrans-form").length > 0 ) {
		return false;
	} else {
		return true;
	}
}

var interTransDeepestElement = -1;

$("#intertrans-active").click( function( e ) {
	if (interTransDone && !$(this).attr("checked")) {
		history.go(0);
	}
});

$(document).find('body *').click( function( e ) {
	if( !canTranslateIt( e ) ) { return; }
	
	e.preventDefault();
	var text = $(this).text();
	var val = $(this).val();
	var alt = $(this).attr('alt');
	var title = $(this).attr('title');
	if ($(this).parent().hasClass('tikihelp')
		|| $(this).parent().hasClass('titletips')
		|| $(this).parent().parent().hasClass('tips'))
	{
		var cluetip_title = $('#cluetip-title').text();
		var cluetip_inner = $('#cluetip-inner').text();
	}
	
	// data is defined on lib/smarty_tiki/function.interactivetranslation.php
	var applicable = $(data).filter( function( k ) {
		var textToSearchFor = $('<span>' + this[1] + '</span>').text(); // The spans just make sure this calls jQuery( html ) instead of another jQuery constructor. text() will strip them.
		return textToSearchFor.length && (( text && text.length && text.indexOf( textToSearchFor ) != -1 )
			|| ( val && val.length && val.indexOf( textToSearchFor ) != -1 )
			|| ( alt && alt.length && alt.indexOf( textToSearchFor ) != -1 )
			|| ( title && title.length && title.indexOf( textToSearchFor ) != -1 )
			|| ( cluetip_title && cluetip_title.length && cluetip_title.indexOf( textToSearchFor ) != -1 )
			|| ( cluetip_inner && cluetip_inner.length && cluetip_inner.indexOf( textToSearchFor ) != -1 ));
	} );
	
	$('#intertrans-form table').empty();
	
	if (applicable.length > 0) {
		$('#intertrans-empty').hide();
		$('#intertrans-close').hide();
		$('#intertrans-submit').show();
		$('#intertrans-cancel').show();
		$('#intertrans-help').show();
		
		$('#intertrans-form table')
			.append( applicable.map( function() {
				var r = $('<tr><td class="original"></td><td><input type="text" name="trans[]"/><input type="hidden" name="source[]"/></td></tr>');
				r.find('td.original').text( this[0] );
				if (this[2]) {	// new ones in italic
					r.find('td.original').css("font-style", 'italic');
				}
				r.find(':hidden').val( this[0] );
				r.find(':text').val( this[1] );
				return r[0];
			} ) );
	} else {
		$('#intertrans-empty').show();
		$('#intertrans-close').show();
		$('#intertrans-submit').hide();
		$('#intertrans-cancel').hide();
		$('#intertrans-help').hide();
	}
	
	$('#intertrans-form').show().keydown(function (e) {
            if (e.keyCode === 27) {
                e.preventDefault();
                $(this).hide();
            }
        }).find("input:first").focus();
	return false;
} ).mouseover(function( e ) {
	if( !canTranslateIt( e ) ) { return; }
	var myparents = $(this).parents();
	if ( myparents.length > interTransDeepestElement ) {	// trying to only highlight one element at a time
		var shad = "black 0 0 5px";
		$(this).css({"box-shadow":shad, "-moz-box-shadow":shad, "-webkit-box-shadow":shad});
		$(myparents[interTransDeepestElement]).css({"box-shadow":"", "-moz-box-shadow":"", "-webkit-box-shadow":""});
		interTransDeepestElement = myparents.length;
	}
}).mouseout(function( e ) {
	if( !canTranslateIt( e ) ) { return; }
	$(this).css({"box-shadow":"", "-moz-box-shadow":"", "-webkit-box-shadow":""});
	interTransDeepestElement = -1;
});
