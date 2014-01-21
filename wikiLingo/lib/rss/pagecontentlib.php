<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
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
		$html = file_get_contents($url);

		// Note: PHP Readability expects UTF-8 encoded content.
		// If your content is not UTF-8 encoded, convert it 
		// first before passing it to PHP Readability. 
		// Both iconv() and mb_convert_encoding() can do this.

		// If we've got Tidy, let's clean up input.
		// This step is highly recommended - PHP's default HTML parser
		// often doesn't do a great job and results in strange output.
		$this->tidy($html);

		// give it to Readability
		$readability = new Readability($html, $url);

		$result = $readability->init();

		if ($result) {
			return array(
				'title' => $readability->getTitle()->textContent,
				'content' =>
					$this->tidy($readability->getContent()->innerHTML),
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
}

