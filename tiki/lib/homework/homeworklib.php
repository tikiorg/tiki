<?php

// $Header: /cvsroot/tikiwiki/tiki/lib/homework/homeworklib.php,v 1.9 2004-03-11 17:12:33 ggeller Exp $

// 20040207 - added function hw_assignment_fetch

/*
    The idea here is that all access to the hw tables goes through this
     library.
 */
include_once ('lib/tikilib.php');

class HomeworkLib extends TikiLib {
  // Constructor receiving a PEAR::Db database object.
  function HomeworkLib($db) {
	TikiLib::TikiLib($db);
	// This is a little defensive programming.
	// The homeworklib and hw_ functions always assume that admin has
	//   the permissions of teacher, teacher has the pemissions of 
	//   grader, and grader has the permissions of student
	//	global $ggg_tracer;
	global $tiki_p_hw_admin;
	global $tiki_p_hw_teacher;
	global $tiki_p_hw_grader;
	global $tiki_p_hw_student;
	if (isset($tiki_p_hw_admin) && $tiki_p_hw_admin == 'y'){
	  $tiki_p_hw_teacher = 'y';
	  $tiki_p_hw_grader = 'y';
	  $tiki_p_hw_student = 'y';
	}
	else if (isset($tiki_p_hw_teacher) && $tiki_p_hw_teacher == 'y'){
	  $tiki_p_hw_grader = 'y';
	  $tiki_p_hw_student = 'y';
	}
	else if (isset($tiki_p_hw_grader) && $tiki_p_hw_grader == 'y'){
	  $tiki_p_hw_student = 'y';
	}
  }

  // Called by: tiki-hw_teacher_assignment_edit.php
  function hw_assignment_store(&$assignment_data) {
	if ($assignment_data["assignmentId"] == 0){ // new assignment
	  $data = array($assignment_data["title"],
                      $assignment_data["teacherName"],
                      $assignment_data["created"],
                      $assignment_data["dueDate"],
                      $assignment_data["modified"],
                      $assignment_data["heading"],
                      $assignment_data["body"],
                      0);
	  $query = "insert into `hw_assignments` (`title`, `teacherName`, `created`, `dueDate`, 
          `modified`, `heading`, `body`, `deleted`)";
	  $query.= " values (?, ?, ?, ?, ?, ?, ?, ?)";
	  $result = $this->query($query,$data);
	}
	else { // Revise existing assignment
	  $data = array($assignment_data["title"],
                      $assignment_data["teacherName"],
                      $assignment_data["dueDate"],
                      $assignment_data["modified"],
                      $assignment_data["heading"],
                      $assignment_data["body"],
                      $assignment_data["assignmentId"]);
	  $query = "update `hw_assignments` set `title` = ?, `teacherName` = ?,
               `dueDate` = ?, `modified` = ?, `heading` = ?, `body` = ? where assignmentId = ?";
	  $result = $this->query($query,$data);
	}

    return 'HW_OK';
  }

  // See if $studentName is a user with $tiki_p_hw_student
  // Stub for now.
  function hw_is_student($studentName){
		//    global $ggg_tracer;
    // $ggg_tracer->outln(__FILE__." line: ".__LINE__." In homeworklib.php,  hw_is_student.");
    // $ggg_tracer->outln(' $studentName = '.$studentName);
    return true;
  }

  // Called by tiki-hw_student_last_changes.php
  // $days (ro) How many days of changes to look for.
  // $offset (ro) Offset in the array
  // $limit (ro) Max number of elements in $lastchanges
  // $sort_mode (ro) what to sort by
  // $findwhat (ro) filter criteria
  // Returns $lastchanges (wo) Nested array with the data of interest
  // TODO: Pass back due date.
	function hw_pages_list($offset = 0, $maxRecords = -1, $sort_mode = 'pageName_desc') {
		global $user;
    if ($sort_mode == 'size_desc') {
			$sort_mode = 'page_size_desc';
    }
		
    if ($sort_mode == 'size_asc') {
			$sort_mode = 'page_size_asc';
    }
		
    $old_sort_mode = '';
		
    if (in_array($sort_mode, array(
																	 'versions_desc',
																	 'versions_asc',
																	 'links_asc',
																	 'links_desc',
																	 'backlinks_asc',
																	 'backlinks_desc'
																	 ))) {
			$old_offset = $offset;
			
			$old_maxRecords = $maxRecords;
			$old_sort_mode = $sort_mode;
			$sort_mode = 'user_desc';
			$offset = 0;
			$maxRecords = -1;
    }

		$mid = " where `id` like ? ";
		$bindvars = array('%%');

		$bindvars = array();
		$query = 'select * from hw_pages where `studentName` = ';
		$query .= "\"$user\"";
    $result = $this->query($query,$bindvars,$maxRecords,$offset);
		$query_cant = 'select count(*) from hw_pages where `studentName` = "Bobby"';
    $cant = $this->getOne($query_cant,$bindvars);
    $ret = array();
		
    while ($res = $result->fetchRow()) {
      // Get the user and assignment title for each
      // $assignment_data = $this->getassignment($res['']);
			// $ggg_tracer->out(__FILE__." line: ".__LINE__.' $res = ');
			// $ggg_tracer->outvar($res);
			$aux = array();
			
			$aux["id"] = $res["id"];
			$assignment_data = array();
			$bResult = $this->hw_assignment_fetch(&$assignment_data, $res["assignmentId"]);
			$aux["title"] = $assignment_data["title"];
			$aux["dueDate"] = $assignment_data["expireDate"];
			$aux["lastModif"] = $res["lastModif"];
			$aux["user"] = $res["user"];
			$aux["ip"] = $res["ip"];
			$aux["len"] = $res["page_size"];
			$aux["comment"] = $res["comment"];
			$aux["student"] = $res["studentName"];
			$ret[] = $aux;
    }

    // If sortmode is versions, links or backlinks sort using the ad-hoc function and reduce using old_offse and old_maxRecords
    if ($old_sort_mode == 'versions_asc') {
			usort($ret, 'compare_versions');
    }

    if ($old_sort_mode == 'versions_desc') {
			usort($ret, 'r_compare_versions');
    }

    if ($old_sort_mode == 'links_desc') {
			usort($ret, 'compare_links');
    }
		
    if ($old_sort_mode == 'links_asc') {
			usort($ret, 'r_compare_links');
    }
		
    if ($old_sort_mode == 'backlinks_desc') {
			usort($ret, 'compare_backlinks');
    }
		
    if ($old_sort_mode == 'backlinks_asc') {
			usort($ret, 'r_compare_backlinks');
    }
		
    if (in_array($old_sort_mode, array(
																			 'versions_desc',
																			 'versions_asc',
																			 'links_asc',
																			 'links_desc',
																			 'backlinks_asc',
																			 'backlinks_desc'
																			 ))) {
			$ret = array_slice($ret, $old_offset, $old_maxRecords);
    }
		
    $retval = array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
	}
	
  // return true if specified version exists for specified page
  // otherwise false
  // $pageId - ro - index into hw_pages and hw_history
  // $nVersion - ro - index into hw_history
  function hw_page_version_exists($pageId, $nVersion){
		//    global $ggg_tracer;
		//    $ggg_tracer->outln(__FILE__." line: ".__LINE__." In homeworklib.php,  hw_page_version_exists.");
		//    $ggg_tracer->outln(' $pageId = '.$pageId);
		//    $ggg_tracer->outln(' $nVersion = '.$nVersion);
    $query = "select `id`, `version`, `lastModif`, `user`, `ip`, `comment`, `data` from `hw_history` where `id`=? and `version`= ?";
    $result = $this->query($query,array($pageId, $nVersion));

    if (!$result->numRows()){
      return false;
    }
    else{
      return true;
    }
  }

  // Get a nVerson of a page from the database
  // Called by tiki-hw_pagehistory.php
  // Stub for now.
  function hw_page_get_version($pageId, $nVersion){
		//    global $ggg_tracer;
		//    $ggg_tracer->outln(__FILE__." line: ".__LINE__." In homeworklib.php,  hw_page_get_version.");
		//    $ggg_tracer->outln(' $pageId = '.$pageId);
		//    $ggg_tracer->outln(' $nVersion = '.$nVersion);
    $query = "select `id`, `version`, `lastModif`, `user`, `ip`, `comment`, `data` from `hw_history` where `id`=? and `version`= ?";
    $result = $this->query($query,array($pageId, $nVersion));

    if (!$result->numRows()){
      return false;
    }
    else{
      $info = $result->fetchRow();
    }
    return $info;
  }

  // Adapted from get_page_history in lib/wiki/histlib.php
  function hw_page_get_history($pageId){
    $query = "select `id`, `version`, `lastModif`, `user`, `ip`, `comment`, `data` from `hw_history` where `id`=? order by `version` desc";
    $result = $this->query($query,array($pageId));
    $ret = array();
    
    while ($res = $result->fetchRow()) {
      $aux = array();
      
      $aux["version"] = $res["version"];
      $aux["lastModif"] = $res["lastModif"];
      $aux["user"] = $res["user"];
      $aux["ip"] = $res["ip"];
      $aux["data"] = $res["data"];
      $aux["id"] = $res["id"];
      // $aux["description"] = $res["description"];
      $aux["comment"] = $res["comment"];
      $ret[] = $aux;
    }
    
    return $ret;
  }

  // Adapted from histlib.php, remove version
  // Stub for now.
  function hw_page_remove_version($pageId,$version, $comment=""){
		//    global $ggg_tracer;
		//    $ggg_tracer->outln(__FILE__." line: ".__LINE__." In homeworklib.php, remove_version.");
		//    $ggg_tracer->outln(' $pageId = '.$pageId);
		//    $ggg_tracer->outln(' $version = '.$version);
    global $user;
    $query = "delete from `hw_history` where `id`=? and `version`=?";
    $result = $this->query($query,array($pageId,$version));
    $action = "Removed version $version";
    $t = date("U");
    $query = "insert into `hw_actionlog`(`action`,`pageId`,`lastModif`,`user`,`ip`,`comment`) values(?,?,?,?,?,?)";
    $result = $this->query($query,array($action,$pageId,$t,$user,$_SERVER["REMOTE_ADDR"],$comment));
    return true;
  }

  // hw_grading_queue table
  //   id Number in the total queue. (int autoincrement)
  //   status (int)
  //       0 - deleted
  //       1 - awaiting grading
  //   submissionDate - timestamp
  //   userLogin - user doing the submitting
  //   userIp - ip of user doing the submitting
  //   pageId (int) - index to hw_pages
  //   pageDate - timestamp when work was done.
  //   pageVersion - version from hw_page
  //   assignmentId (int) - index to hw_assignments

  // Add the page to the grading queue.
  // 
  // db: hw_grading_queue table, read/write
  // $pageId - index of page in hw_pages
  // $pageDate - last modified timestamp from hw_pages
  // $pageVersion - version from hw_pages
  // $assignmentId - index of assignment in hw_assignments
  //
  // If earlier submission of same page is present, mark it deleted.
  // Place page in the grading queue.
  function hw_grading_queue_submit($pageId, $pageDate, $pageVersion, $assignmentId){
    global $user;
		//    global $ggg_tracer;
		//    $ggg_tracer->outln(__FILE__." line: ".__LINE__.' submit detected! ');
		//    $ggg_tracer->outln(__FILE__." line: ".__LINE__.' $pageId = '.$pageId);
		//    $ggg_tracer->outln(__FILE__." line: ".__LINE__.' $pageDate = '.$pageDate);
		//    $ggg_tracer->outln(__FILE__." line: ".__LINE__.' $pageVersion = '.$pageVersion);
		//    $ggg_tracer->outln(__FILE__." line: ".__LINE__.' $assignmentId = '.$assignmentId);
    
		//    $ggg_tracer->outln(__FILE__." line: ".__LINE__.' $user = '.$user);
    
    $ipAddr = $_SERVER["REMOTE_ADDR"];
    
    $date = date('U');
		//    $ggg_tracer->outln(__FILE__." line: ".__LINE__.' $date = '.$date);
    // If this page is in the que, mark that entry as deleted.
    // Add this page to the que.
    $query = "INSERT INTO `hw_grading_queue` (
                     `status`,
                     `submissionDate`,
                     `userLogin`,
                     `userIp`,
                     `pageId`,
                     `pageDate`,
                     `pageVersion`,
                     `assignmentId`)";

    $query.= " values(?,?,?,?,?,?,?,?)";
    $val = array(
		 'status'=>0,
		 'submissionDate'=>$date,
		 'userLogin'=>$user,
                 'userIp'=>$ipAddr,
                 'pageId'=>$pageId,
                 'pageDate'=>$pageDate,
                 'pageVersion'=>$pageVersion,
                 'assignmentId'=>$assignmentId);
    $result = $this->query($query, $val);
    
    while (!$result->EOF) {
      for ($i=0, $max=$result->FieldCount(); $i < $max; $i++)
	print $result->fields[$i].' ';
      $result->MoveNext();
      print "<br>\n";
    }
  }
  // Return the highest hw privilege for $user
  // There are a lot of way to do this using different functions in userlib.
  // Called by: tiki-hw_page.php
  // The best way is probably to add an editor's role field to the hw_pages
  //   table.
  function hw_user_type($user){
    global $userlib;
    $bAdmin = false;
    $bTeacher = false;
    $bGrader = false;
    $bStudent = false;
    $perms = $userlib->get_user_permissions($user);
    foreach ($perms as $perm)
	  {
		switch ($perm)
		  {
		  case 'tiki_p_hw_admin':
			$bAdmin = true;
			break;
		  case 'tiki_p_hw_teacher':
			$bTeacher = true;
			break;
		  case 'HW_GRADER':
			$bGrader = true;
			break;
		  case 'HW_STUDENT':
			$bStudent = true;
			break;
		  }
	  }
    if ($bAdmin)
      return('HW_ADMIN');
    if ($bTeacher)
      return('HW_TEACHER');
    if ($bGrader)
      return('HW_GRADER');
    return('HW_STUDENT');
  }

  // Called by: tiki-hw_rollback.php, tiki-hw_teacher_assignment_edit.php
  // Fetch data for an assignment.
  // $data - wo - row from hw_assignments table
  // $id   - wo - index to hw_assignments table
  // db hw_assignments table
  // Return true if successful, otherwise false
  function hw_assignment_fetch(&$data, $id) {
	$bResult = false;
    $query = "select * from `hw_assignments` where `assignmentId`=?";
    $result = $this->query($query,array((int)$id));
	if ($result->numRows()) {
	  $data = $result->fetchRow();
	  $bResult = true;
	}
	return $bResult;
  }

  // GGG - stub
  // Need to use the id for the page, get the assignment number, etc. etc.
  function hw_grading_queue($pageId) {
		//	global $ggg_tracer;
		//	$ggg_tracer->outln(__FILE__." line: ".__LINE__.' In hw_grading queue stub. ');
		//	$ggg_tracer->outln(__FILE__." line: ".__LINE__.' $pageId = '.$pageId);
	return 0;
  }
  
  // GGG - needs work Confirm that $assignmentId is in hw_assignments table
  function hw_assignmentId_is_valid($assignmentId) {
	return true;
  }

  // Unlock a page
  //
  // $pageID - The index in hw_pages
  function hw_page_unlock($pageId) {
		//	global $ggg_tracer;
		//	$ggg_tracer->outln(__FILE__." line ".__LINE__.' function hw_page_unlock is only a stub!');
  }

  // Grab a homework wiki-like page based on the user (student) and
  //  the assignmentId from the hw_pages table.
  //  
  //  db: hw_pages table, read only
  //  
  //  $info - write only, the query results
  //  $studentName - the name of the student
  //  $assignmentId - index shared with hw_assignment table
  function hw_page_fetch(&$info, $studentName, $assignmentId) {
	// global $ggg_tracer;
	// $ggg_tracer->outln(__FILE__." line: ".__LINE__.": in hw_page_fetch.");

	global $tiki_p_hw_student;
	global $smarty;
	$info = array();
	// assert current user has tiki_p_hw_student permission
	if($tiki_p_hw_student != 'y') {
	  $smarty->assign('msg', __FILE__.tra(" line ").__LINE__.", ".tra("Error: No")." tiki_p_hw_student ".tra("permission."));
	  $smarty->display("error.tpl");
	  die;  
	}

	// assert $assignmentId is in the hw_assignments list
	if(!$this->hw_assignmentId_is_valid($assignmentId)) {
	  $smarty->assign('msg', __FILE__.tra(" line ").__LINE__.", ".tra("Error: Invalid")." assignmentId");
	  $smarty->display("error.tpl");
	  die;
	}

	$query = "select * from `hw_pages` where `studentName`=? and `assignmentId`=?";
	// $ggg_tracer->out(__FILE__." line: ".__LINE__.': $query = ');
	// $ggg_tracer->outvar($query);
    $result = $this->query($query, array("$studentName","$assignmentId"));
	// $ggg_tracer->out(__FILE__." line: ".__LINE__.': $result = ');
	// $ggg_tracer->outvar($result);

	// while (!$result->EOF) {
	//   for ($i=0, $max=$result->FieldCount(); $i < $max; $i++)
	// 	print $result->fields[$i].' ';
	//  $result->MoveNext();
	//  print "<br>\n";
	// }

    if (!$result->numRows()){
	  // $ggg_tracer->outln(__FILE__." line: ".__LINE__);
      return false;
	}
    else{
	  // $ggg_tracer->outln(__FILE__." line: ".__LINE__);
	  $info = $result->fetchRow();
      return true;
	}
  }

  // Grab a homework wiki-like page based on its id in 
  //  the hw_pages table.
  //  
  //  db: hw_pages table, read only
  //  
  //  $info   write-only - put a row from the database
  //  $id     read-only  - index into hw_pages table
  //  $lock   read-only  - lock the page
  //  returns status,    - "HW_OK", "HW_INVALID_ID", or "HW_PAGE_LOCKED"
  function hw_page_fetch_by_id(&$info, $id, $lock=false) {
	// global $ggg_tracer;

	global $tiki_p_hw_student;
	global $smarty;
	$info = array();
	// assert current user has tiki_p_hw_student permission
	if($tiki_p_hw_student != 'y') {
	  $smarty->assign('msg', __FILE__.tra(" line ").__LINE__.", ".tra("Error: No")." tiki_p_hw_student ".tra("permission."));
	  $smarty->display("error.tpl");
	  die;  
	}

	$query = "select * from `hw_pages` where `id`=?";
	// $ggg_tracer->out(__FILE__." line: ".__LINE__.': $query = ');
	// $ggg_tracer->outvar($query);
    $result = $this->query($query, array("$id"));
	// $ggg_tracer->out(__FILE__." line: ".__LINE__.': $result = ');
	// $ggg_tracer->outvar($result);

    if (!$result->numRows()){
      return "HW_INVALID_ID";
    }

    // check the lockUser and the lockExpires from the page
    // break the lock if lockUser == $user
    // break the lock if lockExpires > date
    // otherwise return HW_PAGE_LOCKED

    // Todo: if $lock is true, lock the page
    // This is related to:
    // session.gc_maxlifetime = 1440
    // in /etc/php.ini
    // and some code in tiki-setup_base.php
    // probably want something like lockExpires = date + session.gc_maxlifetime + 60;

    // $ggg_tracer->outln(__FILE__." line: ".__LINE__);
    $info = $result->fetchRow();
    return "HW_OK";
  }

  /*
    There are two tables for homework:
    hw_assignments - Holds assignments created by the teacher.
    hw_pages - Holds the work of the students.

    This is the mysql to create the hw_pages table:
    CREATE TABLE `hw_pages` (
      `id` int(14) NOT NULL  auto_increment,
      `assignmentId` int(14) NOT NULL,
      `studentName` varchar(200) NOT NULL,
      `data` text default NULL,
      `description` varchar(200) default NULL,
      `lastModif` int(14) default NULL,
      `user` varchar(200) default NULL,
      `comment` varchar(200) default NULL,
      `version` int(8) NOT NULL default '0',
      `ip` varchar(15) default NULL,
      `flag` char(1) default NULL,
      `points` int(8) default NULL,
      `votes` int(8) default NULL,
      `cache` text default NULL,
      `wiki_cache` int(10) default '0',
      `cache_timestamp` int(14) default NULL,
      `page_size` int(10) unsigned default '0',
      KEY `id` (`id`),
      KEY `assignmentId` (`assignmentId`),
      KEY `studentName` (`studentName`),
      PRIMARY KEY  (`studentName`,`assignmentId`)
    ) TYPE=MyISAM;

    Explanation of fields:
      id - Each page has a unique id used for anonymous graders and peer review.
      assignmentID - The corresponding id from the hw_assignments table.
      studentName - Only used by and visible to teachers
      data - wiki text of the essay
      description - reserved for future use
      lastModif - date
      user - name of most recent editor (if another student or anon grader, 
        only visible to teacher).
      comment - short description of this edit version
      version - revision number starts at 0
      ip - address of most recent editor, only visible to teacher
      flag - locked if someone else is editing
      points - reserved
      votes - reserved
      cache - reserved
      wiki_cache - reserved
      cache_timestamp - reserved
      page_size - reserved
  */

  // Called by: tiki-hw_editpage.php
  //
  // db - hw_pages table (rw)
  //
  // args:
  // $pageId (ro) - index into hw_pages table
  // $data (ro) - new body of page
  // $comment (ro) - new comment for page
  // $unlock (ro) - ulnlock page?
  //
  function hw_page_update($pageId, $data, $comment, $unlock = true) {
		//    global $ggg_tracer;
		//    $ggg_tracer->outln(__FILE__." line: ".__LINE__.": in hw_page_update.");
		//    $ggg_tracer->outln("Have to update the version number!");
		//    $ggg_tracer->outln("Have to update the time stamp!");

	$oldInfo = array();
	$status = $this->hw_page_fetch_by_id(&$oldInfo, $pageId, false);
	if ($status != 'HW_OK'){
	  $smarty->assign('msg', __FILE__.tra(" line ").__LINE__.", ".tra("Error: Call to hw_page_fetch_by_id failed!"));
	  $smarty->display("error.tpl");
	  die;  
	}

   	$query = "insert into `hw_history`(`id`, `version`, `lastModif`, `user`, `ip`, `comment`, `data`) values(?,?,?,?,?,?,?)";
	$result = $this->query($query,array($oldInfo["id"], (int)$oldInfo["version"], (int)$oldInfo["lastModif"], $oldInfo["user"], $oldInfo["ip"], $oldInfo["comment"], $oldInfo["data"]));

	$version = (int)$oldInfo["version"] + 1;
	$lastModif = date("U");
	$ip = $_SERVER["REMOTE_ADDR"];


	$query = "update `hw_pages` set `version` = ?, `lastModif` = ?, `ip` = ? , `data` = ?, `comment` = ?  where `id`=?";
	$this->query($query,array($version,$lastModif,$ip,$data,$comment,$pageId));
	if ($unlock)
	  $this->hw_page_unlock($pageId);
  }

  // Create a homework page
  //  

  // Have to make a primary key of
  function hw_page_create($user,$assignmentId) {
		//    global $ggg_tracer;
		//    $ggg_tracer->outln(__FILE__." line: ".__LINE__.": in hw_page_create.");
    // $ggg_tracer->out(__FILE__." line: ".__LINE__.': $user = ');
    // $ggg_tracer->outvar($user);
    // $ggg_tracer->out(__FILE__." line: ".__LINE__.': $assignmentId = ');
    // $ggg_tracer->outvar($assignmentId);
    $theDate = date("U");
    $ipAddr = $_SERVER["REMOTE_ADDR"];
    $query = "INSERT INTO `hw_pages` (
                `assignmentId`,
                `studentName`,
                `user`,
                `lastModif`,
                `version`,
                `ip`
                )";

    $query.= " values(?,?,?,?,?,?)";

    // $ggg_tracer->out(__FILE__." line: ".__LINE__.': $query = ');
    // $ggg_tracer->outvar($query);

    $val = array(
		 'assignmentId'=>$assignmentId,
		 'studentName'=>$user,
		 'user'=>$user,
		 'lastModif'=>"$theDate",
		 'version'=>0,
		 'ip'=>$ipAddr );
    
    // $ggg_tracer->out(__FILE__." line: ".__LINE__.': $val = ');
    // $ggg_tracer->outvar($val);
    
    // With the right settings, query checks result.
    // More checking would be good, though.
    $result = $this->query($query, $val);
    
    while (!$result->EOF) {
      for ($i=0, $max=$result->FieldCount(); $i < $max; $i++)
	print $result->fields[$i].' ';
      $result->MoveNext();
      print "<br>\n";
    }
    
    
    return true;

  }

  // Stub for now
  // Called by tiki-hw_rollback.php
  function hw_page_exists($pageId) {
		//    global $ggg_tracer;
		//    $ggg_tracer->outln(__FILE__." line: ".__LINE__.': In hw_page_exists!');
    return true;
  }

  // Stub for now
  // Called by tiki-hw_rollback.php
  function hw_page_use_version($page, $version, $comment = ''){
    global $user;
		//    global $ggg_tracer;
		//    $ggg_tracer->outln(__FILE__." line: ".__LINE__.': In hw_page_use_version!');
	$query = "select * from `hw_history` where `id`=? and `version`=?";
	$result = $this->query($query,array($page,$version));
	if (!$result->numRows())
		return false;
	$res = $result->fetchRow();
	$query = "update `hw_pages` set `data`=?,`lastModif`=?,`user`=?,`comment`=?,`version`=`version`+1,`ip`=? where `id`=?";
	$result = $this->query($query,array($res["data"],$res["lastModif"],$res["user"],$res["comment"],$res["ip"],$page));
	$action = "Changed actual version to $version";
	$t = date("U");
	$query = "insert into `hw_actionlog`(`action`,`pageId`,`lastModif`,`user`,`ip`,`comment`) values(?,?,?,?,?,?)";
	$result = $this->query($query,array($action,$page,$t,$user,$_SERVER["REMOTE_ADDR"],$comment));
	return true;
  }

  // Called by:
  //   tiki-hw_student_assignments.php
  //   tiki-hw_teacher_assignments.php
  // Data:
  //   hw_assignments table - read only
  // Arguments:
  //   offset     - read only - starting point in the vector
  //   maxRecords - read only - maximum number of records to return
  // Returns:
  //   Array with count of results and a data vector, sorted by the due date
  // TODO: Test with a lot of rows in the table and different values of offset and maxRecords
  function hw_assignments_list($offset = 0, $maxRecords = -1) {
    $bindvars=array();
    $query = 'select `hw_assignments`.* from `hw_assignments` ORDER BY `dueDate`';
    $result = $this->query($query,$bindvars,$maxRecords,$offset);
    $ret = array();
    
    $cant = 0;
    while ($res = $result->fetchRow()) {
      $ret[] = $res;
      $cant++;
    }
    
    $retval = array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }
  
  function assignment_store($id,$dueDate,$data,$user,$ip,$description){
	//	global $ggg_tracer;
	//	$ggg_tracer->outln("$id = ".$id);
	//	$ggg_tracer->outln("$dueDate = ".$dueDate);
	//	$ggg_tracer->outln("$data = ".$data);
	//	$ggg_tracer->outln("$user = ".$user);
	//	$ggg_tracer->outln("$ip = ".$ip);
	//	$ggg_tracer->outln("$description = ".$description);
  }

  function create_assignment($name, $hits, $data, $lastModif, $comment, $user = 'system', $ip = '0.0.0.0', $description = '') {

	//	$ggg_tracer->outln("In create_assignment...");
	//	$ggg_tracer->outln("$name = ".$name);
	//	$ggg_tracer->outln("$hits = ".$hits);
	//	$ggg_tracer->outln("$data = ".$data);
	//	$ggg_tracer->outln("$lastModif = ".$lastModif);
	//	$ggg_tracer->outln("$comment = ".$comment);
	//	$ggg_tracer->outln("$user = ".$user);
	//	$ggg_tracer->outln("$ip = ".$ip);
	//	$ggg_tracer->outln("$description = ".$description);
//     global $smarty;
//     global $dbTiki;
//     global $notificationlib;
//     global $sender_email;
//     include_once ('lib/notifications/notificationlib.php');
//     include_once ("lib/commentslib.php");

//     $commentslib = new Comments($dbTiki);

//     // Collect pages before modifying data
//     $pages = $this->get_pages($data);
	
//     if ($this->page_exists($name))
// 	  return false;

//     $query = "insert into `tiki_pages`(`pageName`,`hits`,`data`,`lastModif`,`comment`,`version`,`user`,`ip`,`description`,`creator`,`page_size`) ";
//     $query.= " values(?,?,?,?,?,?,?,?,?,?,?)";
//     $result = $this->query($query, array(
// 		$name,
// 		(int)$hits,
// 		$data,
// 		(int)$lastModif,
// 		$comment,
// 		1,
// 		$user,
// 		$ip,
// 		$description,
// 		$user,
// 		(int)strlen($data)
// 		));

//     $this->clear_links($name);

//     // Pages are collected before adding slashes
//     foreach ($pages as $a_page) {
// 	  $this->replace_link($name, $a_page);
//     }

//     // Update the log
//     if ($name != 'SandBox') {
// 	$action = "Created";

// 	$query = "insert into `tiki_actionlog`(`action`,`pageName`,`lastModif`,`user`,`ip`,`comment`) values(?,?,?,?,?,?)";
// 	$result = $this->query($query, array(
// 		    $action,
// 		    $name,
// 		    (int)$lastModif,
// 		    $user,
// 		    $ip,
// 		    $comment
// 		    ));
//     }

//     $emails = $notificationlib->get_mail_events('wiki_page_changes', '*');

//     foreach ($emails as $email) {
// 	$smarty->assign('mail_site', $_SERVER["SERVER_NAME"]);

// 	$smarty->assign('mail_page', $name);
// 	$smarty->assign('mail_date', date("U"));
// 	$smarty->assign('mail_user', $user);
// 	$smarty->assign('mail_comment', $comment);
// 	$smarty->assign('mail_last_version', 1);
// 	$smarty->assign('mail_data', $data);
// 	$foo = parse_url($_SERVER["REQUEST_URI"]);
// 	$machine = httpPrefix(). dirname( $foo["path"] );
// 	$smarty->assign('mail_machine', $machine);
// 	$smarty->assign('mail_pagedata', $data);
// 	$mail_data = $smarty->fetch('mail/wiki_change_notification.tpl');

// 	if( $this->get_preference('wiki_forum') )
// 	{
// 	    $forums = $commentslib->list_forums( 0, 1,
// 		    'name_asc',
// 		    $this->get_preference('wiki_forum') );
//     if ($forums) {
// 	    $forumEmail = $forums["data"][0]["outbound_from"];

// 	    @mail($email, $name, $mail_data,
// 		    "From: $forumEmail\r\nContent-type: text/plain;charset=utf-8\r\n"
// 		 );
//     }
// 	} else {
// 	    @mail($email, tra('Wiki page'). ' ' . $name . '
// 		    ' . tra('changed'), $mail_data,
// 		    "From: $sender_email\r\nContent-type: text/plain;charset=utf-8\r\n"
// 		 );
// 	}
//     }

    return true;
}

function update_assignment($pageName, $edit_data, $edit_comment, $edit_user, $edit_ip, $description = '', $minor = false) {
  //  global $ggg_tracer;
  //  $ggg_tracer->outln("In update_assignment...");
  //  $ggg_tracer->outln("$pageName = ".$pageName);
  //  $ggg_tracer->outln("$edit_data = ".$edit_data);
  //  $ggg_tracer->outln("$edit_comment = ".$edit_comment);
  //  $ggg_tracer->outln("$edit_user = ".$edit_user);
  //  $ggg_tracer->outln("$edit_ip = ".$edit_ip);
  //  $ggg_tracer->outln("$description = ".$description);
  //  $ggg_tracer->outln("$minor = ".$minor);
//     global $smarty;

//     global $dbTiki;
//     global $notificationlib;
//     global $feature_user_watches;
//     global $wiki_watch_author;
//     global $wiki_watch_comments;
//     global $wiki_watch_editor;
//     global $sender_email;
//     //include_once ('lib/notifications/notificationlib.php');
//     include_once ("lib/commentslib.php");

//     $commentslib = new Comments($dbTiki);

//     $this->invalidate_cache($pageName);
//     // Collect pages before modifying edit_data (see update of links below)
//     $pages = $this->get_pages($edit_data);

//     if (!$this->page_exists($pageName))
// 	return false;

//     $t = date("U");
//     // Get this page information
//     $info = $this->get_page_info($pageName);
//     // Store the old version of this page in the history table
//     $version = $info["version"];
//     $lastModif = $info["lastModif"];
//     $user = $info["user"];
//     $ip = $info["ip"];
//     $comment = $info["comment"];
//     $data = $info["data"];
//     // WARNING: POTENTIAL BUG
//     // The line below is not consistent with the rest of Tiki
//     // (I commented it out so it can be further examined by CVS change control)
//     //$pageName=addslashes($pageName);
//     // But this should work (comment added by redflo):
//     $version += 1;

//     if (!$minor) {
// 	$query = "insert into `tiki_history`(`pageName`, `version`, `lastModif`, `user`, `ip`, `comment`, `data`, `description`)
// 	    values(?,?,?,?,?,?,?,?)";

// # echo "<pre>";print_r(get_defined_vars());echo "</pre>";die();
// 	if ($pageName != 'SandBox') {
// 	    $result = $this->query($query,array($pageName,(int) $version,(int) $lastModif,$user,$ip,$comment,$data,$description));
// 	}
// 	// Update the pages table with the new version of this page

// 	$emails = $notificationlib->get_mail_events('wiki_page_changes', 'wikipage' . $pageName);

// 	foreach ($emails as $email) {
// 	    $smarty->assign('mail_site', $_SERVER["SERVER_NAME"]);

// 	    $smarty->assign('mail_page', $pageName);
// 	    $smarty->assign('mail_date', date("U"));
// 	    $smarty->assign('mail_user', $edit_user);
// 	    $smarty->assign('mail_comment', $edit_comment);
// 	    $smarty->assign('mail_last_version', $version);
// 	    $smarty->assign('mail_data', $edit_data);
// 	    $foo = parse_url($_SERVER["REQUEST_URI"]);
// 	    $machine = httpPrefix(). dirname( $foo["path"] );
// 	    $smarty->assign('mail_machine', $machine);
// 	    $smarty->assign('mail_pagedata', $edit_data);
// 	    $mail_data = $smarty->fetch('mail/wiki_change_notification.tpl');

// 	    if( $this->get_preference('wiki_forum') )
// 	    {
// 		$forums = $commentslib->list_forums( 0, 1,
// 			'name_asc',
// 			$this->get_preference('wiki_forum') );

// 		$forumEmail = $forums["data"][0]["outbound_from"];

// 		@mail($email, $pageName, $mail_data,
// 			"From: $forumEmail\r\nContent-type: text/plain;charset=utf-8\r\n"
// 		     );
// 	    } else {
// 		@mail($email, tra('Wiki page'). ' ' . $pageName . '
// 			' . tra('changed'), $mail_data,
// 			"From: $sender_email\r\nContent-type: text/plain;charset=utf-8\r\n"
// 		     );
// 	    }
// 	}

// 	if ($feature_user_watches == 'y') {
// 	    $nots = $this->get_event_watches('wiki_page_changed', $pageName);

// 	    foreach ($nots as $not) {
// 		if ($wiki_watch_editor != 'y' && $not['user'] == $user) break;
// 		$smarty->assign('mail_site', $_SERVER["SERVER_NAME"]);

// 		$smarty->assign('mail_page', $pageName);
// 		$smarty->assign('mail_date', date("U"));
// 		$smarty->assign('mail_user', $edit_user);
// 		$smarty->assign('mail_comment', $edit_comment);
// 		$smarty->assign('mail_last_version', $version);
// 		$smarty->assign('mail_data', $edit_data);
// 		$smarty->assign('mail_hash', $not['hash']);
// 		$foo = parse_url($_SERVER["REQUEST_URI"]);
// 		$machine = httpPrefix(). dirname( $foo["path"] );
// 		$smarty->assign('mail_machine', $machine);
// 		$parts = explode('/', $foo['path']);

// 		if (count($parts) > 1)
// 		    unset ($parts[count($parts) - 1]);

// 		$smarty->assign('mail_machine_raw', httpPrefix(). implode('/', $parts));
// 		$smarty->assign('mail_pagedata', $edit_data);
// 		$mail_data = $smarty->fetch('mail/user_watch_wiki_page_changed.tpl');
// 		@mail($not['email'], tra('Wiki page'). ' ' . $pageName . ' ' . tra('changed'), $mail_data, "From: $sender_email\r\nContent-type: text/plain;charset=utf-8\r\n");
// 	    }
// 	}
//     }

//     $query = "update `tiki_pages` set `description`=?, `data`=?, `comment`=?, `lastModif`=?, `version`=?, `user`=?, `ip`=?, `page_size`=? where `pageName`=?";
//     $result = $this->query($query,array($description,$edit_data,$edit_comment,(int) $t,$version,$edit_user,$edit_ip,(int)strlen($data),$pageName));
//     // Parse edit_data updating the list of links from this page
//     $this->clear_links($pageName);

//     // Pages collected above
//     foreach ($pages as $page) {
// 	$this->replace_link($pageName, $page);
//     }

//     // Update the log
//     if ($pageName != 'SandBox' && !$minor) {
// 	$action = "Updated";

// 	$query = "insert into `tiki_actionlog`(`action`,`pageName`,`lastModif`,`user`,`ip`,`comment`) values(?,?,?,?,?,?)";
// 	$result = $this->query($query,array($action,$pageName,(int) $t,$edit_user,$edit_ip,$edit_comment));
// 	$maxversions = $this->get_preference("maxVersions", 0);

// 	if ($maxversions) {
// 	    // Select only versions older than keep_versions days
// 	    $keep = $this->get_preference('keep_versions', 0);

// 	    $now = date("U");
// 	    $oktodel = $now - ($keep * 24 * 3600);
// 	    $query = "select `pageName` ,`version` from `tiki_history` where `pageName`=? and `lastModif`<=? order by `lastModif` desc";
// 	    $result = $this->query($query,array($pageName,$oktodel),-1,$maxversions);
// 	    $toelim = $result->numRows();

// 	    while ($res = $result->fetchRow()) {
// 		$page = $res["pageName"];

// 		$version = $res["version"];
// 		$query = "delete from `tiki_history` where `pageName`=? and `version`=?";
// 		$this->query($query,array($pageName,$version));
// 	    }
// 	}
//     }
}

// adaped from get_article in lib/tikilib.php
function get_assignment($articleId) {
    $query = "select `hw_assignments`.*,
	`users_users`.`avatarLibName`,
	`tiki_article_types`.`use_ratings`,
	`tiki_article_types`.`show_pre_publ`,
	`tiki_article_types`.`show_post_expire`,
	`tiki_article_types`.`heading_only`,
	`tiki_article_types`.`allow_comments`,
	`tiki_article_types`.`comment_can_rate_article`,		
	`tiki_article_types`.`show_image`,
	`tiki_article_types`.`show_avatar`,
	`tiki_article_types`.`show_author`,
	`tiki_article_types`.`show_pubdate`,
	`tiki_article_types`.`show_expdate`,
	`tiki_article_types`.`show_reads`,
	`tiki_article_types`.`show_size`,
	`tiki_article_types`.`creator_edit`
	from `hw_assignments`, `tiki_article_types`, `users_users` where `hw_assignments`.`articleId`=?";
    //$query = "select * from `hw_assignments` where `articleId`=?";
    $result = $this->query($query,array((int)$articleId));
//    	global $ggg_tracer;
//  	$ggg_tracer->outln(__FILE__." line: ".__LINE__);
// 	$ggg_tracer->outln('$result = '.$result);
// 	$ggg_tracer->outln('$result->numRows() = '.$result->numRows());
    if ($result->numRows()) {
//  	$ggg_tracer->outln(__FILE__." line: ".__LINE__." Found an assignment.");
	$res = $result->fetchRow();
	$res["entrating"] = floor($res["rating"]);
    } else {
//  	$ggg_tracer->outln(__FILE__." line: ".__LINE__." Failed to find an assignment.");
	return false;
    }
    return $res;
}

 function replace_assignment($title, $authorName, $topicId, $useImage, $imgname, $imgsize, $imgtype, $imgdata, $heading, $body, $publishDate, $expireDate, $user, $articleId, $image_x, $image_y, $type, $rating = 0, $isfloat = 'n') {
   
//    global $ggg_tracer;
//    $ggg_tracer->outln(__FILE__." line: ".__LINE__);

   if ($expireDate < $publishDate) {
     $expireDate = $publishDate;
   }
   $hash = md5($title . $heading . $body);
   $now = date("U");
   if(empty($imgdata)) $imgdata='';
   // Fixed query. -rlpowell
   $query = "select `name`  from `tiki_topics` where `topicId` = ?";
   $topicName = $this->getOne($query, array($topicId) );
   $size = strlen($body);

   // Fixed query. -rlpowell
   if ($articleId) {
     // Update the article
     $query = "update `hw_assignments` set `title` = ?, `authorName` = ?, `topicId` = ?, `topicName` = ?, `size` = ?, `useImage` = ?, `image_name` = ?, ";
     $query.= " `image_type` = ?, `image_size` = ?, `image_data` = ?, `isfloat` = ?, `image_x` = ?, `image_y` = ?, `heading` = ?, `body` = ?, ";
     $query.= " `publishDate` = ?, `expireDate` = ?, `created` = ?, `author` = ?, `type` = ?, `rating` = ?  where `articleId` = ?";

     $result = $this->query($query, array(
       $title, $authorName, (int) $topicId, $topicName, (int) $size, $useImage, $imgname, $imgtype, (int) $imgsize, $imgdata, $isfloat,
       (int) $image_x, (int) $image_y, $heading, $body, (int) $publishDate, (int) $expireDate, (int) $now, $user, $type, (float) $rating, (int) $articleId ) );
   } else {
     // Fixed query. -rlpowell
     // Insert the article
     $query = "insert into `hw_assignments` (`title`, `authorName`, `topicId`, `useImage`, `image_name`, `image_size`, `image_type`, `image_data`, ";
     $query.= " `publishDate`, `expireDate`, `created`, `heading`, `body`, `hash`, `author`, `reads`, `votes`, `points`, `size`, `topicName`, `image_x`, `image_y`, `type`, `rating`, `isfloat`) ";
     $query.= " values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

     $result = $this->query($query, array(
       $title, $authorName, (int) $topicId, $useImage, $imgname, (int) $imgsize, $imgtype, $imgdata, (int) $publishDate, (int) $expireDate, (int) $now, $heading,
       $body, $hash, $user, 0, 0, 0, (int) $size, $topicName, (int) $image_x, (int) $image_y, $type, (float) $rating, $isfloat));

       // Fixed query. -rlpowell
       $query2 = "select max(`articleId`) from `tiki_articles` where `created` = ? and `title`=? and `hash`=?";
       $articleId = $this->getOne($query2, array( (int) $now, $title, $hash ) );
   }
   
   return $articleId;
 }

 // Remove the assignment and all the students' work associated with it.
 function hw_assignment_remove($id){
	 global $ggg_tracer;
	 $ggg_tracer->outln(__FILE__." line: ".__LINE__." in hw_assignment_remove");
	 $ggg_tracer->outln(__FILE__." line: ".__LINE__." $id = ".$id);
	 $ggg_tracer->outln(" TODO: delete all the students work for this assignment.");
	 $query = "delete from `hw_assignments` where `articleId`=?";
	 $result = $this->query($query,array($id));
	 // $this->hw_pages_remove_all($id);
	 return true;
 }

} //end of class HomeworkLib


?>
