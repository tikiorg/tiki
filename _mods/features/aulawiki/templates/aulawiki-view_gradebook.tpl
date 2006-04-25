<p class="pagetitle">{tr}Gradebook{/tr}</p>

<form name="formEditGradeBook" method="post" action="./aulawiki-view_gradebook.php">
<input name="editAssgId" type="hidden" value="{$editAssgId}"/>

<div class="gradebuttons">
<input name="activePeriodId" type="hidden" value="{$activePeriodId}"/>
<select name="periodId" id="periodId">
	      {foreach key=key item=period from=$periods}
	      	<option value="{$period.periodId}" {if $period.periodId==$activePeriodId}selected{/if}>
	      	{$period.name}</option>
	      {/foreach}
</select>
<input class="edubutton" type="submit" name="selectGradebookPeriod" value="{tr}Select period{/tr}"/>      
</div>

<div>
{php}
	global $smarty;
	$assignments =$smarty->get_template_vars("assignments");
	$editAssgId = $smarty->get_template_vars("editAssgId");
	$activePeriodId = $smarty->get_template_vars("activePeriodId");
	
	if (count($assignments)>0){
	    $i=1;
	    $gradetable = "<div class=\"gradebook\">";
	    $assignments[]= array(	"name" => "Average"
							);
		$total = count($assignments);
	    //print_r($assignments);
	    foreach ($assignments as $key => $assignment) {
	      $gradetable .= "<div class=\"graderow\">\n";
	      $gradetable .= "<div class=\"firstGradeBookCol\">&nbsp;</div>";
	   
	      for($ii = 1; $ii<=$total ; $ii++){
		      	$style= "gradeeven";
		      	if ($ii%2 == 0){
		      		$style= "gradeodd";
		      	}
	      		if($ii==$i){
	 	      		$gradetable .= "<div class=\"".$style."Assg\">";
			      	$gradetable .= "<a class=\"".$style."link\" href=\"./aulawiki-view_gradebook.php?editAssgId=".$assignment["assignmentId"]."&activePeriodId=".$activePeriodId."\">".$assignment["name"]." (".$assignment["gradeWeight"]."%)</a>";
			      	$gradetable .= "</div>";
			      	break;
	      		}else{
		      		$gradetable .= "<div class=\"$style\">";
	    		  	$gradetable .= "&nbsp;";
	    		  	$gradetable .= "</div>";
	      		}
	      }
	      
	      $gradetable .= "</div>\n";
	      $i++;
		} //End: Assignments headers
		
		
		//Start user grades
		
		$gradebook =$smarty->get_template_vars("gradebook");
		$users =$smarty->get_template_vars("users");
	    $assgAverages = array();
	    foreach ($users as $id => $userdata) {
	      $userid = $userdata["login"];
	      $usergrade = $gradebook[$userid];
	      $gradetable .= "<div class=\"graderow\">\n";
	      $gradetable .= "<div class=\"userGradeBookCol\">".$userdata["name"]."</div>";
		  $i=1;
		  $sum = 0;
		  $numAssgs = 0;
		  foreach($assignments as $assigId=>$assignment){
		    $assgAvg = $assgAverages[$assigId];
		    if ($assgAvg==null || $assgAvg==""){
		    	$assgAvg = array();
		    	$assgAvg["sum"] = 0;
		    	$assgAvg["numStudents"] = 0; 	
		    }      
		  	if ($i%2 == 0){
		  		$gradetable .= "<div class=\"gradeodd2\">";
		  	}else{
		  		$gradetable .= "<div class=\"gradeeven2\">";
		  	}
		  	
		  	if( $i == $total){
				
				//Pintar nota media del alumno
				$average = 0;
				if ($numAssgs > 0){
					$average = (round($sum*10))/10;
				}
		      	$gradetable .= "<b>".$average."</b>";
		      	$assgAvg["sum"] = $assgAvg["sum"] + $average;
		    	$assgAvg["numStudents"] = $assgAvg["numStudents"] + 1;
		    	 	  	
		    }elseif ($usergrade[$assignment["assignmentId"]]["grade"]!=null && $usergrade[$assignment["assignmentId"]]["grade"]!=""){
				//Paint user grade
		    	if ($editAssgId == $assignment["assignmentId"]){ //edit mode
		    		$gradetable .= "<input class=\"inputgrade\" name=\"usergrade-$userid\" type=\"text\" id=\"usergrade-$userid\" value=\"".$usergrade[$assignment["assignmentId"]]["grade"]."\" size=\"4\" maxlength=\"5\">";
		    	}else{ //view mode
		        	$gradetable .= $usergrade[$assignment["assignmentId"]]["grade"];
		        }
		        $tmpGrade = $usergrade[$assignment["assignmentId"]]["grade"];
		        $gradeWeight = $assignment["gradeWeight"];
	
		        $realGrade = ($tmpGrade*$gradeWeight)/100;
		        $sum = $sum + $realGrade;
		        $numAssgs++;
		        $assgAvg["sum"] = $assgAvg["sum"] + $tmpGrade;
		    	$assgAvg["numStudents"] = $assgAvg["numStudents"] + 1; 	  	
		    	
		    }else{ // No grade for the user-assignment
		    	if ($editAssgId == $assignment["assignmentId"]){ //edit mode
		    		$gradetable .= "<input class=\"inputgrade\" name=\"usergrade-$userid\" type=\"text\" id=\"usergrade-$userid\" value=\"-\" size=\"4\" maxlength=\"5\">";
		    	}else{ //view mode
			    	$gradetable .="-";
			    }
		    
		    }
		    $assgAverages[$assigId] = $assgAvg;
		    
	        $gradetable .= "</div>";
	        $i++;
	      }
	      
	      $gradetable .= "</div>\n";
	    }//End: User grades
	
	    //Assignment Average
	    $i = 1;
	    $gradetable .= "<div class=\"graderow\">\n";
	    $gradetable .= "<div class=\"userGradeBookCol\">Average</div>";
		foreach($assignments as $assigId=>$assignment){
		    
		  	if ($i%2 == 0){
		  		$gradetable .= "<div class=\"gradeodd2\"><b>";
		  	}else{
		  		$gradetable .= "<div class=\"gradeeven2\"><b>";
		  	}
		  	if ($assgAverages[$assigId]["numStudents"]>0){
		  		$gradetable .= $assgAverages[$assigId]["sum"]/$assgAverages[$assigId]["numStudents"];
		  	}else{
		  		$gradetable .= "0";
		  	}
	        $gradetable .= "</b></div>";
	        $i++;
	      }
		$gradetable .= "</div>\n";    
	   	$gradetable .= "&nbsp;</div>\n";
		$gradetable .= "&nbsp;</div>\n";
		$gradetable .= "<div class=\"gradebuttons\">";
		$gradetable .= "<input class=\"edubutton\" type=\"submit\" name=\"editGradebook\" value=\"{tr}Save grades{/tr}\"/>";
		$gradetable .= "</div>";
		
		echo $gradetable;
	}else{
		echo "No assignments for the selected period";
	}
{/php}
</form>
