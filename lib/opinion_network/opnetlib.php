<?php

//Tikiwiki Opinion-Network library by Giotto (m--zsolt@freemail.hu)

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
	if ( strpos( $_SERVER["SCRIPT_NAME"], basename( __FILE__ ) ) !== false ) {
		header( "location: index.php" );
	}

class OpnetLib extends TikiLib {
	function OpnetLib( $db ) {
		if ( !$db ) {
			die ( "Invalid db object passed to OpnetLib constructor" );
		}
		$this->db = $db;
	}
	
	function getUserList() {
	//setting up parameters
		$sort_mode = 'login_asc';
		$offset = 0;
		$maxRecords = -1;
		$find = '';
	//getting the list
		$userlist = $this->list_users($offset,$maxRecords,$sort_mode,$find);
	
		return $userlist;
	}
	
	function getFriendList($username) {
	//setting up parameters
		$sort_mode = 'login_asc';
		$offset = 0;
		$maxRecords = -1;
		$find = '';
	//getting the list
		$friendList = $this->list_user_friends($username,$offset,$maxRecords,$sort_mode,$find);
		return $friendList;
	}
	
// puts out a javascript slider
	function putSlider($name, $inputname, $value) {
	//some hard coded html to be echoed to put out a slider component
		$output = "<div class='slider' id='$name' tabIndex='1'> <input class='slider-input' id='$inputname' />";
		$output .= "<input id='$name"."value"."' name='$name"."value"."' onchange='s.setValue(parseInt(this.value))' /> </div>";		
		$output .= "\n<script type='text/javascript'> \nvar $name = new Slider(document.getElementById('$name'), document.getElementById('$inputname')); \n ";
		$output .= "$name.setValue($value); \n window.onresize = function () { $name.recalculate(); }; \n";
		$output .= "$name.onchange = function () { document.getElementById('$name"."value"."').value = $name.getValue(); }; \n";
	//we have to do this manually too, coz it will set the value field to the starting value of the slider, or else it would be set only if we move the slider (means unchanged slider->0 value)
		$output .= "document.getElementById('$name"."value"."').value = $name.getValue();</script>\n";

		return $output;
	}
	
	
// returns what kind of question-forms are available
/* The variables determines the order of the records.
  $orderby: the field name in the table
  $ascdesc: ascending or descending order ('asc' or 'desc')
  
  The names of fields in the table 'tiki_opnet_formtype' :
  
*/
	function getAvailableFormTypes($orderby,$ascdesc) {

		$query = "SELECT * FROM `tiki_opnet_formtype` ORDER BY ? ? ";
		$bindvars = array ( $orderby, $ascdesc );
		$result = $this->query( $query, $bindvars );
		
		$ret = array();
		
		while ( $res = $result->fetchRow() ) {
			$ret[] = $res;
		}
		
		return $ret;
	}
	
// returns the questions of a question-form from the database
// if you want correct question-answer pairs, then it's the getFilledForm_xxxxx functions are for you.
/* The variables determines the order of the records.
  $orderby: the field name in the table
  $ascdesc: ascending or descending order ('asc' or 'desc')
  
  The names of fields in the table 'tiki_opnet_question' :
  
*/
	function getQuestions($whichform,$orderby,$ascdesc) {

		$query = "SELECT * FROM `tiki_opnet_question` WHERE `formtype`=? ORDER BY ? ? ";
		$bindvars = array( $whichform, $orderby, $ascdesc );
		$result = $this->query( $query, $bindvars );
		
		$ret = array();
		
		while ( $res = $result->fetchRow() ) {
			$ret[] = $res;
		}
		
		return $ret;
	}
	
// returns the answers of a filled question-form from the database
// if you want correct question-answer pairs, then it's the getFilledForm_xxxxx functions are for you.
/* The variables determines the order of the records.
  $orderby: the field name in the table
  $ascdesc: ascending or descending order ('asc' or 'desc')
  
  The names of fields in the table 'tiki_opnet_answer' :
  
*/
	function getAnswers($whichform,$orderby,$ascdesc) {
		$query = "SELECT * FROM `tiki_opnet_answer` WHERE `filledform_id`=? ORDER BY ? ? ";
		$bindvars = array( $whichform, $orderby, $ascdesc );
		$result = $this->query( $query, $bindvars );
		
		$ret = array();
		
		while ( $res = $result->fetchRow() ) {
			$ret[] = $res;
		}
		
		return $ret;
		
	}
	
// returns a filled-form (question - answer pairs) by form ID
function getFilledForm_byId($formID) {
		
		$query = "SELECT `formtype` FROM `tiki_opnet_filledform` WHERE `id`=?";
		$bindvars = array( $formID );
		$formtype = $this->getOne( $query, $bindvars );
		
		if ( !$formtype ) {
			return false;
		};
		
		$query = "SELECT `question`,`value`  FROM `tiki_opnet_question`,`tiki_opnet_answer` ".
				"WHERE `tiki_opnet_question`.`formtype`=? AND `tiki_opnet_answer`.`question_id`=`tiki_opnet_question`.`id` AND ".
				"`tiki_opnet_answer`.`filledform_id`=? ORDER BY `tiki_opnet_question`.`id` ASC";
		$bindvars = array( $formtype, $formID );
		
		$result = $this->query( $query, $bindvars );
		
		$ret=array();
		
		while ( $res = $result->fetchRow() ) {
			$ret[] = $res;
		}
		
		return $ret;
		
	}
	
// returns the ID of a filled-form	
	function getFilledFormId($who,$about_who,$which_formtype) {
		$query = "SELECT `id` FROM `tiki_opnet_filledform` WHERE `who`=? AND `about_who`=? AND `formtype`=?";
		$bindvars = array( $who, $about_who, $which_formtype );
		$ID = $this->getOne( $query, $bindvars );
		
		return $ID;
	}
	
// returns a filled-form (question - answer pairs) by the value of the following fields: the user who have filled the form, 
// the user who is object of the form, and the type of the form (there can be more then one form types)
	function getFilledForm_byUserNamesAndFormType($who,$about_who,$which_formtype) {
		
		$ID = $this->getFilledFormId( $who, $about_who, $which_formtype );
		
		if ( !$ID ) {
			return false;
		};
		
		$query = "SELECT `question`,`value`  FROM `tiki_opnet_question`,`tiki_opnet_answer` ".
				"WHERE `tiki_opnet_question`.`formtype`=? AND `tiki_opnet_answer`.`question_id`=`tiki_opnet_question`.`id` AND ".
				"`tiki_opnet_answer`.`filledform_id`=? ORDER BY `tiki_opnet_question`.`id` ASC";
		$bindvars = array( $which_formtype, $ID );
		$result = $this->query( $query, $bindvars );
				
		$ret = array();		
				
		while ( $res = $result->fetchRow() ) {

			$ret[] = $res;
		}
		
		return $ret;
		
	}

// adds a new question form with the given name to the database 
	function addFormType($formtype) {

		if ( $formtype == "" ) {
			return false;
		}
		
		$now = date( "U" );
		
		$query = "INSERT INTO `tiki_opnet_formtype` (`name`,`timestamp`) VALUES (?,?)";
		$bindvars = array( $formtype, (int)$now );
		$result = $this->query( $query,$bindvars );
		
		return true;
	}
	
// adds a new question to the database 	
	function addQuestion($question_str, $whichform) {
	
		if ( $question_str == "" ) {
			return false;
		}
		
		$query = "INSERT INTO `tiki_opnet_question` (`formtype`,`question`) VALUES (?,?)";
		$bindvars = array( $whichform, $question_str );
		$result = $this->query( $query, $bindvars );
		
		return true;
	}
	
// deletes a form and all of it's questions from the database, also deletes all of the answers for this form
// use with caution!!
	function deleteForm($formID) {
		
		if ( $formID == "" ) {
			return false;
		}
		
	// first we want to delete the answers
	// so first we get the id's of the questions which will be deleted
		$query = "SELECT `id` FROM `tiki_opnet_question` WHERE `formtype`=?";
		$bindvars = array( $formID );				
		$result = $this->query( $query, $bindvars );
		
		$ret = array();		
				
		while ( $res = $result->fetchRow() ) {
			$ret[] = $res;
		}

	// then we use this lame method to delete the answers, coz they say we can't use nested querys in adodb... sigh
		for ( $i = 0; each( $ret ); $i++ ) {
			$query = "DELETE FROM `tiki_opnet_answer` WHERE `question_id`=? ";
			$bindvars = array ( $ret[$i]["id"] );
			$result = $this->query( $query, $bindvars );
		}

	// then we delete the questions
		$query = "DELETE FROM `tiki_opnet_question` WHERE `formtype`=? ";
		$bindvars = array ( $formID );
		$result = $this->query( $query, $bindvars );
		
	// then we delete the answers
		$query = "DELETE FROM `tiki_opnet_answer` WHERE `filledform_id`=? ";
		$result = $this->query( $query, $bindvars );
		
	// then we delete the filled forms
		$query = "DELETE FROM `tiki_opnet_filledform` WHERE `formtype`=? ";
		$result = $this->query( $query, $bindvars );		
		
	// then we delete the form type istelf
		$query = "DELETE FROM `tiki_opnet_formtype` WHERE `id`=? ";
		$result = $this->query( $query, $bindvars );

		return true;
	}
	
// simply returns the name of a form from the database	
	function getFormName($formID) {
		if ( $formID == "" ) {
			return false;
		}
		
		$query = "SELECT `name` FROM `tiki_opnet_formtype` WHERE `id`=? ";
		$bindvars = array( $formID );
		$result = $this->getOne( $query, $bindvars );
		
		return $result;
	}
	
// store the filled form, and the answers
	function storeAnswers( $user, $about_who, $whichform, $res ) {
	
		$now = date( "U" );
	// first we have to check that is there an existing filled form with the same attributes?
		$ID = $this->getFilledFormId( $user, $about_who, $whichform );

		if ( !$ID ) {
		// it seems that no filled form exist with these attributes, so we create it
			
			$query = "INSERT INTO `tiki_opnet_filledform` (`who`,`about_who`,`formtype`,`timestamp`) VALUES (?,?,?,?) ";
			$bindvars = array ( $user, $about_who, $whichform, (int)$now );
			$result = $this->query( $query, $bindvars );
		// we get the ID of our new filled-form
			$newID = $this->getFilledFormId( $user, $about_who, $whichform );
			if ( !$newID ) {
				return false;
			};
		// and we just have to store our answers	
		// we need a question id array
			$question_id = $this->getQuestions( $whichform, 'id' , 'asc' );
		// we store the answers
			for ( $i = 0; $i<count( $res ); $i++ ) {
				$query = "INSERT INTO `tiki_opnet_answer` (`question_id`,`filledform_id`,`value`) VALUES (?,?,?) ";
				$bindvars = array ( $question_id[$i]["id"], $newID, $res[$i] );
				$result = $this->query( $query, $bindvars );
			}
		} else {
		// it seems we had an existing filled form, so we have to update the answers
			$query = "UPDATE `tiki_opnet_filledform` SET `timestamp`=? WHERE `id`=? ";
			$bindvars = array ( (int)$now, $ID );
			$result = $this->query( $query, $bindvars );
		// we need a question id array
			$question_id = $this->getQuestions( $whichform, 'id' , 'asc' );
		// we store the answers
			for ( $i = 0; $i<count( $res ); $i++ ) {
				$query = "UPDATE `tiki_opnet_answer` SET `value`=? WHERE `question_id`=? AND `filledform_id`=?";
				$bindvars = array ( $res[$i], $question_id[$i]["id"], $ID );
				$result = $this->query( $query, $bindvars );
			}
		}
		
	
		
		return true;
	}
	
	function getObjectOfTheFormUsers( $user, $whichform) {
		$query = "SELECT `about_who` FROM `tiki_opnet_filledform` WHERE `who`=? AND `formtype`=? ORDER BY `about_who` ASC";
		$bindvars = array( $user, $whichform );
		$result = $this->query( $query, $bindvars );
		
		$ret = array();		
				
		while ( $res = $result->fetchRow() ) {

			$ret[] = $res;
		}
		return $ret;
	}
	
	function getFilledFormAverages( $about_who, $whichform) {
		
		// first we have to find the filled-forms
		$query = "SELECT `id` FROM `tiki_opnet_filledform` WHERE `about_who`=? AND `formtype`=?";
		$bindvars = array( $about_who, $whichform );
		$result = $this->query( $query, $bindvars );
		
		$ID = array();		
				
		while ( $res = $result->fetchRow() ) {

			$ID[] = $res["id"];
		}
		
		
		if ( !$ID ) {
			return false;
		}
		
		$IDlist = "";
		
		for ( $i = 0; $i<count( $ID ); $i++ ) {
			$IDlist .= $ID[$i];
			if ( $i != count( $ID ) -1 ) {
				$IDlist .= ", ";
			}
		}
		
		$query = "SELECT AVG(`value`) FROM `tiki_opnet_answer` WHERE (`filledform_id` IN ($IDlist)) GROUP BY `question_id`";
		$result = $this->query( $query);
		
		$ret = array();		
				
		while ( $res = $result->fetchRow() ) {

			$ret[] = $res;
		}
		
		return $ret;
	}

}

$opnetlib = new OpnetLib( $dbTiki );

?>