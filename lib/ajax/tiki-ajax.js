function loadComponent(url, template, htmlelement, max_tikitabs, last_user) {
	ajaxLoadingShow(htmlelement);
	
	xajax.config.requestURI = url;
	xajax_loadComponent(template, htmlelement, max_tikitabs, last_user);
}

function ajaxLoadingShow(destName) {
	var $dest, $loading, pos, x, y, w, h;
	
	$dest = $('#' + destName);
	if ($dest.length === 0) {
		return;
	}
	$loading = $('#ajaxLoading');

	// find area of destination element
	pos = $dest.offset();
	pos.top = pos.top - $(window).scrollTop();
	pos.left = pos.left - $(window).scrollLeft();
	// clip to page
	if (pos.left + $dest.width() > $(window).width()) {
		w = $(window).width() - pos.left;
	} else {
		w = $dest.width();
	}
	if (pos.top + $dest.height() > $(window).height()) {
		h = $(window).height() - pos.top;
	} else {
		h = $dest.height();
	}
	x = pos.left + (w / 2) - ($loading.width() / 2);
	y = pos.top + (h / 2) - ($loading.height() / 2);
	
	// position loading div
	$loading.css('left', x).css('top', y);
	$('#ajaxLoadingBG').css('left', pos.left).css('top', pos.top).width($dest.width()).height($dest.height()).fadeIn("fast");
	
	show('ajaxLoading');

	
}

function ajaxLoadingHide() {
	hide('ajaxLoading');
	$('#ajaxLoadingBG').fadeOut("fast");
}

if (typeof xajax !== 'undefined') {
	xajax.callback.global.onRequest = function () {
		show('ajaxLoading');
	};

	xajax.callback.global.beforeResponseProcessing = ajaxLoadingHide;

}
