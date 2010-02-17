<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class SemanticLib
{
	private $knownTokens = false;
	private $newTokens = false;

	private function loadKnownTokens() // {{{
	{
		if( is_array($this->knownTokens) )
			return;

		global $tikilib;

		$this->knownTokens = array();

		$result = $tikilib->query( "SELECT token, label, invert_token FROM tiki_semantic_tokens" );
		while( $row = $result->fetchRow() ) {
			$token = $row['token'];
			$this->knownTokens[$token] = $row;
		}

		ksort( $this->knownTokens );
	} // }}}

	private function loadNewTokens() // {{{
	{
		if( is_array($this->newTokens) )
			return;

		$this->loadKnownTokens();

		$this->newTokens = array();

		global $tikilib;

		$existing = array_keys( $this->knownTokens );

		$result = $tikilib->query( "SELECT DISTINCT reltype FROM tiki_links" );
		while( $row = $result->fetchRow() ) {
			if( empty($row['reltype']) )
				continue;

			$new = array_diff(
				explode( ',', $row['reltype'] ),
				$existing
			);

			$this->newTokens = array_merge( $new, $this->newTokens );
		}

		$this->newTokens = array_unique( $this->newTokens );
	} // }}}

	function getToken( $name, $field = null ) // {{{
	{
		$this->loadKnownTokens();

		if( array_key_exists( $name, $this->knownTokens ) ) {
			$data = $this->knownTokens[$name];

			if( is_null( $field ) )
				return $data;

			if( array_key_exists( $field, $data ) )
				return $data[$field];
		}

		return false;
	} // }}}

	function getInvert( $name, $field = null ) // {{{
	{
		if( false !== $invert = $this->getToken( $name, 'invert_token' ) ) {
			if( empty($invert) )
				$invert = $name;

			return $this->getToken( $invert, $field );
		}

		return false;
	} // }}}

	function getTokens() // {{{
	{
		$this->loadKnownTokens();
		return $this->knownTokens;
	} // }}}

	function getNewTokens() // {{{
	{
		$this->loadNewTokens();
		return $this->newTokens;
	} // }}}

	function getLinksUsing( $token, $conditions = array() ) // {{{
	{
		global $tikilib;

		$token = (array) $token;
		$tokenConds = array();
		$bindvars = array();

		// Multiple tokens can be fetched at the same time
		foreach( $token as $name ) {
			$tokenConds[] = '`reltype` LIKE ?';
			$bindvars[] = "%$name%";
		}


		$mid = array( '`reltype` IS NOT NULL' );
		$mid[] = '( ' . implode( ' OR ', $tokenConds ) . ' )';

		// Filter on source and destination
		foreach( $conditions as $field => $value ) {
			if( ! in_array( $field, array( 'fromPage', 'toPage' ) ) )
				continue;

			$mid[] = "`$field` = ?";
			$bindvars[] = $value;
		}

		$mid = implode( ' AND ', $mid );
		$result = $tikilib->query( $q= "SELECT `fromPage`, `toPage`, reltype FROM tiki_links WHERE $mid ORDER BY `fromPage`, `toPage`",
			$bindvars );
		
		$links = array();
		while( $row = $result->fetchRow() ) {
			$row['reltype'] = explode( ',', $row['reltype'] );

			if( count( array_intersect( $token, $row['reltype'] ) ) > 0 )
				$links[] = $row;
		}

		return $links;
	} // }}}

	function replaceToken( $oldName, $newName, $label, $invert = null ) // {{{
	{
		$exists = ( false !== $this->getToken( $oldName ) );

		if( $oldName != $newName && false !== $this->getToken( $newName ) )
			return tra('Semantic token already exists') . ": $newName";
		if( !$this->isValid( $oldName ) )
			return tra('Invalid semantic token name') . ": $oldName";
		if( !$this->isValid( $newName ) )
			return tra('Invalid semantic token name') . ": $newName";
		if( false === $this->getToken( $invert ) || $invert == $newName )
			$invert = null;

		global $tikilib;

		if( $exists ) {
			$tikilib->query( "DELETE FROM tiki_semantic_tokens WHERE token = ?",
				array( $oldName ) );
		}

		if( is_null( $invert ) ) {
			$tikilib->query( "INSERT INTO tiki_semantic_tokens (token, label) VALUES(?,?)",
				array( $newName, $label ) );
		} else {
			$tikilib->query( "INSERT INTO tiki_semantic_tokens (token, label, invert_token) VALUES(?,?,?)",
				array( $newName, $label, $invert ) );
		}

		if( $oldName != '' && $newName != $oldName ) {
			$tikilib->query( "UPDATE tiki_semantic_tokens SET invert_token = ? WHERE invert_token = ?",
				array( $newName, $oldName ) );

			$this->replaceReferences( $oldName, $newName );
		}

		unset( $this->knownTokens[$oldName] );
		$this->knownTokens[$newName] = array(
			'token' => $newName,
			'label' => $label,
			'invert_token' => $invert,
		);
		ksort( $this->knownTokens );

		return true;
	} // }}}

	private function replaceReferences( $oldName, $newName = null ) // {{{
	{
		global $tikilib;

		if( ! $this->isValid( $oldName ) )
			return tra('Invalid semantic token name') . ": $oldName";
		if( ! is_null($newName) && ! $this->isValid( $newName ) && $valid )
			return tra('Invalid semantic token name') . ": $newName";

		$links = $this->getLinksUsing( $oldName );

		$pagesDone = array();
		foreach( $links as $link )
		{
			// Update tiki_links
			$link['reltype'] = array_diff( $link['reltype'], array($oldName) );
			if( ! empty( $newName ) )
				$link['reltype'] = array_merge( $link['reltype'], array($newName) );
			sort( $link['reltype'] );
			$tikilib->replace_link( $link['fromPage'], $link['toPage'], $link['reltype'] );

			// Page body only needs to be replaced once
			if( ! array_key_exists( $link['fromPage'], $pagesDone ) ) {
				$info = $tikilib->get_page_info( $link['fromPage'] );
				$data = $info['data'];
				$data = str_replace( "($oldName(", "($newName(", $data );

		    	$query = "update `tiki_pages` set `data`=?,`page_size`=? where `pageName`=?";
		    	$tikilib->query($query, array( $data,(int) strlen($data), $link['fromPage']));

				$pagesDone[ $link['fromPage'] ] = true;
			}
		}

		return true;
	} // }}}

	function cleanToken( $token ) // {{{
	{
		$this->replaceReferences( $token );

		$this->newTokens = array_diff( $this->newTokens, array( $token ) );
	} // }}}

	function removeToken( $token, $removeReferences = false ) // {{{
	{
		global $tikilib;

		if( false === $this->getToken( $token ) )
			return tra("Semantic token not found") . ": $token";

		$tikilib->query( "DELETE FROM tiki_semantic_tokens WHERE token = ?",
			array( $token ) );

		unset($this->knownTokens[$token]);

		if( $removeReferences )
			$this->replaceReferences( $token, '' );
		elseif( $this->newTokens !== false )
			$this->newTokens[] = $token;

		return true;
	} // }}}

	function renameToken( $oldName, $newName ) // {{{
	{
		$this->replaceReferences( $oldName, $newName );

		$this->newTokens = array_diff( $this->newTokens, array( $oldName ) );
		if( false === $this->getToken( $newName ) )
			$this->newTokens[] = $newName;
	} // }}}

	function isValid( $token ) // {{{
	{
		return preg_match( "/^[a-z0-9-]{1,15}\\z/", $token );
	} // }}}

	function getRelationList( $page ) // {{{
	{
		global $tikilib, $wikilib;
		$relations = array();

		$result = $tikilib->query( "SELECT `toPage`, `reltype` FROM tiki_links WHERE `fromPage` = ? AND reltype IS NOT NULL", array($page) );
		while( $row = $result->fetchRow() ) {
			foreach( explode( ',', $row['reltype'] ) as $reltype ) {
				if( false === $label = $this->getToken( $reltype, 'label' ) )
					continue;

				$label = tra($label);

				if( ! array_key_exists( $label, $relations ) )
					$relations[$label] = array();

				if( ! array_key_exists( $row['toPage'], $relations[$label] ) )
					$relations[$label][ $row['toPage'] ] = $wikilib->sefurl( $row['toPage'] );
			}
		}

		$result = $tikilib->query( "SELECT `fromPage`, `reltype` FROM tiki_links WHERE `toPage` = ? AND `reltype` IS NOT NULL", array($page) );
		while( $row = $result->fetchRow() ) {
			foreach( explode( ',', $row['reltype'] ) as $reltype ) {
				if( false === $label = $this->getInvert( $reltype, 'label' ) )
					continue;

				$label = tra($label);

				if( ! array_key_exists( $label, $relations ) )
					$relations[$label] = array();

				if( ! array_key_exists( $row['fromPage'], $relations[$label] ) )
					$relations[$label][ $row['fromPage'] ] = $wikilib->sefurl( $row['fromPage'] );
			}
		}

		ksort( $relations );
		foreach( $relations as &$set )
			ksort( $set );

		return $relations;
	} // }}}

	function getAliasContaining( $query, $exact_match = false, $in_lang = NULL ) // {{{
	{
		global $tikilib;
		$orig_query = $query;
		if (!$exact_match) {
			$query = "%$query%";
		}
		$result = $tikilib->query( "SELECT `fromPage`, `toPage`, `reltype` FROM `tiki_links` WHERE `toPage` LIKE ? AND `reltype` IS NOT NULL", array($query) );

		$aliases = array();
		while( $row = $result->fetchRow() ) {
			$types = explode( ',', $row['reltype'] );

			if( in_array( 'alias', $types ) ) {
				unset( $row['reltype'] );
				$aliases[] = $row;
			}
		}		
		$aliases = $this->onlyKeepAliasesFromPageInLanguage($in_lang, $aliases);
		return $aliases;
	} // }}}
	
	function onlyKeepAliasesFromPageInLanguage($language, $aliases)
	{
		global $multilinguallib;
		if (!$language) {
			return $aliases;
		}
		
		$aliasesInCorrectLanguage = array();
		foreach ($aliases as $index => $aliasInfo) {
			$aliasLang = $multilinguallib->getLangOfPage($aliasInfo['fromPage']);
			if ($aliasLang === $language) {
				$aliasesInCorrectLanguage[] = $aliasInfo;
			}			
		}
//		echo "<pre>-- onlyKeepAliasesFromPageInLanguage: exiting</pre>\n";
		return $aliasesInCorrectLanguage;
	}
}

global $semanticlib;
$semanticlib = new SemanticLib;
