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

;
;
;
;
;
;
;
;
;
;
;
;
;
;
;
;
;
;
;
;
;
;
;
;
;
;
;
;
;
;
;
;
;
;
;
;
;
define = window.Aloha.define;document.write('<script data-gg-define="ui/context" src="' + ALOHA_BASE_URL + 'plugins/common/ui/lib/context.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="ui/scopes" src="' + ALOHA_BASE_URL + 'plugins/common/ui/lib/scopes.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="ui/container" src="' + ALOHA_BASE_URL + 'plugins/common/ui/lib/container.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="ui/surface" src="' + ALOHA_BASE_URL + 'plugins/common/ui/lib/surface.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="ui/component" src="' + ALOHA_BASE_URL + 'plugins/common/ui/lib/component.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="ui/tab" src="' + ALOHA_BASE_URL + 'plugins/common/ui/lib/tab.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="ui/subguarded" src="' + ALOHA_BASE_URL + 'plugins/common/ui/lib/subguarded.js"></script>');
;
define = window.Aloha.define;document.write('<script data-gg-define="ui/floating" src="' + ALOHA_BASE_URL + 'plugins/common/ui/lib/floating.js"></script>');
;
define = window.Aloha.define;document.write('<script data-gg-define="ui/nls/i18n" src="' + ALOHA_BASE_URL + 'plugins/common/ui/nls/i18n.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="ui/toolbar" src="' + ALOHA_BASE_URL + 'plugins/common/ui/lib/toolbar.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="ui/settings" src="' + ALOHA_BASE_URL + 'plugins/common/ui/lib/settings.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="ui/ui-plugin" src="' + ALOHA_BASE_URL + 'plugins/common/ui/lib/ui-plugin.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="ui/ui" src="' + ALOHA_BASE_URL + 'plugins/common/ui/lib/ui.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="ui/arena" src="' + ALOHA_BASE_URL + 'plugins/common/ui/lib/arena.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="ui/vendor/jquery-ui-autocomplete-html" src="' + ALOHA_BASE_URL + 'plugins/common/ui/vendor/jquery-ui-autocomplete-html.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="ui/autocomplete" src="' + ALOHA_BASE_URL + 'plugins/common/ui/lib/autocomplete.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="ui/dialog" src="' + ALOHA_BASE_URL + 'plugins/common/ui/lib/dialog.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="ui/utils" src="' + ALOHA_BASE_URL + 'plugins/common/ui/lib/utils.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="ui/menuButton" src="' + ALOHA_BASE_URL + 'plugins/common/ui/lib/menuButton.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="ui/button" src="' + ALOHA_BASE_URL + 'plugins/common/ui/lib/button.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="ui/multiSplit" src="' + ALOHA_BASE_URL + 'plugins/common/ui/lib/multiSplit.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="ui/port-helper-attribute-field" src="' + ALOHA_BASE_URL + 'plugins/common/ui/lib/port-helper-attribute-field.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="ui/port-helper-multi-split" src="' + ALOHA_BASE_URL + 'plugins/common/ui/lib/port-helper-multi-split.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="ui/text" src="' + ALOHA_BASE_URL + 'plugins/common/ui/lib/text.js"></script>');
define = window.Aloha.define;document.write('<script data-gg-define="ui/toggleButton" src="' + ALOHA_BASE_URL + 'plugins/common/ui/lib/toggleButton.js"></script>');}());
