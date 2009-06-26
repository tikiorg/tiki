<?php

/**
 * Abstract class to provide basic functionalities to wiki importers.
 * Based on the work done on http://dev.tikiwiki.org/MediaWiki+to+TikiWiki+converter  
 * 
 * @author Rodrigo Sampaio Primo <rodrigo@utopia.org.br>
 * @package tikiimporter
 */

/**
 * Abstract class to provide basic functionalities to wiki importers.
 * Based on the work done on http://dev.tikiwiki.org/MediaWiki+to+TikiWiki+converter
 * 
 * Child classes must implement the functions validateInput(), parseData() and the variable inputData
 *
 * @package    tikiimporter
 */
class TikiImporter_Wiki extends TikiImporter
{

    /**
     * @see lib/importer/TikiImporter#importOptions
     */
	static public $importOptions = array(/*array('name' => 'attachments', 'type' => 'checkbox', 'label' => 'Import images and attachments'),*/
                                   array('name' => 'wikiRevisions', 'type' => 'text', 'label' => 'Number of page revisions to import (0 for all revisions)'),
                                   array('name' => 'alreadyExistentPageName', 'type' => 'select', 'label' => 'What to do with page names that already exists in TikiWiki?',
                                            'options' => array(array('name' => 'doNotImport', 'label' => 'Do not import'),
                                                               array('name' => 'override', 'label' => 'Override'),
                                                               array('name' => 'appendPrefix', 'label' => 'Append software name as prefix to the page name'))
                                        )     
                             );
    
    /**
     * Main function that starts the importing proccess
     * 
     * Set the import options based on the options the user selected
     * and start the importing proccess by calling the functions to
     * validate, parse and insert the data.
     *  
     * @return void
     */
    public function import()
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
        $this->parseData();
        
        foreach ($this->inputData as $page) {
            $this->importPage($page);
        }
    }
    
    /**
     * Create a new page or new page revision using Tiki bultin functions
     * 
     * Receives an array (actualy a hash) with all the revisions of one specific page
     * and insert the information on Tiki using Tiki bultin functions.
     *
     * This method might be used by wiki importers to insert the pages in Tiki database.
     * In order to do so $data must contain the following keys:
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
     * @param array $data
     * @return void
     */
    protected function importPage($data)
    {
        global $tikilib;

        // remove revisions that are not going to be imported
        if ($this->revisionsNumber > 0)
            $data['revisions'] = array_slice($data['revisions'], -$this->revisionsNumber);
        
        if ($tikilib->page_exists($data['name'])) {
            switch ($this->alreadyExistentPageName) {
                case 'override':
                    $tikilib->remove_all_versions($data['name']);
                    break;
                case 'appendPrefix':
                    $data['name'] = $this->softwareName . '_' . $data['name'];
                    break;
                default:
                    // $this->alreadyExistentPageName equal to 'doNotImport' or equal to invalid value 
                    print "Page already exists, no action taken: {$data['name']}\n";
                    return;
            }
        }
        
        $first = true;
        foreach ($data['revisions'] as $rev) {
            if ($first) {
                // Invalidate cache
                $tikilib->create_page($data['name'], 0, $rev['data'], $rev['lastModif'],
                    $rev['comment'], $rev['user'], $rev['ip']);
            } else {
                $tikilib->cache_page_info = null;
                $tikilib->update_page($data['name'], $rev['data'], $rev['comment'], $rev['user'],
                    $rev['ip'], '', $rev['minor'], '', false, null, $rev['lastModif']);
            }

            $first = false;
        }
    }
}

?>