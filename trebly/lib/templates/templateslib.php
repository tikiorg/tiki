<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class TemplatesLib extends TikiLib
{
	function list_templates($section, $offset, $maxRecords, $sort_mode, $find) {
		$bindvars = array($section);
		if ($find) {
			$findesc = '%'.$find.'%';
			$mid = " and (`content` like ?)";
			$bindvars[] = $findesc;
		} else {
			$mid = "";
		}
		$query = "select `name` ,`created`,tcts.`templateId` from `tiki_content_templates` tct, `tiki_content_templates_sections` tcts ";
		$query.= " where tcts.`templateId`=tct.`templateId` and `section`=? $mid order by ".$this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_content_templates` tct, `tiki_content_templates_sections` tcts ";
		$query_cant.= "where tcts.`templateId`=tct.`templateId` and `section`=? $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();
		while ($res = $result->fetchRow()) {
			$query2 = "select `section`  from `tiki_content_templates_sections` where `templateId`=?";

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

	function get_template($templateId, $lang = null) {
		// echo "<pre>-- templateslib.get_template: \$templateId='$templateId', \$lang='$lang'</pre>\n";
		$query = "select * from `tiki_content_templates` where `templateId`=?";
		// echo "<pre>-- templateslib.get_template: \$query='$query', \$lang='$lang'</pre>\n";		
		$result = $this->query($query,array((int)$templateId));
		if (!$result->numRows()) return false;
		$res = $result->fetchRow();


		if( $res['template_type'] == 'page' ) {
			if( substr( $res['content'], 0, 5 ) == 'page:' ) {
				$res['page_name'] = substr( $res['content'], 5 );
				$res['content'] = $this->get_template_from_page( $res['page_name'], $lang );
			}
		} else {
			$res['page_name'] = '';
		}


		return $res;
	}

	function get_parsed_template($templateId, $lang = null, $format = 'yaml') {
		$res = $this->get_template($templateId, $lang);

		if ( !$res ) {
			return false;
		}

		switch ( $format ) {
			case 'yaml':
				require_once( 'lib/profilelib/profilelib.php' );
				require_once( 'lib/profilelib/installlib.php' );

				$content =
				"{CODE(caption=>YAML)}objects:\n".
				" -\n".
				"  type: file_gallery\n".
				"  data:\n".
				"   ". implode("\n   ", explode("\n", $res['content'])) .
				"{CODE}";

				$profile = Tiki_Profile::fromString( $content, $res['name'] );
				$installer = new Tiki_Profile_Installer();
				$objects = $profile->getObjects();

				if ( isset($objects[0]) ) {
					$data = $installer->getInstallHandler( $objects[0] )->getData();
					unset($data['galleryId'], $data['parentId'], $data['name'], $data['user']);
					$res['content'] = $data;
				} else {
					$res['content'] = array();
				}

				break;
		}

		return $res;
	}

	private function get_template_from_page( $page, $lang ) {
		global $prefs;
		$info = $this->get_page_info( $page );

		if( $prefs['feature_multilingual'] == 'y' ) {
			global $multilinguallib; require_once 'lib/multilingual/multilinguallib.php';

			if( $lang && $info['lang'] && $lang != $info['lang'] ) {
				$bestLangPageId = $multilinguallib->selectLangObj( 'wiki page', $info['page_id'], $lang );

				if ($info['page_id'] != $bestLangPageId) {
					$info = $this->get_page_info_from_id($bestLangPageId);
				}
			}
		}

		if( $info ) {
			return TikiLib::htmldecode( $info['data'] );
		}
	}

	function list_all_templates($offset, $maxRecords, $sort_mode, $find) {
		$bindvars = array();
		if ($find) {
			$bindvars[] = '%' . $find . '%';
			$bindvars[] = '%' . $find . '%';
			$mid = " where (`name` like ?) or (`content` like ?)";
		} else {
			$mid = "";
		}

		$query = "select `name`,`created`,`templateId` from `tiki_content_templates` $mid order by ".$this->convertSortMode($sort_mode);
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

	function replace_template($templateId, $name, $content, $type = 'static') {
		$bindvars = array($content,$name,(int)$this->now, $type);
		if ($templateId) {
			$query = "update `tiki_content_templates` set `content`=?, `name`=?, `created`=?, `template_type`=? where `templateId`=?";
			$bindvars[] = (int) $templateId;
		} else {
			$query = "delete from `tiki_content_templates` where `content`=? and `name`=?";
			$this->query($query,array($content,$name),-1,-1,false);
			$query = "insert into `tiki_content_templates`(`content`,`name`,`created`,`template_type`) values(?,?,?,?)";
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
$templateslib = new TemplatesLib;
