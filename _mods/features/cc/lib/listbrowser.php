<?php
require_once "dbhandle.php";
require_once "dictionary.php";

class listbrowser
{
	var $tablename;
	var $startrow;
	var $filter = array();
	var $page = "";
	var $link = "cc.php?";
	var $context = "";
	var $nolimit;
	var $idvar;

	function listbrowser() {
		$filter = array();
	}


	function setNoLimit() {
		$this->nolimit = "isset";
	}

	function setTable($tablename) {
		$this->tablename = $tablename;
	}

	function setStart($startrow) {
		$this->startrow = $startrow;		
	}

	function setLink($link) {
		$this->link = $link;
	}

	function setIdvar($idvar) {
	    $this->idvar = $idvar;
	  }

	function setFilter($filter) {
		$this->filter = $filter;
	}

	function setPage($page) {
		$this->page = $page;
	}

	function getSelect($varname, $valcol,$valname) {
	    $dict = new dictionary();
	    $dbh = new dbhandle();
	    $sql = $dict->getSql($this->tablename);
	    
		if ($sql == '') {
		$actualtable = 	$dict->getTableName($this->tablename);
		    $result = $dbh->get($actualtable,$this->filter);
		  } else {
		    $result = $dbh->getFilterQuery($sql." and ",$this->filter);
		  }

	    $str = "<SELECT NAME='".$varname."'>";
	    while ($row = $result->fetchrow(DB_FETCHMODE_ASSOC)) {
		$str .= "<OPTION VALUE='".$row[$valcol]."'>";
		$str .= $row[$valname];
		$str .= "</OPTION>\n";
	      }
	    $str .= "</SELECT>";
	    return $str;
	  }

	function get() {
		$dict = new dictionary();
		
		$dbh = new dbhandle();
		if (isset($_REQUEST['start'])) {
			$dbh->start = $_REQUEST['start'];
		} else {
			$dbh->start = 0;
		}
		if ($this->nolimit == '')
			$dbh->limit = "5";
		$str = "";
		$actualtable = 	$dict->getTableName($this->tablename);

		$sql = $dict->getSql($this->tablename);
		if ($sql == '') {
	 		$result = $dbh->get($actualtable,$this->filter);
		   } else {
			$result = $dbh->getFilterQuery($sql." and ",$this->filter);
		  }

		if ($dict->getFilterFields($this->tablename) != '') {
			$str .= "<a href=\"javascript:showFilter('".$this->tablename."_filter','".$this->tablename."','".$this->page."');\">filter results</a></td></tr>";
		}

		$str .= "<TABLE>\n";

		$rowval = "even";
		$count = 0;
		while ($row = $result->fetchrow(DB_FETCHMODE_ASSOC)) {
			$count++;
			$dictrows = $dict->getFieldList($this->tablename);


			if ($count == 1) {
				$str .= "<TR>";
				foreach ($dictrows as $column=>$value) {
				   if ($dict->shouldDisplay($this->tablename,$column)) {
					$str .= "<TH>";
					$str .= $dict->getLabel($this->tablename,$column);
					$str .= "</TH>";
				   }
				}
				$str .= "</TR>\n";
			}


			$str .= "<TR>";
			foreach ($row as $column=>$value) {
			   if ($dict->shouldDisplay($this->tablename,$column)) {
				   $str .= "<TD";
				   if ($rowval == "even")
                 	           {
					$str .= " bgcolor='#EEEEEE' ";
				   } else {
				       $str .= " bgcolor='#FFFFFF' ";
				     }
				   $str .= $dict->getAlign($this->tablename,$column);
				   $str .= ">";

				   if ($dict->hasLink($this->tablename, $column)) {
					$str .= "<A HREF='";

					  if ($this->link == '') {
					      $tablelink =  $dict->getLink($this->tablename, $column);
					    } else {
					      $tablelink = $this->link;
					    }
					$str .= $tablelink;
					$str .= "&";
					if ($this->idvar == '')
					  $str .= "id";
					else
					  $str .= $this->idvar;
					$str .= "=";

					$str .= $row[id];
					$str .= "'>";
				  }
				   $str .= $dict->getListFormat($this->tablename,$column,$value);

				   if ($dict->hasLink($this->tablename, $column, $this->context)) {
					$str .= "</A>\n";
				   }
				   $str .= "</TD>\n";
				}
			}
			$str .= "</TR>\n";
			if ($rowval == "even") {
				$rowval = "odd";
			} else {
				$rowval = "even";
			}
		}

		if ($count == 0) {
			$str .= "<TR><TD>No More Results</TD></TR>";
		}
		$str .= "</TABLE>\n";
		
		$nextcount = $dbh->start + $dbh->limit;
		$nextlink = $this->link."&start=".($dbh->start+$dbh->limit);
		$str .= "<TABLE width='100'><TR>\n";

		if ($this->nolimit == '') {
			if ($dbh->start >= $dbh->limit) {
				if ($dbh->start > 0) {
					$toplink = $this->link;
					$str .= "<TD><A HREF='$toplink'>Top</TD>\n";		
				}
				$prevlink = $this->link."&start=".($dbh->start-$dbh->limit);			
				$str .= "<TD><A HREF='$prevlink'>Previous</TD>\n";		
			}
			if ($count > 0) {
				$str .= "<TD><A HREF='$nextlink'>Next</TD></TR></TABLE>\n";		
			}
		}
		return $str;
	}
}

?>
