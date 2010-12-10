<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Abstract class to provide basic functionalities to wiki importers.
 * Based on the work done on http://dev.tiki.org/MediaWiki+to+TikiWiki+converter  
 * 
 * @author Rodrigo Sampaio Primo <rodrigo@utopia.org.br>
 * @package tikiimporter
 */

require_once('tikiimporter.php');

/**
 * Abstract class to provide basic functionalities to wiki importers.
 * Based on the work done on http://dev.tiki.org/MediaWiki+to+TikiWiki+converter
 * 
 * Child classes must implement the functions validateInput(), parseData()
 *
 * @package    tikiimporter
 */
class TikiImporter_Wiki extends TikiImporter
{

    /**
     * @see lib/importer/TikiImporter#importOptions()
     */
	static public function importOptions()
	{
		$options  = array(
	        array('name' => 'wikiRevisions', 'type' => 'text', 'value' => 1, 'label' => tra('Number of page revisions to import (0 for all revisions)')),
	        array('name' => 'alreadyExistentPageName', 'type' => 'select', 'label' => tra('What to do with page names that already exists in TikiWiki?'),
	            'options' => array(
	                array('name' => 'doNotImport', 'label' => tra('Do not import')),
	                array('name' => 'override', 'label' => tra('Override')),
	                array('name' => 'appendPrefix', 'label' => tra('Append software name as prefix to the page name')),
	            )
	        ),     
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
        // how many revisions to import for each page
        if (!empty($_POST['wikiRevisions']) && $_POST['wikiRevisions'] > 0)
            $this->revisionsNumber = $_POST['wikiRevisions'];
        else
            $this->revisionsNumber = 0;
            
        // what to do with already existent page names
        if (!empty($_POST['alreadyExistentPageName']))
            $this->alreadyExistentPageName = $_POST['alreadyExistentPageName'];
        else
            $this->alreadyExistentPageName = 'doNotImport';
        
        // child classes must implement those two methods
        $this->validateInput();
		$parsedData = $this->parseData();

        $importFeedback = $this->insertData($parsedData);

        $this->saveAndDisplayLog("\nImportation completed!");

        echo "\n\n<b>" . tra("<a href=\"tiki-importer.php\">Click here</a> to finish the import process") . "</b>";;
        flush();

        $_SESSION['tiki_importer_feedback'] = $importFeedback;
        $_SESSION['tiki_importer_log'] = $this->log;
        $_SESSION['tiki_importer_errors'] = $this->errors;
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
        $countPages = 0;

        $countParsedData = count($parsedData);
        
        $this->saveAndDisplayLog("\n" . tra("$countParsedData pages parsed. Starting to insert those pages into Tiki:") . "\n");

        if (!empty($parsedData)) {
            foreach ($parsedData as $page) {
                if ($this->insertPage($page)) {
                    $countPages++;
                    $this->saveAndDisplayLog(tra("Page ${page['name']} sucessfully imported") . "\n");
                } else {
                    $this->saveAndDisplayLog(tra("Page ${page['name']} NOT imported (there was already a page with the same name)") . "\n");
                }
            }
        }

        $countData['totalPages'] = count($parsedData);
        $countData['importedPages'] = $countPages;
        return $countData;
    }

    /**
     * Create a new page or new page revision using Tiki bultin functions
     * 
     * Receives an array (actualy a hash) with all the revisions of one specific page
     * and insert the information on Tiki using Tiki bultin functions.
     *
     * This method might be used by wiki importers to insert the pages in Tiki database.
     * In order to do so $page must contain the following keys:
     * - name: the name of the page
     * - revisions: an array of arrays with all the page revisions. Each revision array must contain the keys:
     *     - data: the page content (in Tiki with sintax, parsing must be done before calling this function)
     *     - lastModif: the modification time
     *     - comment: the edition comment
     *     - user: the username
     *     - ip: ip address
     *     - minor: true or false
     * 
     * It also control the number of revisions to import ($this->revisionsNumber) and what to do if
     * the page name already exist ($this->alreadyExistentPageName) based on parameters passed by POST
     * 
     * @param array $page
     * @return string|bool page name if the page has been imported, otherwise returns false 
     */
    function insertPage($page)
    {
        global $tikilib;
        
        if ($tikilib->page_exists($page['name'])) {
            switch ($this->alreadyExistentPageName) {
                case 'override':
                    $tikilib->remove_all_versions($page['name']);
                    break;
                case 'appendPrefix':
                    $page['name'] = $this->softwareName . '_' . $page['name'];
                    break;
                case 'doNotImport':
                    return false;
            }
        }

        if (!empty($page)) { 
            $first = true;
            foreach ($page['revisions'] as $rev) {
                if ($first) {
                    $tikilib->create_page($page['name'], 0, $rev['data'], $rev['lastModif'],
						$rev['comment'], $rev['user'], $rev['ip'], '', '',
						isset($rev['is_html']) ? $rev['is_html'] : false);
                } else {
                    $tikilib->cache_page_info = null;
                    $tikilib->update_page($page['name'], $rev['data'], $rev['comment'], $rev['user'],
                        $rev['ip'], '', $rev['minor'], '', false, null, $rev['lastModif']);
                }
                $first = false;
            }
        }

        return $page['name'];
    }
}
