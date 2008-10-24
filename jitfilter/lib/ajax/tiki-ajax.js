function loadComponent(url, template, htmlelement, max_tikitabs, last_user) {
	xajaxRequestUri = url;
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



