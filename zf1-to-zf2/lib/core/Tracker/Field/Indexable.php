<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

interface Tracker_Field_Indexable extends Tracker_Field_Interface
{
	function getDocumentPart(Search_Type_Factory_Interface $typeFactory);

	function getProvidedFields();

	function getGlobalFields();

	function getBaseKey();
}

