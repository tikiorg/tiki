<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//This the default icon set, it associates icon names to icon fonts. It is used as fallback for all other icon sets.

// This script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

function iconset_default()
{
	return [
		'name' => tr('Default (Font-awesome)'), // Mandatory, will be displayed as Icon set option in the Look&Feel admin UI
		'description' => tr('The default system icon set using Font-awesome fonts'), // TODO display as Icon set description in the Look&Feel admin UI
		'tag' => 'span', // The default html tag for the icons in the icon set.
		'prepend' => 'fa fa-',
		'append' => ' fa-fw',
		'rotate' => [
			// Rotate the icon (only values accepted by fontawesome)
			'90' => ' fa-rotate-90',
			'180' => ' fa-rotate-180',
			'270' => ' fa-rotate-270',
			'horizontal' => ' fa-flip-horizontal',
			'vertical' => ' fa-flip-vertical',
		],
		'icons' => [
			/* This is the definition of an icon in the icon set if it's an "alias" to one of the default icons.
			 * The key must be unique, it is the "name" parameter at the icon function,
			 * so eg: {icon name="actions"}
			 * will find 'actions' in the array and apply the specified configuration */

			'actions' => [
				'id' => 'play-circle',    // id to match the defaults defined below
			],
			'admin' => [
				'id' => 'cog',
			],
			'add' => [
				'id' => 'plus-circle',
			],
			'admin_ads' => [
				'id' => 'film',
			],
			'admin_articles' => [
				'id' => 'newspaper-o',
			],
			'admin_blogs' => [
				'id' => 'bold',
			],
			'admin_calendar' => [
				'id' => 'calendar',
			],
			'admin_category' => [
				'id' => 'sitemap fa-rotate-270',
			],
			'admin_comments' => [
				'id' => 'comment',
			],
			'admin_community' => [
				'id' => 'group',
			],
			'admin_connect' => [
				'id' => 'link',
			],
			'admin_copyright' => [
				'id' => 'copyright',
			],
			'admin_directory' => [
				'id' => 'folder-o',
			],
			'admin_faqs' => [
				'id' => 'question',
			],
			'admin_features' => [
				'id' => 'power-off',
			],
			'admin_fgal' => [
				'id' => 'folder-open',
			],
			'admin_forums' => [
				'id' => 'comments',
			],
			'admin_freetags' => [
				'id' => 'tags',
			],
			'admin_gal' => [
				'id' => 'file-image-o',
			],
			'admin_general' => [
				'id' => 'cog',
			],
			'admin_i18n' => [
				'id' => 'language',
			],
			'admin_intertiki' => [
				'id' => 'exchange',
			],
			'admin_login' => [
				'id' => 'sign-in',
			],
			'admin_user' => [
				'id' => 'user',
			],
			'admin_look' => [
				'id' => 'image',
			],
			'admin_maps' => [
				'id' => 'map-marker',
			],
			'admin_messages' => [
				'id' => 'envelope-o',
			],
			'admin_metatags' => [
				'id' => 'tag',
			],
			'admin_module' => [
				'id' => 'cogs',
			],
			'admin_payment' => [
				'id' => 'credit-card',
			],
			'admin_performance' => [
				'id' => 'tachometer',
			],
			'admin_polls' => [
				'id' => 'tasks',
			],
			'admin_profiles' => [
				'id' => 'cube',
			],
			'admin_rating' => [
				'id' => 'check-square',
			],
			'admin_rss' => [
				'id' => 'rss',
			],
			'admin_score' => [
				'id' => 'trophy',
			],
			'admin_search' => [
				'id' => 'search',
			],
			'admin_semantic' => [
				'id' => 'arrows-h',
			],
			'admin_security' => [
				'id' => 'lock',
			],
			'admin_sefurl' => [
				'id' => 'search-plus',
			],
			'admin_share' => [
				'id' => 'share-alt',
			],
			'admin_socialnetworks' => [
				'id' => 'thumbs-up',
			],
			'admin_stats' => [
				'id' => 'bar-chart',
			],
			'admin_textarea' => [
				'id' => 'edit',
			],
			'admin_trackers' => [
				'id' => 'database',
			],
			'admin_userfiles' => [
				'id' => 'cog',
			],
			'admin_video' => [
				'id' => 'video-camera',
			],
			'admin_webmail' => [
				'id' => 'inbox',
			],
			'admin_webservices' => [
				'id' => 'cog',
			],
			'admin_wiki' => [
				'id' => 'file-text-o',
			],
			'admin_workspace' => [
				'id' => 'desktop',
			],
			'admin_wysiwyg' => [
				'id' => 'file-text',
			],
			'admin_print' => [
				'id' => 'print',
			],
			'admin_packages' => [
				'id' => 'gift',
			],
			'admin_rtc' => [
				'id' => 'bullhorn',
			],
			//align-center in defaults
			//align-justify in defaults
			//align-left in defaults
			//align-right in defaults
			//anchor in defaults
			'articles' => [
				'id' => 'newspaper-o',
			],
			//arrow-up in defaults
			'attach' => [
				'id' => 'paperclip',
			],
			'audio' => [
				'id' => 'file-audio-o',
			],
			'back' => [
				'id' => 'arrow-left',
			],
			'background-color' => [
				'id' => 'paint-brush',
			],
			'backlink' => [
				'id' => 'reply',
			],
			//backward in defaults
			'backward_step' => [
				'id' => 'step-backward',
			],
			//ban in defaults
			//book in defaults
			'box' => [
				'id' => 'list-alt',
			],
			//caret-left & caret-right in defaults
			'cart' => [
				'id' => 'shopping-cart',
			],
			'chart' => [
				'id' => 'area-chart',
			],
			//code in defaults
			'code_file' => [
				'id' => 'file-code-o',
			],
			'collapsed' => [
				'id' => 'plus-square-o',
			],
			//columns in defaults
			'comments' => [
				'id' => 'comments-o',
			],
			'compose' => [
				'id' => 'pencil',
			],
			'computer' => [
				'id' => 'desktop',
			],
			'contacts' => [
				'id' => 'group',
			],
			'content-template' => [
				'id' => 'file-o',
			],
			//copy in defaults
			'create' => [
				'id' => 'plus',
			],
			//database in defaults
			'delete' => [
				'id' => 'times',
			],
			'difference' => [
				'id' => 'strikethrough',
			],
			'disable' => [
				'id' => 'minus-square',
			],
			'documentation' => [
				'id' => 'book',
			],
			'down' => [
				'id' => 'sort-desc',
			],
			//edit in defaults
			'education' => [
				'id' => 'graduation-cap',
			],
			'envelope' => [
				'id' => 'envelope-o',
			],
			'erase' => [
				'id' => 'eraser',
			],
			'error' => [
				'id' => 'exclamation-circle',
			],
			'excel' => [
				'id' => 'file-excel-o',
			],
			'expanded' => [
				'id' => 'minus-square-o',
			],
			'export' => [
				'id' => 'download',
			],
			//facebook in defaults
			'file' => [
				'id' => 'file-o',
			],
			'file-archive' => [
				'id' => 'folder',
			],
			'file-archive-open' => [
				'id' => 'folder-open',
			],
			//filter in defaults
			//flag in defaults
			'floppy' => [
				'id' => 'floppy-o',
			],
			'font-color' => [
				'id' => 'font',
				'class' => 'text-danger'
			],
			//forward in defaults
			'forward_step' => [
				'id' => 'step-forward',
			],
			'fullscreen' => [
				'id' => 'arrows-alt',
			],
			//group in defaults
			'h1' => [
				'id' => 'header',
			],
			'h2' => [
				'id' => 'header',
				'size' => '.9'
			],
			'h3' => [
				'id' => 'header',
				'size' => '.8'
			],
			'help' => [
				'id' => 'question-circle',
			],
			'history' => [
				'id' => 'clock-o',
			],
			//history in defaults
			'horizontal-rule' => [
				'id' => 'minus',
			],
			'html' => [
				'id' => 'html5',
			],
			'image' => [
				'id' => 'file-image-o',
			],
			'import' => [
				'id' => 'upload',
			],
			//indent in defaults
			'index' => [
				'id' => 'spinner',
			],
			'information' => [
				'id' => 'info-circle',
			],
			//italic in defaults
			'keyboard' => [
				'id' => 'keyboard-o',
			],
			'like' => [
				'id' => 'thumbs-up',
			],
			//link in defaults
			'link-external' => [
				'id' => 'external-link',
			],
			'link-external-alt' => [
				'id' => 'external-link-square',
			],
			//list in defaults
			'list-numbered' => [
				'id' => 'list-ol',
			],
			// special icons for list gui toolbars
			'listgui_display' => [
				'id' => 'desktop',
			],
			'listgui_filter' => [
				'id' => 'filter',
			],
			'listgui_format' => [
				'id' => 'indent',
			],
			'listgui_pagination' => [
				'id' => 'book',
			],
			'listgui_output' => [
				'id' => 'eye',
			],
			'listgui_column' => [
				'id' => 'columns',
			],
			'listgui_tablesorter' => [
				'id' => 'table',
			],
			'listgui_icon' => [
				'id' => 'user',
			],
			'listgui_body' => [
				'id' => 'align-justify',
			],
			'listgui_carousel' => [
				'id' => 'slideshare',
			],
			'listgui_sort' => [
				'id' => 'sort-alpha-desc',
			],
			'listgui_wikitext' => [
				'id' => 'file-text-o',
			],
			'listgui_caption' => [
				'id' => 'align-center',
			],
			//lock in defaults
			//same fa icon used for admin_security, but not the same in other icon sets
			'log' => [
				'id' => 'history',
			],
			'login' => [
				'id' => 'sign-in',
			],
			'logout' => [
				'id' => 'sign-out',
			],
			'mailbox' => [
				'id' => 'inbox',
			],
			//map in defaults
			'menu' => [
				'id' => 'bars',
			],
			'menu-extra' => [
				'id' => 'chevron-down',
			],
			'menuitem' => [
				'id' => 'angle-right',
			],
			'merge' => [
				'id' => 'random',
			],
			'minimize' => [
				'id' => 'compress',
			],
			//minus in defaults
			'module' => [
				'id' => 'cogs',
			],
			'more' => [
				'id' => 'ellipsis-h',
			],
			'move' => [
				'id' => 'exchange',
			],
			'next' => [
				'id' => 'arrow-right',
			],
			'notepad' => [
				'id' => 'file-text-o',
			],
			'notification' => [
				'id' => 'bell-o',
			],
			'off' => [
				'id' => 'power-off',
			],
			'ok' => [
				'id' => 'check-circle',
			],
			//outdent in defaults
			'page-break' => [
				'id' => 'scissors',
			],
			//paste in defaults
			//pause in defaults
			'pdf' => [
				'id' => 'file-pdf-o',
			],
			'permission' => [
				'id' => 'key',
			],
			//play in defaults
			'plugin' => [
				'id' => 'puzzle-piece',
			],
			'popup' => [
				'id' => 'list-alt',
			],
			'post' => [
				'id' => 'pencil',
			],
			'powerpoint' => [
				'id' => 'file-powerpoint-o',
			],
			'previous' => [
				'id' => 'arrow-left',
			],
			//print in defaults
			'quotes' => [
				'id' => 'quote-left',
			],
			'ranking' => [
				'id' => 'sort-numeric-asc',
			],
			//refresh in defaults
			//remove in defaults
			//repeat in defaults
			//rss in defaults
			//scissors in defaults
			'screencapture' => [
				'id' => 'camera',
			],
			//search in defaults
			'selectall' => [
				'id' => 'file-text',
			],
			//send in defaults
			'settings' => [
				'id' => 'wrench',
			],
			//share in defaults
			'sharethis' => [
				'id' => 'share-alt',
			],
			'smile' => [
				'id' => 'smile-o',
			],
			//sort in defaults
			'sort-down' => [
				'id' => 'sort-desc',
			],
			'sort-up' => [
				'id' => 'sort-asc',
			],
			//star in defaults
			'star-empty' => [
				'id' => 'star-o',
			],
			'star-empty-selected' => [
				'id' => 'star-o',
				'class' => 'text-success'
			],
			'star-half-rating' => [
				'id' => 'star-half-full',
			],
			'star-half-selected' => [
				'id' => 'star-half-full',
				'class' => 'text-success'
			],
			'star-selected' => [
				'id' => 'star',
				'class' => 'text-success'
			],
			'status-open' => [
				'id' => 'circle',
				'style' => 'color:green'
			],
			'status-pending' => [
				'id' => 'adjust',
				'style' => 'color:orange'
			],
			'status-closed' => [
				'id' => 'times-circle-o',
				'style' => 'color:grey'
			],
			//stop in defaults
			'stop-watching' => [
				'id' => 'eye-slash',
			],
			'structure' => [
				'id' => 'sitemap',
			],
			'success' => [
				'id' => 'check',
			],
			//table in defaults
			//tag in defaults
			//tags in defaults
			'textfile' => [
				'id' => 'file-text-o',
			],
			//th-list in defaults
			'three-d' => [
				'id' => 'cube',
			],
			//thumbs-down in defaults
			//thumbs-up in defaults
			'time' => [
				'id' => 'clock-o',
			],
			'title' => [
				'id' => 'text-width',
			],
			'toggle-off' => [
				'id' => 'toggle-off',
			],
			'toggle-on' => [
				'id' => 'toggle-on',
			],
			'trackers' => [
				'id' => 'database',
			],
			'translate' => [
				'id' => 'language',
			],
			'trash' => [
				'id' => 'trash-o',
			],
			//twitter in defaults
			//tv in defaults
			//undo in defaults
			//unlink in defaults
			//unlock in defaults
			'unlike' => [
				'id' => 'thumbs-down',
			],
			'up' => [
				'id' => 'sort-asc',
			],
			'video' => [
				'id' => 'file-video-o',
			],
			'video_file' => [
				'id' => 'file-video-o',
			],
			'vimeo' => [
				'id' => 'vimeo-square',
			],
			'view' => [
				'id' => 'search-plus',
			],
			'warning' => [
				'id' => 'exclamation-triangle',
			],
			'watch' => [
				'id' => 'eye',
			],
			'watch-group' => [
				'id' => 'group',
			],
			'wiki' => [
				'id' => 'file-text-o',
			],
			'wizard' => [
				'id' => 'magic',
			],
			'word' => [
				'id' => 'file-word-o',
			],
			'wysiwyg' => [
				'id' => 'file-text',
			],
			'zip' => [
				'id' => 'file-zip-o',
			],
		],
		/*
		 * All the available icons in this set (font-awesome in this case,
		 * from http://fortawesome.github.io/Font-Awesome/cheatsheet/)
		 * Version 4.5
		 */
		'defaults' => [
			'500px',
			'adjust',
			'adn',
			'align-center',
			'align-justify',
			'align-left',
			'align-right',
			'amazon',
			'ambulance',
			'anchor',
			'android',
			'angellist',
			'angle-double-down',
			'angle-double-left',
			'angle-double-right',
			'angle-double-up',
			'angle-down',
			'angle-left',
			'angle-right',
			'angle-up',
			'apple',
			'archive',
			'area-chart',
			'arrow-circle-down',
			'arrow-circle-left',
			'arrow-circle-o-down',
			'arrow-circle-o-left',
			'arrow-circle-o-right',
			'arrow-circle-o-up',
			'arrow-circle-right',
			'arrow-circle-up',
			'arrow-down',
			'arrow-left',
			'arrow-right',
			'arrow-up',
			'arrows',
			'arrows-alt',
			'arrows-h',
			'arrows-v',
			'asterisk',
			'at',
			'automobile',
			'backward',
			'balance-scale',
			'ban',
			'bank',
			'bar-chart',
			'bar-chart-o',
			'barcode',
			'bars',
			'battery-0',
			'battery-1',
			'battery-2',
			'battery-3',
			'battery-4',
			'battery-empty',
			'battery-full',
			'battery-half',
			'battery-quarter',
			'battery-three-quarters',
			'bed',
			'beer',
			'behance',
			'behance-square',
			'bell',
			'bell-o',
			'bell-slash',
			'bell-slash-o',
			'bicycle',
			'binoculars',
			'birthday-cake',
			'bitbucket',
			'bitbucket-square',
			'bitcoin',
			'black-tie',
			'bluetooth',
			'bluetooth-b',
			'bold',
			'bolt',
			'bomb',
			'book',
			'bookmark',
			'bookmark-o',
			'briefcase',
			'btc',
			'bug',
			'building',
			'building-o',
			'bullhorn',
			'bullseye',
			'bus',
			'buysellads',
			'cab',
			'calculator',
			'calendar',
			'calendar-check-o',
			'calendar-minus-o',
			'calendar-o',
			'calendar-plus-o',
			'calendar-times-o',
			'camera',
			'camera-retro',
			'car',
			'caret-down',
			'caret-left',
			'caret-right',
			'caret-square-o-down',
			'caret-square-o-left',
			'caret-square-o-right',
			'caret-square-o-up',
			'caret-up',
			'cart-arrow-down',
			'cart-plus',
			'cc',
			'cc-amex',
			'cc-diners-club',
			'cc-discover',
			'cc-jcb',
			'cc-mastercard',
			'cc-paypal',
			'cc-stripe',
			'cc-visa',
			'certificate',
			'chain',
			'chain-broken',
			'check',
			'check-circle',
			'check-circle-o',
			'check-square',
			'check-square-o',
			'chevron-circle-down',
			'chevron-circle-left',
			'chevron-circle-right',
			'chevron-circle-up',
			'chevron-down',
			'chevron-left',
			'chevron-right',
			'chevron-up',
			'child',
			'chrome',
			'circle',
			'circle-o',
			'circle-o-notch',
			'circle-thin',
			'clipboard',
			'clock-o',
			'clone',
			'close',
			'cloud',
			'cloud-download',
			'cloud-upload',
			'cny',
			'code',
			'code-fork',
			'codepen',
			'codiepie',
			'coffee',
			'cog',
			'cogs',
			'columns',
			'comment',
			'comment-o',
			'commenting',
			'commenting-o',
			'comments',
			'comments-o',
			'compass',
			'compress',
			'connectdevelop',
			'contao',
			'copy',
			'copyright',
			'creative-commons',
			'credit-card',
			'credit-card-alt',
			'crop',
			'crosshairs',
			'css3',
			'cube',
			'cubes',
			'cut',
			'cutlery',
			'dashboard',
			'dashcube',
			'database',
			'dedent',
			'delicious',
			'desktop',
			'deviantart',
			'diamond',
			'digg',
			'dollar',
			'dot-circle-o',
			'download',
			'dribbble',
			'dropbox',
			'drupal',
			'edge',
			'edit',
			'eject',
			'ellipsis-h',
			'ellipsis-v',
			'empire',
			'envelope',
			'envelope-o',
			'envelope-square',
			'eraser',
			'eur',
			'euro',
			'exchange',
			'exclamation',
			'exclamation-circle',
			'exclamation-triangle',
			'expand',
			'expeditedssl',
			'external-link',
			'external-link-square',
			'eye',
			'eye-slash',
			'eyedropper',
			'facebook',
			'facebook-official',
			'facebook-square',
			'fast-backward',
			'fast-forward',
			'fax',
			'female',
			'fighter-jet',
			'file',
			'file-archive-o',
			'file-audio-o',
			'file-code-o',
			'file-excel-o',
			'file-image-o',
			'file-movie-o',
			'file-o',
			'file-pdf-o',
			'file-photo-o',
			'file-picture-o',
			'file-powerpoint-o',
			'file-sound-o',
			'file-text',
			'file-text-o',
			'file-video-o',
			'file-word-o',
			'file-zip-o',
			'files-o',
			'film',
			'filter',
			'fire',
			'fire-extinguisher',
			'firefox',
			'flag',
			'flag-checkered',
			'flag-o',
			'flash',
			'flask',
			'flickr',
			'floppy-o',
			'folder',
			'folder-o',
			'folder-open',
			'folder-open-o',
			'font',
			'fonticons',
			'fort-awesome',
			'forumbee',
			'forward',
			'foursquare',
			'frown-o',
			'futbol-o',
			'gamepad',
			'gavel',
			'gbp',
			'ge',
			'gear',
			'gears',
			'genderless',
			'get-pocket',
			'gg',
			'gg-circle',
			'gift',
			'git',
			'git-square',
			'github',
			'github-alt',
			'github-square',
			'gittip',
			'glass',
			'globe',
			'google',
			'google-plus',
			'google-plus-square',
			'google-wallet',
			'graduation-cap',
			'group',
			'h-square',
			'hacker-news',
			'hand-grab-o',
			'hand-lizard-o',
			'hand-o-down',
			'hand-o-left',
			'hand-o-right',
			'hand-o-up',
			'hand-paper-o',
			'hand-peace-o',
			'hand-pointer-o',
			'hand-rock-o',
			'hand-scissors-o',
			'hand-spock-o',
			'hand-stop-o',
			'hashtag',
			'hdd-o',
			'header',
			'headphones',
			'heart',
			'heartbeat',
			'heart-o',
			'history',
			'home',
			'hospital-o',
			'hotel',
			'hourglass',
			'hourglass-1',
			'hourglass-2',
			'hourglass-3',
			'hourglass-end',
			'hourglass-half',
			'hourglass-o',
			'hourglass-start',
			'houzz',
			'html5',
			'i-cursor',
			'ils',
			'image',
			'inbox',
			'indent',
			'industry',
			'info',
			'info-circle',
			'inr',
			'instagram',
			'institution',
			'internet-explorer',
			'ioxhost',
			'italic',
			'joomla',
			'jpy',
			'jsfiddle',
			'key',
			'keyboard-o',
			'krw',
			'language',
			'laptop',
			'lastfm',
			'lastfm-square',
			'leaf',
			'leanpub',
			'legal',
			'lemon-o',
			'level-down',
			'level-up',
			'life-bouy',
			'life-buoy',
			'life-ring',
			'life-saver',
			'lightbulb-o',
			'line-chart',
			'link',
			'linkedin',
			'linkedin-square',
			'linux',
			'list',
			'list-alt',
			'list-ol',
			'list-ul',
			'location-arrow',
			'lock',
			'long-arrow-down',
			'long-arrow-left',
			'long-arrow-right',
			'long-arrow-up',
			'magic',
			'magnet',
			'mail-forward',
			'mail-reply',
			'mail-reply-all',
			'male',
			'map',
			'map-marker',
			'map-o',
			'map-pin',
			'map-signs',
			'mars',
			'mars-double',
			'mars-stroke',
			'mars-stroke-h',
			'mars-stroke-v',
			'maxcdn',
			'meanpath',
			'medium',
			'medkit',
			'meh-o',
			'mercury',
			'microphone',
			'microphone-slash',
			'minus',
			'minus-circle',
			'minus-square',
			'minus-square-o',
			'mixcloud',
			'mobile',
			'mobile-phone',
			'modx',
			'money',
			'moon-o',
			'mortar-board',
			'motorcycle',
			'mouse-pointer',
			'music',
			'navicon',
			'neuter',
			'newspaper-o',
			'object-group',
			'object-ungroup',
			'odnoklassniki',
			'odnoklassniki-square',
			'opencart',
			'openid',
			'opera',
			'optin-monster',
			'outdent',
			'pagelines',
			'paint-brush',
			'paper-plane',
			'paper-plane-o',
			'paperclip',
			'paragraph',
			'paste',
			'pause',
			'pause-circle',
			'pause-circle-o',
			'paw',
			'paypal',
			'pencil',
			'pencil-square',
			'pencil-square-o',
			'percent',
			'phone',
			'phone-square',
			'photo',
			'picture-o',
			'pie-chart',
			'pied-piper',
			'pied-piper-alt',
			'pinterest',
			'pinterest-p',
			'pinterest-square',
			'plane',
			'play',
			'play-circle',
			'play-circle-o',
			'plug',
			'plus',
			'plus-circle',
			'plus-square',
			'plus-square-o',
			'power-off',
			'print',
			'product-hunt',
			'puzzle-piece',
			'qq',
			'qrcode',
			'question',
			'question-circle',
			'quote-left',
			'quote-right',
			'ra',
			'random',
			'rebel',
			'recycle',
			'reddit',
			'reddit-alien',
			'reddit-square',
			'refresh',
			'registered',
			'remove',
			'renren',
			'reorder',
			'repeat',
			'reply',
			'reply-all',
			'retweet',
			'rmb',
			'road',
			'rocket',
			'rotate-left',
			'rotate-right',
			'rouble',
			'rss',
			'rss-square',
			'rub',
			'ruble',
			'rupee',
			'safari',
			'save',
			'scissors',
			'scribd',
			'search',
			'search-minus',
			'search-plus',
			'sellsy',
			'send',
			'send-o',
			'server',
			'share',
			'share-alt',
			'share-alt-square',
			'share-square',
			'share-square-o',
			'shekel',
			'sheqel',
			'shield',
			'ship',
			'shirtsinbulk',
			'shopping-bag',
			'shopping-basket',
			'shopping-cart',
			'sign-in',
			'sign-out',
			'signal',
			'simplybuilt',
			'sitemap',
			'skyatlas',
			'skype',
			'slack',
			'sliders',
			'slideshare',
			'smile-o',
			'soccer-ball-o',
			'sort',
			'sort-alpha-asc',
			'sort-alpha-desc',
			'sort-amount-asc',
			'sort-amount-desc',
			'sort-asc',
			'sort-desc',
			'sort-down',
			'sort-numeric-asc',
			'sort-numeric-desc',
			'sort-up',
			'soundcloud',
			'space-shuttle',
			'spinner',
			'spoon',
			'spotify',
			'square',
			'square-o',
			'stack-exchange',
			'stack-overflow',
			'star',
			'star-half',
			'star-half-empty',
			'star-half-full',
			'star-half-o',
			'star-o',
			'steam',
			'steam-square',
			'step-backward',
			'step-forward',
			'stethoscope',
			'sticky-note',
			'sticky-note-o',
			'stop',
			'stop-circle',
			'stop-circle-o',
			'street-view',
			'strikethrough',
			'stumbleupon',
			'stumbleupon-circle',
			'subscript',
			'subway',
			'suitcase',
			'sun-o',
			'superscript',
			'support',
			'table',
			'tablet',
			'tachometer',
			'tag',
			'tags',
			'tasks',
			'taxi',
			'television',
			'tencent-weibo',
			'terminal',
			'text-height',
			'text-width',
			'th',
			'th-large',
			'th-list',
			'thumb-tack',
			'thumbs-down',
			'thumbs-o-down',
			'thumbs-o-up',
			'thumbs-up',
			'ticket',
			'times',
			'times-circle',
			'times-circle-o',
			'tint',
			'toggle-down',
			'toggle-left',
			'toggle-off',
			'toggle-on',
			'toggle-right',
			'toggle-up',
			'trademark',
			'train',
			'transgender',
			'transgender-alt',
			'trash',
			'trash-o',
			'tree',
			'trello',
			'tripadvisor',
			'trophy',
			'truck',
			'try',
			'tty',
			'tumblr',
			'tumblr-square',
			'turkish-lira',
			'tv',
			'twitch',
			'twitter',
			'twitter-square',
			'umbrella',
			'underline',
			'undo',
			'university',
			'unlink',
			'unlock',
			'unlock-alt',
			'unsorted',
			'upload',
			'usb',
			'usd',
			'user',
			'user-md',
			'user-plus',
			'user-secret',
			'user-times',
			'users',
			'venus',
			'venus-double',
			'venus-mars',
			'viacoin',
			'vimeo',
			'video-camera',
			'vimeo-square',
			'vine',
			'vk',
			'volume-down',
			'volume-off',
			'volume-up',
			'warning',
			'wechat',
			'weibo',
			'weixin',
			'wheelchair',
			'wifi',
			'wikipedia-w',
			'windows',
			'won',
			'wordpress',
			'wrench',
			'xing',
			'xing-square',
			'y-combinator',
			'yahoo',
			'yc',
			'yelp',
			'yen',
			'youtube',
			'youtube-play',
			'youtube-square',

			// new icons with v4.6
			'american-sign-language-interpreting',
			'asl-interpreting',
			'assistive-listening-systems',
			'audio-description',
			'blind',
			'braille',
			'deaf',
			'deafness',
			'envira',
			'fa',
			'first-order',
			'font-awesome',
			'gitlab',
			'glide',
			'glide-g',
			'google-plus-circle',
			'google-plus-official',
			'hard-of-hearing',
			'instagram',
			'low-vision',
			'pied-piper',
			'question-circle-o',
			'sign-language',
			'signing',
			'snapchat',
			'snapchat-ghost',
			'snapchat-square',
			'themeisle',
			'universal-access',
			'viadeo',
			'viadeo-square',
			'volume-control-phone',
			'wheelchair-alt',
			'wpbeginner',
			'wpforms',
			'yoast',

			// new icons in 4.7
			'address-book',
			'address-book-o',
			'address-card',
			'address-card-o',
			'bandcamp',
			'bath',
			'bathtub',
			'drivers-license',
			'drivers-license-o',
			'eercast',
			'envelope-open',
			'envelope-open-o',
			'etsy',
			'free-code-camp',
			'grav',
			'handshake-o',
			'id-badge',
			'id-card',
			'id-card-o',
			'imdb',
			'linode',
			'meetup',
			'microchip',
			'podcast',
			'quora',
			'ravelry',
			's15',
			'shower',
			'snowflake-o',
			'superpowers',
			'telegram',
			'thermometer',
			'thermometer-0',
			'thermometer-1',
			'thermometer-2',
			'thermometer-3',
			'thermometer-4',
			'thermometer-empty',
			'thermometer-full',
			'thermometer-half',
			'thermometer-quarter',
			'thermometer-three-quarters',
			'times-rectangle',
			'times-rectangle-o',
			'user-circle',
			'user-circle-o',
			'user-o',
			'vcard',
			'vcard-o',
			'window-close',
			'window-close-o',
			'window-maximize',
			'window-minimize',
			'window-restore',
			'wpexplorer',
		],
	];
}
