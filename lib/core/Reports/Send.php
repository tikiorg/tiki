<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Send e-mail reports to users with changes in Tiki
 * in a given period of time.
 * 
 * @package Tiki
 * @subpackage Reports
 */
class Reports_Send
{
	protected $db;
	
	/**
	 * @param TikiDb $db
	 * @return null
	 */
	public function __construct(TikiDb $db)
	{
		$this->db = $db;
	}
	
	
}