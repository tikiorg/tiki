<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Profile_InstallHandler_Rss extends Tiki_Profile_InstallHandler
{
	function getData()
	{
		if ( $this->data )
			return $this->data;

		$data = $this->obj->getData();
		$data = Tiki_Profile::convertLists($data, array('show' => 'y'), true);

		$defaults = array(
			'description' => null,
			'refresh' => 30,
			'show_title' => 'n',
			'show_publication_date' => 'n',
			'article_generator' => null,
		);

		$data = array_merge($defaults, $data);
		$data = Tiki_Profile::convertYesNo($data);

		return $this->data = $data;
	}

	function canInstall()
	{
		$data = $this->getData();

		if ( ! isset( $data['name'], $data['url'] ) )
			return false;

		return true;
	}

	function _install()
	{
		$rsslib = TikiLib::lib('rss');
		$data = $this->getData();

		$this->replaceReferences($data);

		$id = $rsslib->replace_rss_module(0, $data['name'], $data['description'], $data['url'], $data['refresh'], $data['show_title'], $data['show_publication_date'], true);

		if ($data['article_generator']) {
			$rsslib->set_article_generator($id, $data['article_generator']);
		}

		$rsslib->refresh_rss_module($id);

		return $id;
	}

	public static function export(Tiki_Profile_Writer $writer, $id)
	{
		$rsslib = TikiLib::lib('rss');
		$info = $rsslib->get_rss_module($id);

		if (! $info) {
			return false;
		}

		$out = array(
			'name' => $info['name'],
			'url' => $info['url'],
			'description' => $info['description'],
			'refresh' => $info['refresh'],
			'show_title' => $info['showTitle'],
			'show_publication_date' => $info['showPubDate'],
		);

		$out = array_filter($out);

		$generator = $rsslib->get_article_generator($id);
		if ($generator['active']) {
			$generator['atype'] = $writer->getReference('article_type', $generator['atype']);
			if ($generator['topic']) {
				$generator['topic'] = $writer->getReference('article_topic', $generator['topic']);
			}
			$out['article_generator'] = $generator;
		}

		$writer->addObject('rss', $id, $out);
		
		return true;
	}
}
