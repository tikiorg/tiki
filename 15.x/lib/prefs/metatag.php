<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_metatag_list()
{
	return array(
		'metatag_keywords' => array(
			'name' => tra('Keywords'),
			'description' => tra('A list of keywords (separated by commas) that describe this website.'),
			'type' => 'textarea',
			'size' => '4',
			'default' => '',
			'tags' => array('basic'),
		),
		'metatag_freetags' => array(
			'name' => tra('Include tags'),
			'description' => tra('If the Tags feature is enabled, the tags for each page with tags set will be used as meta keywords. This allows individual pages at the site to have different meta tags.'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_freetags',
			),
			'default' => 'n',
		),
		'metatag_threadtitle' => array(
			'name' => tra('Use thread title instead'),
			'description' => tra('Use the forum thread title in the meta title tag.'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_forums',
			),
			'default' => 'n',
		),
		'metatag_imagetitle' => array(
			'name' => tra('Use the image title instead'),
			'description' => tra('Use the image title in the meta title tag'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_galleries',
			),
			'default' => 'n',
		),
		'metatag_description' => array(
			'name' => tra('Description'),
			'description' => tra('A short description of the website. Some search engines display this information with the website\'s listing.'),
			'type' => 'textarea',
			'size' => '5',
			'default' => '',
			'tags' => array('basic'),
		),
		'metatag_pagedesc' => array(
			'name' => tra('Page description'),
			'description' => tra('Use each page description as a meta tag for that page.'),
			'type' => 'flag',
			'default' => 'n',
		),
		'metatag_author' => array(
			'name' => tra('Author'),
			'description' => tra('The author of this website. Typically this is the Admin or Webmaster.'),
			'type' => 'text',
			'size' => '50',
			'default' => '',
			'tags' => array('basic'),
		),
		'metatag_geoposition' => array(
			'name' => tra('geo.position'),
			'description' => tra('The latitude and longitude of the physical location of the site. For example "38.898748, -77.037684".'),
			'type' => 'text',
			'size' => '50',
			'default' => '',
		),
		'metatag_georegion' => array(
			'name' => tra('geo.region'),
			'description' => tra('The ISO-3166 country and region codes for your location. For example, "US-NY".'),
			'type' => 'text',
			'size' => '50',
			'default' => '',
		),
		'metatag_geoplacename' => array(
			'name' => tra('geo.placename'),
			'description' => tra('A free-text description of your location.'),
			'type' => 'text',
			'size' => '50',
			'default' => '',
		),
		'metatag_robots' => array(
			'name' => tra('Meta robots'),
			'description' => tra('Specify how Web robots should index your site. Valid values include: INDEX or NOINDEX, and FOLLOW or NOFOLLOW'),
			'type' => 'text',
			'size' => '50',
			'default' => '',
		),
		'metatag_revisitafter' => array(
			'name' => tra('Revisit after'),
			'description' => tra('Specify how often (in days) Web robots should visit your site.'),
			'type' => 'text',
			'size' => '50',
			'default' => '',
			//'warning' => tra('This feature uses non-standard HTML.'),
			'tags' => array('experimental'),
		),
	);
}
