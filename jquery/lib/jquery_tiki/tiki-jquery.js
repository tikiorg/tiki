// $Id:  $
// JavaScript glue for JQuery (1.2.6) in TikiWiki (3.0)

var $jq = jQuery.noConflict();

jQuery(document).ready( function() {// JQuery's DOM is ready event - before onload

	// override existing show/hide routines here
	show = function (foo,f,section) {
		if ($jq("#" + foo).hasClass("tabcontent")) {
			if (jqueryTiki.effect_tabs == '') {
				$jq("#" + foo).show();
			} else if (jqueryTiki.effect_tabs == 'normal') {
				$jq("#" + foo).show(jqueryTiki.effect_speed);
			} else if (jqueryTiki.effect_tabs == 'slide') {
				$jq("#" + foo).slideDown(jqueryTiki.effect_speed);
			} else if (jqueryTiki.effect_tabs == 'fade') {
				$jq("#" + foo).fadeIn(jqueryTiki.effect_speed);
			}
		} else {
			if (jqueryTiki.effect == '') {
				$jq("#" + foo).show(jqueryTiki.effect_speed);
			} else if (jqueryTiki.effect == 'slide') {
				$jq("#" + foo).slideDown(jqueryTiki.effect_speed);
			} else if (jqueryTiki.effect == 'fade') {
				$jq("#" + foo).fadeIn(jqueryTiki.effect_speed);
			}
		}
		if (f) { setCookie(foo, "o", section); }
	}
	
	hide = function (foo,f, section) {
		if ($jq("#" + foo).hasClass("tabcontent")) {
			if (jqueryTiki.effect_tabs == '') {
				$jq("#" + foo).hide();
			} else if (jqueryTiki.effect_tabs == 'normal') {
				$jq("#" + foo).hide(jqueryTiki.effect_speed);
			} else if (jqueryTiki.effect_tabs == 'slide') {
				$jq("#" + foo).slideUp(jqueryTiki.effect_speed);
			} else if (jqueryTiki.effect_tabs == 'fade') {
				$jq("#" + foo).fadeOut(jqueryTiki.effect_speed);
			}
		} else {
			if (jqueryTiki.effect == '') {
				$jq("#" + foo).hide(jqueryTiki.effect_speed);
			} else if (jqueryTiki.effect == 'slide') {
				$jq("#" + foo).slideUp(jqueryTiki.effect_speed);
			} else if (jqueryTiki.effect == 'fade') {
				$jq("#" + foo).fadeOut(jqueryTiki.effect_speed);
			}
		}
		if (f) {
			var wasnot = getCookie(foo, section, 'x') == 'x';
			setCookie(foo, "c", section);
			if (wasnot) {
				history.go(0);	// ik!
			}
		}
	}
	
});
