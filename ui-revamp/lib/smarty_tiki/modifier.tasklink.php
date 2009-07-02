<?php
// Martin Hausner
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}
function smarty_modifier_tasklink($taskId,$class_name="link",$offset="0",$sort_mode="priority_desc") {
    global $tikilib, $tasklib, $userlib, $user, $dbTiki, $prefs;

include_once('lib/tasks/tasklib.php');

	$info = $tasklib->get_task($user, $taskId); 
	$mouseover = '';
	if ($prefs['feature_community_mouseover'] == 'y'){
		$description = "";
	
		$my_length = strlen($info[description]);
		$my_pos=0;	
		$my_count=0;	
		$append = '';
		if ( $my_length > 0 ){
		   do {
			$my_count++;
        	$my_pos = strpos($info[description],"\n",($my_pos+1));
		   }while(($my_count <= 15) && ($my_pos!=''));
		}
		if (($my_length >= 1300) || ($my_count >= 16)){
			if($my_count < 15) {
				$my_pos = 1300;
			}
			$description .= substr($info[description],0,min(1300,$my_pos+1));
			$append .= "<br /><center><span class=\'highlight\'>".tra("Text cut here")."</span></center>";
		}
		else {
			$description = $info[description];
		}
			$description =str_replace("\"","\'",str_replace("'","\\'",str_replace("\n","", (str_replace("\r\n", "<br />",$tikilib->parse_data($description)))))).$append;

		$fillin = tra("Task").' '.tra("from").' <b>'.$info['creator'].'</b> '.tra("for").' <b>'.$info['user'].'</b>.<br />'.tra("Priority").': <b>'.$info['priority'].'</b>, (<b>'.$info['percentage'].'%</b>) '.tra('done').'.<br />'; 
		if ($info[start] != 0 ){
			$fillin .= tra("Start date:")." ".$tikilib->date_format("%H:%M -- %d. %e. %Y",$info['start'])."<br />";
		}
		else{
			$fillin .= tra("Start date:")." -<br />";
		}
		if ($info[end]){
			$fillin .= tra("End date:")." ".$tikilib->date_format("%H:%M -- %d. %e. %Y",$info['end'])."<br />";
		}
		else{
			$fillin .= tra("End date:")." -<br />";
		}
	 	$fillin .= "<hr />".$description;
		
		$mouseover = " onmouseover=\"return overlib('<table><tr><td>".$fillin."</td></tr></table>',HAUTO,VAUTO,CAPTION,'<div align=\'center\'>&nbsp; ".tra("Task").":&nbsp;&nbsp;".htmlspecialchars($info['title'])."</div>');\" onmouseout=\"nd()\""; 
	}
	$content = "<a class='".$class_name."'".$mouseover." href='tiki-user_tasks.php?taskId=".$taskId."&amp;tiki_view_mode=view&amp;offset=".$offset."&amp;sort_mode=".$sort_mode."' ";
 	if ($info[status] == 'c'){
		$content .= "style=\"text-decoration:line-through;\"";
	}
	$content .= ">".$info['title']."</a>";
    return $content;
}



?>
