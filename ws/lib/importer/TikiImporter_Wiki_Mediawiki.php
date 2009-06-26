<?php

class TikiImporter_Wiki_Mediawiki extends TikiImporter_Wiki
{
    public $softwareName = 'Mediawiki';
    public $options = array();
    public $file = '';
    public $dbInfo = array();
    protected $dom = '';

    function TikiImporter_Wiki_Mediawiki()
    {
        // how many revisions to import for each page
        if (!empty($_POST['wikiRevisions']) && $_POST['wikiRevisions'] > 0)
            $this->revisionsNumber = $_POST['wikiRevisions'];
        else
            $this->revisionsNumber = 0;
            
        // what to do with already existent page names
        $this->alreadyExistentPageName = $_POST['alreadyExistentPageName'];
        
        $this->validateInput();
        $this->import();
    }
    
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

    public function import()
    {
        $pages = $this->dom->getElementsByTagName('page');

        foreach ($pages as $page) {
            $data = $this->extractInfo($page);
            $this->importPage($data);
        }
    }

    function extractInfo(DOMElement $element)
    {
        $data = array();
        $data['revisions'] = array();

        foreach ($element->childNodes as $node) {
            if ($node instanceof DOMElement) {
                switch ($node->tagName)
                {
                case 'id':
                case 'title':
                    $data[$node->tagName] = (string) $node->textContent;
                    break;
                case 'revision':
                    $data['revisions'][] = $this->extractRevision($node);
                    break;
                default:
                    print "Unknown tag : {$node->tagName}\n";
                }
            }
        }
        
        // remove revisions that are not going to be imported
        $data['revisions'] = array_slice($data['revisions'], -$this->revisionsNumber);
            
        return $data;
    }

    function extractRevision(DOMElement $element)
    {
        $data = array();
        $data['minor'] = false;

        foreach ($element->childNodes as $node) {
            if ($node instanceof DOMElement) {
                switch ($node->tagName)
                {
                case 'id':
                case 'comment':
                case 'text':
                    $data[$node->tagName] = (string) $node->textContent;
                    break;

                case 'timestamp':
                    $data[$node->tagName] = strtotime($node->textContent);
                    break;

                case 'minor':
                    $data['minor'] = true;

                case 'contributor':
                    $data['contributor'] = $this->extractContributor($node);
                    break;

                default:
                    print "Unknown tag in revision: {$node->tagName}\n";
                }
            }
        }
                
        return $data;
    }

    function extractContributor(DOMElement $element)
    {
        $data = array();

        foreach ($element->childNodes as $node) {
            if ($node instanceof DOMElement) {
                switch ($node->tagName) {
                case 'id':
                case 'username':
                case 'ip':
                    $data[$node->tagName] = (string) $node->textContent;
                    break;
                default:
                    print "Unknown tag in contributor: {$node->tagName}\n";
                }
            }
        }

        if (!isset($data['username']))
            $data['username'] = 'anonymous';

        if (!isset($data['ip']))
            $data['ip'] = '0.0.0.0';

        return $data;
    }

    function importPage($data)
    {
        global $tikilib;

        if ($tikilib->page_exists($data['title'])) {
            switch ($this->alreadyExistentPageName) {
                case 'doNotImport':
                    print "Page already exists, no action taken: {$data['title']}\n";
                    return;
                case 'override':
                    $tikilib->remove_all_versions($data['title']);
                    break;
                case 'appendPrefix':
                    $data['title'] = $this->softwareName . '_' . $data['title'];
                    break;
            }
        }
        
        $first = true;
        foreach ($data['revisions'] as $rev) {
            $text = $this->convertMarkup($rev['text']);

            if ($first) {
                // Invalidate cache
                $tikilib->create_page(
                    $data['title'],
                    0,
                    $text,
                    $rev['timestamp'],
                    $rev['comment'],
                    $rev['contributor']['username'],
                    $rev['contributor']['ip']
                );
            } else {
                $tikilib->cache_page_info = null;
                $tikilib->update_page(
                    $data['title'],
                    $text,
                    $rev['comment'],
                    $rev['contributor']['username'],
                    $rev['contributor']['ip'],
                    '',
                    $rev['minor'],
                    '',
                    false,
                    null,
                    $rev['timestamp']
                );
            }

            $first = false;
        }
    }

    // Utility for converting MediaWiki markup to TikiWiki markup
    // Uses Text_Wiki PEAR library for heavy lifting   
    function convertMarkup($mediawikiText) {
        require_once('lib/pear/Text/Wiki/Mediawiki.php');
        $parser = new Text_Wiki_Mediawiki();
        $tikiText = $parser->transform($mediawikiText, 'Tiki');
        return $tikiText;
    }
}

?>