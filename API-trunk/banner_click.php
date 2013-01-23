<?php
/**
 * Tiki Feature: Banners.
 * 
 * adds an additional click to the hit counter for a specific banner.
 *
 * @package   Tiki
 * @copyright (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project. All Rights Reserved. See copyright.txt for details and a complete list of authors.
* @license   LGPL. See licence.txt for more details
 */
// $Id$

/** @package Tiki */
require_once ('tiki-setup.php');

$access->check_feature('feature_banners');

$bannerlib = TikiLib::lib('banner');

$bannerlib->add_click($_REQUEST["id"]);
$url = urldecode($_REQUEST["url"]);
header("location: $url");
