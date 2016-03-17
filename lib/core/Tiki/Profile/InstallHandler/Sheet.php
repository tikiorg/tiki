<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Profile_InstallHandler_Sheet extends Tiki_Profile_InstallHandler
{
	function getData()
	{
		if ( $this->data )
			return $this->data;
		$data = $this->obj->getData();
		$this->replaceReferences($data);

		return $this->data = $data;
	}
	
	function canInstall()
	{
		$data = $this->getData();
		
		if (isset($data)) return true;
		else return false;
	}
	
	function _install()
	{
		if ($this->canInstall()) {
			global $user;
			$sheetlib = TikiLib::lib('sheet');
			require_once ('lib/sheet/grid.php');
			
			//here we convert the array to that of what is acceptable to the sheet lib
			$parentSheetId = null;
			$sheets = array();
			$nbsheets = count($this->data);	
			for ($sheetI = 0; $sheetI < $nbsheets; $sheetI++) {
				$title = $this->data[$sheetI]['title'];
				$title = ($title ? $title : "Untitled - From Profile Import");
				$nbdatasheetI = count($this->data[$sheetI]);	
				for ($r = 0; $r < $nbdatasheetI; $r++) {
					$nbdatasheetIr = count($this->data[$sheetI][$r]);
					for ($c = 0; $c < $nbdatasheetIr; $c++) {
						$value = "";
						$formula = "";
						$rawValue = $this->data[$sheetI][$r][$c];
						 
						if (substr($rawValue, 0, 1) == "=") {
							$formula = $rawValue;
						} else {
							$value = $rawValue;
						}
						
						$ri = 'r'.$r;
						$ci = 'c'.$c;
						
						$sheets[$sheetI]->data->$ri->$ci->formula = $formula;
						$sheets[$sheetI]->data->$ri->$ci->value = $value;
						
						$sheets[$sheetI]->data->$ri->$ci->width = 1;
						$sheets[$sheetI]->data->$ri->$ci->height = 1;
					}
				}
				
				$sheets[$sheetI]->metadata->rows = count($this->data[$sheetI]);
				$sheets[$sheetI]->metadata->columns = count($this->data[$sheetI][0]);
				$id = $sheetlib->replace_sheet(0, $title, "", $user, $parentSheetId);
				$parentSheetId = ($parentSheetId ? $parentSheetId : $id);
				
				$grid = new TikiSheet($id);
				$handler = new TikiSheetHTMLTableHandler($sheets[$sheetI]);
				$res = $grid->import($handler);
				$handler = new TikiSheetDatabaseHandler($id);
				$grid->export($handler);
			}
			
			return $parentSheetId;
		}
	}
}
