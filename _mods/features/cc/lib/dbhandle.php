<?PHP

class dbhandle {

  var $limit = 0;
  var $start = 0;
  var $lastsql;

  function dbhandle() { }

  function get($table, $filtervalues=array()) {
		global $tikilib;
	  $sql = "select * from $table where";
	  
	  foreach($filtervalues as $k=>$v) {
	      $sql .= " $k='$v'";
	      $sql .= " and ";
	    }
	  $sql .= " 1=1 ";
	  if (($this->start != '') && ($this->start > 0)) {
			$locallimit = $this->limit;
			if (($this->limit == "") || ($this->limit == 0)) {
				$locallimit = "20";
			}
		 $sql .= " limit ".$this->start.",".$locallimit;
	  } else {
			if ($this->limit != "") {
				$sql .= "limit ".$this->limit;
			}
		}
	  $this->lastsql = $sql;
	  $result = $tikilib->query($sql);

	  return $result;

    }

  function getLastSql() {
		return $this->lastsql;
	}

  function getFilterQuery($query, $filtervalues=array()) {
		global $tikilib;
	  $sql = "$query";
	  foreach($filtervalues as $k=>$v) {
	      $sql .= " $k='$v'";
	      $sql .= " and ";
	    }
	  $sql .= " 1=1 ";
	  if (($this->start != '') && ($this->start > 0)) {
			$locallimit = $this->limit;
			if (($this->limit == "") || ($this->limit == 0)) {
				$locallimit = "20";
			}
			 $sql .= " limit ".$this->start.",".$locallimit;
	  } else {
			if ($this->limit != "") {
				$sql .= "limit ".$this->limit;
			}
		}
	  $result = $tikilib->query($sql);
	  $this->lastsql = $sql;
	  return $result;

    }

   function getOne($table, $filtervalues) {
      $result = $this->get($table, $filtervalues);
			if ($result) {
				return $result->fetchRow();   
			}   
    }
   
   function delete($table,$key,$keyval) {
			global $tikilib;
       $sth = "delete from $table where $key=?";
       $tikilib->query($sth, array($keyval));
     }

  function insert($table,$hashvalues) {
		global $tikilib;
		if (count($hashvalues)) {
			$vals = array_map("addslashes",$hashvalues);
			$query = "insert into $table (". implode(',',array_keys($vals)) .") values('". implode("','",array_values($vals)) ."')";
    	$tikilib->query($query);
      return $tikilib->getOne("select last_insert_id()");
		}
  }

  function update($table,$keycolumn, $hashvalues) {
		global $tikilib;
     $key_value = $hashvalues[$keycolumn];
     $strSql = "UPDATE $table SET ";
     foreach($hashvalues as $k => $v) {
      if ($k != $keycolumn) {
	    	$strSql .= "$k='" . mysql_escape_string($v) . "', ";
 			}
     }
     $strSql .= "$keycolumn = '" . $key_value."' ";
     $strSql = substr($strSql,0,strlen($strSql)-1);
     $strSql = $strSql . " WHERE $keycolumn = $key_value";
     $tikilib->query($strSql);
	  return $hashvalues[$keycolumn];
    }


  function ship($table,$keycolumn,$hashvalues) {
      if ($hashvalues[$keycolumn] == '') {
	  return $this->insert($table,$hashvalues);
	} else {
	  return $this->update($table,$keycolumn,$hashvalues);
	}
  }


}
?>
