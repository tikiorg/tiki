<?php

interface Search_Expr_Interface
{
	function setField($field = 'global');
	function setType($type);
	function walk($callback);
}

