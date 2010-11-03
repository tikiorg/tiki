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
	 * @var array
	 */
	public $validTypes = array('application/xml', 'text/xml');

	/**
	 * @see lib/importer/TikiImporter#importOptions
	 */
	static public $importOptions = array(
        array('name' => 'importAttachments', 'type' => 'checkbox', 'label' => 'Import images and other attachments'),
	);

	/**
	 * List of the imported attachments used
	 * to parse post and page content to change the links
	 * @var array
	 */
	public $newFiles = array();
	
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
		$tikilib->set_preference('feature_freetags', 'y');
		$tikilib->set_preference('feature_categories', 'y');
		$tikilib->set_preference('feature_wiki_comments', 'y');

		parent::setupTiki();
	}
	
	
	/**
	 * Start the importing process by loading the XML file. And
	 * calling wordpress specific import functions (like extractBlogInfo()
	 * and downloadAttachments())
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

		$this->extractBlogInfo();
		
		if (!empty($_POST['importAttachments']) && $_POST['importAttachments'] == 'on') {
			$this->downloadAttachments();
		}

		parent::import();
	}

	/**
	 * There is not DTD for WXR so only a very basic validation
	 * is done by checking the value of the xmlns:wp attribute
	 *
	 * @see lib/importer/TikiImporter#validateInput()
	 */
	function validateInput() {
		$wxrUrl = $this->dom->getElementsByTagName('rss')->item(0)->getAttribute('xmlns:wp');
		if (!preg_match('|http://wordpress\.org/export/\d+\.\d+/|', $wxrUrl)) {
			throw new DOMException(tra('Invalid Wordpress XML file'));
		}
	}

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

	//TODO: handle categories
	/**
	 * Calls the respective functions to extract and parse (when needed)
	 * items (pages, posts and attachments), categories and tags.
	 * 
	 * @return array each key of this array contain one set of data (items, categories and tags)
	 */
	function parseData()
	{
		$parsedData = array();
		
		$this->saveAndDisplayLog("\nStarting to parse data:\n");
		
		// pages or posts
		$parsedData['items'] = $this->extractItems();
		
		$parsedData['tags'] = $this->extractTags();
		$parsedData['categories'] = $this->extractCategories();

		return $parsedData;
	}
	
	//TODO: handle attachments
	/**
	 * Extract pages, posts and attachments
	 * 
	 * @return array all extract items (pages, posts and attachments)
	 */
	function extractItems()
	{
		$data = $this->dom->getElementsByTagName('item');

		foreach ($data as $item) {
			$type = $item->getElementsByTagName('post_type')->item(0)->nodeValue;
			$status = $item->getElementsByTagName('status')->item(0)->nodeValue;

			if (($type == 'post' || $type == 'page') && $status == 'publish') {
				try {
					$items[] = $this->extractInfo($item);
				} catch (ImporterParserException $e) {
					$this->saveAndDisplayLog($e->getMessage(), true);
				}
			} else if ($type == 'attachment') {

			}
		}
		
		return $items;
	}

	/**
	 * Return all tags present in the Wordpress XML file
	 * 
	 * @return array tags
	 */
	function extractTags()
	{
		$tags = array();
		
		$data = $this->dom->getElementsByTagName('tag');

		foreach ($data as $tag) {
			$tags[] = $tag->getElementsByTagName('tag_name')->item(0)->nodeValue;
		}

		return $tags;
	}
	
	/**
	 * Extract categories information from Wordpress XML.
	 * Apparently categories on Wordpress XML are always ordered with the parent
	 * first and the childs right after. We trust in this order to create the categories
	 * without organizing them hierarchically.
	 * 
	 *  @return array categories
	 */
	function extractCategories()
	{
		$categories = array();
		
		$data = $this->dom->getElementsByTagName('category');
		
		foreach ($data as $category) {
			$categ = array();

			if ($category->getElementsByTagName('cat_name')->length == 0) {
				// if category name is not set we don't create it
				continue;	
			}
			
			if ($category->getElementsByTagName('category_parent')->length > 0) {
				$categ['parent'] = $category->getElementsByTagName('category_parent')->item(0)->nodeValue;
			} else {
				$categ['parent'] = '';
			}
			
			$categ['name'] = $category->getElementsByTagName('cat_name')->item(0)->nodeValue;
			
			if ($category->getElementsByTagName('category_description')->length > 0) {
				$categ['description'] = $category->getElementsByTagName('category_description')->item(0)->nodeValue;
			} else {
				$categ['description'] = '';
			}
			
			$categories[] = $categ;
		}
		
		return $categories;
	}
	
	/**
	 * Searches for the last version of each attachments in the XML file
	 * and try to download it to the img/wiki_up/ directory
	 *
	 * @return void
	 */
	function downloadAttachments() {
		global $filegallib; require_once('lib/filegals/filegallib.php');
		
		$attachments = $this->extractAttachmentsInfo();
		
		if (empty($attachments)) {
			$this->saveAndDisplayLog("\n\nNo attachments found to import!\n", true);
			return;
		}

		$this->saveAndDisplayLog("\n\nStarting to import attachments:\n");

		if (!empty($attachments)) {
			$galleryId = $this->createFileGallery();
		}
		
		$client = $this->getHttpClient();
		
		foreach ($attachments as $attachment) {
			$client->setUri($attachment['link']);
			
			try {
				$response = $client->request();
			} catch (Zend_Http_Client_Adapter_Exception $e) {
				$this->saveAndDisplayLog("Unable to download file " . $attachment['fileName'] . ". Error message was: " . $e->getMessage() . "\n", true);
				continue;
			}
			
			$data = $response->getRawBody();
			$size = $response->getHeader('Content-length');
			$mimeType = $response->getHeader('Content-type');

			if ($response->isSuccessful()) {
				//TODO: option to create a new file gallery for blog attachments
				$fileId = $filegallib->insert_file($galleryId, $attachment['name'], '', $attachment['fileName'], $data, $size, $mimeType, $attachment['author'], '', '', $attachment['author']);
				
				$this->newFiles[] = array('fileId' => $fileId, 'oldUrl' => $attachment['link'], 'sizes' => isset($attachment['sizes']) ? $attachment['sizes'] : '');
				
				$this->saveAndDisplayLog("File " . $attachment['fileName'] . " successfully imported!\n");
			} else {
				$this->saveAndDisplayLog("Unable to download file " . $attachment['fileName'] . ". Error message was: " . $response->getStatus() . ' ' . $response->getMessage() . "\n", true);
			}
		}
		
		// close connection
		$adapter = $client->getAdapter();
		$adapter->close();
		
	}

	/**
	 * Create a file gallery to be used as a placeholder
	 * for all imported attachments. Return the new 
	 * gallery id.
	 * 
	 * @return int created gallery id
	 */
	function createFileGallery()
	{
		global $filegallib; require_once('lib/filegals/filegallib.php');
		global $user;

		$gal_info = array(
			'galleryId' => '',
			'parentId' => 1,
			'name' => $this->blogInfo['title'],
			'description' => '',
			'user' => $user,
			'public' => 'y',
			'visible' => 'y',
		);
		
		$id = $filegallib->replace_file_gallery($gal_info);
		
		return $id;
	}
	
	/**
	 * Extract all the attachments from a XML Wordpress file
	 * and return them.
	 * 
	 * @return array all the attachments
	 */
	function extractAttachmentsInfo()
	{
		$attachments = array();
		$items = $this->dom->getElementsByTagName('item');
		
		foreach ($items as $item) {
			if ($item->getElementsByTagName('post_type')->item(0)->textContent == 'attachment') {
				$attachment = array();
				
				$attachment['name'] = $item->getElementsByTagName('title')->item(0)->textContent;
				$attachment['link'] = $item->getElementsByTagName('attachment_url')->item(0)->textContent;
				$attachment['created'] = strtotime($item->getElementsByTagName('pubDate')->item(0)->textContent);
				$attachment['author'] = $item->getElementsByTagName('creator')->item(0)->textContent;
				
				$tags = $item->getElementsByTagName('postmeta');
				
				foreach ($tags as $tag) {
					if ($tag->getElementsByTagName('meta_key')->item(0)->textContent == '_wp_attached_file') {
						$fileName = $tag->getElementsByTagName('meta_value')->item(0)->textContent;
						
						// remove year and month from file name (e.g. 2009/10/fileName.jpg becomes fileName.jpg)
						$attachment['fileName'] = preg_replace('|.+/|', '', $fileName);
					} else if ($tag->getElementsByTagName('meta_key')->item(0)->textContent == '_wp_attachment_metadata') {
						$metadata = unserialize($tag->getElementsByTagName('meta_value')->item(0)->textContent);
						
						if (is_array($metadata) && isset($metadata['sizes'])) {
							$sizes = array();
							foreach ($metadata['sizes'] as $key => $size) {
								$sizes[$key] = array(
									'name' => $size['file'],
									'width' => $size['width'],
									'height' => $size['height'],
								);
							}
							$attachment['sizes'] = $sizes; 
						}						
					}
				}
				
				$attachments[] = $attachment;
			}
		}
			
		return $attachments;
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
						$data['content'] = (string) $this->parseContentAttachmentsUrl($node->textContent);
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
			$revision['comment'] = '';
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
	 * Parse the content of a page or post replacing old 
	 * attachments URLs with the new URLs of the attachments
	 * already imported to Tiki file galleries 
	 * 
	 * @param string $content post or page content
	 * @return string parsed content
	 */
	function parseContentAttachmentsUrl($content)
	{
		global $filegallib;
		
		if (!empty($this->newFiles)) {
			foreach ($this->newFiles as $file) {
				$baseOldUrl = preg_replace('|(.+/).*|', '\\1', $file['oldUrl']);
				$baseNewUrl = 'tiki-download_file.php?fileId=' . $file['fileId'] . '&display'; 
				
				$newUrls = array();
				$oldUrls = array();
				
				$newUrls[] = $baseNewUrl;
				$oldUrls[] = $file['oldUrl'];
				
				if (!empty($file['sizes'])) {
					foreach ($file['sizes'] as $size) {
						$newUrls[] = $baseNewUrl . '&x=' . $size['width'] . '&y=' . $size['height'];
						$oldUrls[] = $baseOldUrl . $size['name']; 
					}
				}
				
				$content = str_replace($oldUrls, $newUrls, $content);
			}
		}
		
		return $content;
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
	 * Extract blog information (title, description etc) and
	 * set $this->blogInfo.
	 */
	function extractBlogInfo()
	{
		$data = array();

		$data['title'] = $this->dom->getElementsByTagName('title')->item(0)->nodeValue;
		$data['desc'] = $this->dom->getElementsByTagName('description')->item(0)->nodeValue;
		$data['created'] = strtotime($this->dom->getElementsByTagName('pubDate')->item(0)->nodeValue);

		$this->blogInfo = $data;
	}
	
	//TODO: check if a proxy is configured and than use Zend_Http_Client_Adapter_Proxy
	/**
	 * Set $this->httpClient property as an instance of Zend_Http_Client
	 * 
	 * @return void
	 */
	function getHttpClient()
	{
		require_once('Zend/Loader.php');
		Zend_Loader::loadClass('Zend_Http_Client');

		return new Zend_Http_Client();
	}
}
