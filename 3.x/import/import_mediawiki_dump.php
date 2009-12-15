<?php
/**
 * Imports a MediaWiki-style XML dump in tikiwiki.
 *
 * Requires PHP5 DOM extension.
 *
 * Requires Text_Wiki libraries. See http://dev.tikiwiki.org/MediaWiki+to+TikiWiki+converter
 * http://pear.php.net/package/Text_Wiki_Mediawiki
 * http://pear.php.net/package/Text_Wiki_Tiki
 *
 **/

//this script may only be included - so its better to die if called directly.
if ( basename($_SERVER['SCRIPT_NAME']) == basename(__FILE__) ) {
  header("location: index.php");
  exit;
}

if( phpversion() < '5.0.0' )
	die( 'PHP 5 Required. Version ' . phpversion() . " detected\n" );

if( !file_exists('Text/Wiki.php') || !file_exists('Text/Wiki/Wiki.php') || !file_exists('Text/Wiki/Mediawiki.php')) {
	$smarty->assign('msg', tra('Text_Wiki libraries need to be installed. Please see http://dev.tikiwiki.org/MediaWiki+to+TikiWiki+converter'));
        $smarty->display('error.tpl');
	die;
}

# Require Text_Wiki libraries

require_once('Text/Wiki.php');
require_once('Text/Wiki/Mediawiki.php');
require_once('Text/Wiki/Tiki.php');

class ImportMediaWikiDump
{
	function import( DOMDocument $dom )
	{
		$pages = $dom->getElementsByTagName( 'page' );

		foreach( $pages as $page )
		{
			$data = $this->extractInfo( $page );
			$this->importPage( $data );
		}
	}

	function extractInfo( DOMElement $element )
	{
		$data = array();
		$data['revisions'] = array();

		foreach( $element->childNodes as $node )
			if( $node instanceof DOMElement )
				switch( $node->tagName )
				{
				case 'id':
				case 'title':
					$data[$node->tagName] = (string) $node->textContent;
					break;
				case 'revision':
					$data['revisions'][] = $this->extractRevision( $node );
					break;
				default:
					print "Unknown tag : {$node->tagName}\n";
				}

		return $data;
	}

	function extractRevision( DOMElement $element )
	{
		$data = array();
		$data['minor'] = false;

		foreach( $element->childNodes as $node )
			if( $node instanceof DOMElement )
				switch( $node->tagName )
				{
				case 'id':
				case 'comment':
				case 'text':
					$data[$node->tagName] = (string) $node->textContent;
					break;

				case 'timestamp':
					$data[$node->tagName] = strtotime( $node->textContent );
					break;

				case 'minor':
					$data['minor'] = true;

				case 'contributor':
					$data['contributor'] = $this->extractContributor( $node );
					break;

				default:
					print "Unknown tag in revision: {$node->tagName}\n";
				}

		return $data;
	}

	function extractContributor( DOMElement $element )
	{
		$data = array();

		foreach( $element->childNodes as $node )
			if( $node instanceof DOMElement )
				switch( $node->tagName )
				{
				case 'id':
				case 'username':
				case 'ip':
					$data[$node->tagName] = (string) $node->textContent;
					break;
				default:
					print "Unknown tag in contributor: {$node->tagName}\n";
				}

		if( !isset( $data['username'] ) )
			$data['username'] = 'anonymous';

		if( !isset( $data['ip'] ) )
			$data['ip'] = '0.0.0.0';

		return $data;
	}

	function importPage( $data )
	{
		global $tikilib;

		if( $tikilib->page_exists( $data['title'] ) )
		{
			print "Page already exists, no action taken: {$data['title']}\n";
			return;
		}

		$first = true;
		foreach( $data['revisions'] as $rev )
		{
			$text = $this->convertMarkup($rev['text']);
			
			if( $first )
			{
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
			}
			else
			{
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

	# Utility for converting MediaWiki markup to TikiWiki markup
	# Uses Text_Wiki PEAR library for heavy lifting
	
	function convertMarkup($mediawiki_text)
	{
		$tw = new Text_Wiki_Mediawiki();
		$tiki_text = $tw->transform($mediawiki_text, 'Tiki');
		return $tiki_text;
	}
}

?>
