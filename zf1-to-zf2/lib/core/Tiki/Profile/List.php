<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Tiki_Profile_List 
 * 
 */
class Tiki_Profile_List
{
	function getSources()
	{
		global $prefs;
		$raw = explode("\n", $prefs['profile_sources']);
		$raw = array_map('trim', $raw);
		$sources = array();

		foreach ( $raw as $source )
			if ( !empty($source) ) {
				$file = $this->getCacheLocation($source);
				$last = $this->getCacheLastUpdate($source);
				$short = dirname($source);
				$sources[] = array(
						'url' => $source,
						'domain' => (0 === strpos($short, 'http://')) ? substr($short, 7) : $short,
						'short' => $short,
						'status' => ($last && filesize($file)) ? 'open' : 'closed',
						'lastupdate' => $last,
						'formatted' => $last ? date('Y-m-d H:i:s', $last) : '' );
			}

		return $sources;
	}

	function refreshCache( $path )
	{
		global $tikilib;
		$file = $this->getCacheLocation($path);

		// Replace existing with blank file
		if ( file_exists($file) )
			unlink($file);
		touch($file);

		$content = $tikilib->httprequest($path);

		$parts = explode("\n", $content);
		$parts = array_map('trim', $parts);
		$good = false;

		foreach ( $parts as $line ) {
			// All lines contain 3 entries
			if ( empty($line) )
				continue;
			if ( substr_count($line, "\t") != 2 )
				return false;

			$good = true;
		}

		// A valid file has at least one profile
		if ( !$good )
			return false;

		file_put_contents($file, $content . "\n");

		return true;
	}

	function getCategoryList( $source = '' )
	{
		$category_list = array();

		$sources = $this->getSources();

		foreach ( $sources as $s ) {
			if ( $source && $s['url'] != $source )
				continue;

			if ( !$s['lastupdate'] )
				continue;

			$fp = fopen($this->getCacheLocation($s['url']), 'r');

			while ( false !== $row = fgetcsv($fp, 200, "\t") ) {
				$c = $row[0];
				if ($c)
					$category_list[] = $c;
			}
		}

		natsort($category_list);
		return(array_unique($category_list));
	}

	function getList( $source = '', $categories = array(), $profilename = '' )
	{
		$installer = new Tiki_Profile_Installer;
		$list = array();

		$sources = $this->getSources();

		foreach ( $sources as $s ) {
			if ( $source && $s['url'] != $source )
				continue;

			if ( !$s['lastupdate'] )
				continue;

			$fp = fopen($this->getCacheLocation($s['url']), 'r');

			while (false !== $row = fgetcsv($fp, 200, "\t")) {
				if ( count($row) != 3 )
					continue;

				list($c, $t, $i) = $row;

				$key = "{$s['url']}#{$i}";

				if ( $profilename && stripos($i, $profilename) === false )
					continue;

				if ( array_key_exists($key, $list) ) {
					$list[$key]['categories'][] = $c;
				} else {
					$list[$key] = array(
							'domain' => $s['domain'],
							'categories' => array($c),
							'name' => $i,
							'installed' => $installer->isKeyInstalled($s['domain'], $i),
							);
				}
			}

			fclose($fp);

			// Apply category filter
			foreach ($list as $pkey => $profile) {
				$in = true; // If there are no required categories, don't filter anything.
				if (!empty($categories)) {
					foreach ($categories as $category) {
						$in = false; // Start assuming this required category isn't in this profile's categories
						foreach ($profile['categories'] as $pcategory) {
							if ( $category == $pcategory ) {
								$in = true;
								break;
							}
						}
						if (!$in) {
							break;
						}
					}
				}
				if (!$in) {
					unset($list[$pkey]);
				}
			}
		}

		return array_values($list);
	}

	private function getCacheLocation( $path )
	{
		$hash = md5($path);
		return "temp/cache/profile$hash";
	}

	private function getCacheLastUpdate( $path )
	{
		$file = $this->getCacheLocation($path);
		if ( ! file_exists($file) )
			return 0;

		return filemtime($file);
	}
}
