<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

require_once("lib/multilingual/multilinguallib.php");
require_once('lib/categories/categlib.php');

class PageListLib extends MultilingualLib {

  var $orderList = array( 'score_desc', 'score_asc', 'priority_asc', 'priority_desc', 
                      'page_name_asc', 'page_name_desc');

  /**
   * Adds a list type to the list type table if it doesn't exist, able
   * to force update
   * @param $name string list name
   * @param $title string list title used for display
   * @param $description string list description used for display
   * @param $forceUpdate bool force update of a list type if list type exists
   * @return bool
   */
  function addListType( $name, $title="", $description="", $forceUpdate=false ) {
    if ( !$this->listTypeExists($name) ) {
      $query = "INSERT INTO `tiki_page_list_types` (`name`, `title`, `description`) VALUES(?,?,?)";
      $vars = array( $name, $title, $description );
      if ( $this->query($query, $vars) )
        return true;
    } elseif ( $forceUpdate ) {
      if ( $this->updateListType($name, $title, $description) )
        return true;
    }
    
    return false;  
  }
  
  /**
   * Updates or adds a list type to the list type table
   * @param $name string list name
   * @param $title string list title used for display
   * @param $description string list description used for display
   * @return mixed
   */
  function updateListType($name, $title="", $description="", $oldName=false ) {
    if ( !$this->listTypeExists($name) && $oldName === false )
      return $this->addListType($name, $title, $description);
      
    $query = "UPDATE `tiki_page_list_types` SET `name`=?, `title`=?, `description`=? WHERE `name`=? LIMIT 1";
    $vars = array( $name, $title, $description, ( $oldName !== false ) ? $oldName : $name );

    return $this->query($query, $vars);
  } 

  /**
   * Removes a list type entry
   * @param $name string list name
   * @return mixed
   */
  function deleteListType($name) {
    if ( $this->listTypeExists($name) ) {
      $query = "DELETE FROM `tiki_page_list_types` WHERE `name`=? LIMIT 1";
      $vars = array( $name );
      
      return $this->query($query, $vars);
    }

    return false;
  }
  
  /**
   * Fetches a list type's info using its ID
   * @param $id int ID of list type
   * @return mixed 
   */  
  function getListTypeById( $id ) {
    $query = "SELECT `name`, `title`, `description` FROM `tiki_page_list_types` WHERE `id`=?";
    $vars = array( $id );
    
    $list = $this->query( $query, $vars );
    
    return ($list) ? $list->fetchRow() : false;
  }  

  /**
   * Fetches a list type's info using its name
   * @param $name string list name
   * @return mixed
   */ 
  function getListType( $name ) {
    $query = "SELECT `name`, `title`, `description` FROM `tiki_page_list_types` WHERE `name`=?";
    $vars = array( $name );
    
    $list = $this->query( $query, $vars );
    
    return ($list) ? $list->fetchRow() : false;
  }
  
  /**
   * Fetches all list types
   * @return array
   */ 
  function getAllListTypes() {
    $query = "SELECT `name`, `title`, `description` FROM `tiki_page_list_types`";
    $vars = array();
    $results = $this->query( $query, $vars );
    $lists = array();
    
    while ( $list = $results->fetchRow() ) 
      $lists[] = $list;
     
    return $lists;
  }
 
  /**
   * Fetches a list's ID using its name
   * @param $name string list name
   * @return int
   */ 
  function getListTypeId( $name ) {
    $query = "SELECT `id` from `tiki_page_list_types` WHERE `name`=?";
    $vars = array( $name );
    
    return (int) $this->getOne($query, $vars);
  }
  
  /**
   * Checks if list type exists
   * @param $name string list name
   * @return bool
   */
  function listTypeExists( $name ) {
    $query = "SELECT COUNT(*) from `tiki_page_list_types` WHERE `name`=?";
    $vars = array( $name );
    
    return $this->getOne($query, $vars);
  }
  
  /**
   * Updates a list's items by adding new items
   * @param $items array associative array containing items
   * @param $listName string list name for items 
   * @return mixed
   */
  function updateListItems( $items, $listName ) {
    if ( !is_array($items) || !count($items) )
      return false; 
    
    if ( !$this->listTypeExists($listName) )
      $this->addListType($listName);
    
    $query = "INSERT INTO `tiki_page_lists` (`list_type_id`, `priority`, `score`, `page_name`) " . $this->getSQLForItemsInsert(count($items)) . " ON DUPLICATE KEY UPDATE `priority`=VALUES(`priority`), `score`=VALUES(`score`)";
    $vars = $this->getArrayForItemsInsert($items, $listName);
    
    return $this->query($query, $vars);
  }
  
  /**
   * Updates a list's items by adding, updating, and removing
   * items
   * @param $items array associative array containing items
   * @param $listName string list name for items
   * @return mixed
   */
  function updateList( $items, $listName ) {
    if ( !is_array($items) || !count($items) || !$this->checkItemFormat($items) )
      return false; 
      
    if ( !$this->listTypeExists($listName) )
      $this->addListType($listName);
        
    $removePages = array_diff($this->getListPagesOnly($listName), $this->getPagesFromArray($items));
    
    if ($removePages)
      $this->purgeListItemsByPage($removePages, $listName);
    
    return $this->updateListItems($items, $listName);
  }
  
  /**
   * Removes items from list by list name and pages
   * @param $pages array pages to remove from list
   * @param $listName string a list name
   * @return mixed
   */
  function purgeListItemsByPage($pages, $listName) {
    if ( !is_array($pages) )
      return false;
      
    $listId = $this->getListTypeId($listName);
    
    if ( $listId ) {
      $query = "DELETE FROM `tiki_page_lists` WHERE `list_type_id`=? AND `page_name` IN (" . rtrim(str_repeat("?,", count($pages)), ",") . ") LIMIT " . count($pages);
      $vars = array_merge(array( $listId ), $pages);
    
      return $this->query($query, $vars);
    }
    
    return false;
  }

  /**
   * Removes items from list by list name
   * @param $listName string a list name
   * @return mixed
   */  
  function purgeListItemsByList( $listName ) {
    $listId = $this->getListTypeId($listName);
    
    if ( $listId ) {
      $query = "DELETE FROM `tiki_page_lists` WHERE `list_type_id`=?";
      $vars = array( $listId );
    
      return $this->query($query, $vars);
    }
    
    return false;     
  }
  
  /**
   * Help function to create SQL query syntax for inserting items
   * into a list
   * @param $n int number of items being inserted into list
   * @return string formatted query syntax
   */
  function getSQLForItemsInsert($n) {
    if ( !is_int($n) || $n <= 0 )
      return "VALUES()";
      
    $sql = "VALUES(?,?,?,?)";
    $n--;
    
    while( $n > 0 ) {
      $sql .= ", (?,?,?,?)";
      $n--;
    }
    
    return $sql;
  }
  
  /**
   * Helper function to create proper array for binding items to 
   * formatted SQL queries
   * @param $items array items being added to list
   * @param $listName string a list name
   * @return array
   */
  function getArrayForItemsInsert($items, $listName) {
    $itemsArray = array();
    
    foreach( $items as $item ) {
      $itemsArray[] = $this->getListTypeId($listName);
      $itemsArray[] = $item['priority'];
      $itemsArray[] = $item['score'];
      $itemsArray[] = isset($item['page']) ? $item['page'] : $item['page_name'];
    }
    
    return $itemsArray;
  }
  
  /**
   * Takes an associative array of items and gets page names
   * @param $items items to fetch array of page names from
   * @return array
   */              
  function getPagesFromArray($items) {
    $pages = array();
    
    foreach ( $items as $item ) 
      $pages[] = isset($item['page']) ? $item['page'] : $item['page_name'];
  
    return $pages;
  }
  
  /**
   * Fetches page names for all items in a list
   * @param $listName string a list name
   * @return array
   */
  function getListPagesOnly($listName) { 
    $pages = array();
    
    $listId = $this->getListTypeId($listName);
    $query = "SELECT `page_name` FROM `tiki_page_lists` WHERE `list_type_id`=?";
    $vars = array( $listId );
    $results = $this->query($query, $vars);
    
    while( $row = $results->fetchRow() ) {
      $pages[] = $row['page_name'];
    }
    
    return $pages;
  }
  
  /**
   * Fetches and finds l10n information for a specific list
   * @param $listName string a list name
   * @param $lang string language used to find l10n status
   * @param $offset int offset inside list
   * @param $limit int limit returned list items
   * @param $order string sort order of list items
   * @param $filter string filter list by l10n status
   * @return array
   */
  function getl10nList($listName, $lang="", $offset=-1, $limit=-1, $order="priority_asc", $filter="", $scoreLimit=0) {
    if ( $filter && in_array($filter, array('translated', 'needs review', 'needs updating', 'needs translation') ) ) {
      $filter = array( 'type' => $filter,
                       'current' => 0, 
                       'limit' => ( intval($limit) > 0 ) ? $limit : 0, 
                       'offset' => ( intval($offset) > 0 ) ? $offset: 0 );      
      $limit = -1;
      $offset = -1;
    } else {
      $filter = "";
    }    
    
    $order = $this->checkOrder($order);
    $listPages = $this->getListPages($listName, $order, $offset, $limit);
    $pages = array();
    $currentScore = 0;
       
    foreach ($listPages as $page) {
      $page['status'] = $this->getl10nStatus($page['page_name'], $lang);
      $page['local_page_name'] = $this->getl10nPageName($page['page_name'], $lang);
      
      if ( $filter['type'] ) {
        if ( $filter['type'] == $page['status'] && ( !$filter['limit'] || $filter['current'] < $filter['limit'] )  && ( $filter['offset'] || $filter['current'] >= $filter['offset'] ) ) {
          $pages[] = $page;
          $filter['current']++;
          if ( $scoreLimit ) {
            $currentScore += $page['score'];
            if ( $currentScore >= $scoreLimit ) break;
          }
          if ( $filter['limit'] == $filter['current']) break;
        } 
      } else { 
        $pages[] = $page;
        if ( $scoreLimit ) {
            $currentScore += $page['score'];
            if ( $currentScore >= $scoreLimit ) break;
        }
      }
    }
    
    return $pages;
    
  }
  
  /**
   * Fetches list items for a list
   * @param $listName string a list name
   * @param $order string sort order of list items
   * @param $offset int offset for list items
   * @param $limit int limit number of returned list items
   * @return array
   */
  function getListPages($listName, $order="priority_asc", $offset=-1, $limit=-1) {
    $order = $this->checkOrder($order);    
    $query = "SELECT * FROM `tiki_page_lists` WHERE `list_type_id`=? ORDER BY " . $this->convertSortMode($order);
    $vars = array( $this->getListTypeId($listName) );
    $results = $this->query($query, $vars, $limit, $offset);
    
    $pages = array();
    
    while ($page = $results->fetchRow() ) {
      $pages[] = $page;
    }
    
    return $pages;
    
  }
  
  /**
   * Return the number of pages in a list
   * @param $listName string name of list
   * @return int
   */
  function getListPagesCant($listName) {
  	$query = "SELECT count(*) FROM `tiki_page_lists` WHERE `list_type_id`=?";
  	$vars = array( $this->getListTypeId($listName) );
  	
  	return $this->getOne($query, $vars);
  }
  
  /**
   * Calculate page l10n status for a language
   * @param $page string page name to find l10n status for
   * @param $lang string language used to find l10n status
   * @return string
   */
  function getl10nStatus($page, $lang="") {
    global $dbTiki, $locale, $prefs;
   
    if ( !$lang ) $lang = array_shift($this->preferredLangs());
   
    $type = "wiki page";
    $translatedId = $this->getTranslation($type, $this->get_page_id_from_name($page), $lang);
     
    if ( !$translatedId )
      return "needs translation";

    $translatedName = $this->get_page_name_from_id($translatedId);    
    
    if ( $prefs['feature_wikiapproval'] != 'y' ) {
      if ( $this->isl10nOutOfDate($translatedId) )
        return "needs updating";
      return "translated";
    }

    if ( !$this->page_exists($prefs['wikiapproval_prefix'] . $translatedName ) ) { 
      $categlib = new CategLib($dbTiki);    
      $pageCats = $categlib->get_object_categories("wiki page", $translatedName);
           
      if ( in_array(3, $pageCats) )
        return "draft";
        
      if ( $this->isl10nOutOfDate($translatedId) )
        return "needs updating";
      return "translated";
    } else {
      return $this->getl10nStatusForStaging($translatedName);
    }
    
    return "unknown";  
  }
  
  /**
   * Calculate page l10n status for staging page
   * @param $pageName string page name to find l10n status for
   * @return mixed
   */
  function getl10nStatusForStaging($pageName) {
    global $dbTiki, $prefs;
    
    if ( $prefs['feature_wikiapproval'] != 'y' || !$prefs['wikiapproval_outofsync_category'] )
      return false;
      
    if ( !$this->page_exists($prefs['wikiapproval_prefix'] . $pageName) )
      return "needs review";
   
    $stagingName = $prefs['wikiapproval_prefix'] . $pageName;
    $stagingId = $this->get_page_id_from_name($stagingName);
    
    if ( $this->isl10nOutOfDate($stagingId) )
      return "needs updating"; 
      
    $categlib = new CategLib($dbTiki);    
    $stagingCats = $categlib->get_object_categories("wiki page", $stagingName);
    
    if ( in_array($prefs['wikiapproval_outofsync_category'], $stagingCats) )
      return "needs review";
      
    return "translated";
  }
  
  /**
   * Check if a page has been marked as "out of date"
   * @param $pageId int page ID
   * @return bool
   */
  function isl10nOutOfDate($pageId) {
    $bits = $this->getMissingTranslationBits( 'wiki page', $pageId, 'critical', true );
    
    if ( count($bits) )
      return true;
      
    return false;
  }
  
  /**
   * Fetch page name for a specific language if it exists
   * @param $pageName string a page name
   * @param $lang string a language
   * @return string
   */
  function getl10nPageName($pageName, $lang="") {
    global $dbTiki, $locale, $prefs;
    if ( !$lang ) $lang = array_shift($this->preferredLangs());
    
    $type = "wiki page";
    $translatedId = $this->getTranslation($type, $this->get_page_id_from_name($pageName), $lang);
     
    if ( !$translatedId )
      return $pageName;

    return $this->get_page_name_from_id($translatedId);
  }

  /**
   * Calculate l10n progress for a language
   * @param $listName string list name to find l10n status for
   * @param $lang string language used to find l10n status
   * @param $limit int limit number of returned items
   * @param $order string sort order for items
   * @return mixed
   */  
  function getl10nProgress($listName, $lang="", $limit=-1, $order="score_desc") {
    global $locale;
    
    if ( !$this->listTypeExists($listName) )
      return false;
    
    if ( !$lang ) $lang = array_shift($this->preferredLangs());
    
    $order = $this->checkOrder($order);
    $listPages = $this->getListPages($listName, $order, -1, $limit);
    $progress = array();
    $progress['total'] = 0;
    $progress['scoreTotal'] = array();
     
    foreach ($listPages as $page) {
      $status = $this->getl10nStatus($page['page_name'], $lang);
      $progress[$status]++;
      $progress['total']++;
      $progress['scoreTotal'][$status] += $page['score']; 
    }
    
    return $progress;    
  }
  
  function checkOrder($order) {
    return (in_array($order, $this->orderList)) ? $order : "priority_asc";
  }
  
  function checkItemFormat($items) {
    return ((isset($items[0]['page_name']) || isset($items[0]['page'])) && isset($items[0]['priority']));
  }        
}

$pagelistlib = new PageListLib;
