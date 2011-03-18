<?php

class Tracker_Field_Action implements Tracker_Field_Interface
{
	function getFieldData(array $requestData = array())
	{
		return array();
	}

	function renderInput($context = array())
	{
		return null;
	}

	function renderOutput($context = array())
	{
		return null;
	}
}
