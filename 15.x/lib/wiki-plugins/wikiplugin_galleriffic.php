<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_galleriffic_info()
{
	return array(
		'name' => tra('Galleriffic'),
		'documentation' => 'PluginGalleriffic',
		'description' => tra('Display a slideshow of images on a page'),
		'iconname' => 'image',
		'prefs' => array('wikiplugin_galleriffic', 'feature_file_galleries'),
		'introduced' => 8,
		'params' => array(
			'fgalId' => array(
				'required' => true,
				'name' => tra('File Gallery ID'),
				'description' => tra('ID number of the file gallery that contains the images to be displayed'),
				'since' => '8.0',
				'filter' => 'digits',
				'accepted' => 'ID',
				'default' => '',
				'profile_reference' => 'file_gallery',
			),
			'sort_mode' => array(
				'required' => false,
				'name' => tra('Sort Mode'),
				'description' => tr('Sort by database table field name, ascending (_asc) or descending (_desc). Examples:
					%0 or %1.', '<code>fileId_asc</code>', '<code>name_desc</code>'),
				'since' => '8.0',
				'filter' => 'word',
				'accepted' => tr('%0 or %1 with actual table field name in place of %2.', 'fieldname_asc',
					'fieldname_desc', 'fieldname'),
				'default' => 'created_desc',
			),
			'thumbsWidth' => array(
				'required' => false,
				'name' => tra('Thumbs Width'),
				'description' => tr('Width in pixels or percentage. (e.g. %0 or %1)', '<code>200px</code>',
					'<code>100%</code>'),
				'since' => '8.0',
				'filter' => 'text',
				'accepted' => 'Number of pixels followed by \'px\' or percent followed by \'%\' (e.g. "200px" or "100%").',
				'default' => '300px',
			),
			'imgWidth' => array(
				'required' => false,
				'name' => tra('Image Slideshow Width'),
				'description' => tr('Width in pixels or percentage of the largest image. (e.g. %0 or %1)',
					'<code>200px</code>', '<code>100%</code>'),
				'since' => '8.0',
				'filter' => 'text',
				'accepted' => 'Number of pixels followed by \'px\' or percent followed by \'%\' (e.g. "200px" or "100%").',
				'default' => '550px',
			),
			'imgHeight' => array(
				'required' => false,
				'name' => tra('Image Slideshow Height'),
				'description' => tr('Height in pixels or percentage of the largest images. (e.g. %0 or %1)',
					'<code>200px</code>', '<code>100%</code>'),
				'since' => '8.0',
				'filter' => 'text',
				'accepted' => 'Number of pixels followed by \'px\' or percent followed by \'%\' (e.g. "200px" or "100%").',
				'default' => '502px',
			),
			'autoStart' => array(
				'required' => false,
				'name' => tra('Start Slideshow'),
				'description' => tra('Automatically start the slideshow'),
				'since' => '9.2',
				'filter' => 'alpha',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('No'), 'value' => 'n'),
					array('text' => tra('Yes'), 'value' => 'y'),
				),
			),
			'delay' => array(
				'required' => false,
				'name' => tra('Delay'),
				'description' => tra('Delay in milliseconds between each transition'),
				'since' => '9.2',
				'filter' => 'digits',
				'default' => '2500',
			),
			'numThumbs' => array(
				'required' => false,
				'name' => tra('Number of Thumbnails'),
				'description' => tra('The number of thumbnails to show per page'),
				'since' => '9.2',
				'filter' => 'digits',
				'default' => '15',
			),
			'topPager' => array(
				'required' => false,
				'name' => tra('Show Top Pager'),
				'description' => tra('Display thumbnail pager at top'),
				'since' => '9.2',
				'filter' => 'alpha',
				'default' => 'y',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n'),
				),
			),
			'bottomPager' => array(
				'required' => false,
				'name' => tra('Show Bottom Pager'),
				'description' => tra('Display thumbnail pager at bottom'),
				'since' => '9.2',
				'filter' => 'alpha',
				'default' => 'y',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n'),
				),
			),
		),
	);
}

function wikiplugin_galleriffic($data, $params)
{
	$smarty = TikiLib::lib('smarty');
	static $igalleriffic = 0;
	$smarty->assign('igalleriffic', $igalleriffic++);
	$plugininfo = wikiplugin_galleriffic_info();
	foreach ($plugininfo['params'] as $key => $param) {
		$default["$key"] = $param['default'];
	}
	$params = array_merge($default, $params);
	extract($params, EXTR_SKIP);

    $filegallib = TikiLib::lib('filegal');
	$files = $filegallib->get_files(0, -1, $params['sort_mode'], '', $params['fgalId']);
	if (empty($files['cant'])) {
		return '';
	}
	$headerlib = TikiLib::lib('header');
	$headerlib->add_cssfile('lib/jquery_tiki/galleriffic/css/galleriffic-2.css');		// tiki needs modified css otherwise .content gets hidden
	$headerlib->add_jsfile('vendor/jquery/plugins/galleriffic/js/jquery.galleriffic.js');
	$headerlib->add_jsfile('vendor/jquery/plugins/galleriffic/js/jquery.opacityrollover.js');
	$playLinkText = tra('Play Slideshow');
	$pauseLinkText = tra('Pause SlideShow');
	$prevLinkText = '&lsaquo; '.tra('Previous Photo');
	$nextLinkText = tra('Next Photo').' &rsaquo;';
	$nextPageLinkText = tra('Next').' &rsaquo;';
	$prevPageLinkText = '&lsaquo; '.tra('Prev');
	$autoStart = $autoStart === 'n' ? 'false' : 'true';
	$topPager = $topPager === 'n' ? 'false' : 'true';
	$bottomPager = $bottomPager === 'n' ? 'false' : 'true';

$jq = <<<JQ
if (\$('#thumbs').length) {
	// We only want these styles applied when javascript is enabled
	\$('div.navigation').css({'width' : '$thumbsWidth', 'float' : 'left'});
	\$('div.gcontent').css('display', 'block');
	\$('.thumbs').show();

	// Initially set opacity on thumbs and add
	// additional styling for hover effect on thumbs
	var onMouseOutOpacity = 0.67;
	\$('#thumbs ul.thumbs li').opacityrollover({
		mouseOutOpacity:   onMouseOutOpacity,
		mouseOverOpacity:  1.0,
		fadeSpeed:         'fast',
		exemptionSelector: '.selected'
	});
    var gallery = \$('#thumbs').galleriffic({
        delay:                     $delay, // in milliseconds
        numThumbs:                 $numThumbs, // The number of thumbnails to show page
        preloadAhead:              10, // Set to -1 to preload all images
        enableTopPager:            $topPager,
        enableBottomPager:         $bottomPager,
        maxPagesToShow:            7,  // The maximum number of pages to display in either the top or bottom pager
        imageContainerSel:         '#slideshow', // The CSS selector for the element within which the main slideshow image should be rendered
        controlsContainerSel:      '#controls', // The CSS selector for the element within which the slideshow controls should be rendered
        captionContainerSel:       '#caption', // The CSS selector for the element within which the captions should be rendered
        loadingContainerSel:       '#loading', // The CSS selector for the element within which should be shown when an image is loading
        renderSSControls:          true, // Specifies whether the slideshow's Play and Pause links should be rendered
        renderNavControls:         true, // Specifies whether the slideshow's Next and Previous links should be rendered
		playLinkText:              '$playLinkText',
		pauseLinkText:             '$pauseLinkText',
		prevLinkText:              '$prevLinkText',
		nextLinkText:              '$nextLinkText',
		nextPageLinkText:          '$nextPageLinkText',
		prevPageLinkText:          '$prevPageLinkText',
        enableHistory:             false, // Specifies whether the url's hash and the browser's history cache should update when the current slideshow image changes
        enableKeyboardNavigation:  false, // Specifies whether keyboard navigation is enabled
        autoStart:                 $autoStart, // Specifies whether the slideshow should be playing or paused when the page first loads
        syncTransitions:           false, // Specifies whether the out and in transitions occur simultaneously or distinctly
        defaultTransitionDuration: 1000   // If using the default transitions, specifies the duration of the transitions
    });
	\$('div.gcontent').css({'width' : '$imgWidth'});
	\$('div.loader').css({'width' : '$imgWidth', 'height' : '$imgHeight'});
	\$('div.slideshow a.advance-link').css({'width' : '$imgWidth', 'height' : '$imgHeight', 'line-height' : '$imgHeight'});
	\$('div.span.image-caption').css({'width' : '$imgWidth'});
	\$('div.slideshow-container').css({'height' : '$imgHeight'});
}
JQ;

	$headerlib->add_jq_onready($jq);
$css = <<<CSS
div.slideshow-container {
	height: $imgHeight;
}
div.loader {
	width: $imgWidth;
	height: $imgHeight;
}
div.slideshow a.advance-link {
	width: $imgWidth;
	height: $imgHeight;
	line-height: $imgHeight;
}
span.image-caption {
	width: $imgWidth;
}
.thumbs {
	display: none;
}

CSS;
	$headerlib->add_css($css, 50);

	$smarty->assign('images', $files['data']);
	$smarty->assign('imgWidth', $imgWidth - 8);// arbritary number to allow some padding
	return $smarty->fetch('wiki-plugins/wikiplugin_galleriffic.tpl');
}
