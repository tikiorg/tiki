/*!
 * This file is part of Aloha Editor project http://aloha-editor.org
 *
 * Aloha Editor is a WYSIWYG HTML5 inline editing library and editor. 
 * Copyright (c) 2010-2012 Gentics Software GmbH, Vienna, Austria.
 * Contributors http://aloha-editor.org/contribution.php 
 * 
 * Aloha Editor is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or any later version.
 *
 * Aloha Editor is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * 
 * As an additional permission to the GNU GPL version 2, you may distribute
 * non-source (e.g., minimized or compacted) forms of the Aloha-Editor
 * source code without the copy of the GNU GPL normally required,
 * provided you include this license notice and a URL through which
 * recipients can access the Corresponding Source.
 */
(function() {
// Because almond.js clobbers these global variables, we preserve them.
// Also see aloha-define-cleanup.js
// This is the same as in lib/aloha-define-preserve.js
Aloha = window.Aloha || {};
Aloha._defineReplacedByAloha = window.define;
Aloha._requireReplacedByAloha = window.require;
Aloha._requirejsReplacedByAloha = window.requirejs;
// The modular build defers initialization by default.
Aloha.deferInit = true;

define = window.Aloha.define;document.write('<script data-gg-define="vendor/almond" src="' + ALOHA_BASE_URL + 'lib/vendor/almond.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="aloha-define-plugin" src="' + ALOHA_BASE_URL + 'lib/aloha-define-plugin.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="vendor/gg-define-anon" src="' + ALOHA_BASE_URL + 'lib/vendor/gg-define-anon.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="aloha-define" src="' + ALOHA_BASE_URL + 'lib/aloha-define.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="i18n" src="' + ALOHA_BASE_URL + 'lib/i18n.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="vendor/class" src="' + ALOHA_BASE_URL + 'lib/vendor/class.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="vendor/pubsub/js/pubsub" src="' + ALOHA_BASE_URL + 'lib/vendor/pubsub/js/pubsub.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="util/json2" src="' + ALOHA_BASE_URL + 'lib/util/json2.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="vendor/amplify.store" src="' + ALOHA_BASE_URL + 'lib/vendor/amplify.store.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="aloha/nls/i18n" src="' + ALOHA_BASE_URL + 'lib/aloha/nls/i18n.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="aloha/rangy-core" src="' + ALOHA_BASE_URL + 'lib/aloha/rangy-core.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="util/class" src="' + ALOHA_BASE_URL + 'lib/util/class.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="util/lang" src="' + ALOHA_BASE_URL + 'lib/util/lang.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="aloha/ecma5shims" src="' + ALOHA_BASE_URL + 'lib/aloha/ecma5shims.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="util/dom" src="' + ALOHA_BASE_URL + 'lib/util/dom.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="aloha/pluginmanager" src="' + ALOHA_BASE_URL + 'lib/aloha/pluginmanager.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="aloha/core" src="' + ALOHA_BASE_URL + 'lib/aloha/core.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="aloha/console" src="' + ALOHA_BASE_URL + 'lib/aloha/console.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="util/range" src="' + ALOHA_BASE_URL + 'lib/util/range.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="util/arrays" src="' + ALOHA_BASE_URL + 'lib/util/arrays.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="util/strings" src="' + ALOHA_BASE_URL + 'lib/util/strings.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="aloha/engine" src="' + ALOHA_BASE_URL + 'lib/aloha/engine.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="aloha/selection" src="' + ALOHA_BASE_URL + 'lib/aloha/selection.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="aloha/block-jump" src="' + ALOHA_BASE_URL + 'lib/aloha/block-jump.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="aloha/markup" src="' + ALOHA_BASE_URL + 'lib/aloha/markup.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="aloha/observable" src="' + ALOHA_BASE_URL + 'lib/aloha/observable.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="aloha/registry" src="' + ALOHA_BASE_URL + 'lib/aloha/registry.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="aloha/contenthandlermanager" src="' + ALOHA_BASE_URL + 'lib/aloha/contenthandlermanager.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="util/trees" src="' + ALOHA_BASE_URL + 'lib/util/trees.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="util/maps" src="' + ALOHA_BASE_URL + 'lib/util/maps.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="util/browser" src="' + ALOHA_BASE_URL + 'lib/util/browser.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="util/dom2" src="' + ALOHA_BASE_URL + 'lib/util/dom2.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="util/functions" src="' + ALOHA_BASE_URL + 'lib/util/functions.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="util/misc" src="' + ALOHA_BASE_URL + 'lib/util/misc.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="aloha/ephemera" src="' + ALOHA_BASE_URL + 'lib/aloha/ephemera.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="aloha/editable" src="' + ALOHA_BASE_URL + 'lib/aloha/editable.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="aloha/plugin" src="' + ALOHA_BASE_URL + 'lib/aloha/plugin.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="aloha/command" src="' + ALOHA_BASE_URL + 'lib/aloha/command.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="aloha/jquery.aloha" src="' + ALOHA_BASE_URL + 'lib/aloha/jquery.aloha.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="aloha/sidebar" src="' + ALOHA_BASE_URL + 'lib/aloha/sidebar.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="util/position" src="' + ALOHA_BASE_URL + 'lib/util/position.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="aloha/repositorymanager" src="' + ALOHA_BASE_URL + 'lib/aloha/repositorymanager.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="aloha/repository" src="' + ALOHA_BASE_URL + 'lib/aloha/repository.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="aloha/repositoryobjects" src="' + ALOHA_BASE_URL + 'lib/aloha/repositoryobjects.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="aloha" src="' + ALOHA_BASE_URL + 'lib/aloha.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="aloha-define-restore" src="' + ALOHA_BASE_URL + 'lib/aloha-define-restore.js"></script>');}());
