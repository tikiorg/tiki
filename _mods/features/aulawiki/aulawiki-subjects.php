<?php
/*
* @author: Javier Reyes Gomez (jreyes@escire.com)
* @date: 27/01/2006
* @copyright (C) 2006 Javier Reyes Gomez (eScire.com)
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*/
require_once('tiki-setup.php');
require_once('lib/aulawiki/subjectlib.php');
require_once('lib/aulawiki/studieslib.php');

if ($aulawiki_p_admin_subjects != 'y') {
	$smarty->assign('msg', tra("Permission denied"));
	$smarty->display("error.tpl");
	die;
}

$subjectLib = new SubjectLib($dbTiki);
$studiesLib = new StudiesLib($dbTiki);

if (isset($_REQUEST["studies"]))
	echo "ESTUDIOS ".$_REQUEST["studies"];

$idAsg = "";
if(isset($_REQUEST["send"])) {
	
	if (isset($_REQUEST["id"]) && ($_REQUEST["id"]!="" )){ 
		$subjectLib->update_subject($_REQUEST["id"],$_REQUEST["code"],$_REQUEST["name"],$_REQUEST["desc"],1,$_REQUEST["studies"]);
	}else{ 
		$subjectLib->add_subject($_REQUEST["code"],$_REQUEST["name"],$_REQUEST["desc"],1,$_REQUEST["studies"]);
	}
/*	
	if (isset($_REQUEST["grupos"])){
		$subjectLib->add_grupos($_REQUEST["idAsignatura"],$_REQUEST["grupos"]);
	}else{
		$subjectLib->add_grupos($_REQUEST["idAsignatura"],null);
	}
	$gruposTmp = "";
	if (isset($_REQUEST["grupos"])){
		$gruposTmp = $_REQUEST["grupos"];
	}
	//Categorizar asignatura
	$cat_type='asignatura';
	$cat_objid = $_REQUEST["idAsignatura"];
	$cat_desc = $_REQUEST["desc"];
	$cat_name = $_REQUEST["idAsignatura"];
	$cat_href="./aulawiki-homeasg.php?idAsignatura=".$_REQUEST["idAsignatura"];
	include_once("categorize.php");
	altaRecursosAsg($_REQUEST["idAsignatura"],$_REQUEST["nomAsignatura"],$_REQUEST["desc"],$gruposTmp,$dbTiki,$tikilib,$userlib,$categlib);

	header ("location: aulawiki-asignatura.php");
	die;
	*/
}else if(isset($_REQUEST["edit"])) {
	$subject = $subjectLib->get_subject_by_id($_REQUEST["edit"]);
	//$idAsg = $asignatura['subjectId'];
	//$smarty->assign_by_ref('gruposAll', $gruposAll);
}else if(isset($_REQUEST["delete"])) {
	$subjectLib->del_subject($_REQUEST["delete"]);
	//borraRecursosAsg($idAsg,$dbTiki,$tikilib,$userlib);
	header ("location: aulawiki-subjects.php");
	die;
}
/*
$cat_type = 'asignatura';
$cat_objid = $idAsg;
include_once ("categorize_list.php");



$gruposAll = $subjectLib->generaGrupos($idAsg);
$smarty->assign_by_ref('gruposAll', $gruposAll);
*/
if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'name_desc';
} else {
	$sort_mode = 'name_desc';
	$sort_mode = $_REQUEST["sort_mode"];
}

$smarty->assign_by_ref('sort_mode', $sort_mode);

if (!isset($_REQUEST["numrows"])) {
	$numrows = $maxRecords;
} else {
	$numrows = $_REQUEST["numrows"];
}


$smarty->assign_by_ref('numrows', $numrows);

if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}

$smarty->assign_by_ref('offset', $offset);

if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}

$smarty->assign('find', $find);
echo "antes del get $sort_mode";
$subjectsData = $subjectLib->get_subjects_list($offset, $numrows, $sort_mode, $find);
$cant_pages = ceil($subjectsData["cant"] / $numrows);
$smarty->assign_by_ref('cant_pages', $cant_pages);
$smarty->assign('actual_page', 1 + ($offset / $numrows));

if ($subjectsData["cant"] > ($offset + $numrows)) {
	$smarty->assign('next_offset', $offset + $numrows);
} else {
	$smarty->assign('next_offset', -1);
}

// If offset is > 0 then prev_offset
if ($offset > 0) {
	$smarty->assign('prev_offset', $offset - $numrows);
} else {
	$smarty->assign('prev_offset', -1);
}

//Get studies
$studiesData = $studiesLib->get_studies_list();

$smarty->assign_by_ref('studies', $studiesData["data"]);
$smarty->assign_by_ref('subjects', $subjectsData["data"]);
$smarty->assign_by_ref('subject',$subject);
$smarty->assign('mid','aulawiki-subjects.tpl');
$smarty->display('tiki.tpl');


function borraRecursosAsg($asgId,$dbTiki,$tikilib,$userlib){
	include_once ('lib/structures/structlib.php');
	include_once('lib/filegals/filegallib.php');
	include_once ('lib/blogs/bloglib.php');
	include_once ('lib/calendar/calendarlib.php');	
	include_once ("lib/commentslib.php");
	include_once ("lib/imagegals/imagegallib.php");
	include_once ('lib/categories/categlib.php');
	if (!isset($asgId) || $asgId=="")
		return;
	
	//Borrar categoria de la asignatura
	$categoriesData = $categlib->list_all_categories(0, -1, 'name_asc', $asgId, '', '');
	$categories =$categoriesData["data"];
	$asgCatId = "";
	for ($i=0; $i < count($categories) ; $i++){
		if($categories[$i]["name"] == $asgId){ //Categoria de la asignatura
			$asgCatId = $categories[$i]["categId"];
		}
	}
	
	//Borrar galeria de ficheros
	if (isset($asgCatId) && $asgCatId!="")
	$categlib->remove_category($asgCatId);
	
	$galId='';
	$galerias = $filegallib->list_file_galleries(0,-1,'name_desc', 'admin', $asgId );
	if (isset($galerias) && count($galerias['data'])>0){
		$galeria = $galerias['data'][0];
		$galId = $galeria['galleryId'];
	}
	$galId = $filegallib->remove_file_gallery($galId);
	
	//Borrar galería de imagenes
	$galimgId='';
	$galImgs = $tikilib->list_galleries(0, -1, "name_desc", 'admin', $asgId);
	if (isset($galImgs) && count($galImgs['data'])>0){
		$galImg = $galImgs['data'][0];
		$galimgId = $galImg['galleryId'];
	}
	
	$galimgId = $imagegallib->remove_gallery($galimgId);
	
	//Borrar Foro
	$commentslib = new Comments($dbTiki);
	$channels = $commentslib->list_forums(0, -1, 'name_desc', $asgId);
	$forumId='';
	if (isset($channels) && count($channels['data'])>0){
		$forumId = $channels['data'][0]['forumId'];
	}
	$forumId = $commentslib->remove_forum($forumId);
	
	//Borrar blogProblemas
	$blogs = $tikilib->list_blogs(0, -1, 'created_desc',$asgId."Problemas");
	$blogId ='';
	if (isset($blogs) && count($blogs['data'])>0){
		$blog = $blogs['data'][0];
		$blogId	= $blog['blogId'];
	}
	$blogId	= $bloglib->remove_blog($blogId);
	
	
	//Borrar grupos
	$groups = $userlib->get_groups(0, -1, 'groupName_desc', $asgId);
	foreach($groups['data'] as $key=>$group){
		$userlib->remove_group($group["groupName"]);
	}
	
	
	//Borrar blogs de Seguimiento
	$blogs = $tikilib->list_blogs(0, -1, 'created_desc',$asgId);
	foreach($blogs['data'] as $key=>$blog){
		$blogId = $blog['blogId'];
		$bloglib->remove_blog($blogId);
	}
	
	
	
	
}

function altaRecursosAsg($asgId,$asgNom,$asgDesc,$grupos,$dbTiki,$tikilib,$userlib,$categlib){
	include_once ('lib/structures/structlib.php');
	include_once('lib/filegals/filegallib.php');
	include_once ('lib/blogs/bloglib.php');
	include_once ('lib/calendar/calendarlib.php');	
	include_once ("lib/commentslib.php");
	include_once ("lib/imagegals/imagegallib.php");
	//include_once ('lib/categories/categlib.php');

	
	$commentslib = new Comments($dbTiki);

	//Crear categoria de la asignatura
	$categoriesData = $categlib->list_all_categories(0, -1, 'name_asc', $asgId, '', '');
	$categories =$categoriesData["data"];
	$asgCatId = "";
	$cursoCatId = "";
	for ($i=0; $i < count($categories) ; $i++){
		if($categories[$i]["name"] == $asgId){ //Categoria de la asignatura
			$asgCatId = $categories[$i]["categId"];
		}
	}

	//Recuperamos la categoria de la asignatura para colgar de ella una categoria de la asg.
	$catsAsg = $categlib->get_object_categories("asignatura", $asgId);
	$cursoCatId = $catsAsg[0];
	
	if (isset($asgCatId) && $asgCatId!=""){
		$categlib->update_category($asgCatId, $asgId, $asgDesc, $cursoCatId);
	}else{
		 $categlib->add_category($cursoCatId, $asgId, $asgDesc);
		$categoriesData = $categlib->list_all_categories(0, -1, 'name_asc', $asgId, '', '');
		$asgCatId = $categoriesData["data"][0]["categId"];

	}


	//Alta de estructura curriculo
	$structure_id = $structlib->s_create_page(null, null , $asgId."-Contenidos", $asgId."-Contenidos");
	//Cannot create a structure if a structure already exists
	if (!isset($structure_id)) {
		//echo "La estructura ya existe";
	}

	//Categorizar pagina principal del curriculo
	$categlib->uncategorize_object("wiki page", $asgId."-Contenidos");
	$idCatObj = $categlib->add_categorized_object("wiki page", $asgId."-Contenidos", "P&aacute;gina principal de contenidos de ".$asgNom, $asgId."-Contenidos","./tiki-index.php?page=".$asgId."-Contenidos" );
	$categlib->categorize($idCatObj, $asgCatId);

	
	//Alta de galeria de ficheros
	$galId='';
	$galerias = $filegallib->list_file_galleries(0,-1,'name_desc', 'admin', $asgId."-Documentos" );
	if (isset($galerias) && count($galerias['data'])>0){
		$galeria = $galerias['data'][0];
		$galId = $galeria['galleryId'];
	}
	$galId = $filegallib->replace_file_gallery($galId, $asgId."-Documentos" , "Documentos de ".$asgDesc, "admin", 15, 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 2024);
	
	//Categorizar galeria de ficheros
	$categlib->uncategorize_object("file gallery", $galId);
	$idCatObj = $categlib->add_categorized_object("file gallery", $galId, "Documentos de ".$asgNom, $asgId."-Documentos","./tiki-list_file_gallery.php?galleryId=$galId" );
	$categlib->categorize($idCatObj, $asgCatId);

	//Alta galería de imagenes
	$galimgId='';
	$galImgs = $tikilib->list_galleries(0, -1, "name_desc", 'admin', $asgId."-Imagenes");
	if (isset($galImgs) && count($galImgs['data'])>0){
		$galImg = $galImgs['data'][0];
		$galimgId = $galImg['galleryId'];
	}
	
	$galimgId = $imagegallib->replace_gallery($galimgId, $asgId."-Imagenes", "Im&aacute;genes de ".$asgNom, '', "admin", 3, 4, 80, 80, 'y', 'y');
		
	//Categorizar galeria de imágenes
	$categlib->uncategorize_object("image gallery", $galimgId);
	$idCatObj = $categlib->add_categorized_object("image gallery", $galimgId, "Im&aacute;genes de ".$asgNom, $asgId."-Imagenes","./tiki-browse_gallery.php?galleryId=".$galimgId );
	$categlib->categorize($idCatObj, $asgCatId);


	//Alta de Foro
	$channels = $commentslib->list_forums(0, -1, 'name_desc', $asgId."-Foro");
	$forumId='';
	if (isset($channels) && count($channels['data'])>0){
		$forumId = $channels['data'][0]['forumId'];
	}
	$forumId = $commentslib->replace_forum($forumId, $asgId."-Foro", "Foro de ".$asgNom ,
		'n', 120, 'admin', '', 'n',
		'n', 1200,
		'n', 1200, 20, 'commentDate_desc', 'commentDate_desc',
		'Asignaturas', 'y', 'y', 'n',
		'y', 'y', 'n',
		'y', '',
		110, '', '', 
		'',
		'',
		'n', 'y', 'n', 'n', 'n',
		'n', 'n', 'n', 'queue_anon',
		'Profesores', '', 'n', 'att_no',
		'db', '', 1000000,0);
	
	//Categorizar galeria de imágenes
	$categlib->uncategorize_object("forum", $forumId);
	$idCatObj = $categlib->add_categorized_object("forum", $forumId, "Foro de ".$asgNom, $asgId."-Foro","./tiki-view_forum.php?forumId=".$forumId );
	$categlib->categorize($idCatObj, $asgCatId);

	
	
	//Alta blogProblemas
	$blogs = $tikilib->list_blogs(0, -1, 'created_desc',$asgId."-Diario");
	$blogId ='';
	if (isset($blogs) && count($blogs['data'])>0){
		$blog = $blogs['data'][0];
		$blogId	= $blog['blogId'];
	}
	$head = '{include file="aulawiki-blogProblemas.tpl"}';
	$blogId	= $bloglib->replace_blog($asgId."-Diario","Diario de contenidos ".$asgNom, "admin", 'y',10,$blogId,$head, 'y','y','y');
	
	//Categorizar blog de problemas
	$categlib->uncategorize_object("blog", $blogId);
	$idCatObj = $categlib->add_categorized_object("blog", $blogId, "Diario de contenidos ".$asgNom, $asgId."-Diario","tiki-view_blog.php?blogId=".$blogId );
	$categlib->categorize($idCatObj, $asgCatId);
	
	
	$userlib->add_group($asgId."PROF","Profesores de la asignatura ".$asgId,'');
	$userlib->remove_inclusion($asgId."PROF","Profesores");
	$userlib->group_inclusion($asgId."PROF", "Profesores");
	
	if (isset($grupos) && $grupos!=""){
		foreach ($grupos as $letraGrp){
			//Blog de Seguimiento
			$blogs = $tikilib->list_blogs(0, -1, 'created_desc',$asgId.$letraGrp."-Seguimiento");
			$blogId ='';
			if (isset($blogs) && count($blogs['data'])>0){
				$blog = $blogs['data'][0];
				$blogId	= $blog['blogId'];
			}
			$head = '{include file="aulawiki-blogHead.tpl"}';
			$blogId	= $bloglib->replace_blog($asgId.$letraGrp."-Seguimiento","Seguimiento del curso de ".$asgDesc." Grupo ".$letraGrp, "admin", 'y',10,$blogId,$head, 'y','y','y');

			//Categorizar blog de seguimiento
			$categlib->uncategorize_object("blog", $blogId);
			$idCatObj = $categlib->add_categorized_object("blog", $blogId, "Seguimiento del curso de ".$asgDesc." Grupo ".$letraGrp,$asgId.$letraGrp."-Seguimiento","tiki-view_blog.php?blogId=".$blogId );
			$categlib->categorize($idCatObj, $asgCatId);
			//Crear calendario de grupo
			$nombreGrupo = substr($asgId,4).$letraGrp;
			$calendarioData = $calendarlib->list_calendars(0,-1, 'created_desc', $nombreGrupo."-Calendario");
			$calendarioId = '';
			foreach($calendarioData['data'] as $key=>$val){
				$calendario = $val;
				$calendarioId = $calendario['calendarId'];
			}
			$customflags["customlanguages"] = 'n';
			$customflags["customlocations"] = 'y';
			$customflags["customcategories"] = 'y';
			$customflags["custompriorities"] = 'y';
			$calendarioId = $calendarlib->set_calendar($calendarioId,'admin',$nombreGrupo."-Calendario","Calendario del grupo ".$nombreGrupo,$customflags);
			//Categorizar calendario
			$categlib->uncategorize_object("calendar", $calendarioId);
			$idCatObj = $categlib->add_categorized_object("calendar", $calendarioId, "Calendario del grupo ".$nombreGrupo,$nombreGrupo."-Calendario","tiki-calendar.php?calendarId=".$calendarioId."&calIds[]=".$calendarioId."&viewmode=month");
			$categlib->categorize($idCatObj, $asgCatId);
	
			//Crear Grupos de usuarios
			$userlib->add_group($nombreGrupo."ALUM","Alumnos del grupo ".$nombreGrupo,'');
			$userlib->add_group($nombreGrupo."TUTOR","Tutor del grupo ".$nombreGrupo,'');
			$userlib->add_group($asgId.$letraGrp."ALUM","Alumnos de la asignatura ".$asgId." grupo ".$nombreGrupo,'');
			$userlib->add_group($asgId.$letraGrp."PROF","Profesores de la asignatura ".$asgId." grupo ".$nombreGrupo,'');
			$userlib->remove_inclusion($nombreGrupo."ALUM", $asgId.$letraGrp."ALUM");
			$userlib->group_inclusion($nombreGrupo."ALUM", $asgId.$letraGrp."ALUM");
			$userlib->remove_inclusion($asgId.$letraGrp."PROF", $asgId."PROF");
			$userlib->group_inclusion($asgId.$letraGrp."PROF", $asgId."PROF");
			
		}
	}
}
?>