<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Class to provide basic functionalities to blog importers. So far
 * used only for the Wordpress importer. For more information
 * see http://dev.tiki.org/Wordpress+importer and http://doc.tiki.org/Wordpress+importer  
 * 
 * @author Rodrigo Sampaio Primo <rodrigo@utopia.org.br>
 * @package tikiimporter
 */

require_once('tikiimporter.php');
require_once('lib/blogs/bloglib.php');

/**
 * Class to provide basic functionalities to blog importers. So far
 * used only for the Wordpress importer. For more information
 * see http://dev.tiki.org/Wordpress+importer and http://doc.tiki.org/Wordpress+importer
 *
 * This class has the methods to insert data into Tiki blog. Probably they can 
 * be reused by all the importers. Child classes must only implement the functions
 * to extract and prepare the data (validateInput(), parseData())
 *
 * @package	tikiimporter
 */
class TikiImporter_Blog extends TikiImporter
{

	/**
	 * @see lib/importer/TikiImporter#importOptions
	 */
	static public $importOptions = array();

	/**
	 * Blog information extracted from the XML file (title, description, created etc)
	 * @var array
	 */
	public $blogInfo = array();

	/**
	 * Instance of TikiImporter_Wiki
	 * @var TikiImporter_Wiki
	 */
	public $importerWiki = '';

	/**
	 * The id of the blog created by the importer
	 * @var int
	 */
	public $blogId = '';

	/**
	 * Main function that starts the importing proccess
	 * 
	 * Set the import options based on the options the user selected
	 * and start the importing proccess by calling the functions to
	 * validate, parse and insert the data.
	 *  
	 * @return void 
	 */
	function import()
	{
		$this->setupTiki();
		
		// child classes must implement those two methods
		$this->validateInput();
		$parsedData = $this->parseData();

		$importFeedback = $this->insertData($parsedData);

		$this->saveAndDisplayLog("\nImportation completed!");

		echo "\n\n<b><a href=\"tiki-importer.php\">Click here</a> to finish the import process</b>";
		flush();

		$_SESSION['tiki_importer_feedback'] = $importFeedback;
		$_SESSION['tiki_importer_log'] = $this->log;
		$_SESSION['tiki_importer_errors'] = $this->errors;
	}

	/**
	 * This function change all the necessary Tiki preferences
	 * in order to setup the site for the data that will be imported.
	 * Also implemented by child classes.
	 * 
	 * @return void
	 */
	function setupTiki()
	{
		global $tikilib;
		
		$tikilib->set_preference('feature_blogs', 'y');
	}
	
	/**
	 * Insert the imported data into Tiki.
	 * 
	 * @param array $parsedData the return of $this->parseData()
	 *
	 * @return array $countData stats about the content that has been imported
	 */
	function insertData($parsedData)
	{
		$countData = array();
		$countItems= 0;

		$this->saveAndDisplayLog("\n" . count($parsedData) . " items (pages and posts) parsed. Starting to insert them into Tiki:\n");

		$this->createBlog();

		if (!empty($parsedData)) {
			foreach ($parsedData as $item) {
				$methodName = 'insert' . ucfirst($item['type']);

				if ($objId = $this->$methodName($item)) {
					$countItems++;
					$this->saveAndDisplayLog('Item ' . $item['name'] . " sucessfully imported\n");					
					
					if (!empty($item['comments'])) {
						if ($item['type'] == 'page') {
							$type = 'wiki page';
						} else if ($item['type'] == 'post') {
							$type = 'blog post';
						}
						
						$this->insertComments($objId, $type, $item['comments']);
					}
					
				} else {
					$this->saveAndDisplayLog('Item ' . $item['name'] . " NOT imported (there was already a item with the same name)\n");
				}
			}
		}

		$countData['totalPages'] = count($parsedData);
		$countData['importedPages'] = $countItems;
		return $countData;
	}

	/**
	 * Create blog based on $this->blogInfo
	 *
	 * @return void
	 */
	function createBlog()
	{
		global $bloglib, $user;
		//TODO: refactor replace_blog() to have default values
		//TODO: blog user can be different that the user logged in the system
		//TODO: interface to select blog options
		//TODO: show error when not possible to create blog
		$this->blogId = $bloglib->replace_blog($this->blogInfo['title'], $this->blogInfo['desc'], $user, 'y', 10, false, '', 'y', 'n', 'y', 'n', 'y', 'y', 'y', 'y', 'y', 'n', '', 'y', 5, 'n');
	}

	/**
	 * Insert page into Tiki using its builtin methods
	 *
	 * @param array $page
	 * @return bool true or false depending on whether was possible or not to create the new page
	 */
	function insertPage($page)
	{
		$this->instantiateImporterWiki();
		return $this->importerWiki->insertPage($page);
	}

	/**
	 * This function just create an instance of
	 * TikiImporter_Wiki and set some default values
	 *
	 * @return void
	 */
	function instantiateImporterWiki()
	{
		require_once('tikiimporter_wiki.php');
		$this->importerWiki = new TikiImporter_Wiki;
		$this->importerWiki->alreadyExistentPageName = 'appendPrefix';
		$this->importerWiki->softwareName = $this->softwareName;
	}

	/**
	 * Insert post into Tiki using its builtin methods
	 *
	 * @param array $post
	 * @return int|bool post id if one was created or false
	 */
	function insertPost($post)
	{
		global $bloglib;
		return $bloglib->blog_post($this->blogId, $post['content'], $post['excerpt'], $post['author'], $post['name'], '', 'n', $post['created']);	
	}
	
	/**
	 * Insert comments for a wiki page or post
	 * 
	 * @param int|string $objId int for a post id or string for a wiki page name (used as id)
	 * @param string $objType 'blog post' or 'wiki page'
	 * @param array $comments
	 * @return void
	 */
	function insertComments($objId, $objType, $comments)
	{
		global $commentslib; require_once('lib/comments/commentslib.php');
		
		if (!is_object($commentslib)) {
			$commentslib = new Comments();
		}
		
		$objRef = $objType . ':' . $objId;
		
		// not used but required by $commentslib->post_new_comment() as is passed by reference
		$message_id = '';
				
		foreach ($comments as $comment) {
			// set empty values for comments properties if they are not set
			if (!isset($comment['author'])) {
				$comment['author'] = '';
			}
			if (!isset($comment['author_email'])) {
				$comment['author_email'] = '';
			}
			if (!isset($comment['author_url'])) {
				$comment['author_url'] = '';
			}

			$commentslib->post_new_comment($objRef, 0, null, '', $comment['data'], $message_id, '', 'n', '', '', '',
				$comment['author'], $comment['created'], $comment['author_email'], $comment['author_url']);
		}
	}
}
