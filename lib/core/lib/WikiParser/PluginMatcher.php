<?php

class WikiParser_PluginMatcher implements Iterator, Countable
{
	private $starts = array();
	private $ends = array();
	private $level = 0;

	private $text;

	private $scanPosition;

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
			$this->recordMatch( $match );
		}
	}

	function findMatches( $start, $end )
	{
		$pos = $start;
		while( false !== $pos = strpos( $this->text, '{', $pos ) ) {
			if( $pos >= $end ) {
				return;
			}

			$match = new WikiParser_PluginMatcher_Match( $this, $pos );
			++$pos;

			if( ! $match->findName( $end ) ) {
				//die('NAME LOOKUP FAILED');
				continue;
			}

			if( ! $match->findArguments( $end ) ) {
				//die('ARG LOOKUP FAILED');
				continue;
			}

			if( $match->getEnd() !== false ) {
				// End already reached
				$this->recordMatch( $match );
				$pos = $match->getEnd();

			} else {

				$bodyStart = $match->getBodyStart();

				if( ! $match->findEnd( $bodyStart, $end ) ) {
					$pos = $bodyStart;
					continue;
				}

				$candidate = $match->getEnd();

				$sub = $this->getSubMatcher( $bodyStart, $candidate );
				$nextBegin = $sub->getFirstStart( $bodyStart );

				// Look for closing before any matches
				if( $nextBegin === false || $candidate < $nextBegin ) {
					$this->recordMatch( $match );
					$pos = $match->getEnd();
					continue;
				}

				$sub = $this->getSubMatcher( $bodyStart, $end );
				// Find matches as part of the body
				$last = $sub->getLastEnd();

				if( ! $match->findEnd( $last, $end ) ) {
					// This one could not match, but the sub matcher still contains valid matches
					$this->appendSubMatcher( $sub );
					$pos = $last;
					continue;
				}

				// Closing and adding sub matcher
				$this->recordMatch( $match );
				$this->appendSubMatcher( $sub );
				$pos = $match->getEnd();
			}
		}
	}

	private function recordMatch( $match )
	{
		$this->starts[$match->getStart()] = $match;
		$this->ends[$match->getEnd()] = $match;
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
		return $this->scanPosition != -1;
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
		$pos = strpos( $this->text, $string, $from );

		if( $pos === false || $pos + strlen($string) > $to )
			return false;

		return $pos;
	}
}

class WikiParser_PluginMatcher_Match
{
	const LONG = 1;
	const SHORT = 2;
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
		$name = strtok( $candidate, " (\n\r," );

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

		if( $this->matchType == self::LONG && $this->matcher->findText( ')', $pos - 1, $limit ) !== $pos - 1 ) {
			$this->invalidate();
			return false;
		}

		$this->bodyStart = $pos + 1;

		$arguments = trim( $this->matcher->getChunkFrom( $this->nameEnd, $pos - $this->nameEnd ), '()' );
		$this->arguments = trim( $arguments );

		if( $this->matchType == self::SHORT ) {
			$this->end = $this->bodyStart;
		}

		return true;
	}

	function findEnd( $after, $limit )
	{
		if( $this->bodyStart === false )
			return false;

		$endToken = '{' . strtoupper( $this->name ) . '}';

		if( false === $bodyEnd = $this->matcher->findText( $endToken, $after, $limit ) ) {
			$this->invalidate();
			return false;
		}

		$this->bodyEnd = $bodyEnd;
		$this->end = $bodyEnd + strlen( $endToken );

		return true;
	}

	function inside( $match )
	{
		return $this->start > $match->start
			&& $this->end < $match->end;
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

	public function __toString()
	{
		return $this->matcher->getChunkFrom( $this->start, $this->end - $this->start );
	}
}

?>
