<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once 'lib/graph-engine/core.php';

class GridBasedGraphic extends Graphic // {{{1
{
	var $dependant;
	var $independant;
	var $vertical;
	var $horizontal;
	
	function GridBasedGraphic() // {{{2
	{
		Graphic::Graphic();
	}
	
	function _getMinValue( $type ) // {{{2
	{
		// Type is 'dependant' or 'independant'
		die( "Abstract Function Call" );
	}

	function _getMaxValue( $type ) // {{{2
	{
		// Type is 'dependant' or 'independant'
		die( "Abstract Function Call" );
	}

	function _getLabels( $type ) // {{{2
	{
		// Type is 'dependant' or 'independant'
		die( "Abstract Function Call" );
	}

	function _drawContent( &$renderer ) // {{{2
	{
		$top = 0;
		$left = 0;
		$bottom = 1;
		$right = 1;

		$layout = $this->_layout();
		
		$this->_initScales( $renderer, $layout, 'dependant' );
		$this->_initScales( $renderer, $layout, 'independant' );
		$this->_drawScales( $renderer, $layout, $left, $top, $right, $bottom );
		$this->_drawGridArea( new Fake_GRenderer( $renderer, $left, $top, $right, $bottom ), $layout );
	}

	function _initScales( &$renderer, $layout, $type ) // {{{2
	{
		switch( $layout["grid-$type-scale"] )
		{
		case 'linear':
			$this->$type = new LinearGridScale( $type, $layout, $this->_getMinValue( $type ), $this->_getMaxValue( $type ) );
			break;
		case 'static':
			$this->$type = new StaticGridScale( $type, $layout, $this->_getLabels( $type ) );
			break;
		}

		// Setting the vertical or horizontal members to the same scale
		$ori = $this->$type->orientation;
		$this->$ori = &$this->$type;
	}

	function _drawScales( &$renderer, $layout, &$left, &$top, &$right, &$bottom ) // {{{2
	{
		do // Loop until scales are stable
		{
			$otop = $top;
			$oleft = $left;
			$obottom = $bottom;
			$oright = $right;

			$size = $this->vertical->getSize( $renderer, $bottom - $top );
			switch( $layout['grid-vertical-position'] )
			{
			case 'left':
				$left = $size;
				break;
			case 'right':
				$right = 1 - $size;
				break;
			}

			$size = $this->horizontal->getSize( $renderer, $right - $left );
			switch( $layout['grid-horizontal-position'] )
			{
			case 'top':
				$top = $size;
				break;
			case 'bottom':
				$bottom = 1 - $size;
				break;
			}

		} while( $oleft != $left || $otop != $top || $oright != $right || $obottom != $bottom );

		switch( $layout['grid-vertical-position'] )
		{
		case 'left':
			$this->vertical->drawScale( new Fake_GRenderer( $renderer, 0, $top, $left, $bottom ) );
			break;
		case 'right':
			$this->vertical->drawScale( new Fake_GRenderer( $renderer, $right, $top, 1, $bottom ) );
			break;
		}

		switch( $layout['grid-horizontal-position'] )
		{
		case 'top':
			$this->horizontal->drawScale( new Fake_GRenderer( $renderer, $left, 0, $right, $top ) );
			break;
		case 'bottom':
			$this->horizontal->drawScale( new Fake_GRenderer( $renderer, $left, $bottom, $right, 1 ) );
			break;
		}
	}

	function _drawGridArea( &$renderer, $layout ) // {{{2
	{
		$renderer->drawRectangle( 0, 0, 1, 1, $renderer->getStyle( $layout['grid-background'] ) );
		$this->vertical->drawGrid( $renderer );
		$this->horizontal->drawGrid( $renderer );

		$this->_drawGridContent( $renderer );
	}

	function _drawGridContent( &$renderer ) // {{{2
	{
		die( "Abstract Function Call" );
	}

	function _default() // {{{2
	{
		return array_merge( parent::_default(), array(
			'grid-independant-location' => 'horizontal',
			'grid-reverse' => false,
			'grid-background' => 'FillStroke-Gray',
			'grid-horizontal-position' => 'bottom',
			'grid-vertical-position' => 'left',

			'grid-independant-scale' => 'linear',
			'grid-independant-linear-count' => 10,
			'grid-independant-zero-style' => 'Bold-LineStroke-Black',
			'grid-independant-minor-style' => 'Thin-LineStroke-Black',
			'grid-independant-minor-size' => 0.01,
			'grid-independant-minor-font' => false,
			'grid-independant-minor-guide' => false,
			'grid-independant-major-style' => 'LineStroke-Black',
			'grid-independant-major-size' => 0.02,
			'grid-independant-major-font' => 'Large-Text',
			'grid-independant-major-guide' => false,

			'grid-dependant-scale' => 'linear',
			'grid-dependant-linear-count' => 10,
			'grid-dependant-zero-style' => 'Bold-LineStroke-Black',
			'grid-dependant-minor-style' => 'Thin-LineStroke-Black',
			'grid-dependant-minor-size' => 0.01,
			'grid-dependant-minor-font' => false,
			'grid-dependant-minor-guide' => false,
			'grid-dependant-major-style' => 'LineStroke-Black',
			'grid-dependant-major-size' => 0.02,
			'grid-dependant-major-font' => 'Large-Text',
			'grid-dependant-major-guide' => 'Thin-LineStroke-Black',
		) );
	}
} // }}}1

class GridScale // {{{1
{
	var $orientation;
	var $type;
	var $layout;
	
	function GridScale( $type, $layout ) // {{{2
	{
		$this->type = $type;
		$this->layout = $layout;
		
		if( $type == 'independant' )
			$this->orientation = $layout['grid-independant-location'];
		else
			$this->orientation = ( $layout['grid-independant-location'] == 'vertical' ) ? 'horizontal' : 'vertical';
	}

	function drawScale( &$renderer ) // {{{2
	{
		die( "Abstract Function Call" );
	}

	function drawGrid( &$renderer ) // {{{2
	{
		die( "Abstract Function Call" );
	}

	function getLocation( $value ) // {{{2
	{
		die( "Abstract Function Call" );
	}

	function getRange( $value ) // {{{2
	{
		die( "Abstract Function Call" );
	}

	function getSize( &$renderer, $available ) // {{{2
	{
		die( "Abstract Function Call" );
	}

} // }}}1

class LinearGridScale extends GridScale // {{{1
{
	var $min;
	var $max;

	var $majorScaleCount;
	var $majorScaleRound;
	var $minorScaleCount;
	var $minorScaleRound;

	var $zero;
	var $value;

	var $skip;
	var $count;
	
	function LinearGridScale( $type, $layout, $min, $max ) // {{{2
	{
		parent::GridScale( $type, $layout );
		$this->min = $min;
		$this->max = $max;
		$this->count = 0;
		$this->skip = 1;

		$this->_adjustScale();
	}

	function _adjustScale() // {{{2
	{
		$max = $this->max;
		$min = $this->min;
		
		$base = "grid-{$this->type}";
		$default = $this->layout["$base-linear-count"];

		$maj = ceil( ( $max - $min ) / $default );
		switch( true )
		{
		case $max >= 0 && $min == 0:
			$this->majorScaleCount = ceil( $max / $maj );
			break;
		case $max >= 0 && $min > 0:
			$this->majorScaleCount = ceil( ($max - $min) / $maj );
			break;
		case $max >= 0 && $min < 0:
			$this->majorScaleCount = ceil( $max / $maj ) + ceil( abs($min) / $maj );
			break;
		case $max < 0:
			$this->majorScaleCount = ceil( (abs($min) - abs($max)) / $maj );
			break;
		}

		$this->majorScaleRound = 0; // Need to be changed.

		$this->minorScaleCount = 5;
		while( $this->minorScaleCount > 1 )
			if( round($maj / $this->minorScaleCount) == $maj / $this->minorScaleCount )
				break;
			else
				--$this->minorScaleCount;
			
		$this->minorScaleRound = 0;

		$this->zero = $this->_getZeroLocation();
		$this->value = $maj / (1 / $this->majorScaleCount);
	}
	
	function drawGrid( &$renderer ) // {{{2
	{
		$base = "grid-{$this->type}";
		
		$major = null;
		$minor = null;
		if( $this->layout["$base-major-guide"] !== false )
			$major = $renderer->getStyle( $this->layout["$base-major-guide"] );
		if( $this->layout["$base-minor-guide"] !== false )
			$minor = $renderer->getStyle( $this->layout["$base-minor-guide"] );

		$start = $this->zero;
		if( $start != 0 && $start != 1 && $this->layout["$base-zero-style"] !== false )
			$this->_drawGridLine( $renderer, $start, $renderer->getStyle( $this->layout["$base-zero-style"] ) );

		$major_int = $this->_getMajorInterval();
		$minor_int = $this->_getMinorInterval();
		for( $i = $start; $i > 0; $i -= $major_int )
		{
			if( !is_null( $major ) )
				$this->_drawGridLine( $renderer, $i, $major );

			if( !is_null( $minor ) )
				for( $j = $i - $minor_int; $i - $major_int < $j && 0 < $j; $j -= $minor_int )
					$this->_drawGridLine( $renderer, $j, $minor );
		}

		for( $i = $start; $i < 1; $i += $major_int )
		{
			if( !is_null( $major ) )
				$this->_drawGridLine( $renderer, $i, $major );

			if( !is_null( $minor ) )
				for( $j = $i + $minor_int; $i + $major_int > $j && 1 > $j; $j += $minor_int )
					$this->_drawGridLine( $renderer, $j, $minor );
		}
	}

	function drawScale( &$renderer ) // {{{2 
	{
		$base = "grid-{$this->type}";
		
		$major_font = null;
		$minor_font = null;
		$major_style = null;
		$minor_style = null;
		if( $this->layout["$base-major-font"] !== false )
			$major_font = $renderer->getStyle( $this->layout["$base-major-font"] );
		if( $this->layout["$base-minor-font"] !== false )
			$minor_font = $renderer->getStyle( $this->layout["$base-minor-font"] );

		if( $this->layout["$base-major-style"] !== false )
			$major_style = $renderer->getStyle( $this->layout["$base-major-style"] );
		if( $this->layout["$base-minor-style"] !== false )
			$minor_style = $renderer->getStyle( $this->layout["$base-minor-style"] );

		$minor_size = $this->layout["$base-minor-size"];
		$major_size = $this->layout["$base-major-size"];

		$start = $this->zero;
		if( $start != 0 && $start != 1 )
			$this->_drawGridTick( $renderer, $start, $major_style, $major_font, $major_size );

		$major_int = $this->_getMajorInterval();
		$minor_int = $this->_getMinorInterval();

		for( $i = $start; $i > 0; $i -= $major_int )
		{
			if( !is_null( $major_style ) || !is_null( $major_font ) )
				$this->_drawGridTick( $renderer, $i, $major_style, $major_font, $major_size, $this->majorScaleRound );

			if( !is_null( $minor_style ) || !is_null( $minor_font ) )
				for( $j = $i - $minor_int; $i - $major_int < $j && 0 < $j; $j -= $minor_int )
					$this->_drawGridTick( $renderer, $j, $minor_style, $minor_font, $minor_size, $this->minorScaleRound );
		}

		for( $i = $start; $i < 1; $i += $major_int )
		{
			if( !is_null( $major_style ) || !is_null( $major_font ) )
				$this->_drawGridTick( $renderer, $i, $major_style, $major_font, $major_size, $this->majorScaleRound );

			if( !is_null( $minor_style ) || !is_null( $minor_font ) )
				for( $j = $i + $minor_int; $i + $major_int > $j && 1 > $j; $j += $minor_int )
					$this->_drawGridTick( $renderer, $j, $minor_style, $minor_font, $minor_size, $this->minorScaleRound );
		}
	}

	function _drawGridLine( &$renderer, $pos, $style ) // {{{2
	{
		if( $this->orientation == 'vertical' )
			$renderer->drawLine( 0, $pos, 1, $pos, $style );
		else
			$renderer->drawLine( $pos, 0, $pos, 1, $style );
	}

	function _drawGridTick( &$renderer, $pos, $style, $font, $size, $round = false ) // {{{2
	{
		if( $this->orientation == 'vertical' )
		{
			$size = $size / $renderer->width;
			if( $this->layout['grid-vertical-position'] == 'left' )
			{
				if( !is_null( $style ) ) $renderer->drawLine( 1 - $size, $pos, 1, $pos, $style );
				if( !is_null( $font ) && $this->count++ % $this->skip == 0 ) 
				{
					$height = $renderer->getTextHeight( $font );
					$value = $this->_getValue( $pos, $round );
					$renderer->drawText( $value, 0, 1, $pos - $height / 2, $font );
				}
			}
			else
			{
				if( !is_null( $style ) ) $renderer->drawLine( 0, $pos, $size, $pos, $style );
				if( !is_null( $font ) && $this->count++ % $this->skip == 0 )
				{
					$height = $renderer->getTextHeight( $font );
					$value = $this->_getValue( $pos, $round );
					$renderer->drawText( $value, $size, 1, $pos - $height / 2, $font );
				}
			}
		}
		else
		{
			$size = $size / $renderer->height;
			if( $this->layout['grid-horizontal-position'] == 'bottom' )
			{
				if( !is_null( $style ) ) $renderer->drawLine( $pos, 0, $pos, $size, $style );
				if( !is_null( $font ) && $this->count++ % $this->skip == 0 )
				{
					$value = $this->_getValue( $pos, $round );
					$width = $renderer->getTextWidth( $value, $font ) * 0.55;
					$renderer->drawText( $value, $pos - $width, $pos + $width, $size, $font );
				}
			}
			else
			{
				if( !is_null( $style ) ) $renderer->drawLine( $pos, 1 - $size, $pos, 1, $style );
				if( !is_null( $font ) )
				{
					$value = $this->_getValue( $pos, $round );
					$width = $renderer->getTextWidth( $value, $font ) * 0.55;
					$renderer->drawText( $value, $pos - $width, $pos + $width, 0, $font );
				}
			}
		}
	}

	function getSize( &$renderer, $available ) // {{{2
	{
		$param = $this->layout["grid-{$this->type}-major-font"];
		if( $param !== false )
			$font = $renderer->getStyle( $param );
		$size = $this->layout["grid-{$this->type}-major-size"];
		switch( $this->orientation )
		{
		case 'vertical':
			$this->skip = $this->_calculateSkip( $renderer->getTextHeight( $font ), $available / $this->majorScaleCount );
			return (($param!==false)?$this->_getLargest( $renderer, $font ):0) + $size;
			break;
		case 'horizontal':
			$this->skip = $this->_calculateSkip( $this->_getLargest( $renderer, $font ), $available / $this->majorScaleCount );
			return (($param!==false)?$renderer->getTextHeight( $font ):0) + $size;
			break;
		}
	}

	function _calculateSkip( $size, $space ) // {{{2
	{
		$skip = 0;
		while( $size > $space * ++$skip );

		return $skip;
	}

	function _getLargest( &$renderer, $font ) // {{{2
	{
		return  max(
			$renderer->getTextWidth( $this->min, $font ),
			$renderer->getTextWidth( $this->max, $font )
		);
	}

	function _getMajorInterval() // {{{2
	{
		return 1 / $this->majorScaleCount;
	}

	function _getMinorInterval() // {{{2
	{
		return 1 / $this->majorScaleCount / $this->minorScaleCount;
	}
	
	function _getZeroLocation() // {{{2
	{
		$loc = $this->max / ($this->max - $this->min);

		if( $this->orientation != 'vertical' )
			$loc = 1 - $loc;

		return $loc;
	}

	function _getValue( $pos, $round = false ) // {{{2
	{
		$zpos = $this->zero;

		if( $this->orientation == 'vertical' )
		{
			$pos = 1 - $pos;
			$zpos = 1 - $zpos;
		}
		$v = $this->value * ($pos-$zpos);

		if( $round === false )
			return $v;
		else
			return round($v, $round);
	}

	function getLocation( $value ) // {{{2
	{
		$pos = $value / $this->value;

		if( $this->orientation == 'vertical' )
		{
			$pos += 1 - $this->zero;
			$pos = 1 - $pos;
		}
		else
			$pos += $this->zero;

		return $pos;
	}
	function getRange( $value ) // {{{2
	{
		$width = $this->_getMinorInterval() / 2;
		$pos = $this->getLocation( $value );
		$locs = array( $pos - $width, $pos + $width );
		sort( $locs );
		return $locs;
	}
} // }}}1

class StaticGridScale extends GridScale // {{{1
{
	var $labels;
	var $width;
	var $layers;
	var $count;
	
	function StaticGridScale( $type, $layout, $labels ) // {{{2
	{
		parent::GridScale( $type, $layout );
		$this->labels = $labels;
		$this->width = 1 / count( $labels );
		$this->count = 0;
	}

	function drawGrid( &$renderer ) // {{{2
	{
		$base = "grid-{$this->type}";
		
		$major = null;
		if( $this->layout["$base-major-guide"] !== false )
			$major = $renderer->getStyle( $this->layout["$base-major-guide"] );

		for( $i = 0; $i < 1; $i += $this->width )
			if( !is_null( $major ) )
				$this->_drawGridLine( $renderer, $i, $major );
	}

	function drawScale( &$renderer ) // {{{2 
	{
		$base = "grid-{$this->type}";
		
		$major_font = null;
		$major_style = null;
		if( $this->layout["$base-major-font"] !== false )
			$major_font = $renderer->getStyle( $this->layout["$base-major-font"] );

		if( $this->layout["$base-major-style"] !== false )
			$major_style = $renderer->getStyle( $this->layout["$base-major-style"] );

		$major_size = $this->layout["$base-major-size"];

		for( $i = 0; $i < 1; $i += $this->width )
			if( !is_null( $major_style ) || !is_null( $major_font ) )
				$this->_drawGridTick( $renderer, $i, $major_style, $major_font, $major_size );
	}

	function _calculateSkip( $size, $space ) // {{{2
	{
		$space = abs($space);
		$skip = 0;
		if (empty($space))
			return $skip;
		while( $size > $space * ++$skip );

		return $skip;
	}

	function _drawGridLine( &$renderer, $pos, $style ) // {{{2
	{
		if( $this->orientation == 'vertical' )
			$renderer->drawLine( 0, $pos, 1, $pos, $style );
		else
			$renderer->drawLine( $pos, 0, $pos, 1, $style );
	}

	function _drawGridTick( &$renderer, $pos, $style, $font, $size ) // {{{2
	{
		if( $this->orientation == 'vertical' )
		{
			$size = $size / $renderer->width;
			$width = (1 - $size) / $this->layers;

			if( $this->layout['grid-vertical-position'] == 'left' )
			{
				if( !is_null( $style ) ) $renderer->drawLine( 1 - $size, $pos, 1, $pos, $style );
				if( !is_null( $font ) ) 
				{
					$height = $renderer->getTextHeight( $font );
					$offset = ++$this->count % $this->layers * $width;
					$value = $this->_getValue( $pos );
					$renderer->drawText( $value, $offset, $offset + $width, $pos + ( $this->width - $height ) / 2, $font );
				}
			}
			else
			{
				if( !is_null( $style ) ) $renderer->drawLine( 0, $pos, $size, $pos, $style );
				if( !is_null( $font ) )
				{
					$offset = ++$this->count % $this->layers * $width + $size;
					$height = $renderer->getTextHeight( $font );
					$value = $this->_getValue( $pos );
					$renderer->drawText( $value, $offset, $offset + $width, $pos + ( $this->width - $height ) / 2, $font );
				}
			}
		}
		else
		{
			$size = $size / $renderer->height;
			if( $this->layout['grid-horizontal-position'] == 'bottom' )
			{
				if( !is_null( $style ) ) $renderer->drawLine( $pos, 0, $pos, $size, $style );
				if( !is_null( $font ) )
				{
					$y = $size + $renderer->getTextHeight( $font ) * ($this->count++ % $this->layers);
					$value = $this->_getValue( $pos );
					$width = $renderer->getTextWidth( $value, $font ) * 0.55;
					$pos += $this->width / 2;
					$renderer->drawText( $value, $pos - $width, $pos + $width, $y, $font );
				}
			}
			else
			{
				if( !is_null( $style ) ) $renderer->drawLine( 1 - $pos, 1 - $size, 1 - $pos, 1, $style );
				if( !is_null( $font ) )
				{
					$y = $renderer->getTextHeight( $font ) * (++$this->count % $this->layers);
					$value = $this->_getValue( $pos );
					$width = $renderer->getTextWidth( $value, $font ) / 2;
					$pos += $this->width / 2;
					$renderer->drawText( $value, $pos - $width, $pos + $width, $y, $font );
				}
			}
		}
	}

	function getSize( &$renderer, $available ) // {{{2
	{
		$param = $this->layout["grid-{$this->type}-major-font"];
		if( $param !== false )
			$font = $renderer->getStyle( $param );
		$size = $this->layout["grid-{$this->type}-major-size"];

		$max = 0;
		if( $param !== false )
			foreach( $this->labels as $label )
				$max = max( $max, $renderer->getTextWidth( $label, $font ) );

		switch( $this->orientation )
		{
		case 'vertical':
			$this->layers = $this->_calculateSkip( $renderer->getTextHeight( $font ), $available / count( $this->labels ) );
			return $max * $this->layers + $size;
			break;
		case 'horizontal':
			$this->layers = $this->_calculateSkip( $max, $available / count( $this->labels ) );
			return (($param!==false)?$renderer->getTextHeight( $font ):0) * $this->layers + $size;
			break;
		}
	}

	function _getValue( $pos ) // {{{2
	{
		$index = intval( $pos / $this->width );
		if( isset( $this->labels[ $index ] ) )
			return $this->labels[ $index ];
		else
			return null;
	}

	function getLocation( $value ) // {{{2
	{
		return array_sum( $this->getRange( $value ) ) / 2;
	}
	function getRange( $value ) // {{{2
	{
		$key = array_search( $value, $this->labels );
		$begin = $key * $this->width;
		$end = $begin + $this->width;

		return array( $begin, $end );
	}
} // }}}1
