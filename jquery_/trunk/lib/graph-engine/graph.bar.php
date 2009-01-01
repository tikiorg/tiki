<?php
require_once 'graph-engine/abstract.gridbased.php';

class BarBasedGraphic extends GridBasedGraphic // {{{1
{
	var $columns;
	var $styleMap;
	var $columnMap;

	function BarBasedGraphic() // {{{2
	{
		GridBasedGraphic::GridBasedGraphic();
		$this->columns = array();
		$this->styleMap = array();
		$this->columnMap = array();
	}
	
	function getRequiredSeries() // {{{2
	{
		return array(
			'label' => false,
			'color' => false,
			'style' => false,
			'x' => true,
			'y0' => true
		);
	}

	function _getMinValue( $type ) // {{{2
	{
		switch( $type )
		{
		case 'dependant':
			$extremes = array();
			foreach( $this->columns as $line )
				$extremes[] = min( $line );

			$min = min( $extremes );
			break;
		case 'independant':
			$min = min( array_keys( $this->columns ) );
		}

		if( $min > 0 )
			$min = 0;

		return $min;
	}

	function _getMaxValue( $type ) // {{{2
	{
		switch( $type )
		{
		case 'dependant':
			$extremes = array();
			foreach( $this->columns as $line )
				$extremes[] = max( $line );

			return max( $extremes );
		case 'independant':
			return max( array_keys( $this->columns ) );
		}
	}

	function _getLabels( $type ) // {{{2
	{
		switch( $type )
		{
		case 'dependant':
			return array();
		case 'independant':
			return array_keys( $this->columns );
		}
	}
	
	function _handleData( $data ) // {{{2
	{
		$columns = array();
		for( $i = 0; isset( $data['y' . $i] ); ++$i )
			$columns[] = $data['y' . $i];

		$count = count( $columns );
		if( !isset( $data['color'] ) )
		{
			$data['color'] = array();
			for( $i = 0; $count > $i; ++$i )
				$data['color'][] = $this->_getColor();
		}

		if( !isset( $data['style'] ) )
			for( $i = 0; $count > $i; ++$i )
				$data['style'][] = 'FillStroke-' . $data['color'][$i];

		if( isset( $data['label'] ) )
			foreach( $data['label'] as $key => $label )
				$this->addLegend( $data['color'][$key], $label, 
					(isset($data['link']) && isset($data['link'][$key])) ? $data['link'][$key] : 0 );

		foreach( $columns as $key => $line )
		{
			$style = $data['style'][$key];
			$this->styleMap[$style] = "y$key";

			foreach( $line as $key => $value )
			{
				$x = $data['x'][$key];
				$this->columnMap[$x] = $key;

				if( !isset( $this->columns[$x] ) )
					$this->columns[$x] = array();

				if( !empty( $value ) )
					$this->columns[$x][$style] = $value;
				else
					$this->columns[$x][$style] = 0;
			}
		}

		return true;
	}
	
	function _drawGridContent( &$renderer ) // {{{2
	{
		$layout = $this->_layout();
		$zero = $this->dependant->getLocation( 0 );

		foreach( $this->columns as $label => $values )
		{
			$range = $this->independant->getRange( $label );
			switch( $this->independant->orientation )
			{
			case 'vertical':
				$ren = &new Fake_GRenderer( $renderer, 0, $range[0], 1, $range[1] );
				break;
			case 'horizontal':
				$ren = &new Fake_GRenderer( $renderer, $range[0], 0, $range[1], 1 );
				break;
			}
			$positions = $this->_drawColumn( $ren, $values, $zero );

			if( is_array( $positions ) )
			{
				$index = $this->columnMap[$label];
				foreach( $positions as $style => $positionData )
				{
					$series = $this->styleMap[$style];
					$this->_notify( $ren, $positionData, $series, $index );
				}
			}
		}
	}

	function _drawColumn( &$renderer, $values, $zero )
	{
		die( "Abstract Function Call" );
	}

	function _drawBox( &$renderer, $left, $top, $right, $bottom, $style )
	{
		$style = $renderer->getStyle( $style );
		switch( $this->independant->orientation )
		{
		case 'vertical':
			$renderer->drawRectangle( $bottom, $left, $top, $right, $style );
			break;
		case 'horizontal':
			$renderer->drawRectangle( $left, $top, $right, $bottom, $style );
			break;
		}
	}

	function _drawLegendBox( &$renderer, $color ) // {{{2
	{
		$renderer->drawRectangle( 0, 1, 1, 0, $renderer->getStyle( "FillStroke-$color" ) );
	}

	function _default() // {{{2
	{
		return array_merge( parent::_default(), array(
			'grid-independant-scale' => 'static',
			'grid-independant-major-guide' => 'Thin-LineStroke-Black'
		) );
	} 
} // }}}1

class BarStackGraphic extends BarBasedGraphic // {{{1
{
	function BarStackGraphic() // {{{2
	{
		BarBasedGraphic::BarBasedGraphic();
	}

	function _getMinValue( $type ) // {{{2
	{
		switch( $type )
		{
		case 'dependant':
			$extremes = array();
			foreach( $this->columns as $line )
				$extremes[] = array_sum( $line );

			$min = min( $extremes );
		case 'independant':
			$min = min( array_keys( $this->columns ) );
		}

		if( $min > 0 )
			$min = 0;

		return $min;
	}

	function _getMaxValue( $type ) // {{{2
	{
		switch( $type )
		{
		case 'dependant':
			$extremes = array();
			foreach( $this->columns as $line )
				$extremes[] = array_sum( $line );

			return max( $extremes );
		case 'independant':
			return max( array_keys( $this->columns ) );
		}
	}

	function _drawColumn( &$renderer, $values, $zero ) // {{{2
	{
		$layout = $this->_layout();
		$begin = ( 1 - $layout['stack-column-width'] ) / 2;
		$end = $begin + $layout['stack-column-width'];

		$positive = 0;
		$negative = 0;
		foreach( $values as $style=>$value )
		{
			if( $value == 0 ) continue;

			if( $value > 0 )
			{
				$bottom = $positive;
				$positive += $value;
				$top = $positive;
			}
			else
			{
				$top = $negative;
				$negative += $value;
				$bottom = $negative;
			}

			$this->_drawBox( $renderer, $begin, $this->dependant->getLocation( $top ), $end, $this->dependant->getLocation( $bottom ), $style );
		}
	}

	function _default() // {{{2
	{
		return array_merge( parent::_default(), array(
			'stack-column-width' => 0.6
		) );
	} 
} // }}}1

class MultibarGraphic extends BarBasedGraphic // {{{1
{
	function MultibarGraphic() // {{{2
	{
		BarBasedGraphic::BarBasedGraphic();
	}

	function _drawColumn( &$renderer, $values, $zero ) // {{{2
	{
		$layout = $this->_layout();
		$count = count( $values );
		$width = $layout['multi-columns-width'] / $count;
		$pad = ( 1 - $layout['multi-columns-width'] ) / 2;

		$positions = array();
		$i = 0;
		foreach( $values as $style=>$value )
		{
			$base = $pad + $width * $i++;
			
			if( $value == 0 ) continue;

			$bottom = $this->dependant->getLocation( $value );
			$this->_drawBox( $renderer, $base, $zero, $base + $width, $bottom, $style );
			$positions[$style] = array( 'left' => $base, 'top' => $zero, 'right' => $base + $width, 'bottom' => $bottom );
		}

		return $positions;
	}

	function _default() // {{{2
	{
		return array_merge( parent::_default(), array(
			'multi-columns-width' => 0.8
		) );
	} 
} // }}}1

?>
