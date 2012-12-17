var define;
(function () {
	'use strict';

	var origDefine = define;

	/**
	 * A define implementation that can be used to wrap any other amd
	 * define implementation to provide module naming functionality.
	 *
	 * Only works with not-injected script elements. Not-injected script
	 * elements are elements that appear either literally in the
	 * document (a plain old <script ...></script>) or that are written
	 * to the document with document.write.
	 */
	function defineAnon(module) {
		var args = Array.prototype.slice.call(arguments);
		if ('string' !== typeof module) {
			var scripts = document.getElementsByTagName('script');
			var script;
			// On IE, it's the first script element that is in interactive readyState.
			// On other browsers, it's the last script element.
			for (var i = 0; i < scripts.length; i++) {
				script = scripts[i];
				if ('interactive' === script.readyState) {
					break;
				}
			}
			if (   script
				&& script.getAttribute
				&& script.getAttribute('data-gg-define')) {
				args.unshift(script.getAttribute('data-gg-define'));
			}
		}
		return origDefine.apply(null, args);
	}

	// Because the define.amd property is read, for example by jQuery,
	// for I know not what reason.
    defineAnon.amd = origDefine.amd;

    define = defineAnon;
}());
