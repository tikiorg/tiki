var define;
(function (global) {
	'use strict';

	// Aloha's define implementation depens on almond.js
	var origDefine = define;

	// This list is the same as in build-modular.js
	var alohaPlugins = [
		'common/ui',
		'common/link',
		'common/table',
		'common/list',
		'common/image',
		'common/highlighteditables',
		'common/format',
		'common/dom-to-xhtml',
		'common/contenthandler',
		'common/characterpicker',
		'common/commands',
		'common/align',
		'common/abbr',
		'common/block',
		'common/horizontalruler',
		'common/undo',
		'common/paste',
		'extra/cite',
		'extra/flag-icons',
		'extra/numerated-headers',
		'extra/formatlesspaste',
		'extra/linkbrowser',
		'extra/imagebrowser',
		'extra/ribbon',
		'extra/toc',
		'extra/wai-lang',
		'extra/headerids',
		'extra/metaview',
		'extra/listenforcer'
	];

	function qualifyPlugin(plugin) {
		for (var i = 0; i < alohaPlugins.length; i++) {
			var qualifiedPlugin = alohaPlugins[i];
			var parts = qualifiedPlugin.split('/');
			if (parts[1] === plugin) {
				return qualifiedPlugin;
			}
		}
		throw "Unable to determine bundle for plugin " + plugin;
	}

	function pluginFromModule(module) {
		// An aloha plugin's module name is of the form link/link-plugin.
		var parts = module.match(/^(.*)\/(.*)-plugin$/);
		return parts && parts[1] === parts[2] && qualifyPlugin(parts[2]);
	}

	function ensurePlugin(plugin) {
		var Aloha = global.Aloha = global.Aloha || {};
		var settings = Aloha.settings = Aloha.settings || {};
		var plugins = settings.plugins = settings.plugins || {};
		var load = plugins.load = plugins.load || [];
		for (var i = 0; i < load.length; i++) {
			if (load[i] === plugin) {
				return;
			}
		}
		load.push(plugin);
	}

	/**
	 * A define that wraps an existing define implementation (almond.js)
	 * and ensures that any aloha plugins that are defined will also be
	 * loaded.
	 */
	function definePlugin(module) {
		if ('string' === typeof module) {
			var plugin = pluginFromModule(module);
			if (plugin) {
				ensurePlugin(plugin);
			}
		}
		return origDefine.apply(null, arguments);
	}

	define = definePlugin;
}(window));
