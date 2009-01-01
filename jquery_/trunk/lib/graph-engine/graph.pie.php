<?php
require_once 'graph-engine/core.php';

class PieChartGraphic extends Graphic // {{{1
{
	var $pie_data;

	function PieChartGraphic() // {{{2
	{
		Graphic::Graphic();
		$this->pie_data = array();
	}
	
	function getRequiredSeries() // {{{2
	{
		return array(
			'label' => false,
			'value' => true,
			'color' => false,
			'style' => false
		);
	}
	
	function _handleData( $data ) // {{{2
	{
		$elements = count( $data['value'] );
		
		if( !isset( $data['color'] ) )
		{
			$data['color'] = array();
			for( $i = 0; $elements > $i; ++$i )
				$data['color'][] = $this->_getColor();
		}

		if( !isset( $data['style'] ) )
			for( $i = 0; $elements > $i; ++$i )
				$data['style'][] = 'FillStroke-' . $data['color'][$i];

		if( isset( $data['label'] ) )
			foreach( $data['label'] as $key => $label )
				$this->addLegend( $data['color'][$key], $label );

		$total = array_sum( $data['value'] );
		foreach( $data['value'] as $key => $value )
			if( is_numeric( $value ) )
				$this->pie_data[] = array( $data['style'][$key], $value / $total * 360 );

		return true;
	}
	
	function _drawContent( &$renderer ) // {{{2
	{
		$layout = $this->_layout();
		$centerX = $layout['pie-center-x'];
		$centerY = $layout['pie-center-y'];
		$radius = $layout['pie-radius'];
		
		$base = 0;

		foreach( $this->pie_data as $info )
		{
			list( $style, $degree ) = $info;
			$renderer->drawPie(
				$centerX,
				$centerY, 
				$radius, 
				$base, 
				$base + $degree,
				$renderer->getStyle( $style ) );

			$base += $degree;
		}
	}

	function _drawLegendBox( &$renderer, $color ) // {{{2
	{
		$renderer->drawRectangle( 0, 0, 1, 1, $renderer->getStyle( "FillStroke-$color" ) );
	}

	function _default() // {{{2
	{
		return array_merge( parent::_default(), array(
			'pie-center-x' => 0.5,
			'pie-center-y' => 0.5,
			'pie-radius' => 0.4
		) );
	}
} // }}}1
?>
