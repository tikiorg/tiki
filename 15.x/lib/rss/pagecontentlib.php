<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$


class PageContentLib
{
	function augmentInformation($data)
	{
		global $prefs;

		if ($prefs['page_content_fetch'] == 'y') {
			$new = $this->grabContent($data['url']);
			if ($new) {
				$data['content'] = $new['content'];
			}
		}

		return $data;
	}

	function grabContent($url)
	{
		$tikilib = TikiLib::lib('tiki');

		$client = $tikilib->get_http_client($url);
		$response = $tikilib->http_perform_request($client);

		// Obtain the URL after redirections
		$url = (string) $client->getUri();
		$html = $response->getBody();

		// Note: PHP Readability expects UTF-8 encoded content.
		// If your content is not UTF-8 encoded, convert it 
		// first before passing it to PHP Readability. 
		// Both iconv() and mb_convert_encoding() can do this.

		// If we've got Tidy, let's clean up input.
		// This step is highly recommended - PHP's default HTML parser
		// often doesn't do a great job and results in strange output.
		$html = $this->tidy($html);

		// give it to Readability
		global $prefs;
		if (is_file($prefs['page_content_fetch_readability'])) {
			require_once($prefs['page_content_fetch_readability']);
		}
		if (!class_exists('Readability')) {
			return false;
		}

		$readability = new Readability($html, $url);

		$result = $readability->init();

		if ($result) {
			$content = $this->tidy($readability->getContent()->innerHTML);
			$content = $this->replacePaths($content, $url);
			return array(
				'title' => $readability->getTitle()->textContent,
				'content' => $content,
			);
		}
	}

	private function tidy($html)
	{
		if (function_exists('tidy_parse_string')) {
			$tidy = tidy_parse_string($html, array(), 'UTF8');
			$tidy->cleanRepair();
			$html = $tidy->value;
		}

		return $html;
	}

	private function getUrls($url) {
		// From http://stackoverflow.com/questions/21201062/using-readability-api-to-scrape-most-relavant-image-from-page

		// Parse URL
		$urlArr = parse_url($url);

		// Determine Base URL, with scheme, host, and port
		$base = $urlArr['scheme'] . "://" . $urlArr['host'];
		if(array_key_exists("port",$urlArr) && $urlArr['port'] != 80) {
			$base .= ":" . $urlArr['port'];
		}

		// Truncate the Path using the position of the last forward slash
		$relative = $base . substr($urlArr['path'], 0, strrpos($urlArr['path'], "/") + 1);

		// Return our two URLs
		return array($base, $relative);
	}

	function replacePaths($html, $url) {
		// Modified from: http://stackoverflow.com/questions/21201062/using-readability-api-to-scrape-most-relavant-image-from-page

		// Retrieve our URLs
		list($baseUrl, $relativeUrl) = $this->getUrls($url);

		$convert = function ($url) use ($baseUrl, $relativeUrl) {
			// Resolve relative paths
			if(substr($url, 0, 2) == "//") { // Missing protocol
				// Fine, use current
			} elseif(substr($url, 0, 1) == "/") { // Path Relative to Base
				$url = $baseUrl . $url;
			} elseif(substr($url, 0, 4) !== "http") { // Path Relative to Dimension
				$url = $relativeUrl . $url;
			}

			return $url;
		};

		libxml_use_internal_errors(true);

		$dom = new DOMDocument();
		$dom->loadHTML($html);

		foreach($dom->getElementsByTagName('img') as $node) {
			$image = $node->getAttribute('src');

			$node->setAttribute('src', $convert($image));
		}

		foreach($dom->getElementsByTagName('a') as $node) {
			$link = $node->getAttribute('href');

			$node->setAttribute('href', $convert($link));
		}

		return $dom->saveHTML();
	}
}

