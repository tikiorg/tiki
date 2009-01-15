// $Id:  $
// JavaScript glue for JQuery (1.2.6) in TikiWiki (3.0)

var $jq = jQuery.noConflict();

jQuery(document).ready( function() {// JQuery's DOM is ready event - before onload

	// override existing show/hide routines here
	show = function (foo,f,section) {
		$jq("#" + foo).show("fast");
		if (f) { setCookie(foo, "o", section); }
	}
	
	hide = function (foo,f, section) {
		$jq("#" + foo).hide("fast");
		if (f) {
			var wasnot = getCookie(foo, section, 'x') == 'x';
			setCookie(foo, "c", section);
			if (wasnot) {
				history.go(0);	// ik!
			}
		}
	}
	
});
