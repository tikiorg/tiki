<?php
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
}

class MultilingualLib extends TikiLib {
	/** brief add a translation
	  */
	function MultilingualLib($db) {
		parent::TikiLib($db);
	}
	/* @brief add an object and its transaltion set into the set of translations of another one
	 * @param: type = (idem tiki_categ) 'wiki page'...
	 * @param: srcId = id of the source
	 * @param: srcLang = lang of the source
	 * @param: objId = id of the translation
	 * @param: objLang = lang of the translation
	 * @requirment: no translation of the source in this lang must exist
	 */
	function insertTranslation($type, $srcId, $srcLang, $objId, $objLang) {
		$srcTrads = $this->getTrads($type, $srcId);
		$objTrads = $this->getTrads($type, $objId);
		if (!$srcTrads && !$objTrads) {
			$query = "insert into `tiki_translated_objects` (`type`,`objId`,`lang`) values (?,?,?)";
			$this->query($query, array($type, $srcId, $srcLang));
			$query = "insert into `tiki_translated_objects` (`type`,`objId`,`traId`,`lang`) values (?,?,last_insert_id(),?)";
			$this->query($query, array($type, $objId, $objLang));
			return  null;
		}
		elseif (!$srcTrads) {
			if ($this->exist($objTrads, $srcLang))
					return "alreadyTrad";
			$query = "insert into `tiki_translated_objects` (`type`,`objId`,`traId`,`lang`) values (?,?,?,?)";
			$this->query($query, array($type, $srcId, $objTrads[0]['traId'], $srcLang));
			return null;
		}
		elseif (!$objTrads) {
			if ($this->exist($srcTrads, $objLang))
					return "alreadyTrad";
			$query = "insert into `tiki_translated_objects` (`type`,`objId`,`traId`,`lang`) values (?,?,?,?)";
			$this->query($query, array($type, $objId, $srcTrads[0]['traId'], $objLang));
			return null;
		}
		elseif  ($srcTrads[0]['traId'] == $objTrads[0]['traId']) {
			return "alreadySet";
		}
		else {
			foreach ($srcTrads as $t) {
				if ($this->exist($objTrads, $t['lang']))
					return "alreadyTrad";
			}
			$query = "update `tiki_translated_objects`set `traId`=? where `tradId`=?";
			$this->query = $this->query($query, array($srcTrads[0]['traId'], $objTrads[0]['traId']));
			return null;
		}
	}

	/* @brief update the object for the language of a translation set
	 * @param $objId: new object for the translation of $srcId of type $type in the language $objLang
	 */
	function updateTranslation($type, $srcId, $objId, $objLang) {
		$query = "update `tiki_translated_objects` set `objId`=? where `type`=? and `srcId`=? and `lang`=?";
		$this->query($query, array($objId, $type, $srcId, $objLang));
	}

	/** @brief get the translation in a language of an object if exists
	 * @return array(objId, traId)
	 */
	function getTranslation($type, $srcId) {
		$query = "select t2.`objId`, t2.`traId` from `tiki_translated_objects` as t1, `tiki_translated_objects` as t2 where t1.`traId`=t2.`traId` and t1.`type`=? and  t1.`objId`=? and t2.`lang`=?";
		return $this->getOne($query, array($type, $srcId, $objLang));
	}

	function getTrads($type, $objId) {
		$query = "select t2.`traId`, t2.`objId`, t2.`lang` from `tiki_translated_objects` as t1, `tiki_translated_objects` as t2 where t1.`traId`=t2.`traId` and t2.`objId`!= t1.`objId` and t1.`type`=? and  t1.`objId`=?";
		$result = $this->query($query, array($type, $objId));
		$ret = array();
		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}
		return $ret;
	}

	/* @brief gets all the translations of an object
	 * @param type = (idem tiki_categ) 'wiki page'...
	 * @param objId = object Id
	 * @return if long is false: array(objId, lang, langName ) with langName=localized language name
	 * @return if long id true: array(objId, objName, lang, langLongFormat)
	 */
	function getTranslations($type, $objId, $objName, $objLang, $long=false) {
		$query = "select t2.`objId`, t2.`lang` from `tiki_translated_objects` as t1, `tiki_translated_objects` as t2 where t1.`traId`=t2.`traId` and t2.`objId`!= t1.`objId` and t1.`type`=? and  t1.`objId`=?";
		$result = $this->query($query, array($type, $objId));
		$ret = array();
		if ($long) {
			$l = $this->format_language_list(array($objLang));
			$ret0 = array('objId'=>$objId, 'page'=>$objName, 'lang'=> $objLang, 'langName'=>$l[0]['name']);
			while ($res = $result->fetchRow()) {
				$l = $this->format_language_list(array($res['lang']));
				$res['langName'] = $l[0]['name'];
				$res['page'] = $this->get_page_name_from_id($res['objId']);
				$ret[] = $res;
			}
		}
		else {
			$l = $this->format_language_list(array($objLang), 'y');
			$ret0 = array('objId'=>$objId, 'page'=>$objName, 'lang'=> $objLang, 'langName'=>$l[0]['name']);
			while ($res = $result->fetchRow()) {
				$l = $this->format_language_list(array($res['lang']), 'y');
				$res['langName'] = $l[0]['name'];
				$ret[] = $res;
				}
		}
		usort($ret, array('MultilingualLib', 'compare_lang'));
		array_unshift($ret, $ret0);
		return $ret;
	}

	/* @brief sort function on langName string
	 */
	function compare_lang($l1, $l2) {
		return strcmp($l1['langName'], $l2['langName']);
	}

	/* @brief: update lang in all tiki pages
	 */
	function updatePageLang($objId, $lang) {
		$query = "update `tiki_pages` set `lang`=? where `page_id`=?";
		$this->query($query,array($lang, $objId));
		$query = "update `tiki_translated_objects` set `lang`=? where `objId`=?";
		$this->query($query,array($lang, $objId));
	}

	/* @brief: detach one translation
	 */
	function detachTranslation($type, $objId) {
		$query = "delete from `tiki_translated_objects` where `type`= ? and `objId`=?";
		$this->query($query,array($type, $objId));
//@@a faire si 1 obj
	}
	
	/* @brief : test si lang exists in a tab of langs
	 */
	function exist($tab, $lang) {
		foreach ($tab as $t) {
			if ($t['lang'] == $lang)
				return true;
		}
		return false;
	}
}
$multilinguallib = new MultilingualLib($dbTiki);
?>