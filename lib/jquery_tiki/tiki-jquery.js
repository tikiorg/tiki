// $Id: tiki-jquery.js 16989 2009-02-27 19:37:11Z jonnybradley $
// JavaScript glue for JQuery (1.3.2) in TikiWiki (3.0)

var $jq = jQuery.noConflict();

$jq(document).ready( function() { // JQuery's DOM is ready event - before onload

	// override existing show/hide routines here
	show = function (foo,f,section) {
		if ($jq("#" + foo).hasClass("tabcontent")) {
			showJQ("#" + foo, jqueryTiki.effect_tabs, jqueryTiki.effect_tabs_speed, jqueryTiki.effect_tabs_direction);
		} else {
			showJQ("#" + foo, jqueryTiki.effect, jqueryTiki.effect_speed, jqueryTiki.effect_direction);
		}
		if (f) { setCookie(foo, "o", section); }
	};
	
	hide = function (foo,f, section) {
		if ($jq("#" + foo).hasClass("tabcontent")) {
			hideJQ("#" + foo, jqueryTiki.effect_tabs, jqueryTiki.effect_tabs_speed, jqueryTiki.effect_tabs_direction);
		} else {
			hideJQ("#" + foo, jqueryTiki.effect, jqueryTiki.effect_speed, jqueryTiki.effect_direction);
		}
		if (f) {
			var wasnot = getCookie(foo, section, 'x') == 'x';
			setCookie(foo, "c", section);
			if (wasnot) {
				history.go(0);	// ik!
			}
		}
	};
	
	// flip function... unfortunately didn't use show/hide (ay?)
	
	flip = function (foo,style) {
		if ($jq("#" + foo).css("display") == "none") {
			setSessionVar('show_' + escape(foo),'y');
			showJQ("#" + foo, jqueryTiki.effect, jqueryTiki.effect_speed, jqueryTiki.effect_direction);
		} else {
			setSessionVar('show_' + escape(foo), 'n');
			hideJQ("#" + foo, jqueryTiki.effect, jqueryTiki.effect_speed, jqueryTiki.effect_direction);
		}
	};

	/* toggle CSS (tableless) layout columns
	 * won't animate nicely - one TODO for 4.0
	toggleCols = function (id,zeromargin,maincol) {	// TODO (it properly!)
		var showit = 'show_' + escape(id);
		if (!zeromargin) zeromargin = '';
		if (!id) id = '';
		if (!maincol) maincol = 'col1';
		if (document.getElementById(id).style.display == "none") {
			//show(id);
	        $jq("#" + id).fadeIn();
		    if (zeromargin == 'left') {
		        $jq("#" + maincol).animate({"marginLeft": ""});
		        //$jq("#" + maincol).css("marginLeft", "");
		    } else {
		        $jq("#" + maincol).animate({"marginRight": ""});
		        //$jq("#" + maincol).css("marginRight", "");
		    }
			setCookie(showit,'y');
		} else {
	        $jq("#" + id).fadeOut();
		    if (zeromargin == 'left') {
		        $jq("#" + maincol).animate({"marginLeft": "0"});
		        //$jq("#" + maincol).css("marginLeft", "0");

		    } else {
		        $jq("#" + maincol).animate({"marginRight": "0"});
		        //$jq("#" + maincol).css("marginRight", "0");
		    }
			hide(id);
			setCookie(showit,'n');
		}
	}; */


	
	// handle JQ effects
	
	showJQ = function (selector, effect, speed, dir) {
		if (effect == 'none') {
			$jq(selector).show();
		} else if (effect == '' || effect == 'normal') {
			$jq(selector).show(speed);
		} else if (effect == 'slide') {
			$jq(selector).slideDown(speed);
		} else if (effect == 'fade') {
			$jq(selector).fadeIn(speed);
		} else if (effect.match(/(.*)_ui$/).length > 1) {
			$jq(selector).show(effect.match(/(.*)_ui$/)[1], {direction: dir }, speed);
		} else {
			$jq(selector).show();
		}
	};
	
	hideJQ = function (selector, effect, speed, dir) {
		if (effect == 'none') {
			$jq(selector).hide();
		} else if (effect == '' || effect == 'normal') {
			$jq(selector).hide(speed);
		} else if (effect == 'slide') {
			$jq(selector).slideUp(speed);
		} else if (effect == 'fade') {
			$jq(selector).fadeOut(speed);
		} else if (effect.match(/(.*)_ui$/).length > 1) {
			$jq(selector).hide(effect.match(/(.*)_ui$/)[1], {direction: dir }, speed);
		} else {
			$jq(selector).hide();
		}
	};
	
	// tooltip functions and setup
	if (jqueryTiki.tooltips) {	// apply "cluetips" to all .tips class anchors
		$jq('a.tips').cluetip({splitTitle: '|', showTitle: false, width: '150px', cluezIndex: 400});
		$jq('a.titletips').cluetip({splitTitle: '|', cluezIndex: 400});
		
		/* overrides for overlib tooltips
		 * another one TODO for 4.0?
		overlib = function(data) {
			$jq().mousemove(function(e) {
				$jq().cluetip({splitTitle: '|', showTitle: false, width: '150px', cluezIndex: 400, tracking: true});
				$jq().cluetipContents = data;
				$jq().cluetip.activate;
				// just doesn't appear...
			});
			
		};
		nd = function() {
			// remove the tooltip
			$jq().cluetip.inactivate;
			$jq().unbind("mousemove", e);
		};*/
	}
	
	// superfish setup (CSS menu effects)
	if (jqueryTiki.superfish) {
		$jq('ul.cssmenu_horiz').superfish({
			animation: {opacity:'show', height:'show'},	// fade-in and slide-down animation
			speed: 'fast'								// faster animation speed
		});
		$jq('ul.cssmenu_vert').superfish({
			animation: {opacity:'show', height:'show'},	// fade-in and slide-down animation
			speed: 'fast'								// faster animation speed
		});
	}
	
	// tablesorter setup (sortable tables?)
	if (jqueryTiki.tablesorter) {
		$jq('.sortable').tablesorter({
			widthFixed: true,							// ??
//			widgets: ['zebra'],							// stripes (coming soon)
			debug: true 
		});
	}
});		// end $jq(document).ready


/* Autocomplete assistants */

function parseAutoJSON(data) {
	var parsed = [];
	return $jq.map(data, function(row) {
		return {
			data: row,
			value: row,
			result: row
		}
	});
}



