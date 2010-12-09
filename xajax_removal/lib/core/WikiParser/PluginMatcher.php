<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class WikiParser_PluginMatcher implements Iterator, Countable
{
	private $starts = array();
	private $ends = array();
	private $level = 0;

	private $ranges = array();

	private $text;

	private $scanPosition = -1;

	private $leftOpen = 0;

	public static function match( $text )
	{
		$matcher = new self;
		$matcher->text = $text;
		$matcher->findMatches( 0, strlen( $text ) );

		return $matcher;
	}

	private function getSubMatcher( $start, $end )
	{
		$sub = new self;
		$sub->level = $this->level + 1;
		$sub->text = $this->text;
		$sub->findMatches( $start, $end );

		return $sub;
	}

	private function appendSubMatcher( $matcher )
	{
		foreach( $matcher->starts as $match ) {
			$match->changeMatcher( $this );
			$this->recordMatch( $match );
		}
	}

	private function isComplete()
	{
		return $this->leftOpen == 0;
	}

	function findMatches( $start, $end )
	{
		$this->findNoParseRanges( $start, $end );

		$pos = $start;
		while( false !== $pos = strpos( $this->text, '{', $pos ) ) {
			if( $pos >= $end ) {
				return;
			}

			if( ! $this->isParsedLocation( $pos ) ) {
				++$pos;
				continue;
			}

			$match = new WikiParser_PluginMatcher_Match( $this, $pos );
			++$pos;

			if( ! $match->findName( $end ) ) {
				continue;
			}

			if( ! $match->findArguments( $end ) ) {
				continue;
			}

			if( $match->getEnd() !== false ) {
				// End already reached
				$this->recordMatch( $match );
				$pos = $match->getEnd();

			} else {

				++$this->leftOpen;

				$bodyStart = $match->getBodyStart();
				$lookupStart = $bodyStart;

				while( $match->findEnd( $lookupStart, $end ) ) {
					$candidate = $match->getEnd();
					$sub = $this->getSubMatcher( $bodyStart, $candidate - 1 );

					if( $sub->isComplete() ) {
						$this->recordMatch( $match );
						$this->appendSubMatcher( $sub );
						$pos = $match->getEnd();
						--$this->leftOpen;
						break;
					}

					$lookupStart = $candidate;
				}
			}
		}
	}

	function getText()
	{
		return $this->text;
	}

	private function recordMatch( $match )
	{
		$this->starts[$match->getStart()] = $match;
		$this->ends[$match->getEnd()] = $match;
	}

	private function findNoParseRanges( $from, $to )
	{
		while( false !== $open = $this->findText( '~np~', $from, $to ) ) {
			if( false !== $close = $this->findText( '~/np~', $open, $to ) ) {
				$from = $close;
				$this->ranges[] = array( $open, $close );
			} else {
				return;
			}
		}
	}

	function isParsedLocation( $pos )
	{
		foreach( $this->ranges as $range ) {
			list( $open, $close ) = $range;

			if( $pos > $open && $pos < $close )
				return false;
		}

		return true;
	}

	function count()
	{
		return count( $this->starts );
	}

	function current()
	{
		return $this->starts[ $this->scanPosition ];
	}

	function next()
	{
		foreach( $this->starts as $key => $m ) {
			if( $key > $this->scanPosition ) {
				$this->scanPosition = $key;
				return $m;
			}
		}

		$this->scanPosition = -1;
	}

	function key()
	{
		return $this->scanPosition;
	}

	function valid()
	{
		return isset($this->starts[$this->scanPosition]);
	}

	function rewind()
	{
		reset( $this->starts );
		$this->scanPosition = key( $this->starts );
	}

	function getChunkFrom( $pos, $size )
	{
		return substr( $this->text, $pos, $size );
	}

	private function getFirstStart( $lower )
	{
		foreach( $this->starts as $key => $match )
			if( $key >= $lower )
				return $key;

		return false;
	}

	private function getLastEnd()
	{
		return end( array_keys( $this->ends ) );
	}

	function findText( $string, $from, $to )
	{
		if( $from >= strlen($this->text) )
			return false;

		$pos = strpos( $this->text, $string, $from );

		if( $pos === false || $pos + strlen($string) > $to )
			return false;

		return $pos;
	}

	function performReplace( $match, $string )
	{
		$start = $match->getStart();
		$end = $match->getEnd();

		$sizeDiff = - ($end - $start - strlen( $string ) );
		$this->text = substr_replace( $this->text, $string, $start, $end - $start ); 

		$this->ranges = array();
		$this->findNoParseRanges(0, strlen($this->text));

		$matches = $this->ends;
		$toRemove = array($match);
		$toAdd = array();

		foreach( $matches as $key => $m ) {
			if( $m->inside( $match ) ) {
				$toRemove[] = $m;
			} elseif( $key > $end ) {
				unset( $this->ends[$m->getEnd()] );
				unset( $this->starts[$m->getStart()] );
				$m->applyOffset( $sizeDiff );
				$toAdd[] = $m;
			}
		}

		foreach ($toRemove as $m) {
			unset( $this->ends[$m->getEnd()] );
			unset( $this->starts[$m->getStart()] );
			$m->invalidate();
		}

		foreach ($toAdd as $m) {
			$this->ends[$m->getEnd()] = $m;
			$this->starts[$m->getStart()] = $m;
		}

		$sub = $this->getSubMatcher( $start, $start + strlen( $string ) );
		if ($sub->isComplete()) {
			$this->appendSubMatcher($sub);
		}

		ksort( $this->ends );
		ksort( $this->starts );

		if( $this->scanPosition == $start ) {
			$this->scanPosition = $start - 1;
		}
	}
}

class WikiParser_PluginMatcher_Match
{
	const LONG = 1;
	const SHORT = 2;
	const LEGACY = 3;
	const NAME_MAX_LENGTH = 50;

	private $matchType = false;
	private $nameEnd = false;
	private $bodyStart = false;
	private $bodyEnd = false;

	private $matcher = false;
	private $start = false;
	private $end = false;
	private $arguments = false;

	function __construct( $matcher, $start )
	{
		$this->matcher = $matcher;
		$this->start = $start;
	}

	function findName( $limit )
	{
		$candidate = $this->matcher->getChunkFrom( $this->start + 1, self::NAME_MAX_LENGTH );
		$name = strtok( $candidate, " (}\n\r," );

		if( empty( $name ) || !ctype_alpha( $name ) ) {
			$this->invalidate();
			return false;
		}

		// Upper case uses long syntax
		if( strtoupper( $name ) == $name ) {
			$this->matchType = self::LONG;

			// Parenthesis required when using long syntax
			if( $candidate{ strlen($name) } != '(' ) {
				$this->invalidate();
				return false;
			}
		}
		else
			$this->matchType = self::SHORT;

		$nameEnd = $this->start + 1 + strlen( $name );

		if( $nameEnd > $limit ) {
			$this->invalidate();
			return false;
		}

		$this->name = strtolower( $name );
		$this->nameEnd = $nameEnd;

		return true;
	}

	function findArguments( $limit )
	{
		if( $this->nameEnd === false )
			return false;

		$pos = $this->matcher->findText( '}', $this->nameEnd, $limit );

		if( false === $pos ) {
			$this->invalidate();
			return false;
		}

		$unescapedFound = $this->countUnescapedQuotes( $this->nameEnd, $pos );

		while( 1 == ( $unescapedFound % 2 ) ) {
			$old = $pos;
			$pos = $this->matcher->findText( '}', $pos + 1, $limit );
			if( false === $pos ) {
				$this->invalidate();
				return false;
			}

			$unescapedFound += $this->countUnescapedQuotes( $old, $pos );
		}

		if ($this->matchType == self::LONG && $this->matcher->findText( '/', $pos - 1, $limit ) === $pos - 1 ) {
			$this->matchType = self::LEGACY;
			--$pos;
		}

		$seek = $pos;
		while( ctype_space($this->matcher->getChunkFrom($seek-1, '1')) ) {
			$seek--;
		}

		if( in_array($this->matchType, array(self::LONG, self::LEGACY)) && $this->matcher->findText( ')', $seek - 1, $limit ) !== $seek - 1 ) {
			$this->invalidate();
			return false;
		}

		$arguments = trim( $this->matcher->getChunkFrom( $this->nameEnd, $pos - $this->nameEnd ), '() ' );
		$this->arguments = trim( $arguments );

		if ($this->matchType == self::LEGACY) {
			++$pos;
		}

		$this->bodyStart = $pos + 1;

		if( $this->matchType == self::SHORT || $this->matchType == self::LEGACY ) {
			$this->end = $this->bodyStart;
			$this->bodyStart = false;
		}

		return true;
	}

	function findEnd( $after, $limit )
	{
		if( $this->bodyStart === false )
			return false;

		$endToken = '{' . strtoupper( $this->name ) . '}';

		do {
			if( isset( $bodyEnd ) )
				$after = $bodyEnd + 1;

			if( false === $bodyEnd = $this->matcher->findText( $endToken, $after, $limit ) ) {
				$this->invalidate();
				return false;
			}
		} while( ! $this->matcher->isParsedLocation( $bodyEnd ) );

		$this->bodyEnd = $bodyEnd;
		$this->end = $bodyEnd + strlen( $endToken );

		return true;
	}

	function inside( $match )
	{
		return $this->start > $match->start
			&& $this->end < $match->end;
	}

	function replaceWith( $string )
	{
		$this->matcher->performReplace( $this, $string );
	}

	function getName()
	{
		return $this->name;
	}

	function getArguments()
	{
		return $this->arguments;
	}

	function getBody()
	{
		return $this->matcher->getChunkFrom( $this->bodyStart, $this->bodyEnd - $this->bodyStart );
	}

	function getStart()
	{
		return $this->start;
	}

	function getEnd()
	{
		return $this->end;
	}

	function getBodyStart()
	{
		return $this->bodyStart;
	}

	function invalidate()
	{
		$this->matcher = false;
		$this->start = false;
		$this->end = false;
	}

	function applyOffset( $offset )
	{
		$this->start += $offset;
		$this->end += $offset;

		if ($this->nameEnd !== false) {
			$this->nameEnd += $offset;
		}

		if ($this->bodyStart !== false) {
			$this->bodyStart += $offset;
		}

		if ($this->bodyEnd !== false) {
			$this->bodyEnd += $offset;
		}
	}

	private function countUnescapedQuotes( $from, $to )
	{
		$string = $this->matcher->getChunkFrom( $from, $to - $from );
		$count = 0;

		$pos = -1;
		while( false !== $pos = strpos( $string, '"', $pos + 1 ) ) {
			++$count;
			if( $pos > 0 && $string{ $pos - 1} == "\\" )
				--$count;
		}

		return $count;
	}

	function changeMatcher( $matcher )
	{
		$this->matcher = $matcher;
	}

	public function __toString()
	{
		return $this->matcher->getChunkFrom( $this->start, $this->end - $this->start );
	}

	public function debug($level = 'X')
	{
		echo "\nMatch [$level] {$this->name} ({$this->arguments}) = {$this->getBody()}\n";
		echo "{$this->bodyStart}-{$this->bodyEnd} {$this->nameEnd} ({$this->matchType})\n";
	}
}
