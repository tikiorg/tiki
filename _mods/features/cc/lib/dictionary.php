<?PHP
class dictionary
{
  var $dictdata;
  
  function dictionary()
    {
      $this->dictdata['cc_cc'] = array(
	  "fields" => array(		  
		"id" => array(
					"type"=> "varchar(10)",
					"comment"=> 'primary key',
					 "label"=>"cc id",
 					 "link"=>"yes",
					 "linkurl"=>"cc.php?page=registeruserforcc"
					 ),

		"cc_name" => array(
					 "type"=> "varchar(20)",
					 "comment"=>"Name of cc",
					 "label"=>"cc name"
					 ),
					 
		"cc_description" => array(
					 "type"=> "text",
					 "comment"=>"description of cc",
		          "label"=>"description"
		          ),
		          
		"owner_id" => array(
					 "type"=>"varchar(10)",
					 "comment"=>"Id of account that set up cc",
					 "label"=>"owner"	
					 ),
//					    hide=>"yes",
	
		"requires_approval" => array(
					 "type"=>"char",
					 "comment"=>"Indicates approval",
					 "label"=>"approval?"
					 )
		        ),
	  "comment"=> "Table to describe cc",
	  );


      $this->dictdata['registeredcc'] = array(

      "sql"=>"select cc.id as cc_id,cc.cc_name from cc_cc cc, cc_ledger l where l.cc_id = cc.id "
				      );

      $this->dictdata['cc_ledger'] = array(
	"fields"=>array(
		"seq" => array(
			      "type"=>"int",
			      "comment"=>"primary key"
			      ),
		"acct_id" => array(
			      "type"=>"varchar(10)",
			      "comment"=>"Account id for ledger entry"
				   ),
		"cc_id"=> array(
			      "type"=>"varchar(10)",
			      "comment"=>"cc id for ledger entry"
				      ),
		"balance" => array(
			      "type"=>"int",
			      "align"=>"right",
			      "comment"=>"balance for acct,cc pair"
				   ),
		"tr_total"=>array(
			      "type"=>"int",
			      "align"=>"right",
			      "comment"=>"Total Number of transactions"
				      ),
		"last_tr_date"=>array(
			      "type"=>"datetime",
			      "comment"=>"Date of Last transaction"
					 ),
			),
	"comment"=>"Ledger Information"
       );					

      $this->dictdata['history'] = array(
	 "sql" => "select t.tr_date, acc.id,t.item,t.type,t.amount,t.balance from cc_transaction t, cc_cc cc,users_users acc where acc.userId=t.other_id and t.cc_id = cc.id 
",					 
	 "fields" => array(
			   "tr_date" => array(
						 "type"=>"datetime",
						 "label"=>"date"
						 ),
			   "id" => array(
						   "label"=>" with",
					     "align"=>"centre"
						   ),
			   "item" => array(
						  "type"=>"varchar(20)",
					     "align"=>"left",
						  "label"=>"for"
						 ),
			   "type" => array(
					   "type"=>"varchar(20)",
	  			      "align"=>"centre",
					   "label"=>" i/o "
					   ),
			   "amount" => array(
					     "align"=>"right",
					     "type"=>"int",
					     "label"=> "amount"
					     ),
			   "balance" => array( 
					      "align"=>"right",
					      "type"=>"int",
						"label"=>"balance"
						)
			   )
	 );
			   
      $this->dictdata['tr_summary'] = array(
      "sql" => "select cc.cc_name,cc.id,l.balance,l.tr_total,l.tr_count,l.last_tr_date,l.cc_id as id from cc_ledger l, cc_cc cc where cc.id=l.cc_id",

      "fields" => array(
      
			"cc_name" => array(
						 "type"=> "varchar(20)",
						 "label"=>"cc name"
						 ),

			"id" => array(
						 "type"=> "varchar(10)",
						 "label"=>"cc id"
						 ),
						 
			"balance"=> array(
					    "label"=>"balance",
					    "align"=>"right",
					    "type"=>"int"
					    ),
					    
			"tr_total"=>array(
					    "label"=>"total",
		      		 "align"=>"right",
					    "type"=>"int"
					    ),
					    
			"tr_count"=>array(
					    "label"=>"count",
					    "align"=>"right",
					    "type"=>"int"
					    ),

			"last_tr_date"=>array(
					    "label"=>"     ",
					    "link"=>"yes",
					    "linkurl"=>"cc.php?page=history",
					    "linktext"=>"more"
					    )

			)
	      );


    }



  function getFieldList($tablename)
    {
      $fields = $this->dictdata[$tablename]['fields'];
      
      return $fields;
    }
  
  function getTableList()
    {
      $hash = array();
      foreach ($this->dictdata as $k => $v)
	{	
	  $hash[$k] = $k;
	}
      return $hash;
    }
  
  function shouldDisplay($tablename,$column)
    {
      if ($this->dictdata[$tablename]['fields'][$column] == '')
	{
	  return (1==0);
	}
      return ($this->dictdata[$tablename]['fields'][$column]['hide'] != 'yes');
    }
  
  function canEdit($tablename,$column)
    {
      return ($this->dictdata[$tablename]['fields'][$column]['edit'] == 'yes');
    }

  function hasLink($tablename,$column)
    {
      return ($this->dictdata[$tablename]['fields'][$column]['link'] == 'yes');
    }

  function getLinkText($tablename,$column)
    {
      return $this->dictdata[$tablename]['fields'][$column]['linktext'];
    }

  function getLink($tablename, $column)
    {
      return $this->dictdata[$tablename]['fields'][$column]['linkurl'];
    }
  
  function getLabel($tablename, $column)
    {
      $label = $this->dictdata[$tablename]['fields'][$column]['label'];
      if ($label == '')
	{
	  return $column;
	}
      else
	{
	  return $label;
	}
    }
  
  function getTableName($tablename)
    {
      if (isset($this->dictdata[$tablename]['table']) and $this->dictdata[$tablename]['table'] != '')
	{
	  return $this->dictdata[$tablename]['table'];
	}
      return $tablename;
    }
  
  function getInputHtml($tablename,$column,$value)
    {
      
      $format = $this->getFormat($tablename,$column);
      if ($format == "boolean")
	{
	  $str = "<SELECT name='$tablename"."__"."$column'>\n";
	  $str .= "<OPTION value='Y'";
	  if ($value == "Y")
	    {
	      $str .= " selected";
	    }
	  $str .= ">";
	  $str .= "Yes";
	  $str .= "</OPTION>";
	  $str .= "<OPTION value='N'";
	  if ($value == "N")
	    {
	      $str .= " selected";
	    }
	  $str .= ">No</OPTION>";
	  $str .= "</SELECT>";
	  return $str;
	}
      
      if ($format == "domain")
	{
	  $domain = $this->getColumnDomain($tablename,$column);
	  return $this->getDomain($tablename."__".$column,$domain,$value);
	}
      return  "<input type='text' name='$tablename"."__"."$column' value='$value'  size='30'>";
    }	
  
  function getListFormat($tablename,$column,$value)
    {
      if ($this->hasLink($tablename,$column) == 'yes')
	{
	  $text = $this->getLinkText($tablename,$column);
	  if ($text != '')
	    {
	      return $text;
	    }
	}

      $format = $this->getFormat($tablename,$column);


      if ($format == "boolean")
	{
	  if ($value == "Y")
	    return "Yes";
	  if ($value == "N")
	    return "No";
	  return "No";
	}
      if ($format == "dollar")
	{
	  return "\$".$value.".00";
	}
      if (($format == 'mysqldate') ||
	  ($format == 'mysql_date'))
	{
	  return $this->fixDate($value);
	}
      if ($format == 'domain')
	{
	  $domain = $this->getColumnDomain($tablename,$column);
	  return $this->domains[$domain][$value];
	}
      if ($format == 'card')
	{
	  return substr($value,strlen($value)-4,4);
	}
      return $value;
    }
  
  function getFormat($tablename,$column)
    {
      if ($this->dictdata[$tablename]['fields'][$column] == '')
	{
	  return "";
	}
      
      return $this->dictdata[$tablename]['fields'][$column][format];
    }
  
  function getAlign($tablename,$column)
    {
      if ($this->dictdata[$tablename]['fields'][$column] == '')			
	return "";
      if ($this->dictdata[$tablename]['fields'][$column][align] == '')						
	return "";
      return " align='".$this->dictdata[$tablename]['fields'][$column][align]."' ";
    }
  
  
  // function to format mySQL DATE values
  function fixDate($val) 
    {
      //split it up into components
      $arr = split(" ", $val);
      
      $datearr = split("-", $arr[0]);
      // create a timestamp with mktime(), format it with date()
      return date("m/d/Y", mktime(0, 0, 0, $datearr[1], $datearr[2], $datearr[0]));
      //	return $datearr[2]."/".$datearr[1]."/".$datearr[0];
    }
  
  function getSql($tablename)
    {
      return $this->dictdata[$tablename]['sql'];
    }
  
  function getColumnDomain($tablename,$column)
    {
      return $this->dictdata[$tablename]['fields'][$column]['domain'];
    }
  
  function getDomain($column,$domain,$value)
    {
      return $this->getSelect($column,
			      $value,
			      $this->domains[$domain]);
    }
  
  function getSelect($column, $value,$data)
    {
      $str= "<SELECT name='$column'>\n";
      
      foreach ($data as $k=>$v)
	{
	  $str .= "<OPTION value='$k'";
	  if ($value == $k)
	    $str .= " SELECTED ";
	  $str .= ">$v</OPTION>";	
	}
      $str .= "</SELECT>";
      return $str;
      
    }
  
  function getTemplate($tablename,$templatename)
    {
      if ($this->dictdata[$tablename]['template'] == '')
	return '';
      return $this->dictdata[$tablename]['template'][$templatename];
    }	
  
  function getFilterFields($tablename)
    {
      
			if (isset($this->dictdata[$tablename]['filters'])) {
      return $this->dictdata[$tablename]['filters'];
			} else {
				return array();
			}
    }
  
  function getFilterSql($tablename,$filtername)
    {
      $hash = $this->getFilterFields($tablename);
      if ( $hash[$filtername]['sql'] == '')
	return $filtername;
      else
	return $hash[$filtername]['sql'];
    }
 
}
