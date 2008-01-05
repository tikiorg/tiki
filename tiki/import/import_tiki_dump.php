<?php
/**
 * Imports a MediaWiki-style XML dump in tikiwiki.
 *
 * Requires PHP5 DOM extension.
 */

if( phpversion() < '5.0.0' )
	die( 'PHP 5 Required. Version ' . phpversion() . " detected\n" );

class ImportTikiDump
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
			if( $first )
			{
				// Invalidate cache
				$tikilib->create_page(
					$data['title'],
					0,
					$rev['text'],
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
					$rev['text'],
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
}

?>
