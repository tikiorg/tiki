<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/* This library is LGPL
 * written by Louis-Philippe Huberdeau
 *
 * vim: fdm=marker tabstop=4 shiftwidth=4 noet:
 *
 * This file contains the GD graphic renderer.
 */
require_once('lib/graph-engine/core.php');

class GD_GRenderer extends GRenderer // {{{1
{
	var $gd;
	var $styles;
	var $colors;
	var $fonts;

	var $format;
	var $width;
	var $height;

	var $imageMap;

	function GD_GRenderer( $width = 0, $height = 0, $format = 'png' ) // {{{2
	{
		// Null size does not create a graphic.
		$this->styles = array();
		$this->colors = array();
		$this->fonts = array();

		if ( $width !== 0 && $height !== 0 ) {
			$this->gd = imagecreate($width, $height);
			$this->_getColor('white');
		}

		$this->format = $format;
		$this->width = $width;
		$this->height = $height;
	}
	
	function addLink( $target, $left, $top, $right, $bottom, $title = null ) // {{{2
	{
		$this->_convertPosition($left, $top);
		$this->_convertPosition($right, $bottom);
		$target = htmlspecialchars($target);
		$title = htmlspecialchars($title);

		$this->imageMap .= "<area shape=\"rect\" coords=\"$left,$top,$right,$bottom\" href=\"$target\" alt=\"$title\" title=\"$title\"/>\n";
	}

	function drawLine( $x1, $y1, $x2, $y2, $style ) // {{{2
	{
		$this->_convertPosition($x1, $y1);
		$this->_convertPosition($x2, $y2);
		imagesetthickness($this->gd, $style['line-width']);
		imageline($this->gd, $x1, $y1, $x2, $y2, $style['line']);
	}

	function drawRectangle( $left, $top, $right, $bottom, $style ) // {{{2
	{
		if ( $top > $bottom ) {
			// Filled rect has a problem when coordinates are inverted.
			$a = $top;
			$top = $bottom;
			$bottom = $a;
		}
		if ( $left > $right ) {
			// Filled rect has a problem when coordinates are inverted.
			$a = $left;
			$left = $right;
			$right = $a;
		}
		
		$this->_convertPosition($left, $top);
		$this->_convertPosition($right, $bottom);

		if ( isset($style['fill']) )
			imagefilledrectangle($this->gd, $left, $top, $right, $bottom, $style['fill']);

		imagesetthickness($this->gd, $style['line-width']);
		imagerectangle($this->gd, $left, $top, $right, $bottom, $style['line']);
	}

	function drawPie( $centerX, $centerY, $radius, $begin, $end, $style ) // {{{2
	{
		$radius = $radius * 2;
		if ( $begin != 0 || $end != 360 ) {
			$tmp = -$begin;
			$begin = -$end;
			$end = $tmp;
		}

		$this->_convertPosition($centerX, $centerY);
		$radius = $radius * min($this->width, $this->height);
		imagefilledarc($this->gd, $centerX, $centerY, $radius, $radius, $begin, $end, $style['fill'], IMG_ARC_PIE);

		imagesetthickness($this->gd, $style['line-width']);
		imagefilledarc($this->gd, $centerX, $centerY, $radius, $radius, $begin, $end, $style['line'], IMG_ARC_NOFILL | IMG_ARC_EDGED);
	}

	function drawText( $text, $left, $right, $height, $style ) // {{{2
	{
		$h = $height; // Creating duplicate (temp)
		$this->_convertPosition($left, $height);
		$this->_convertPosition($right, $h);
		switch( $style['align'] ) {
		case 'left':
			$this->_drawLeftText($text, $left, $height, $style);
    		break;
		case 'center':
			$this->_drawCenterText($text, $left, $right, $height, $style);
    		break;
		case 'right':
			$this->_drawRightText($text, $right, $height, $style);
    		break;
		}
	}

	function getTextWidth( $text, $style ) // {{{2
	{
		return imagefontwidth($style['font']) * strlen($text) / $this->width;
	}

	function getTextHeight( $style ) // {{{2
	{
		return imagefontheight($style['font']) / $this->height;
	}

	function getStyle( $name ) // {{{2
	{
		if ( isset($this->styles[$name]) )
			return $this->styles[$name];

		return $this->styles[$name] = $this->_findStyle($name);
	}

	function httpOutput( $filename ) // {{{2
	{
		switch( $this->format ) {
		case 'png':
			header("Content-type: image/png");
			imagepng($this->gd);
    		break;
		case 'jpg':
			header("Content-type: image/jpeg");
			imagejpeg($this->gd);
    		break;
		default:
			echo "Unknown Format: {$this->format}\n";
		}

		imagedestroy($this->gd);
	}

	function writeToStream( $stream ) // {{{2
	{
		ob_start();
		switch( $this->format ) {
		case 'png':
			imagepng($this->gd);
    		break;
		case 'jpg':
			imagejpeg($this->gd);
    		break;
		default:
			echo "Unknown Format: {$this->format}\n";
		}
		fwrite($stream, ob_get_contents());
		ob_end_clean();
		imagedestroy($this->gd);
	}

	function getMapContent() // {{{2
	{
		return $this->imageMap;
	}

	function _convertLength( $value, $type ) // {{{2
	{
		// $type is either 'width' or 'height'
		// $value is a 0-1 float
		return floor($value * $this->$type);
	}

	function _convertPosition( &$x, &$y ) // {{{2
	{
		// Parameters passed by ref!
		$x = $this->_convertLength($x, 'width');
		$y = $this->_convertLength($y, 'height');
	}

	function _findStyle( $name ) // {{{2
	{
		$parts = explode('-', $name);
		$style = array();

		switch( $parts[0] ) {
		case 'Thin':
			$style['line-width'] = 1;
			array_shift($parts);
    		break;
		case 'Bold':
			$style['line-width'] = 2;
			array_shift($parts);
    		break;
		case 'Bolder':
			$style['line-width'] = 3;
			array_shift($parts);
    		break;
		case 'Large':
			$style['font'] = 5;
			array_shift($parts);
    		break;
		case 'Small':
			$style['font'] = 2;
			array_shift($parts);
    		break;
		case 'Normal':
			array_shift($parts);
		default:
			if ( $parts[0] == 'Text' )
				$style['font'] = 4;
			else
				$style['line-width'] = 1;
    		break;
		}

		switch( $parts[0] ) {
		case 'LineStroke':
			$style['line'] = $this->_getColor($parts[1]);
    		break;
		case 'FillStroke':
			$style['fill'] = $this->_getColor($parts[1]);
			$style['line'] = $this->_getColor('Black');
    		break;
		case 'Text':
			if ( !isset($parts[1]) )
				$parts[1] = null;
			switch( $parts[1] ) {
			case 'Center':
				$style['align'] = 'center';
    			break;
			case 'Right':
				$style['align'] = 'right';
    			break;
			case 'Left':
			default:
				$style['align'] = 'left';
    			break;
			}
    		break;
		default:
			return GRenderer::getStyle($name);
		}

		return $style;
	}

	function _getColor( $name ) // {{{2
	{
		$name = strtolower($name);

		if ( isset($this->colors[$name]) )
			return $this->colors[$name];

		return $this->colors[$name] = $this->_findColor($name);
	}

	function _findColor( $name ) // {{{2
	{
		$color = $this->_getRawColor($name);
		return imagecolorallocate($this->gd, (int)$color['r'], (int)$color['g'], (int)$color['b']);
	}

	function _drawLeftText( $string, $left, $height, $style ) // {{{2
	{
		imagestring($this->gd, $style['font'], $left, $height, $string, $this->_getColor('Black'));
	}

	function _drawCenterText( $string, $left, $right, $height, $style ) // {{{2
	{
		$width = imagefontwidth($style['font']) * strlen($string);
		$x = ( $right - $left ) / 2 + $left - $width / 2;

		imagestring($this->gd, $style['font'], $x, $height, $string, $this->_getColor('Black'));
	}

	function _drawRightText( $string, $right, $height, $style ) // {{{2
	{
		$width = imagefontwidth($style['font']) * strlen($string);
		$x = $right - $width;

		imagestring($this->gd, $style['font'], $x, $height, $string, $this->_getColor('Black'));
	}
} // }}}1
