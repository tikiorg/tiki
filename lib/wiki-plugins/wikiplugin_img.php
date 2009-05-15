<?php

function wikiplugin_img_info()
{
	return array(
		'name' => tra( 'Img' ),
		'description' => tra( 'Displays an image.' ),
		'prefs' => array( 'wikiplugin_img' ),
		'params' => array(
			'src' => array(
				'required' => false,
				'name' => tra('Image Source'),
				'description' => tra('Full URL to the image to display.'),
			),
			'alt' => array(
				'required' => false,
				'name' => tra('Alternate Text'),
				'description' => tra('Alternate text to display if impossible to load the image.'),
			),
			'height' => array(
				'required' => false,
				'name' => tra('Height'),
				'description' => tra('Height of the image to display.'),
			),
			'width' => array(
				'required' => false,
				'name' => tra('Width'),
				'description' => tra('Width of the image to display.'),
			),
			'link' => array(
				'required' => false,
				'name' => tra('Link'),
				'description' => tra('Alias: lnk. Location the image should point to.'),
			),
			'rel' => array(
				'required' => false,
				'name' => tra('Link Relation'),
				'description' => tra('"rel" attribute to add to the link.'),
			),
			'title' => array(
				'required' => false,
				'name' => tra('Link Title'),
				'description' => tra('Description.'),
			),
			'align' => array(
				'required' => false,
				'name' => tra('Alignment'),
				'description' => tra('Image alignment in the page. (left, right, center) Uses HTML IMG tag.'),
			),
			'imalign' => array(
				'required' => false,
				'name' => tra('Float Alignment'),
				'description' => tra('Image alignment in the flow of the document. (left, right, none) Uses CSS float property.'),
			),
			'desc' => array(
				'required' => false,
				'name' => tra('Description'),
				'description' => tra('Image description to display on the page.'),
			),
			'usemap' => array(
				'required' => false,
				'name' => tra('Image Map'),
				'description' => tra('Name of the image map to use for the image.'),
			),
			'class' => array(
				'required' => false,
				'name' => tra('CSS Class'),
				'description' => tra('Style to apply to the image.'),
			),
		),
	);
}

function wikiplugin_img( $data, $params, $offset, $parseOptions )
{
	global $tikidomain, $tikiroot, $prefs;

	$imgdata = array();
	$imgdata["src"] = '';
	$imgdata["height"] = '';
	$imgdata["width"] = '';
	$imgdata["lnk"] = '';
	$imgdata["rel"] = '';
	$imgdata["title"] = '';
	$imgdata["align"] = '';
	$imgdata["desc"] = '';
	$imgdata["imalign"] = '';
	$imgdata["alt"] = '';
	$imgdata["usemap"] = '';
	$imgdata["class"] = '';

	// strip single quotes from params () to preserve 2.x {img} behaviour
	$params = preg_replace("/^'(.*)'$/", '$1', $params);
	$imgdata = array_merge( $imgdata, $params );

	// Support both 'link' and 'lnk' syntax
	if ( isset($imgdata['link']) && $imgdata['lnk'] == '' )
		$imgdata['lnk'] = $imgdata['link'];

	if (stristr(str_replace(' ', '', $imgdata["src"]),'javascript:')) {
		$imgdata["src"]  = '';
	}
	if ($tikidomain && !preg_match('|^https?:|', $imgdata['src'])) {
		$imgdata["src"] = preg_replace("~img/wiki_up/~","img/wiki_up/$tikidomain/",$imgdata["src"]);
	}
	if (strstr($imgdata["src"],'javascript:')) {
		$imgdata["src"]  = '';
	}

	// Handle absolute links (e.g. to send a newsletter with images that remains on the tiki site)

	$absolute_links = isset($parseOptions['absolute_links']) ? $parseOptions['absolute_links'] : false;
	if ( $imgdata['src'] != '' && $absolute_links && ! preg_match('|^[a-zA-Z]+:\/\/|', $imgdata['src']) ) {
		global $base_host, $url_path;
		$imgdata['src'] = $base_host.( $imgdata['src'][0] == '/' ? '' : $url_path ).$imgdata['src'];
	}

	$imgdata_dim = '';
	if ( $prefs['feature_filegals_manager'] == 'y' ) {
		global $detected_lib;
		include_once('lib/images/images.php');
	} else {
		$detected_lib = '';
	}

	if ( $detected_lib != '' && ereg('^'.$tikiroot.'tiki-download_file.php\?', $imgdata['src']) ) {
		// If an image lib has been detected and if we are using an image from a file gallery,
		//   then also resize the image server-side, because it will generally imply less data to download from the user
		//   (i.e. speed up the page download) and a better image quality (browser resize algorithms are quick but bad)
		//
		//   Note: ctype_digit is used to ensure there is only digits in width and height strings (e.g. to avoid '50%', ...)
		//
		if ( (int)$imgdata['width'] > 0 && ctype_digit($imgdata['width']) ) $imgdata['src'] .= '&amp;x='.$imgdata['width'];
		if ( (int)$imgdata['height'] > 0 && ctype_digit($imgdata['height']) ) $imgdata['src'] .= '&amp;y='.$imgdata['height'];
	}
	if ( $imgdata['width'] ) $imgdata_dim .= ' width="' . $imgdata['width'] . '"';
	if ( $imgdata['height'] ) $imgdata_dim .= ' height="' . $imgdata['height'] . '"';

	$repl = '<img alt="' . $imgdata["alt"] . '" src="'.$imgdata["src"].'" border="0" '.$imgdata_dim;

	if ($imgdata["imalign"]) {
		$repl .= ' style="float: ' . $imgdata["imalign"] . '"';
	}
	if ($imgdata["usemap"]) {
		$repl .= ' usemap="#'.$imgdata["usemap"].'"';
	}
	if ($imgdata["class"]) {
		$repl .= ' class="'.$imgdata["class"].'"';
	}

	$repl .= ' />';

	if ($imgdata["lnk"]) {
		$imgtarget= '';

		if ($prefs['popupLinks'] == 'y' && (preg_match('#^([a-z0-9]+?)://#i', $imgdata['lnk']) || preg_match('#^www\.([a-z0-9\-]+)\.#i',$imgdata['lnk']))) {
			$imgtarget = ' target="_blank"';
		}

		if ($imgdata['rel'])
			$linkrel = ' rel="'.$imgdata['rel'].'"';
		else
			$linkrel = '';

		if ($imgdata['title'])
			$linktitle = ' title="'.$imgdata['title'].'"';
		else
			$linktitle = '';
		$repl = '<a href="'.$imgdata["lnk"].'"'.$linkrel.$imgtarget.$linktitle.'>' . $repl . '</a>';
	}

	if ($imgdata["desc"]) {
		$repl = '<table cellpadding="0" cellspacing="0"><tr><td>' . $repl . '</td></tr><tr><td class="mini">' . $imgdata["desc"] . '</td></tr></table>';
	}

	if ($imgdata["align"]) {
		$repl = '<div class="img" align="' . $imgdata["align"] . '">' . $repl . "</div>";
	} elseif (!$imgdata["desc"]) {
		$repl = '<span class="img">' . $repl . "</span>";
	}

	return $repl;
}

?>
