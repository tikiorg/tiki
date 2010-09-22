function loadComponent(url, template, htmlelement, max_tikitabs, last_user) {
	ajaxLoadingShow(htmlelement);
	
	xajax.config.requestURI = url;
	xajax_loadComponent(template, htmlelement, max_tikitabs, last_user);
}

if (typeof xajax !== 'undefined') {
	xajax.callback.global.onRequest = function () {
		show('ajaxLoading');
	};

	xajax.callback.global.beforeResponseProcessing = ajaxLoadingHide;

}
