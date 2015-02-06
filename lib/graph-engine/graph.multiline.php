<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once 'lib/graph-engine/abstract.gridbased.php';

/**
 * MultilineGraphic
 *
 * @uses GridBasedGraphic
 */
class MultilineGraphic extends GridBasedGraphic
{
	var $lines;

	function MultilineGraphic()
	{
		GridBasedGraphic::GridBasedGraphic();
		$this->lines = array();
	}

	function getRequiredSeries()
	{
		return array(
			'label' => false,
			'color' => false,
			'style' => false,
			'x' => true,
			'y0' => true
		);
	}

	function _getMinValue( $type )
	{
		switch ( $type ) {
		case 'dependant':
			$extremes = array();
			foreach ( $this->lines as $line )
				$extremes[] = min($line);

			$min = min($extremes);
			break;

		case 'independant':
			$extremes = array();
			foreach ( $this->lines as $line )
				$extremes[] = min(array_keys($line));

			$min = min($extremes);
		}

		if ( $min > 0 )
			$min = 0;

		return $min;
	}

	function _getMaxValue( $type )
	{
		switch( $type )
		{
		case 'dependant':
			$extremes = array();
			foreach ( $this->lines as $line )
				$extremes[] = max($line);

			return max($extremes);

		case 'independant':
			$extremes = array();
			foreach ( $this->lines as $line )
				$extremes[] = max(array_keys($line));

			return max($extremes);
		}
	}

	function _getLabels( $type )
	{
		return array();
	}

	function _handleData( $data )
	{
		$lines = array();
		for ($i = 0; isset($data['y' . $i]); ++$i)
			$lines[] = $data['y' . $i];

		$count = count($lines);
		if ( !isset($data['color']) ) {
			$data['color'] = array();
			for ( $i = 0; $count > $i; ++$i )
				$data['color'][] = $this->_getColor();
		}

		if ( !isset( $data['style'] ) )
			for ( $i = 0; $count > $i; ++$i )
				$data['style'][] = 'Bold-LineStroke-' . $data['color'][$i];

		if ( isset( $data['label'] ) )
			foreach ( $data['label'] as $key => $label )
				$this->addLegend($data['color'][$key], $label);

		foreach ( $lines as $key => $line ) {
			$style = $data['style'][$key];
			$this->lines[$style] = array();

			foreach ( $line as $key => $value ) {
				$x = $data['x'][$key];
				if ( !empty( $value ) || $value === 0 )
					$this->lines[$style][$x] = $value;
			}

			ksort($this->lines[$style]);
		}

		return true;
	}

	function _drawGridContent( &$renderer )
	{
		$layout = $this->_layout();

		foreach ($this->lines as $style => $line) {
			$previous = null;
			$style = $renderer->getStyle($style);

			foreach ( $line as $x => $y )
				if ($layout['grid-independant-location'] == 'horizontal') {
					$xPos = $this->independant->getLocation($x);
					$yPos = $this->dependant->getLocation($y);

					if ( !is_null($previous) )
						$renderer->drawLine($previous['x'], $previous['y'], $xPos, $yPos, $style);

					$previous = array('x' => $xPos, 'y' => $yPos);
				} else {
					$xPos = $this->dependant->getLocation($y);
					$yPos = $this->independant->getLocation($x);

					if ( !is_null($previous) )
						$renderer->drawLine($previous['x'], $previous['y'], $xPos, $yPos, $style);

					$previous = array('x' => $xPos, 'y' => $yPos);
				}
		}
	}

	function _drawLegendBox( &$renderer, $color )
	{
		$renderer->drawLine(0, 1, 1, 0, $renderer->getStyle("Bold-LineStroke-$color"));
	}

	function _default()
	{
		return array_merge(parent::_default(), array());
	}
}
