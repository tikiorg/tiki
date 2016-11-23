<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Range.php 57971 2016-03-17 20:09:05Z jonnybradley $

class Search_Expr_Distance implements Search_Expr_Interface
{
	private $distance;
	private $lat;
	private $lon;
	private $field;
	private $weight;

	function __construct($distance, $lat, $lon, $field = 'geo_point', $weight = 1.0)
	{
		$this->distance = $distance;
		$this->lat = (float) $lat;
		$this->lon = (float) $lon;
		$this->field = $field;
		$this->weight = (float) $weight;
	}

	/*
	 * url for testing tiki-searchindex.php?filter~distance~distance=1000m&filter~distance~lat=51.4139354&filter~distance~lon=-0.2333563
	 */

	/**
	 * @return mixed
	 */
	public function getDistance()
	{
		return $this->distance;
	}

	/**
	 * @return float
	 */
	public function getLat(): float
	{
		return $this->lat;
	}

	/**
	 * @return float
	 */
	public function getLon(): float
	{
		return $this->lon;
	}

	function setType($type)
	{
	}

	function setField($field = 'geo_point')
	{
	}

	function setWeight($weight)
	{
		$this->weight = (float) $weight;
	}

	function getWeight()
	{
		return $this->weight;
	}

	function walk($callback)
	{
		return call_user_func($callback, $this, array());
	}

	function getValue(Search_Type_Factory_Interface $typeFactory)
	{
		$type = $this->type;
		return $typeFactory->$type($this->string);
	}

	function getField()
	{
		return $this->field;
	}

	function traverse($callback)
	{
		return call_user_func($callback, $callback, $this, array());
	}
}