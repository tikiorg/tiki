<?php

require_once('tikiimporter_wiki.php');
require_once('Text/Wiki/Mediawiki.php');

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
    public $dom = '';

    /**
     * @see lib/importer/TikiImporter#importOptions
     */
    static public $importOptions = array();    

    /**
     * Start the importing process by loading the XML file.
     * 
     * @see lib/importer/TikiImporter_Wiki#import()
     *
     * @param string $filePath path to the XML file
     * @return void
     */
    function import($filePath)
    {
        $this->dom = new DOMDocument;
        $this->dom->load($filePath);
        parent::import();
    }

    /**
     * At present this method only validates the Mediawiki XML
     * against its DTD (Document Type Definition)
     * 
     * @see lib/importer/TikiImporter#validateInput()
     */
    function validateInput()
    {
        try {
            $this->dom->schemaValidate(dirname(__FILE__) . '/mediawiki_dump.xsd');
        } catch (Exception $e) {
            throw new DOMException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Foreach page call $this->extractInfo() and append the
     * returned value to $parsedData array
     * 
     * @see lib/importer/TikiImporter#parseData()
     *
     * @return array $parsedData
     */
    function parseData()
    {
        $parsedData = array();
        $pages = $this->dom->getElementsByTagName('page');

        foreach ($pages as $page) {
            $parsedData[] = $this->extractInfo($page);
        }

        return $parsedData;
    }

    /**
     * Parse an DOM representation of a Mediawiki page and return all the values
     * that will be imported (page name, page content for all revisions)
     * 
     * Note: the names of the keys are changed to reflected the names used by
     * Tiki builtin function (i.e. 'title' is changed to 'name' as used in 
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
                    break;
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
     * Tiki builtin function (i.e. 'text' is changed to 'data' as used in TikiLib::create_page())
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
                    break;
                case 'comment':
                    $data['comment'] = $node->textContent;
                    break;
                case 'text':
                    $data['data'] = $this->convertMarkup($node->textContent);
                    break;

                case 'timestamp':
                    $data['lastModif'] = strtotime($node->textContent);
                    break;

                case 'minor':
                    $data['minor'] = true;
                    break;

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
                    break;
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
        if (!empty($mediawikiText)) {
            $parser = Text_Wiki::factory('Mediawiki');
            $tikiText = $parser->transform($mediawikiText, 'Tiki');
            return $tikiText;
        }
    }
}

?>
