<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_slider.php 34180 2011-04-30 13:35:27Z lphuberdeau $

function wikiplugin_slider_info() {
	return array(
		'name' => tra('Slider'),
		'documentation' => 'PluginSlider',
		'description' => tra('Arrange content in a sliding area'),
		'prefs' => array( 'wikiplugin_slider' ),
		'body' => tra('Content content separated by /////'),
		'icon' => 'pics/icons/cool.gif',
		'params' => array(
			'name' => array(
				'required' => false,
				'name' => tra('Tabset Name'),
				'description' => tra('Unique tabset name (if you want it to remember its last state). Ex: user_profile_tabs'),
				'default' => '',
			),
			'tabs' => array(
				'required' => true,
				'name' => tra('Tab Titles'),
				'description' => tra('Pipe separated list of tab titles. Ex: tab 1|tab 2|tab 3'),
				'default' => '',
			),
			'width' => array(
				'required' => false,
				'name' => tra('Width'),
				'description' => tra('Width in pixels or percentage. Default value is page width. e.g. "200px" or "100%"'),
				'filter' => 'striptags',
				'accepted' => 'Number of pixels followed by \'px\' or percent followed by % (e.g. "200px" or "100%").',
				'default' => 'Slider width',
			),
			'height' => array(
				'required' => false,
				'name' => tra('Height'),
				'description' => tra('Height in pixels or percentage. Default value is complete slider height.'),
				'filter' => 'striptags',
				'accepted' => 'Number of pixels followed by \'px\' or percent followed by % (e.g. "200px" or "100%").',
				'default' => 'Slider height',
			),
			'theme' => array(
				'required' => false,
				'name' => tra('Theme'),
				'description' => tra('The theme to use in slider.'),
				'filter' => 'striptags',
				'accepted' => 'name of the theme you want to use',
				'default' => 'default',
				'options' => array(
					array('text' => 'default', 'value' => ''), 
					array('text' => 'construction', 'value' => 'construction'), 
					array('text' => 'portfolio', 'value' => 'portfolio'), 
					array('text' => 'metallic', 'value' => 'metallic'), 
					array('text' => 'minimalist-round', 'value' => 'minimalist-round'),
					array('text' => 'minimalist-square', 'value' => 'minimalist-square')
				)
			),
			'expand' => array(
				'required' => false,
				'name' => tra('Expand'),
				'description' => tra('if y, the entire slider will expand to fit the parent element'),
				'filter' => 'alpha',
				'accepted' => 'y or n',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'resizecontents' => array(
				'required' => false,
				'name' => tra('Resize Contents'),
				'description' => tra('if y, solitary images/objects in the panel will expand to fit the viewport'),
				'filter' => 'alpha',
				'accepted' => 'y or n',
				'default' => 'y',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'showmultiple' => array(
				'required' => false,
				'name' => tra('Show Multiple'),
				'description' => tra('Set this value to a number and it will show that many slides at once'),
				'filter' => 'alpha',
				'accepted' => 'y or n',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'buildarrows' => array(
				'required' => false,
				'name' => tra('Build Arrows'),
				'description' => tra('if y, builds the forwards and backwards buttons'),
				'filter' => 'alpha',
				'accepted' => 'y or n',
				'default' => 'y',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'buildnavigation' => array(
				'required' => false,
				'name' => tra('Build Navigation'),
				'description' => tra('if y, builds a list of anchor links to link to each panel'),
				'filter' => 'alpha',
				'accepted' => 'y or n',
				'default' => 'y',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'buildstartstop' => array(
				'required' => false,
				'name' => tra('Build Start Stop'),
				'description' => tra('if y, builds the start/stop button and adds slideshow functionality'),
				'filter' => 'alpha',
				'accepted' => 'y or n',
				'default' => 'y',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'togglearrows' => array(
				'required' => false,
				'name' => tra('Toggle Arrows'),
				'description' => tra('if y, side navigation arrows will slide out on hovering & hide @ other times'),
				'filter' => 'alpha',
				'accepted' => 'y or n',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'togglecontrols' => array(
				'required' => false,
				'name' => tra('Toggle Controls'),
				'description' => tra('if y, slide in controls (navigation + play/stop button) on hover and slide change, hide @ other times'),
				'filter' => 'alpha',
				'accepted' => 'y or n',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'enablearrows' => array(
				'required' => false,
				'name' => tra('Enable Arrows'),
				'description' => tra('if n, arrows will be visible, but not clickable.'),
				'filter' => 'alpha',
				'accepted' => 'y or n',
				'default' => 'y',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'enablenavigation' => array(
				'required' => false,
				'name' => tra('Enable Navigation'),
				'description' => tra('if n, navigation links will still be visible, but not clickable.'),
				'filter' => 'alpha',
				'accepted' => 'y or n',
				'default' => 'y',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'enablestartstop' => array(
				'required' => false,
				'name' => tra('Enable Start Stop'),
				'description' => tra('if n, the play/stop button will still be visible, but not clickable. Previously "enablePlay"'),
				'filter' => 'alpha',
				'accepted' => 'y or n',
				'default' => 'y',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'enablekeyboard' => array(
				'required' => false,
				'name' => tra('Enable Keyboard'),
				'description' => tra('if n, keyboard arrow keys will not work for this slider.'),
				'filter' => 'alpha',
				'accepted' => 'y or n',
				'default' => 'y',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'autoplay' => array(
				'required' => false,
				'name' => tra('Auto Play'),
				'description' => tra('if y, the slideshow will start running; replaces "startStopped" option'),
				'filter' => 'alpha',
				'accepted' => 'y or n',
				'default' => 'f',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'autoplaylocked' => array(
				'required' => false,
				'name' => tra('Auto Play Locked'),
				'description' => tra('if y, user changing slides will not stop the slideshow'),
				'filter' => 'alpha',
				'accepted' => 'y or n',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'autoplaydelayed' => array(
				'required' => false,
				'name' => tra('Auto Play Delayed'),
				'description' => tra('if y, starting a slideshow will delay advancing slides; if n, the slider will immediately advance to the next slide when slideshow starts‰'),
				'filter' => 'alpha',
				'accepted' => 'y or n',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'pauseonhover' => array(
				'required' => false,
				'name' => tra('Pause On Hover'),
				'description' => tra('if y & the slideshow is active, the slideshow will pause on hover'),
				'filter' => 'alpha',
				'accepted' => 'y or n',
				'default' => 'y',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'stopatend' => array(
				'required' => false,
				'name' => tra('Stop At End'),
				'description' => tra('if y & the slideshow is active, the slideshow will stop on the last page. This also stops the rewind effect when infiniteSlides is false.'),
				'filter' => 'alpha',
				'accepted' => 'y or n',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'playrtl' => array(
				'required' => false,
				'name' => tra('Play Right To Left'),
				'description' => tra('if y, the slideshow will move right-to-left'),
				'filter' => 'alpha',
				'accepted' => 'y or n',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'resumeonvideoend' => array(
				'required' => false,
				'name' => tra('Resume On Video End'),
				'description' => tra('if y & the slideshow is active & a supported video is playing, it will pause the autoplay until the video is complete'),
				'filter' => 'alpha',
				'accepted' => 'y or n',
				'default' => 'y',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
		),
	);
}

function wikiplugin_slider($data, $params) {
	global $tikilib, $headerlib;
	extract ($params,EXTR_SKIP);
	
	$headerlib->add_jsfile( 'lib/jquery/anythingslider/js/swfobject.js' );
	$headerlib->add_jsfile( 'lib/jquery/anythingslider/js/jquery.anythingslider.js' );
	$headerlib->add_jsfile( 'lib/jquery/anythingslider/js/jquery.anythingslider.fx.js' );
	$headerlib->add_jsfile( 'lib/jquery/anythingslider/js/jquery.anythingslider.video.js' );
	$headerlib->add_cssfile( 'lib/jquery/anythingslider/css/anythingslider.css' );
	
	if (isset($theme) && !empty($theme)) {
		switch (strtolower($theme)) {
			case 'construction':
			case 'portfolio':
			case 'metallic':
			case 'minimalist-round':
			case 'minimalist-square':
				$theme = $theme;
				break;
			default:
				$theme = 'default';
		}
	} else {
		$theme = 'default';
	}
	
	function makeBool($val, $default) {
		if (isset($val) && !empty($val)) {
			$val = ($val == 'y' ? 'true' : 'false');
		} else {
			$val = $default;
		}
		return ($val ? 'true' : 'false');
	}
	
	$headerlib->add_jq_onready("
		function formatText(i, p) {
			var possibleText = $('.tiki-slider-title').eq(i - 1).text();
			return (possibleText ? possibleText : i);
		}
		
		$('.tiki-slider').anythingSlider({
			theme               : '$theme',
			expand              : ".makeBool($expand, false).",
			resizeContents      : ".makeBool($resizecontents, true).",
			showMultiple        : false,
			easing              : 'swing',

			buildArrows         : ".makeBool($buildarrows, true).",
			buildNavigation     : ".makeBool($buildnavigation, true).",
			buildStartStop      : ".makeBool($buildstartstop, true).",

			toggleArrows        : ".makeBool($togglearrows, false).",
			toggleControls      : ".makeBool($togglecontrols, false).",

			startText           : 'Start',
			stopText            : 'Stop',
			forwardText         : '&raquo;',
			backText            : '&laquo;',
			tooltipClass        : 'tooltip',

			// Function
			enableArrows        : ".makeBool($enablearrows, true).",
			enableNavigation    : ".makeBool($enablenavigation, true).",
			enableStartStop     : ".makeBool($enablestartstop, true).",
			enableKeyboard      : ".makeBool($enablekeyboard, true).",

			// Navigation
			startPanel          : 1,
			changeBy            : 1,

			// Slideshow options
			autoPlay            : ".makeBool($autoplay, false).",
			autoPlayLocked      : ".makeBool($autoplaylocked, false).",
			autoPlayDelayed     : ".makeBool($autoplaydelayed, false).",
			pauseOnHover        : ".makeBool($pauseonhover, true).",
			stopAtEnd           : ".makeBool($stopatend, false).",
			playRtl             : ".makeBool($playrtl, false).",

			// Times
			delay               : 3000,
			resumeDelay         : 15000,
			animationTime       : 600,

			// Video
			resumeOnVideoEnd    : ".makeBool($resumeonvideoend, true).",
			addWmodeToObject    : 'opaque',
			
			navigationFormatter: formatText
		});
	");
	
	$tabs = array();
	if (!empty($params['tabs'])) {
		$tabs = explode('|', $params['tabs']);
	}
	
	$sliderData = array();
	if (!empty($data)) {
		$data = $tikilib->parse_data($data, array('suppress_icons' => true));
		$sliderData = explode('/////', $data);
	}
	
	$ret = '';
	foreach($sliderData as $i => $slide) {
		$ret .= "<div>
			".(isset($tabs[$i]) ? "<span class='tiki-slider-title' style='display: none;'>".$tabs[$i]."</span>" : "")."
			$slide
		</div>";
	}
	
	return <<<EOF
	~np~<div class='tiki-slider' style='width: $width; height: $height;'>$ret</div>~/np~
EOF;
}
