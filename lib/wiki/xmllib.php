<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}
define('WIKI_XML', 'wiki.xml');

class XmlLib extends TikiLib
{
	public $errors = array();
	public $errorsArgs = array();
	public $xml = '';
	public $zip = '';
	public $config = array('comments'=>true, 'attachments'=>true, 'history'=>true, 'images'=>true, 'debug'=>false);
	public $structureStack = array();

	function get_error()
	{
		$str = '';
		foreach ($this->errors as $i=>$error) {
			$str = $error;
			if (is_array($this->errorsArgs[$i])) {
				$str .= ': '.implode(', ', $this->errorsArgs[$i]);
			} else {
				$str .= ': ' . $this->errorsArgs[$i];
			}
		}
		return $str;
	}

	/* Export a list of pages or a structure */
	function export_pages($pages=null, $structure=null, $zipFile='dump/xml.zip', $config=null)
	{
		if (! class_exists('ZipArchive')) {
			$this->errors[] = 'Problem zip initialisation';
			$this->errorsArgs[] = 'ZipArchive class not found';
			return false;
		}

		$this->zip = new ZipArchive;

		if (!$this->zip->open($zipFile, ZIPARCHIVE::OVERWRITE)) {
			$this->errors[] = 'The file cannot be opened';
			$this->errorsArgs[] = $zipFile;
			return false;
		}

		if (!empty($config)) {
			$this->config = array_merge($this->config, $config);
		}

		$this->xml .= '<?xml version="1.0" encoding="UTF-8"?>'."\n";

		if (count($pages) >= 1) {
			$this->xml .= "<pages>\n";
			foreach ($pages as $page) {
				if (!$this->export_page($page)) {
					return false;
				}
			}
			$this->xml .= "</pages>\n";
		}

		if (!empty($structure)) {
			$structlib = TikiLib::lib('struct');
			$pages = $structlib->s_get_structure_pages($structure);
			$stack = array();
			foreach ($pages as $page) {
				while (count($stack) && $stack[count($stack) - 1] != $page['parent_id']) {
					array_pop($stack);
					$this->xml .= "</structure>\n";
				}
				$this->xml .= "<structure>\n";
				$stack[] = $page['page_ref_id'];
				if (!$this->export_page($page['pageName'])) {
					return false;
				}
			}

			while (count($stack)) {
				array_pop($stack);
				$this->xml .= "</structure>\n";
			}
		}

		if (!$this->zip->addFromString(WIKI_XML, $this->xml) ) {
			$this->errors[] = 'Can not add the xml';
			$this->errorsArgs[] = WIKI_XML;
			return false;
		}
		if ($this->config['debug']) {
			echo '<pre>'.htmlspecialchars($this->xml).'</pre>';
		}
		$this->zip->close();
		return true;
	}

	/* export one page */
	function export_page($page)
	{
		global $prefs, $tikidomain;
		$tikilib = TikiLib::lib('tiki');
		$smarty = TikiLib::lib('smarty');
		$parserlib = TikiLib::lib('parser');
		$info = $tikilib->get_page_info($page);

		if (empty($info)) {
			$this->errors[] = 'Page does not exist';
			$this->errorsArgs[] = $page;
			return false;
		}

		$dir = $page;
		$info['zip'] = "$dir/" . $page;
		$smarty->assign_by_ref('info', $info);

		if (!$this->zip->addFromString($info['zip'], $info['data'])) {
			$this->errors[] = 'Can not add the page';
			$this->errorsArgs[] = $info['zip'];
			return false;
		}

		if ($prefs['feature_wiki_comments'] == 'y' && $this->config['comments']) {
			$commentslib = TikiLib::lib('comments');
			$comments = $commentslib->get_comments('wiki page:'.$page, 0, 0, 0, 'commentDate_asc', '', 0, 'commentStyle_plain');
			if (!empty($comments['cant'])) {
				$smarty->assign_by_ref('comments', $comments['data']);
			}
		}
		$images = array();

		if ($prefs['feature_wiki_pictures'] == 'y'
				&& $this->config['images']
				&& preg_match_all('/\{img\s*\(?([^\}]+)\)?\s*\}/i', $info['data'], $matches)
		) {
			global $tikiroot;
			foreach ($matches[1] as $match) {
				$args = $parserlib->plugin_split_args($match);
				if ( ! empty($args['src']) && preg_match('|img/wiki_up/(.*)|', $args['src'], $m)) {
					$file = empty($tikidomain)?$args['src']: str_replace('img/wiki_up/', "img/wiki_up/$tikidomain/", $args['src']);
					$image = array('filename' => $m[1], 'where' => 'wiki', 'zip'=>"$dir/images/wiki/".$m[1], 'wiki'=>$args['src']);
					if (!$this->zip->addFile($file, $image['zip'])) {
						$this->errors[] = 'Can not add the image ';
						$this->errorsArgs[] = $file;
						return false;
					}
				} elseif (! empty($args['src']) && preg_match('|show_image.php\?(.*)|', $args['src'], $m)) {
					$imagegallib = TikiLib::lib('imagegal');
					if (($i = strpos($args['src'], 'tiki-download_file.php')) > 0) {
						$path = $_SERVER['HTTP_HOST'].$tikiroot.substr($args['src'], $i);
					} else {
						$path = $_SERVER['HTTP_HOST'].$tikiroot.$args['src'];
					}
					$img = $this->httprequest($path);
					$this->parse_str($m[1], $p);

					if (isset($p['name']) && isset($p['galleryId']))
						$id = $imagegallib->get_imageid_byname($p['name'], $p['galleryId']);
					elseif (isset($p['name']))
						$id = $imagegallib->get_imageid_byname($p['name']);
					elseif (isset($p['id']))
						$id = $p['id'];

					$image = array('where' => 'gal', 'zip' => "$dir/images/gal/".$id, 'wiki'=>$args['src']);

					if (!$this->zip->addFromString($image['zip'], $img)) {
						$this->errors[] = 'Can not add the image';
						$this->errorsArgs[] = $m[1];
						return false;
					}
				} elseif (! empty($args['src']) && preg_match('|tiki-download_file.php\?(.*)|', $args['src'], $m)) {
					if (($i = strpos($args['src'], 'tiki-download_file.php')) > 0) {
						$path = $_SERVER['HTTP_HOST'].$tikiroot.substr($args['src'], $i);
					} else {
						$path = $_SERVER['HTTP_HOST'].$tikiroot.$args['src'];
					}

					$img = $this->httprequest($path);
					$this->parse_str($m[1], $p);
					$image = array('where' => 'fgal', 'zip'=>"$dir/images/fgal/".$p['fileId'], 'wiki'=>$args['src']);

					if (!$this->zip->addFromString($image['zip'], $img)) {
						$this->errors[] = 'Can not add the image';
						$this->errorsArgs[] = $m[1];
						return false;
					}
				} /* else no idea where the img comes from - suppose there are outside tw */
				$images[] = $image;
			}
		}

		$smarty->assign_by_ref('images', $images);

		if ($prefs['feature_wiki_attachments'] == 'y' && $this->config['attachments']) {
			$wikilib = TikiLib::lib('wiki');
			$attachments = $wikilib->list_wiki_attachments($page, 0, -1);
			if (!empty($attachments['cant'])) {
				foreach ($attachments['data'] as $key=>$att) {
					$att_info = $wikilib->get_item_attachment($att['attId']);
					$attachments['data'][$key]['zip'] = "$dir/attachments/".$att['attId'];
					if ($prefs['w_use_dir']) {
						if (!$this->zip->addFile($prefs['w_use_dir'].$att_info['path'], $attachments['data'][$key]['zip'])) {
							$this->errors[] = 'Can not add the attachment';
							$this->errorsArgs[] = $att_info['attId'];
							return false;
						}
					} else {
						if (!$this->zip->addFromString($attachments['data'][$key]['zip'], $att_info['data'])) {
							$this->errors[] = 'Can not add the attachment';
							$this->errorsArgs[] = $att_info['attId'];
							return false;
						}
					}
				}
				$smarty->assign_by_ref('attachments', $attachments['data']);
			}
		}

		if ($prefs['feature_history'] == 'y' && $this->config['history']) {
			$histlib = TikiLib::lib('hist');
			$history = $histlib->get_page_history($page, false);
			foreach ($history as $key=>$hist) {
				$all = $histlib->get_version($page, $hist['version']); // can be optimised if returned in the list
				//$history[$key]['data'] = $all['data'];
				$history[$key]['zip'] = "$dir/history/".$all['version'].'.txt';
				if (!$this->zip->addFromString($history[$key]['zip'], $all['data'])) {
					$this->errors[] = 'Can not add the history';
					$this->errorsArgs[] = $all['version'];
					return false;
				}
			}
			$smarty->assign_by_ref('history', $history);
		}

		$smarty->assign_by_ref('config', $this->config);
		$this->xml .= $smarty->fetch('tiki-export_page_xml.tpl');
		return true;
	}

	/* import pages or structure */
	function import_pages($zipFile='dump/xml.zip', $config=null)
	{
		if (!empty($config)) {
			$this->config = array_merge($this->config, $config);
		}

		if (!($this->zip = new ZipArchive())) {
			$this->errors[] = 'Problem zip initialisation';
			$this->errorsArgs[] = '';
			return false;
		}

		if (!$this->zip->open($zipFile)) {
			$this->errors[] = 'The file cannot be opened';
			$this->errorsArgs[] = $zipFile;
			return false;
		}

		if (($this->xml = $this->zip->getFromName(WIKI_XML)) === false) {
			$this->errors[] = 'Can not unzip';
			$this->errorsArgs[] = WIKI_XML;
			return false;
		}

		$parser = new page_Parser();
		$parser->setInput($this->xml);
		$ok = $parser->parse();
		if (PEAR::isError($ok)) {
			$this->errors[] = $ok->getMessage();
			$this->errorsArgs[] = '';
			return false;
		}
		$infos = $parser->getPages();

		if ($this->config['debug']) {
			echo 'XML PARSING<pre>';print_r($infos);echo '</pre>';
		}

		foreach ($infos as $info) {
			if (!$this->create_page($info)) {
				return false;
			}
		}
		$this->zip->close();
		return true;
	}

	/* create a page from an xml parsing result */
	function create_page($info)
	{
		global $prefs, $tiki_p_wiki_attach_files, $tiki_p_edit_comments, $tikidomain;
		$tikilib = TikiLib::lib('tiki');

		if (($info['data'] = $this->zip->getFromName($info['zip'])) === false) {
			$this->errors[] = 'Can not unzip';
			$this->errorsArgs[] = $info['zip'];
			return false;
		}

		if ($this->page_exists($info['name'])) {
			$old = true;
			$tikilib->update_page(
				$info['name'],
				$info['data'],
				'Updated from import',
				!empty($this->config['fromUser']) ? $this->config['fromUser'] : $info['user'],
				!empty($this->config['fromSite']) ? $this->config['fromSite'] : $info['ip'],
				$info['description'],
				0,
				isset($info['lang']) ? $info['lang'] : '',
				isset($info['is_html']) ? $info['is_html'] : false,
				null,
				null,
				isset($info['wysiwyg']) ? $info['wysiwyg'] : NULL
			);
		} else {
			$old = false;
			$tikilib->create_page(
				$info['name'],
				$info['hits'],
				$info['data'],
				$info['lastModif'],
				$info['comment'],
				!empty($this->config['fromUser']) ? $this->config['fromUser'] : $info['user'],
				!empty($this->config['fromSite']) ? $this->config['fromSite'] : $info['ip'],
				$info['description'],
				isset($info['lang']) ? $info['lang'] : '',
				isset($info['is_html']) ? $info['is_html'] : false,
				null,
				isset($info['wysiwyg']) ? $info['wysiwyg'] : NULL,
				'',
				0,
				$info['created']
			);
		}

		if ($prefs['feature_wiki_comments'] == 'y' && $tiki_p_edit_comments == 'y' && !empty($info['comments'])) {
			$newThreadIds = array();

			foreach ($info['comments'] as $comment) {
				$commentslib = TikiLib::lib('comments');
				$parentId = empty($comment['parentId']) ? 0: $newThreadIds[$comment['parentId']];
				if ($parentId) {
					$reply_info = $commentslib->get_comment($parentId);
					$in_reply_to = $reply_info['message_id'];
				}

				$newThreadIds[$comment['threadId']] = $commentslib->post_new_comment(
					'wiki page:' . $info['name'],
					$parentId,
					$this->config['fromUser'] ? $this->config['fromUser'] : $comment['user'],
					$comment['title'],
					$comment['data'],
					$message_id,
					$in_reply_to,
					'n',
					'',
					'',
					'',
					'',
					$comment['date']
				);
			}
		}

		if ($prefs['feature_wiki_attachments'] == 'y' && $tiki_p_wiki_attach_files == 'y' && !empty($info['attachments'])) {
			foreach ($info['attachments'] as $attachment) {
				if (($attachment['data'] = $this->zip->getFromName($attachment['zip'])) === false) {
					$this->errors[] = 'Can not unzip attachment';
					$this->errorsArgs[] = $attachment['zip'];
					return false;
				}
				if ($prefs['w_use_db'] == 'y') {
					$fhash = '';
				} else {
					$fhash = $this->get_attach_hash_file_name($attachment['filename']);
					if ($fw = fopen($prefs['w_use_dir'].$fhash, 'wb')) {
						if (!fwrite($fw, $attachment['data'])) {
							$this->errors[] = 'Cannot write to this file';
							$this->errorsArgs[] = $prefs['w_use_dir'].$fhash;
						}
						fclose($fw);
						$attachment['data'] = '';
					} else {
						$this->errors[] = 'Cannot open this file';
						$this->errorsArgs[] = $prefs['w_use_dir'].$fhash;
					}
				}

				$wikilib = TikiLib::lib('wiki');
				$wikilib->wiki_attach_file(
					$info['name'],
					$attachment['filename'],
					$attachment['filetype'],
					$attachment['filesize'],
					$attachment['data'],
					$attachment['comment'],
					$attachment['user'],
					$fhash,
					$attachment['created']
				);
			}
		}

		if ($prefs['feature_wiki_pictures'] == 'y' && !empty($info['images'])) {
			foreach ($info['images'] as $image) {
				if (empty($image['zip'])) {//external link to image
					continue;
				}
				if (($image['data'] = $this->zip->getFromName($image['zip'])) === false) {
					$this->errors[] = 'Can not unzip image';
					$this->errorsArgs[] = $image['zip'];
					return false;
				}
				if ($image['where'] == 'wiki') {
					$wiki_up = 'img/wiki_up/';
					if ($tikidomain)
						$wiki_up.= "$tikidomain/";
					$name = str_replace('img/wiki_up/', '', $image['wiki']);
					file_put_contents($wiki_up.$name, $image['data']);
					chmod($wiki_up.$name, 0644);
				}
			}
		}

		if ($prefs['feature_history'] == 'y' && !empty($info['history'])) {
			$query = 'select max(`version`) from `tiki_history` where `pageName`=?';
			$maxVersion = $this->getOne($query, array($info['name']));

			if (!$maxVersion) {
				$maxVersion = 0;
			}
			$newVersion = $maxVersion;

			foreach ($info['history'] as $version) {
				if (($version['data'] = $this->zip->getFromName($version['zip'])) === false) {
					$this->errors[] = 'Can not unzip history';
					$this->errorsArgs[] = $version['version'];
					return false;
				}
				$query = 'insert into `tiki_history`(`pageName`, `version`, `lastModif`, `user`, `ip`, `comment`, `data`, `description`) values(?,?,?,?,?,?,?,?)';

				$this->query(
					$query,
					array(
						$info['name'],
						$version['version'] + $maxVersion,
						$old ? $tikilib->now : $version['lastModif'],
						$version['user'],
						$version['ip'],
						$version['comment'],
						$version['data'],
						$version['description']
					)
				);

				$newVersion = max($version['version']+$maxVersion, $newVersion);
			}
			$query = 'update `tiki_pages` set `version`=? where `pageName`=?';
			$this->query($query, array($newVersion, $info['name']));
		}

		if ($prefs['feature_wiki_structure'] == 'y' && !empty($info['structure'])) {
			$structlib = TikiLib::lib('struct');
			//TODO alias
			if ($info['structure'] == 1) {
				$this->structureStack[$info['structure']] = $structlib->s_create_page(null, null, $info['name'], '');
				if (empty($this->structureStack[$info['structure']])) {
					$this->errors[] = 'A structure already exists';
					$this->errorsArgs[] = $info['name'];
					return false;
				}
			} elseif (!empty($info['structure'])) {
				$this->structureStack[$info['structure']] = $structlib->s_create_page(
					$this->structureStack[$info['structure'] - 1],
					isset($this->structureStack[$info['structure']]) ? $this->structureStack[$info['structure']] : '',
					$info['name'],
					'',
					$this->structureStack[1]
				);
			}
		}
		return true;
	}

}
$xmllib = new XmlLib;

class page_Parser extends XML_Parser
{
	var $page;
	var $currentTag = null;
	var $context = null;
	var $folding = false; // keep tag as original
	var $commentsStack = array();
	var $commentId = 0;
	var $iStructure = 0;

	function startHandler($parser, $name, $attribs)
	{
		switch ($name) {
			case 'page':
				$this->context = null;
				if (is_array($attribs)) {
					$this->page = array(
									'data'=>'',
									'comment'=>'',
									'description'=>'',
									'user'=>'admin',
									'ip'=>'0.0.0.0',
									'lang'=>'',
									'is_html'=>false,
									'hash'=>null,
									'wysiwyg'=>null
					);
					$this->page = array_merge($this->page, $attribs);
				}
				if ($this->iStructure > 0 ) {
					$this->page['structure'] = $this->iStructure;
				}
				break;

			case 'structure':
				++$this->iStructure;
				break;

			case 'comments':
				$comentsStack = array();

			case 'attachments':
			case 'history':
			case 'images':
				$this->context = $name;
				$this->i = -1;
				break;

			case 'comment':
				if ($this->context == 'comments') {
					++$this->i;
					$this->page[$this->context][$this->i] = $attribs;
					$this->page[$this->context][$this->i]['parentId'] = empty($this->commentsStack)?0: $this->commentsStack[count($this->commentsStack) - 1];
					$this->page[$this->context][$this->i]['threadId'] = ++$this->commentId;
					array_push($this->commentsStack, $this->commentId);
				} else {
					$this->currentTag = $name;
				}
				break;

			case 'attachment':
				++$this->i;
				$this->page[$this->context][$this->i] = array('comment'=>'');
				$this->page[$this->context][$this->i] = array_merge($this->page[$this->context][$this->i], $attribs);
				break;

			case 'version':
				++$this->i;
				$this->page[$this->context][$this->i] = array('comment' =>'', 'description'=>'', 'ip'=>'0.0.0.0');
				$this->page[$this->context][$this->i] = array_merge($this->page[$this->context][$this->i], $attribs);
				break;

			case 'image':
				++$this->i;
				$this->page[$this->context][$this->i] = $attribs;
				break;

			default:
				$this->currentTag = $name;
				break;
		}
	}

	function endHandler($parser, $name)
	{
		$this->currentTag = null;
		switch ($name) {
			case 'comments':
			case 'attachements':
			case 'history':
			case 'images':
				$this->context = null;
				break;

			case 'comment':
				array_pop($this->commentsStack);
				break;

			case 'page':
				$this->pages[] = $this->page;
				break;

			case 'structure':
				--$this->iStructure;
				break;
		}
	}

	function cdataHandler($parser, $data)
	{
		$data = trim($data);
		if (empty($data)) {
			return true;
		}
		if (empty($this->context)) {
			$this->page[$this->currentTag] = $data;
		} else {
			$this->page[$this->context][$this->i][$this->currentTag] = $data;
		}
	}

	function getPages()
	{
		return $this->pages;
	}
}
