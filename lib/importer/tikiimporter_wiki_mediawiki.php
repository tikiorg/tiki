<?php

/**
 * Parses a MediaWiki-style XML dump to import it into TikiWiki.
 * Requires PHP5 DOM extension.
 * Based on the work done on http://dev.tikiwiki.org/MediaWiki+to+TikiWiki+converter  
 *
 * @package    tikiimporter
 */
class TikiImporter_Wiki_Mediawiki extends TikiImporter_Wiki
{
    public $softwareName = 'Mediawiki';
    
    /**
     * The DOM representation of the Mediawiki XML dump
     * @var DOMDocument object
     */
    protected $dom = '';

    /**
     * @see lib/importer/TikiImporter#importOptions
     */
    static public $importOptions = array();    
    
    /**
     * At present this method only validates the Mediawiki XML
     * against its DTD (Document Type Definition)
     * 
     * @see lib/importer/TikiImporter#validateInput()
     */
    public function validateInput()
    {
        global $smarty;
        
        $this->dom = new DOMDocument;
        $this->dom->load($_FILES['importFile']['tmp_name']);
        if (!$this->dom->schemaValidate('./lib/importer/mediawiki_dump.xsd')) {
            $msg = tra('File does not validate against schema. Try again.');
            $smarty->assign('msg', $msg);
            $smarty->display('error.tpl');
            die;
        }
    }

    /**
     * Foreach page call $this->extractInfo() and assign the
     * returned value to $this->inputData array
     * 
     * @see lib/importer/TikiImporter#parseData()
     */
    public function parseData()
    {
        $pages = $this->dom->getElementsByTagName('page');

        foreach ($pages as $page) {
            $this->inputData[] = $this->extractInfo($page);
        }
    }

    /**
     * Parse an DOM representation of a Mediawiki page and return all the values
     * that will be imported (page name, page content for all revisions)
     * 
     * Note: the names of the keys are changed to reflected the names used by
     * Tiki builtin function (i.e. 'title' is changed to 'name' as it is used of 
     * TikiLib::create_page() which will be called by TikiImporter_Wiki::insertPage())
     * 
     * @param DOMElement $page
     * @return unknown_type
     */
    function extractInfo(DOMElement $page)
    {
        $data = array();
        $data['revisions'] = array();

        foreach ($page->childNodes as $node) {
            if ($node instanceof DOMElement) {
                switch ($node->tagName)
                {
                case 'id':
                case 'title':
                    $data['name'] = (string) $node->textContent;
                    break;
                case 'revision':
                    $data['revisions'][] = $this->extractRevision($node);
                    break;
                default:
                    print "Unknown tag : {$node->tagName}\n";
                }
            }
        }
            
        return $data;
    }

    /**
     * Parse an DOM representation of a Mediawiki page revisions and return all the values
     * that will be imported (page content converted to Tiki syntax, lastModif, minor, user and ip address)
     *
     * Note: the names of the keys are changed to reflected the names used by
     * Tiki builtin function (i.e. 'text' is changed to 'data' as it is used of TikiLib::create_page())
     * 
     * @param DOMElement $page
     * @return unknown_type
     */
    function extractRevision(DOMElement $revision)
    {
        $data = array();
        $data['minor'] = false;

        foreach ($revision->childNodes as $node) {
            if ($node instanceof DOMElement) {
                switch ($node->tagName)
                {
                case 'id':
                case 'comment':
                case 'text':
                    $data['data'] = $this->convertMarkup($node->textContent);
                    break;

                case 'timestamp':
                    $data['lastModif'] = strtotime($node->textContent);
                    break;

                case 'minor':
                    $data['minor'] = true;

                case 'contributor':
                    $data = array_merge($data, $this->extractContributor($node));
                    break;

                default:
                    print "Unknown tag in revision: {$node->tagName}\n";
                }
            }
        }
                
        return $data;
    }

    /**
     * Parse an DOM representation of a Mediawiki page revision contributor and return
     * the username and ip address
     * 
     * @param DOMElement $contributor
     * @return array $data
     */
    function extractContributor(DOMElement $contributor)
    {
        $data = array();

        foreach ($contributor->childNodes as $node) {
            if ($node instanceof DOMElement) {
                switch ($node->tagName) {
                case 'id':
                case 'ip':
                    $data[$node->tagName] = (string) $node->textContent;
                    break;
                case 'username':
                    $data['user'] = (string) $node->textContent;
                    break;
                default:
                    print "Unknown tag in contributor: {$node->tagName}\n";
                }
            }
        }

        if (!isset($data['user']))
            $data['user'] = 'anonymous';

        if (!isset($data['ip']))
            $data['ip'] = '0.0.0.0';

        return $data;
    }
    
    /**
     * Utility for converting MediaWiki markup to TikiWiki markup
     * Uses Text_Wiki PEAR library for heavy lifting
     *  
     * @param string $mediawikiText 
     * @return string $tikiText
     */
    function convertMarkup($mediawikiText) {
        require_once('Text/Wiki/Mediawiki.php');
        $parser = new Text_Wiki_Mediawiki();
        $tikiText = $parser->transform($mediawikiText, 'Tiki');
        return $tikiText;
    }
}

?>
