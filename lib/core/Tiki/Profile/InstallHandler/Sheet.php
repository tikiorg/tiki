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
		if ($this->data) {
			return $this->data;
		}
		$data = $this->obj->getData();
		$this->replaceReferences($data);

		return $this->data = $data;
	}

	function canInstall()
	{
		$data = $this->getData();

		if (isset($data)) {
			return true;
		} else {
			return false;
		}
	}

	function _install()
	{
		if ($this->canInstall()) {
			global $user;
			$sheetlib = TikiLib::lib('sheet');
			require_once('lib/sheet/grid.php');

			//here we convert the array to that of what is acceptable to the sheet lib
			$parentSheetId = 0;
			$sheets = [];
			$nbsheets = count($this->data);
			for ($sheetI = 0; $sheetI < $nbsheets; $sheetI++) {
				$sheets[$sheetI] = new stdClass();
				$sheets[$sheetI]->rows = [];
				$sheets[$sheetI]->metadata = new stdClass();
				$sheets[$sheetI]->metadata->widths = [];

				$title = (isset($this->data[$sheetI]['title']) && $this->data[$sheetI]['title']) ? $this->data[$sheetI]['title'] : tra("Untitled - From Profile Import");

				$rows = [];
				if (isset($this->data[$sheetI]['rows'])) {
					$rows = $this->data[$sheetI]['rows'];
				} elseif (isset($this->data[$sheetI][0])) {
					$rows = $this->data[$sheetI];
					if (isset($rows['title'])) {
						unset($rows['title']);
					}
				}
				$nbdatasheetI = count($rows);
				for ($r = 0; $r < $nbdatasheetI; $r++) {
					$nbdatasheetIr = count($rows[$r]);
					$sheets[$sheetI]->rows[$r]->columns = [];

					for ($c = 0; $c < $nbdatasheetIr; $c++) {
						$sheets[$sheetI]->rows[$r]->columns[$c] = new stdClass();

						$value = "";
						$formula = "";
						$rawValue = $rows[$r][$c];

						if (substr($rawValue, 0, 1) == "=") {
							$formula = $rawValue;
						} else {
							$value = $rawValue;
						}

						$sheets[$sheetI]->rows[$r]->columns[$c]->formula = $formula;
						$sheets[$sheetI]->rows[$r]->columns[$c]->value = $value;

						$sheets[$sheetI]->rows[$r]->columns[$c]->width = 1;
						$sheets[$sheetI]->rows[$r]->columns[$c]->height = 1;
					}
				}

				$sheets[$sheetI]->metadata->widths[] = $nbdatasheetIr;
				$sheets[$sheetI]->metadata->rows = $nbdatasheetI;
				$sheets[$sheetI]->metadata->columns = count($rows[0]);

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
