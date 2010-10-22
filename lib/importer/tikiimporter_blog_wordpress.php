<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('tikiimporter_blog.php');

/**
 * Parses a Wordpress XML file and prepare it to be imported into Tiki.
 * Requires PHP5 DOM extension.
 *
 * @package	tikiimporter
 */
class TikiImporter_Blog_Wordpress extends TikiImporter_Blog
{
	public $softwareName = 'Wordpress';
	
	/**
	 * The DOM representation of the Wordpress XML dump
	 * @var DOMDocument object
	 */
	public $dom = '';

	/**
	 * Array of the valid mime types for the
	 * input file
	 */
	public $validTypes = array('application/xml', 'text/xml');

	/**
	 * @see lib/importer/TikiImporter#importOptions
	 */
	static public $importOptions = array(
        array('name' => 'importAttachments', 'type' => 'checkbox', 'label' => 'Import images and attachments (see documentation for more information)'),
	);

	/**
     * Check for DOMDocument.
     * 
     * @see lib/importer/TikiImporter#checkRequirements()
     *
     * @return void 
     * @throws Exception if DOMDocument not available
     */
	function checkRequirements()
	{
		if (!class_exists('DOMDocument')) {
			throw new Exception(tra('Class DOMDocument not available, check your PHP installation. For more information see http://php.net/manual/en/book.dom.php'));
		}
	}

	/*
	 * @see lib/importer/TikiImporter_Blog#setupTiki()
	 */
	function setupTiki()
	{
		global $tikilib;

		$tikilib->set_preference('feature_blogposts_comments', 'y');
		$tikilib->set_preference('feature_comments_moderation', 'y');
		$tikilib->set_preference('comments_notitle', 'y');

		parent::setupTiki();
	}
	
	
	/**
	 * Start the importing process by loading the XML file.
	 * 
	 * @see lib/importer/TikiImporter_Blog#import()
	 *
	 * @param string $filePath path to the XML file
	 * @return void 
	 * @throws UnexpectedValueException if invalid file mime type
	 */
	function import($filePath)
	{
		if (isset($_FILES['importFile']) && !in_array($_FILES['importFile']['type'], $this->validTypes)) {
			throw new UnexpectedValueException(tra('Invalid file mime type'));
		}

		if (!empty($_POST['importAttachments']) && $_POST['importAttachments'] == 'on') {
			$this->checkRequirementsForAttachments();
		}

		$this->saveAndDisplayLog("Loading and validating the XML file\n");

		$this->dom = new DOMDocument;
		$this->dom->load($filePath);

		if (!empty($_POST['importAttachments']) && $_POST['importAttachments'] == 'on') {
			$this->downloadAttachments();
		}

		parent::import();
	}

	/**
	 * There is not DTD for WXR so no validation is done
	 * for the moment 
	 *
	 * @see lib/importer/TikiImporter#validateInput()
	 */
	function validateInput() {}

	/**
	 * Check for all the requirements to import attachments.
	 * If one of them is not satisfied the script will die.
	 *
	 * @returns void
	 */
	function checkRequirementsForAttachments()
	{
		if (ini_get('allow_url_fopen') === false) {
			$this->saveAndDisplayLog("ABORTING: you need to enable the PHP setting 'allow_url_fopen' to be able to import attachments. Fix the problem or try to import without the attachments.\n");
			die;
		}
	}

	//TODO: handle tags, categories and comments
	/**
	 * Foreach <item> element check if it is a post, page or
	 * attachment and call the proper method
	 * 
	 * @return array $parsedData
	 */
	function parseData()
	{
		$parsedData = array();
		$items = $this->dom->getElementsByTagName('item');

		$this->saveAndDisplayLog("\nStarting to parse data:\n");

		$this->extractBlogInfo();

		foreach ($items as $item) {
			$type = $item->getElementsByTagName('post_type')->item(0)->nodeValue;
			$status = $item->getElementsByTagName('status')->item(0)->nodeValue;

			if (($type == 'post' || $type == 'page') && $status == 'publish') {
				try {
					$parsedData[] = $this->extractInfo($item);
				} catch (ImporterParserException $e) {
					$this->saveAndDisplayLog($e->getMessage(), true);
				}
			} else if ($type == 'attachment') {

			}
		}

		return $parsedData;
	}

	/**
	 * Searches for the last version of each attachments in the XML file
	 * and try to download it to the img/wiki_up/ directory
	 *
	 * Note: it is not possible to generate the Mediawiki
	 * XML file with the <upload> tag through the web interface
	 * (Special:Export). This is only possible through the Mediawiki
	 * script maintanance/dumpBackup.php with the experimental option
	 * --uploads
	 *
	 * @return void
	 */
	function downloadAttachments() {
/*		$pages = $this->dom->getElementsByTagName('page');

		if ($this->dom->getElementsByTagName('upload')->length == 0) {
			$this->saveAndDisplayLog("\n\nNo attachments found to import! Make sure you have created your XML file with the dumpDump.php script and with the option --uploads. This is the only way to import attachment.\n", true);
			return;
		}

		$this->saveAndDisplayLog("\n\nStarting to import attachments:\n");

		foreach ($pages as $page) {
			$attachments = $page->getElementsByTagName('upload');

			if ($attachments->length > 0) {
				$i = $attachments->length - 1;
				$lastVersion = $attachments->item($i);

				$fileName = $lastVersion->getElementsByTagName('filename')->item(0)->nodeValue;
				$fileUrl = $lastVersion->getElementsByTagName('src')->item(0)->nodeValue;

				if (file_exists($this->attachmentsDestDir . $fileName)) {
					$this->saveAndDisplayLog("NOT importing file $fileName as there is already a file with the same name in the destination directory ($this->attachmentsDestDir)\n", true);
					continue;
				}

				if (@fopen($fileUrl, 'r')) {
					$attachmentContent = @file_get_contents($fileUrl);
					$newFile = fopen($this->attachmentsDestDir . $fileName, 'w');
					fwrite($newFile, $attachmentContent);
					$this->saveAndDisplayLog("File $fileName successfully imported!\n");
				} else {
					$this->saveAndDisplayLog("Unable to download file $fileName. File not found.\n", true);
				}
			}
		}*/
	}

	/**
	 * Parse an DOM representation of a Wordpress item and return all the values
	 * that will be imported (title, content, comments etc).
	 *  
	 * @param DOMElement $item
	 * @return array $data information for one item (page or post) 
	 * @throws ImporterParserException if fail to parse an item
	 */
	function extractInfo(DOMElement $item)
	{	
		$data = array();
		$data['categories'] = array();
		$data['tags'] = array();
		$data['comments'] = array();

		$i = 0;
		foreach ($item->childNodes as $node) {
			if ($node instanceof DOMElement) {
				switch ($node->tagName)	{
					case 'id':
						break;
					case 'title':
						$data['name'] = (string) $node->textContent;
						break;
					case 'wp:post_type':
						$data['type'] = (string) $node->textContent;
						break;
					case 'wp:post_date':
						$data['created'] = strtotime($node->textContent);
						break;
					case 'dc:creator':
						$data['author'] = (string) $node->textContent;
						break;
					case 'category':
						if ($node->hasAttribute('nicename')) {
							if ($node->getAttribute('domain') == 'tag') {
								$data['tags'][] = $node->textContent;
							} else if ($node->getAttribute('domain') == 'category') {
								$data['categories'][] = $node->textContent;
							}
						}
						break;
					case 'content:encoded':
						$data['content'] = (string) $node->textContent;
						break;
					case 'excerpt:encoded':
						$data['excerpt'] = (string) $node->textContent;
						break;
					case 'wp:comment':
						$comment = $this->extractComment($node);
						if ($comment) {
							$data['comments'][] = $comment;
						} 
						break;
					default:
						break;					
				}
			}
		}

		// create revision key to reuse TikiImporter_Wiki::insertPage()
		if ($data['type'] == 'page') {
			$revision = array();
			$revision['data'] = $data['content'];
			$revision['lastModif'] = $data['created'];
			$revision['user'] = $data['author'];
			$revision['ip'] = '';
			$revision['is_html'] = true;
			$data['revisions'][] = $revision;
		}

		$msg = 'Item "' . $data['name'] . '" successfully extracted.' . "\n";
		$this->saveAndDisplayLog($msg);

		return $data;
	}

	/**
	 * Extract information from a comment node and return it. Comments marked
	 * as spam, trash or pingback are ignored by the importer. Pingbacks are
	 * ignore because they are not supported by Tiki yet.
	 * 
	 * @param DOMElement $commentNode
	 * @return array|false $comment return false if comment is marked as spam
	 */
	function extractComment(DOMElement $commentNode)
	{
		$comment = array();
		
		// if comment is marked as spam, trash or pigback we ignore it
		if ($commentNode->getElementsByTagName('comment_approved')->item(0)->textContent == 'spam'
			|| $commentNode->getElementsByTagName('comment_approved')->item(0)->textContent == 'trash'
			|| $commentNode->getElementsByTagName('comment_type')->item(0)->textContent == 'pingback') {

			return false;
		}
		
		foreach ($commentNode->childNodes as $node) {
			if ($node instanceof DOMElement) {
				switch ($node->tagName) {
					case 'wp:comment_author':
						$comment['author'] = $node->textContent;
						break;
					case 'wp:comment_author_email':
						$comment['author_email'] = $node->textContent;
						break;
					case 'wp:comment_author_url':
						$comment['author_url'] = ($node->textContent != 'http://') ? $node->textContent : '';
						break;
					case 'wp:comment_author_IP':
						$comment['author_ip'] = $node->textContent;
						break;
					case 'wp:comment_date':
						$comment['created'] = strtotime($node->textContent);
						break;
					case 'wp:comment_content':
						$comment['data'] = $node->textContent;
						break;
					case 'wp:comment_approved':
						$comment['approved'] = $node->textContent;
						break;
					case 'wp:comment_type':
						$comment['type'] = $node->textContent;
						break;
					default:
						break;
				}
			}
		}
		
		return $comment;
	}
	
	/**
	 * Extract blog information (title, description etc)
	 *
	 * @return array blog information
	 */
	function extractBlogInfo()
	{
		$data = array();

		$data['title'] = $this->dom->getElementsByTagName('title')->item(0)->nodeValue;
		$data['desc'] = $this->dom->getElementsByTagName('description')->item(0)->nodeValue;
		$data['created'] = strtotime($this->dom->getElementsByTagName('pubDate')->item(0)->nodeValue);

		$this->blogInfo = $data;
	}
}
