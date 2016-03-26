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
	return array(
		'name' => tr('Default (Font-awesome)'), // Mandatory, will be displayed as Icon set option in the Look&Feel admin UI
		'description' => tr('The default system icon set using Font-awesome fonts'), // TODO display as Icon set description in the Look&Feel admin UI
		'tag' => 'span', // The default html tag for the icons in the icon set.
		'prepend' => 'fa fa-',
		'append' => ' fa-fw',
		'icons' => array(
			/* This is the definition of an icon in the icon set if it's an "alias" to one of the default icons.
			 * The key must be unique, it is the "name" parameter at the icon function,
			 * so eg: {icon name="actions"}
			 * will find 'actions' in the array and apply the specified configuration */

			'actions' => array( 
				'id' => 'play-circle',    // id to match the defaults defined below
			),
			'admin' => array(
				'id' => 'cog',
			),
			'add' => array(
				'id' => 'plus-circle',
			),
			'admin_ads' => array(
				'id' => 'film',
			),
			'admin_articles' => array(
				'id' => 'newspaper-o',
			),
			'admin_blogs' => array(
				'id' => 'bold',
			),
			'admin_calendar' => array(
				'id' => 'calendar',
			),
			'admin_category' => array(
				'id' => 'sitemap fa-rotate-270',
			),
			'admin_comments' => array(
				'id' => 'comment',
			),
			'admin_community' => array(
				'id' => 'group',
			),
			'admin_connect' => array(
				'id' => 'link',
			),
			'admin_copyright' => array(
				'id' => 'copyright',
			),
			'admin_directory' => array(
				'id' => 'folder-o',
			),
			'admin_faqs' => array(
				'id' => 'question',
			),
			'admin_features' => array(
				'id' => 'power-off',
			),
			'admin_fgal' => array(
				'id' => 'folder-open',
			),
			'admin_forums' => array(
				'id' => 'comments',
			),
			'admin_freetags' => array(
				'id' => 'tags',
			),
			'admin_gal' => array(
				'id' => 'file-image-o',
			),
			'admin_general' => array(
				'id' => 'cog',
			),
			'admin_i18n' => array(
				'id' => 'language',
			),
			'admin_intertiki' => array(
				'id' => 'exchange',
			),
			'admin_login' => array(
				'id' => 'sign-in',
			),
			'admin_look' => array(
				'id' => 'image',
			),
			'admin_maps' => array(
				'id' => 'map-marker',
			),
			'admin_messages' => array(
				'id' => 'envelope-o',
			),
			'admin_metatags' => array(
				'id' => 'tag',
			),
			'admin_module' => array(
				'id' => 'cogs',
			),
			'admin_payment' => array(
				'id' => 'credit-card',
			),
			'admin_performance' => array(
				'id' => 'tachometer',
			),
			'admin_polls' => array(
				'id' => 'tasks',
			),
			'admin_profiles' => array(
				'id' => 'cube',
			),
			'admin_rating' => array(
				'id' => 'check-square',
			),
			'admin_rss' => array(
				'id' => 'rss',
			),
			'admin_score' => array(
				'id' => 'trophy',
			),
			'admin_search' => array(
				'id' => 'search',
			),
			'admin_semantic' => array(
				'id' => 'arrows-h',
			),
			'admin_security' => array(
				'id' => 'lock',
			),
			'admin_sefurl' => array(
				'id' => 'search-plus',
			),
			'admin_share' => array(
				'id' => 'share-alt',
			),
			'admin_socialnetworks' => array(
				'id' => 'thumbs-up',
			),
			'admin_stats' => array(
				'id' => 'bar-chart',
			),
			'admin_textarea' => array(
				'id' => 'edit',
			),
			'admin_trackers' => array(
				'id' => 'database',
			),
			'admin_userfiles' => array(
				'id' => 'cog',
			),
			'admin_video' => array(
				'id' => 'video-camera',
			),
			'admin_webmail' => array(
				'id' => 'inbox',
			),
			'admin_webservices' => array(
				'id' => 'cog',
			),
			'admin_wiki' => array(
				'id' => 'file-text-o',
			),
			'admin_workspace' => array(
				'id' => 'desktop',
			),
			'admin_wysiwyg' => array(
				'id' => 'file-text',
			),
			//align-center in defaults
			//align-justify in defaults
			//align-left in defaults
			//align-right in defaults
			//anchor in defaults
			'articles' => array(
				'id' => 'newspaper-o',
			),
			//arrow-up in defaults
			'attach' => array(
				'id' => 'paperclip',
			),
			'audio' => array(
				'id' => 'file-audio-o',
			),
			'back' => array(
				'id' => 'arrow-left',
			),
			'background-color' => array(
				'id' => 'paint-brush',
			),
			'backlink' => array(
				'id' => 'reply',
			),
			//backward in defaults
			'backward_step' => array(
				'id' => 'step-backward',
			),
			//ban in defaults
			//book in defaults
			'box' => array(
				'id' => 'list-alt',
			),
			//caret-left & caret-right in defaults
			'cart' => array(
				'id' => 'shopping-cart',
			),
			'chart' => array(
				'id' => 'area-chart',
			),
			'check' => array(
				'id' => 'check-square-o',
			),
			//code in defaults
			'code_file' => array(
				'id' => 'file-code-o',
			),
			'collapsed' => array(
				'id' => 'plus-square-o',
			),
			//columns in defaults
			'comments' => array(
				'id' => 'comments-o',
			),
			'compose' => array(
				'id' => 'pencil',
			),
			'computer' => array(
				'id' => 'desktop',
			),
			'contacts' => array(
				'id' => 'group',
			),
			'content-template' => array(
				'id' => 'file-o',
			),
			//copy in defaults
			'create' => array(
				'id' => 'plus',
			),
			//database in defaults
			'delete' => array(
				'id' => 'times',
			),
			'difference' => array(
				'id' => 'strikethrough',
			),
			'disable' => array(
				'id' => 'minus-square',
			),
			'documentation' => array(
				'id' => 'book',
			),
			'down' => array(
				'id' => 'sort-desc',
			),
			//edit in defaults
			'education' => array(
				'id' => 'graduation-cap',
			),
			'envelope' => array(
				'id' => 'envelope-o',
			),
			'erase' => array(
				'id' => 'eraser',
			),
			'error' => array(
				'id' => 'exclamation-circle',
			),
			'excel' => array(
				'id' => 'file-excel-o',
			),
			'expanded' => array(
				'id' => 'minus-square-o',
			),
			'export' => array(
				'id' => 'download',
			),
			//facebook in defaults
			'file' => array(
				'id' => 'file-o',
			),
			'file-archive' => array(
				'id' => 'folder',
			),
			'file-archive-open' => array(
				'id' => 'folder-open',
			),
			//filter in defaults
			//flag in defaults
			'floppy' => array(
				'id' => 'floppy-o',
			),
			'font-color' => array(
				'id' => 'font',
				'class' => 'text-danger'
			),
			//forward in defaults
			'forward_step' => array(
				'id' => 'step-forward',
			),
			'fullscreen' => array(
				'id' => 'arrows-alt',
			),
			//group in defaults
			'h1' => array(
				'id' => 'header',
			),
			'h2' => array(
				'id' => 'header',
				'size' => '.9'
			),
			'h3' => array(
				'id' => 'header',
				'size' => '.8'
			),
			'help' => array(
				'id' => 'question-circle',
			),
			'history' => array(
				'id' => 'clock-o',
			),
			//history in defaults
			'horizontal-rule' => array(
				'id' => 'minus',
			),
			'html' => array(
				'id' => 'html5',
			),
			'image' => array(
				'id' => 'file-image-o',
			),
			'import' => array(
				'id' => 'upload',
			),
			//indent in defaults
			'index' => array(
				'id' => 'spinner',
			),
			'information' => array(
				'id' => 'info-circle',
			),
			//italic in defaults
			'keyboard' => array(
				'id' => 'keyboard-o',
			),
			//link in defaults
			'link-external' => array(
				'id' => 'external-link',
			),
			'link-external-alt' => array(
				'id' => 'external-link-square',
			),
			//list in defaults
			'list-numbered' => array(
				'id' => 'list-ol',
			),
			//lock in defaults
			//same fa icon used for admin_security, but not the same in other icon sets
			'log' => array(
				'id' => 'history',
			),
			'login' => array(
				'id' => 'sign-in',
			),
			'logout' => array(
				'id' => 'sign-out',
			),
			'mailbox' => array(
				'id' => 'inbox',
			),
			//map in defaults
			'menu' => array(
				'id' => 'bars',
			),
			'menu-extra' => array(
				'id' => 'chevron-down',
			),
			'menuitem' => array(
				'id' => 'angle-right',
			),
			'merge' => array(
				'id' => 'random',
			),
			'minimize' => array(
				'id' => 'compress',
			),
			//minus in defaults
			'module' => array(
				'id' => 'cogs',
			),
			'more' => array(
				'id' => 'ellipsis-h',
			),
			'move' => array(
				'id' => 'exchange',
			),
			'next' => array(
				'id' => 'arrow-right',
			),
			'notepad' => array(
				'id' => 'file-text-o',
			),
			'notification' => array(
				'id' => 'bell-o',
			),
			'off' => array(
				'id' => 'power-off',
			),
			'ok' => array(
				'id' => 'check-circle',
			),
			//outdent in defaults
			'page-break' => array(
				'id' => 'scissors',
			),
			//paste in defaults
			//pause in defaults
			'pdf' => array(
				'id' => 'file-pdf-o',
			),
			'permission' => array(
				'id' => 'key',
			),
			//play in defaults
			'plugin' => array(
				'id' => 'puzzle-piece',
			),
			'popup' => array(
				'id' => 'list-alt',
			),
			'post' => array(
				'id' => 'pencil',
			),
			'powerpoint' => array(
				'id' => 'file-powerpoint-o',
			),
			'previous' => array(
				'id' => 'arrow-left',
			),
			//print in defaults
			'quotes' => array(
				'id' => 'quote-left',
			),
			'ranking' => array(
				'id' => 'sort-numeric-asc',
			),
			//refresh in defaults
			//remove in defaults
			//repeat in defaults
			//rss in defaults
			//scissors in defaults
			'screencapture' => array(
				'id' => 'camera',
			),
			//search in defaults
			'selectall' => array(
				'id' => 'file-text',
			),
			//send in defaults
			'settings' => array(
				'id' => 'wrench',
			),
			//share in defaults
			'sharethis' => array(
				'id' => 'share-alt',
			),
			'smile' => array(
				'id' => 'smile-o',
			),
			//sort in defaults
			'sort-down' => array(
				'id' => 'sort-desc',
			),
			'sort-up' => array(
				'id' => 'sort-asc',
			),
			//star in defaults
			'star-empty' => array(
				'id' => 'star-o',
			),
			'star-empty-selected' => array(
				'id' => 'star-o',
				'class' => 'text-success'
			),
			'star-half-rating' => array(
				'id' => 'star-half-full',
			),
			'star-half-selected' => array(
				'id' => 'star-half-full',
				'class' => 'text-success'
			),
			'star-selected' => array(
				'id' => 'star',
				'class' => 'text-success'
			),
			'status-open' => array(
				'id' => 'circle',
				'style' => 'color:green'
			),
			'status-pending' => array(
				'id' => 'circle',
				'style' => 'color:orange'
			),
			'status-closed' => array(
				'id' => 'circle',
				'style' => 'color:lightgrey'
			),
			//stop in defaults
			'stop-watching' => array(
				'id' => 'eye-slash',
			),
			'structure' => array(
				'id' => 'sitemap',
			),
			'success' => array(
				'id' => 'check',
			),
			//table in defaults
			//tag in defaults
			//tags in defaults
			'textfile' => array(
				'id' => 'file-text-o',
			),
			//th-list in defaults
			'three-d' => array(
				'id' => 'cube',
			),
			//thumbs-down in defaults
			//thumbs-up in defaults
			'time' => array(
				'id' => 'clock-o',
			),
			'title' => array(
				'id' => 'text-width',
			),
			'toggle-off' => array(
				'id' => 'toggle-off',
			),
			'toggle-on' => array(
				'id' => 'toggle-on',
			),
			'trackers' => array(
				'id' => 'database',
			),
			'translate' => array(
				'id' => 'language',
			),
			'trash' => array(
				'id' => 'trash-o',
			),
			//twitter in defaults
			//tv in defaults
			//undo in defaults
			//unlink in defaults
			//unlock in defaults
			'up' => array(
				'id' => 'sort-asc',
			),
			'video' => array(
				'id' => 'file-video-o',
			),
			'video_file' => array(
				'id' => 'file-video-o',
			),
			'vimeo' => array(
				'id' => 'vimeo-square',
			),
			'view' => array(
				'id' => 'search-plus',
			),
			'warning' => array(
				'id' => 'exclamation-triangle',
			),
			'watch' => array(
				'id' => 'eye',
			),
			'watch-group' => array(
				'id' => 'group',
			),
			'wiki' => array(
				'id' => 'file-text-o',
			),
			'wizard' => array(
				'id' => 'magic',
			),
			'word' => array(
				'id' => 'file-word-o',
			),
			'wysiwyg' => array(
				'id' => 'file-text',
			),
			'zip' => array(
				'id' => 'file-zip-o',
			),
		),
		/*
		 * All the available icons in this set (font-awesome in this case,
		 * from http://fortawesome.github.io/Font-Awesome/cheatsheet/)
		 * Version 4.5
		 */
		'defaults' => array(
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
		),
	);

}