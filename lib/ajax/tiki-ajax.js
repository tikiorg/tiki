function loadComponent(url, template, htmlelement, max_tikitabs, last_user) {
	ajaxLoadingShow(htmlelement);
	
	xajax.config.requestURI = url;
	xajax_loadComponent(template, htmlelement, max_tikitabs, last_user);
}

if (typeof xajax != "undefined") {
	xajax.callback.global.onRequest = function() {
		show('ajaxLoading');
	};

	xajax.callback.global.beforeResponseProcessing = ajaxLoadingHide;

}

function ajaxLoadingShow(destName) {
	var $dest, $loading, pos, x, y, w, h;
	
	$dest = $jq('#' + destName);
	if ($dest.length === 0) {
		return;
	}
	$loading = $jq('#ajaxLoading');

	// find area of destination element
	pos = $dest.offset();
	pos.top = pos.top - $jq(window).scrollTop();
	pos.left = pos.left - $jq(window).scrollLeft();
	// clip to page
	if (pos.left + $dest.width() > $jq(window).width()) {
		w = $jq(window).width() - pos.left;
	} else {
		w = $dest.width();
	}
	if (pos.top + $dest.height() > $jq(window).height()) {
		h = $jq(window).height() - pos.top;
	} else {
		h = $dest.height();
	}
	x = pos.left + (w / 2) - ($loading.width() / 2);
	y = pos.top + (h / 2) - ($loading.height() / 2);
	
	// position loading div
	$loading.css('left', x).css('top', y);
	$jq('#ajaxLoadingBG').css('left', pos.left).css('top', pos.top).width($dest.width()).height($dest.height()).fadeIn("fast");
	
	show('ajaxLoading');

	
}

function ajaxLoadingHide() {
	hide('ajaxLoading');
	$jq('#ajaxLoadingBG').fadeOut("fast");
}

