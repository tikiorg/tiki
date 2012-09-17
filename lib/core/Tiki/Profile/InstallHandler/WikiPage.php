<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Profile_InstallHandler_WikiPage extends Tiki_Profile_InstallHandler
{
	private $content;
	private $description;
	private $namespace;
	private $name;
	private $lang;
	private $translations = array();
	private $message;
	private $structure;
	private $wysiwyg;
	private $wiki_authors_style;
	
	private $mode = 'create_or_update';
	private $exists;

	function fetchData()
	{
		if ( $this->name )
			return;

		$data = $this->obj->getData();

		if ( array_key_exists('message', $data) )
			$this->message = $data['message'];

		if ( array_key_exists('name', $data) )
			$this->name = $data['name'];
		if ( array_key_exists('namespace', $data) )
			$this->namespace = $data['namespace'];
		if ( array_key_exists('description', $data) )
			$this->description = $data['description'];
		if ( array_key_exists('lang', $data) )
			$this->lang = $data['lang'];
		if ( array_key_exists('content', $data) )
			$this->content = $data['content'];
		if ( array_key_exists('mode', $data) )
			$this->mode = $data['mode'];
		if ( $this->lang
			&& array_key_exists('translations', $data)
			&& is_array($data['translations']) )
			$this->translations = $data['translations'];
		if ( array_key_exists('structure', $data) )
			$this->structure = $data['structure'];
		if ( array_key_exists('wysiwyg', $data) )
			$this->wysiwyg = $data['wysiwyg'];
		if ( array_key_exists('wiki_authors_style', $data) )
			$this->wiki_authors_style = $data['wiki_authors_style'];
	}

	function canInstall()
	{
		$this->fetchData();
		if ( empty( $this->name ) || empty( $this->content ) )
			return false;

		$this->convertMode();

		return true;
	}

	private function convertMode()
	{
		global $tikilib;

		$name = $this->getPageName();

		$this->exists = $tikilib->page_exists($name);

		switch( $this->mode ) {
		case 'create':
			if ( $this->exists ) {
				throw new Exception("Page {$name} already exists and profile does not allow update.");
			}
			break;
		case 'update':
		case 'append':
			if ( ! $this->exists ) {
				throw new Exception("Page {$name} does not exist and profile only allows update.");
			}
			break;
		case 'create_or_update':
			return $this->exists ? 'update' : 'create';
		case 'create_or_append':
			return $this->exists ? 'append' : 'create';
		default:
			throw new Exception("Invalid mode '{$this->mode}' for wiki handler.");
		}

		return $this->mode;
	}

	function _install()
	{
		// Normalize mode
		$this->canInstall();

		global $tikilib;
		$this->fetchData();
		$this->replaceReferences($this->name);
		$this->replaceReferences($this->namespace);
		$this->replaceReferences($this->description);
		$this->replaceReferences($this->content);
		$this->replaceReferences($this->lang);
		$this->replaceReferences($this->translations);
		$this->replaceReferences($this->message);
		$this->replaceReferences($this->structure);
		$this->replaceReferences($this->wysiwyg);
		$this->replaceReferences($this->wiki_authors_style);
	
		$this->mode = $this->convertMode();

		if ( strpos($this->content, 'wikidirect:') === 0 ) {
			$pageName = substr($this->content, strlen('wikidirect:'));
			$this->content = $this->obj->getProfile()->getPageContent($pageName);
		}

		$finalName = $this->getPageName();

		if ( $this->mode == 'create' ) {
			if ( $this->wysiwyg ) {
				$this->wysiwyg = 'y';
				$is_html = true;
			} else {
				$this->wysiwyg = 'n';
				$is_html = false;
			} 
			if ( ! $this->message ) {
				$this->message = tra('Created by profile installer');
			}
			if ( ! $tikilib->create_page($finalName, 0, $this->content, time(), $this->message, 'admin', '0.0.0.0', $this->description, $this->lang, $is_html, null, $this->wysiwyg, $this->wiki_authors_style))
				return null;
		} else {
			$info = $tikilib->get_page_info($finalName, true, true);

			if ( ! $this->wysiwyg ) {
				if ( ! empty($info['wysiwyg']) ) { 
					$this->wysiwyg = $info['wysiwyg'];
				} else {
					$this->wysiwyg = 'n';
				}
				if ( isset($info['is_html']) ) {
					$is_html = $info['is_html'];
				} else {
					$is_html = false;
				} 
			} else {
				$this->wysiwyg = 'y';
				$is_html = true;
			}

			if ( ! $this->description )
				$this->description = $info['description'];

			if ( ! $this->lang )
				$this->lang = $info['lang'];

			if ( $this->mode == 'append' ) {
				$this->content = rtrim($info['data']) . "\n" . trim($this->content) . "\n";
			}

			if ( ! $this->message ) {
				$this->message = tra('Page updated by profile installer');
			}

			$tikilib->update_page($finalName, $this->content, $this->message, 'admin', '0.0.0.0', $this->description, 0, $this->lang, $is_html, null, null, $this->wysiwyg, $this->wiki_authors_style);
		}

		global $multilinguallib;
		require_once 'lib/multilingual/multilinguallib.php';

		$current = $tikilib->get_page_id_from_name($finalName);
		foreach ( $this->translations as $targetName ) {
			$target = $tikilib->get_page_info($targetName);

			if ( $target && $target['lang'] && $target['lang'] != $this->lang ) {
				$multilinguallib->insertTranslation('wiki page', $current, $this->lang, $target['page_id'], $target['lang']);
			}
		}
		
		if (!empty($this->structure)) {
			global $structlib; include_once 'lib/structures/structlib.php';
			$structlib->s_create_page($this->structure, 0, $finalName, '', $this->structure);
		}

		return $finalName;
	}

	private function getPageName()
	{
		global $prefs;

		$name = $this->name;

		if ($this->namespace) {
			$name = "{$this->namespace}{$prefs['namespace_separator']}{$name}";
		}

		return $name;
	}
}
