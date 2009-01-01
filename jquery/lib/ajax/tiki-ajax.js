function loadComponent(url, template, htmlelement, max_tikitabs, last_user) {
	xajax.config.requestURI = url;
	show('ajaxLoading');
	xajax_loadComponent(template, htmlelement, max_tikitabs, last_user);
}
if (typeof xajax != "undefined") {
	xajax.loadingFunction = function() {
		show('ajaxLoading');
	};

	xajax.doneLoadingFunction = function() {
		hide('ajaxLoading');
	};
}



