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
     * Array of the valid mime types for the
     * input file
     */
    public $validTypes = array('application/xml', 'text/xml');

    /**
     * @see lib/importer/TikiImporter#importOptions
     */
    static public $importOptions = array(
        array('name' => 'importAttachments', 'type' => 'checkbox', 'label' => 'Import images and attachments'),
    );    

    /**
     * The directory used to save the attachments.
     * It is defined on $this->import()
     */
    var $attachmentsDestDir = '';

    /**
     * Wheter to import or not the attachments if the option has been
     * marked by the user and if it is possible to write in the
     * destination dir
     */
    var $importAttachments = false;

    /**
     * Start the importing process by loading the XML file.
     * 
     * @see lib/importer/TikiImporter_Wiki#import()
     *
     * @param string $filePath path to the XML file
     * @return parent::import()
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
        return parent::import();
    }

    /**
     * At present this method only validates the Mediawiki XML
     * against its DTD (Document Type Definition)
     * 
     * @see lib/importer/TikiImporter#validateInput()
     *
     * @throws DOMException if XML file does not validate against schema
     */
    function validateInput()
    {
        if (!@$this->dom->schemaValidate(dirname(__FILE__) . '/mediawiki_dump.xsd')) {
            throw new DOMException(tra('XML file does not validate against the Mediawiki XML schema'));
        }
    }

    /**
     * Check for all the requirements to import attachments
     * and also set the $this->attachmentsDestDir.
     * If one of them is not satisfied the script will die.
     * Otherwise set $this->importAttachments to true
     *
     * @returns void
     */
    function checkRequirementsForAttachments()
    {
        global $tikidomain;

        $this->attachmentsDestDir = dirname(__FILE__) . '/../../img/wiki_up/';
        if ($tikidomain)
            $this->attachmentsDestDir .= $tikidomain;

        if (ini_get('allow_url_fopen') === false) {
            $this->saveAndDisplayLog("ABORTING: you need to enable the PHP setting 'allow_url_fopen' to be able to import attachments. Fix the problem or try to import without the attachments.\n");
            die;
        }

        if (!file_exists($this->attachmentsDestDir)) {
            $this->saveAndDisplayLog("ABORTING: destination directory for attachments ($this->attachmentsDestDir) does no exist. Fix the problem or try to import without the attachments.\n");
            die;
        } elseif (!is_writable($this->attachmentsDestDir)) {
            $this->saveAndDisplayLog("ABORTING: destination directory for attachments ($this->attachmentsDestDir) is not writable. Fix the problem or try to import without attachments.\n");
            die;
        }

        $this->importAttachments = true;
    }

    /**
     * Foreach page check if it is a wiki page or a wiki page
     * attachment and call the proper method, respectively 
     * $this->extractInfo() and $this->handleFileUpload()
     *
     * In the case of a wiki page append the returned value of
     * $this->extractInfo() to $parsedData array
     * 
     * @return array $parsedData
     */
    function parseData()
    {
        $parsedData = array();
        $pages = $this->dom->getElementsByTagName('page');

        $this->saveAndDisplayLog("\nStarting to parse " . $pages->length . " pages:\n");

        foreach ($pages as $page) {
            // TODO: discover if there is a better to to check if $page has 'upload' element
            $upload = $page->getElementsByTagName('upload');

            if ($upload->length >= 1) {
                // is a reference to a wiki page attachment
                if ($this->importAttachments)
                    $this->downloadAttachment($upload->item(0));
            } else {
                // is a wiki page
                try {
                    $parsedData[] = $this->extractInfo($page);
                } catch (ImporterParserException $e) {
                    $this->saveAndDisplayLog($e->getMessage());
                }
            }
        }

        return $parsedData;
    }

    /**
     * Receive an DOMElement page with the attribute 'upload'
     * and try to download the file to the 
     * img/wiki_up/ directory
     *
     * @param DOMElement $upload
     * @return void
     */
    function downloadAttachment(DOMElement $upload) {
        $fileName = $upload->getElementsByTagName('filename')->item(0)->nodeValue;
        $fileUrl = $upload->getElementsByTagName('src')->item(0)->nodeValue;

        if ($attachmentContent = file_get_contents($fileUrl)) {
            $newFile = fopen($this->attachmentsDestDir . $fileName, 'w');
            fwrite($newFile, $attachmentContent);
            $this->saveAndDisplayLog("File $fileName sucessfully imported!\n");
        } else {
            $this->saveAndDisplayLog("Unable to import file $fileName\n");
        }
        // check if is possible to download file
        // download file
        // save it on img/wiki_up/$domain
        // print message to the user
        // what to do with conflicting names?
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
     * @return array $data information for one wiki page 
     * @throws ImporterParserException if fail to parse all revisions of a page
     */
    function extractInfo(DOMElement $page)
    {
        $data = array();
        $data['revisions'] = array();

        $i = 0;
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
                    $i++;
                    try {
                        $data['revisions'][] = $this->extractRevision($node);
                    } catch (ImporterParserException $e) {
                        $this->saveAndDisplayLog('Error while parsing revision ' . $i . ' of the page "' . $data['name'] . '". Or there is a problem on the page syntax or on the Text_Wiki parser (the parser used by the importer).' . "\n");
                    }
                    break;
                default:
                    print "Unknown tag : {$node->tagName}\n";
                }
            }
        }

        if (count($data['revisions']) > 0) {
            $msg = 'Page "' . $data['name'] . '" succesfully parsed with ' . count($data['revisions']) . " revisions (from a total of $i revisions).\n";
            $this->saveAndDisplayLog($msg);
            return $data;
        } else {
            throw new ImporterParserException('Page "' . $data['name'] . '" is NOT going to be imported. It was not possible to parse any of the page revisions.' . "\n");
        }
    }

    /**
     * Parse an DOM representation of a Mediawiki page revisions and return all the values
     * that will be imported (page content converted to Tiki syntax, lastModif, minor, user and ip address)
     *
     * Note: the names of the keys are changed to reflected the names used by
     * Tiki builtin function (i.e. 'text' is changed to 'data' as used in TikiLib::create_page())
     * 
     * @param DOMElement $page
     * @return array $data information for one wiki page revision
     * @throws ImporterParserException if unable to parse revision content
     */
    function extractRevision(DOMElement $revision)
    {
        $data = array();
        $data['minor'] = false;
        $data['comment'] = '';

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
                    $text = $this->convertMarkup($node->textContent);
                    if (get_class($text) == 'PEAR_Error') {
                        throw new ImporterParserException($text->message);
                    } else {
                        $data['data'] = $text;
                    }
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

            // do not replace space by underscore in wikilinks
            $parser->setParseConf('Wikilink', 'spaceUnderscore', false);

            $tikiText = $parser->transform($mediawikiText, 'Tiki');
            return $tikiText;
        }
    }
}

class ImporterParserException extends Exception {}

?>
