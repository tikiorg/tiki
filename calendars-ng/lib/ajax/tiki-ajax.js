function loadComponent(url, template, htmlelement, max_tikitabs) {
	xajaxRequestUri = url;
	xajax_loadComponent(template, htmlelement, max_tikitabs);
}
if (typeof xajax != "undefined") {
	xajax.loadingFunction = function() {
		show('ajaxLoading');
	};

	xajax.doneLoadingFunction = function() {
		hide('ajaxLoading');
	};
}



