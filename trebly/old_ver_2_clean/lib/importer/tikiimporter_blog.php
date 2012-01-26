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
	 * @see lib/importer/TikiImporter#importOptions()
	 */
	static public function importOptions()
	{
		$options = array(
			array('name' => 'setAsHomePage', 'type' => 'checkbox', 'label' => tra('Set new blog as Tiki homepage')),
		);
		
		return $options;
	}
	
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
		
		// child classes must implement this method
		$parsedData = $this->parseData();

		$importFeedback = $this->insertData($parsedData);

		$this->saveAndDisplayLog("\n" . tra('Importation completed!'));

		echo "\n\n<b>" . tra('<a href="tiki-importer.php">Click here</a> to finish the import process') . "</b>";
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

		$countPosts = count($parsedData['posts']);
		$countPages = count($parsedData['pages']);
		$countTags = count($parsedData['tags']);
		$countCategories = count($parsedData['categories']);
		
		$this->saveAndDisplayLog("\n" . tra("Found $countPosts posts, $countPages pages, $countTags tags and $countCategories categories. Inserting them into Tiki:") . "\n");

		if (!empty($parsedData['posts'])) {
			$this->createBlog();
		}

		if (!empty($parsedData)) {
			if (!empty($parsedData['tags'])) {
				$this->createTags($parsedData['tags']);
			}
			
			if (!empty($parsedData['categories'])) {
				$this->createCategories($parsedData['categories']);
			}
			
			$items = array_merge($parsedData['posts'], $parsedData['pages']);
			
			if (!empty($items)) {
				//TODO: move this foreach to a function (insertItems())
				foreach ($items as $item) {
					$methodName = 'insert' . ucfirst($item['type']);
	
					if ($objId = $this->$methodName($item)) {
						if ($item['type'] == 'page') {
							$type = 'wiki page';
							$msg = tra("Page \"${item['name']}\" sucessfully imported");
						} else if ($item['type'] == 'post') {
							$type = 'blog post';
							$msg = tra("Post \"${item['name']}\" sucessfully imported");
						}
						
						if (!empty($item['comments'])) {
							$this->insertComments($objId, $type, $item['comments']);
						}
						
						if (!empty($item['tags'])) {
							$this->linkObjectWithTags($objId, $type, $item['tags']);
						}
						
						if (!empty($item['categories'])) {
							$this->linkObjectWithCategories($objId, $type, $item['categories']);
						}
						
						$this->saveAndDisplayLog($msg . "\n");					
					} else {
						//TODO: improve feedback reporting the difference between the number of items found and items imported
						if ($item['type'] == 'page') {
							$countPages--;
						} else {
							$countPosts--;
						}
						
						$this->saveAndDisplayLog(tra("Item \"${item['name']}\" NOT imported (there was already a item with the same name)") . "\n");
					}
				}
			}
		}

		$countData['importedPages'] = $countPages;
		$countData['importedPosts'] = $countPosts;
		$countData['importedTags'] = $countTags;
		$countData['importedCategories'] = $countCategories;
		
		return $countData;
	}

	/**
	 * Create blog based on $this->blogInfo and 
	 * set new blog as Tiki home page if option selected.
	 *
	 * @return void
	 */
	function createBlog()
	{
		global $bloglib, $user, $tikilib;
		
		//TODO: refactor replace_blog() to have default values
		//TODO: blog user can be different that the user logged in the system
		
		if (isset($this->blogInfo['created'])) {
			$created = $this->blogInfo['created'];
		} else {
			$created = $tikilib->now;
		}
		
		$this->blogId = $bloglib->replace_blog($this->blogInfo['title'], $this->blogInfo['desc'], $user, 'y', 10, false,
			'', 'y', 'n', 'y', 'n', 'y', 'y', 'y', 'y', 'y', 'n', '', 'y', 5, 'n', $created, $this->blogInfo['lastModif']);
		
		if (isset($_REQUEST['setAsHomePage']) && $_REQUEST['setAsHomePage'] == 'on') {
			$tikilib->set_preference('home_blog', $this->blogId);
			$tikilib->set_preference('tikiIndex', 'tiki-view_blog.php?blogId=' . $this->blogId);
		}
	}

	/**
	 * Create all existing tags for a blog. Tags here
	 * are just created, not related yet with any object (post or page)
	 * 
	 * @param array $tags
	 * @return void
	 */
	function createTags($tags)
	{
		global $freetaglib; require_once('lib/freetag/freetaglib.php');
		foreach ($tags as $tag) {
			$freetaglib->find_or_create_tag($tag);
		}
	}

	/**
	 * Link an object with its tags
	 * 
	 * @param int|string $objId
	 * @param string $type
	 * @param array $tags
	 * @return void
	 */
	function linkObjectWithTags($objId, $type, $tags)
	{
		global $freetaglib; require_once('lib/freetag/freetaglib.php');
		global $user;
		
		$freetaglib->_tag_object_array($user, $objId, $type, $tags);
	}
	
	/**
	 * Create all existing categories for a blog.
	 * 
	 * @param array $categories
	 * @return void
	 */
	function createCategories($categories)
	{
		global $categlib; require_once('lib/categories/categlib.php');
		
		foreach ($categories as $categ) {
			if (!empty($categ['parent'])) {
				$categ['parentId'] = $categlib->get_category_id($categ['parent']); 
			} else {
				$categ['parentId'] = 0;
			}
			
			$categlib->add_category($categ['parentId'], $categ['name'], $categ['description']);
		}
	}
	
	/**
	 * Link an object with its categories
	 * 
	 * @param int|string $objId
	 * @param string $type
	 * @param array $categories
	 * @return void
	 */
	function linkObjectWithCategories($objId, $type, $categories)
	{
		global $categlib; require_once('lib/categories/categlib.php');
		
		foreach ($categories as $categName) {
			$categId = $categlib->get_category_id($categName);
			
			//$catObjId is the id on tiki_objects table and $objId the id on object own table
			$catObjId = $categlib->get_object_id($type, $objId);
			
			// apparently this is needed only to create an entry on tiki_categorized_objects
			$categlib->add_categorized_object($type, $objId, '', '', '');
			
			$categlib->categorize($catObjId, $categId);
		}
	}
	
	/**
	 * Insert page into Tiki using its builtin methods
	 *
	 * @param array $page
	 * @return string|bool page name if was possible to create the new page or false
	 */
	function insertPage($page)
	{
		global $objectlib; require_once('lib/objectlib.php');
		
		$this->instantiateImporterWiki();
		$pageName = $this->importerWiki->insertPage($page);
		
		// maybe this should go to TikiImporter_Wiki::insertPage()
		if ($pageName) {
			$objectlib->insert_object('wiki page', $pageName, '', $pageName, 'tiki-index.php?page=' . urlencode($pageName));
		}
		
		return $pageName;
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
		global $objectlib; require_once('lib/objectlib.php');
		$postId = $bloglib->blog_post($this->blogId, $post['content'], $post['excerpt'], $post['author'], $post['name'], '', 'n', $post['created']);

		if ($postId) {
			$objectlib->insert_object('blog post', $postId, '', $post['name'], 'tiki-view_blog_post.php?postId=' . urlencode($postId));
		}
		
		return $postId;
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

			$commentId = $commentslib->post_new_comment($objRef, 0, null, '', $comment['data'], $message_id, '', 'n', '', '', '',
				$comment['author'], $comment['created'], $comment['author_email'], $comment['author_url']);
				
			if ($comment['approved'] == 0) {
				$commentslib->approve_comment($commentId, 'n');
			}
		}
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
}
