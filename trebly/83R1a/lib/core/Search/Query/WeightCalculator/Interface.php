<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Interface.php 33690 2011-03-28 17:39:07Z jonnybradley $

interface Search_Query_WeightCalculator_Interface
{
	function calculate(Search_Expr_Interface $expr);
}

