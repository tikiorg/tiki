<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Interface.php 37365 2011-09-17 18:13:35Z lphuberdeau $

interface  Search_Formatter_DataSource_Interface
{
	function getInformation(Search_ResultSet $resultSet, array $fields);
}

