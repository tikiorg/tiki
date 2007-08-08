function loadComponent(url, template, htmlelement) {
    xajaxRequestUri = url;
    xajax_loadComponent(template, htmlelement);      
}
if (typeof xajax != "undefined") {
	xajax.loadingFunction = function() {
		show('ajaxLoading');
	};

	xajax.doneLoadingFunction = function() {
		hide('ajaxLoading');
	};
}



