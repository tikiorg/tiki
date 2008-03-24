<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class TemplatesLib extends TikiLib {
	function TemplatesLib($db) {
		$this->TikiLib($db);
	}

	function list_all_templates($offset, $maxRecords, $sort_mode, $find) {
		$bindvars = array();
		if ($find) {
			$bindvars[] = '%' . $find . '%';
			$mid = " where (`content` like ?)";
		} else {
			$mid = "";
		}

		$query = "select `name`,`created`,`templateId` from `tiki_content_templates` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_content_templates` $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$query2 = "select `section` from `tiki_content_templates_sections` where `templateId`=?";
			$result2 = $this->query($query2,array((int)$res["templateId"]));
			$sections = array();
			while ($res2 = $result2->fetchRow()) {
				$sections[] = $res2["section"];
			}
			$res["sections"] = $sections;
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function replace_template($templateId, $name, $content) {
		$bindvars = array($content,$name,(int)$this->now);
		if ($templateId) {
			$query = "update `tiki_content_templates` set `content`=?, `name`=?, `created`=? where `templateId`=?";
			$bindvars[] = (int) $templateId;
		} else {
			$query = "delete from `tiki_content_templates` where `content`=? and `name`=?";
			$this->query($query,array($content,$name),-1,-1,false);
			$query = "insert into `tiki_content_templates`(`content`,`name`,`created`) values(?,?,?)";
		}

		$result = $this->query($query,$bindvars);
		$id = $this->getOne("select max(`templateId`) from `tiki_content_templates` where `created`=? and `name`=?",array((int)$this->now,$name));
		return $id;
	}

	function add_template_to_section($templateId, $section) {
		$this->query("delete from `tiki_content_templates_sections` where `templateId`=? and `section`=?",array((int)$templateId,$section),-1,-1,false);
		$query = "insert into `tiki_content_templates_sections`(`templateId`,`section`) values(?,?)";
		$result = $this->query($query,array((int)$templateId,$section));
	}

	function remove_template_from_section($templateId, $section) {
		$result = $this->query("delete from `tiki_content_templates_sections` where `templateId`=? and `section`=?",array((int)$templateId,$section));
	}

	function template_is_in_section($templateId, $section) {
		$cant = $this->getOne("select count(*) from `tiki_content_templates_sections` where `templateId`=? and `section`=?",array((int)$templateId,$section));
		return $cant;
	}

	function remove_template($templateId) {
		$query = "delete from `tiki_content_templates` where `templateId`=?";
		$result = $this->query($query,array((int)$templateId));
		$query = "delete from `tiki_content_templates_sections` where `templateId`=?";
		$result = $this->query($query,array((int)$templateId));
		return true;
	}
}
global $dbTiki;
$templateslib = new TemplatesLib($dbTiki);

?>
