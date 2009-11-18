function loadComponent(url, template, htmlelement, max_tikitabs, last_user) {
	// position loading div
	var destElement, loadingElement, pos, x, y, w, h;
	destElement = $jq('#' + htmlelement);
	loadingElement = $jq('#ajaxLoading');
	pos = destElement.offset();
	// clip to page
	if (pos.left + destElement.width() > $jq(window).width()) {
		w = $jq(window).width() - pos.left;
	} else {
		w = destElement.width();
	}
	if (pos.top + destElement.height() > $jq(window).height()) {
		h = $jq(window).height() - pos.top;
	} else {
		h = destElement.height();
	}
	x = pos.left + (w / 2) - (loadingElement.width() / 2);
	y = pos.top + (h / 2) - (loadingElement.height() / 2);
	
	loadingElement.css('left', x).css('top', y);
	$jq('#ajaxLoadingBG').css('left', pos.left).css('top', pos.top).width(destElement.width()).height(destElement.height()).fadeIn("fast");
	
	show('ajaxLoading');

	xajax.config.requestURI = url;
	xajax_loadComponent(template, htmlelement, max_tikitabs, last_user);
}

if (typeof xajax != "undefined") {
	xajax.callback.global.onRequest = function() {
		show('ajaxLoading');
	};

	xajax.callback.global.beforeResponseProcessing = function() {
		hide('ajaxLoading');
		$jq('#ajaxLoadingBG').fadeOut("fast");
	};
}

