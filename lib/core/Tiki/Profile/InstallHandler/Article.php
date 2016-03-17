<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Profile_InstallHandler_Article extends Tiki_Profile_InstallHandler
{
	function getData()
	{
		if ( $this->data )
			return $this->data;

		$data = $this->obj->getData();

		$defaults = array(
			'author' => 'Anonymous',
			'heading' => '',
			'publication_date' => time(),
			'expiration_date' => time() + 3600*24*365,
			'type' => 'Article',
			'topic' => 0,
			'topline' => '',
			'subtitle' => '',
			'link_to' => '',
			'language' => 'en',
			'geolocation' => '',
		);

		$data = array_merge($defaults, $data);

		return $this->data = $data;
	}

	function canInstall()
	{
		$data = $this->getData();

		if ( ! isset( $data['title'], $data['topic'], $data['body'] ) )
			return false;

		return true;
	}

	function _install()
	{
		global $prefs;
		$artlib = TikiLib::lib('art');
		$data = $this->getData();

		$this->replaceReferences($data);

		$dateConverter = new Tiki_Profile_DateConverter;

		$id = $artlib->replace_article(
			$data['title'],
			$data['author'],
			$data['topic'],
			'n',
			null,
			null,
			null,
			null,
			$data['heading'],
			$data['body'],
			$dateConverter->convert($data['publication_date']),
			$dateConverter->convert($data['expiration_date']),
			'admin',
			0,
			0,
			0,
			$data['type'],
			$data['topline'],
			$data['subtitle'],
			$data['link_to'],
			null,
			$data['language']
		);

		if ($prefs['geo_locate_article'] == 'y' && ! empty($data['geolocation'])) {
			TikiLib::lib('geo')->set_coordinates('article', $id, $data['geolocation']);
		}

		return $id;
	}

	public static function export(Tiki_Profile_Writer $writer, $id, $withTopic = false, $withType = false)
	{
		$artlib = TikiLib::lib('art');
		$info = $artlib->get_article($id, false);

		if (! $info) {
			return false;
		}

		$bodypage = "article_{$id}_body";
		$writer->writeExternal($bodypage, $writer->getReference('wiki_content', $info['body']));
		$out = array(
			'title' => $info['title'],
			'author' => $info['authorName'],
			'body' => "wikicontent:$bodypage",
			'type' => $writer->getReference('article_type', $info['type']),
			'publication_date' => $info['publishDate'],
			'expiration_date' => $info['expireDate'],
			'topline' => $info['topline'],
			'subtitle' => $info['subtitle'],
			'link_to' => $info['linkto'],
			'language' => $info['lang'],
		);

		if ($info['topicId']) {
			if ($withTopic) {
				Tiki_Profile_InstallHandler_ArticleTopic::export($writer, $info['topicId']);
			}

			$out['topic'] = $writer->getReference('article_topic', $info['topicId']);
		}

		if ($info['heading']) {
			$headerpage = "article_{$id}_heading";
			$writer->writeExternal($headerpage, $writer->getReference('wiki_content', $info['heading']));
			$out['heading'] = "wikicontent:$headerpage";
		}

		$out = array_filter($out);
		$writer->addObject('article', $id, $out);

		if ($withType) {
			Tiki_Profile_InstallHandler_ArticleType::export($writer, $info['type']);
		}

		return true;
	}
}
