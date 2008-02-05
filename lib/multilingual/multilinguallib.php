<?php
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
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
		global $prefs;
		if ($type == 'wiki page' && $prefs['feature_wikiapproval'] == 'y') {
			$srcPageName = $this->get_page_name_from_id($srcId);
			$objPageName = $this->get_page_name_from_id($objId);
			if (substr($srcPageName, 0, strlen($prefs['wikiapproval_prefix'])) != $prefs['wikiapproval_prefix']
				&& substr($objPageName, 0, strlen($prefs['wikiapproval_prefix'])) != $prefs['wikiapproval_prefix']) {
				$srcStagingPageName = $prefs['wikiapproval_prefix'] . $srcPageName;
				$objStagingPageName = $prefs['wikiapproval_prefix'] . $objPageName;
				if ($this->page_exists($srcStagingPageName) && $this->page_exists($objStagingPageName)) {
					$this->insertTranslation($type, $this->get_page_id_from_name($srcStagingPageName), $srcLang, $this->get_page_id_from_name($objStagingPageName), $objLang);
				}
			}
		}
		$srcTrads = $this->getTrads($type, $srcId);
		$objTrads = $this->getTrads($type, $objId);
		if (!$srcTrads && !$objTrads) {
			$query = "insert into `tiki_translated_objects` (`type`,`objId`,`lang`) values (?,?,?)";
			$this->query($query, array($type, $srcId, $srcLang));
			$query = "select max(`traId`) from `tiki_translated_objects` where `type`=? and `objId`=?";
			$tmp_traId = $this->getOne($query, array( $type, $srcId ) );
			$query = "insert into `tiki_translated_objects` (`type`,`objId`,`traId`,`lang`) values (?,?,?,?)";
			$this->query($query, array($type, $objId, $tmp_traId, $objLang));
			//last_insert_id is not postgres compatible
			//$query = "insert into `tiki_translated_objects` (`type`,`objId`,`traId`,`lang`) values (?,?,last_insert_id(),?)";
			//$this->query($query, array($type, $objId, $objLang));
			return  null;
		}
		elseif (!$srcTrads) {
			if ($this->exist($objTrads, $srcLang, 'lang'))
					return "alreadyTrad";
			$query = "insert into `tiki_translated_objects` (`type`,`objId`,`traId`,`lang`) values (?,?,?,?)";
			$this->query($query, array($type, $srcId, $objTrads[0]['traId'], $srcLang));
			return null;
		}
		elseif (!$objTrads) {
			if ($this->exist($srcTrads, $objLang, 'lang'))
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
				if ($this->exist($objTrads, $t['lang'], 'lang'))
					return "alreadyTrad";
			}
			$query = "update `tiki_translated_objects`set `traId`=? where `traId`=?";
			$this->query = $this->query($query, array($srcTrads[0]['traId'], $objTrads[0]['traId']));
			return null;
		}
	}

	/* @brief update the object for the language of a translation set
	 * @param $objId: new object for the translation of $srcId of type $type in the language $objLang
	 */
	function updateTranslation($type, $srcId, $objId, $objLang) {
		global $prefs;
		if ($type == 'wiki page' && $prefs['feature_wikiapproval'] == 'y') {
			$srcPageName = $this->get_page_name_from_id($srcId);
			$objPageName = $this->get_page_name_from_id($objId);
			if (substr($srcPageName, 0, strlen($prefs['wikiapproval_prefix'])) != $prefs['wikiapproval_prefix']
				&& substr($objPageName, 0, strlen($prefs['wikiapproval_prefix'])) != $prefs['wikiapproval_prefix']) {
				$srcStagingPageName = $prefs['wikiapproval_prefix'] . $srcPageName;
				$objStagingPageName = $prefs['wikiapproval_prefix'] . $objPageName;
				if ($this->page_exists($srcStagingPageName) && $this->page_exists($objStagingPageName)) {
					$this->updateTranslation($type, $this->get_page_id_from_name($srcStagingPageName), $this->get_page_id_from_name($objStagingPageName), $objLang);
				}
			}
		}
		$query = "update `tiki_translated_objects` set `objId`=? where `type`=? and `srcId`=? and `lang`=?";
		$this->query($query, array($objId, $type, $srcId, $objLang));
	}

	/** @brief get the translation in a language of an object if exists
	 * @return array(objId, traId)
	 */
	function getTranslation($type, $srcId, $objLang) {
		$query = "select t2.`objId`, t2.`traId` from `tiki_translated_objects` as t1, `tiki_translated_objects` as t2 where t1.`traId`=t2.`traId` and t1.`type`=? and  t1.`objId`=? and t2.`lang`=?";
		return $this->getOne($query, array($type, $srcId, $objLang));
	}

	function getTrads($type, $objId) {
		$query = "select t2.`traId`, t2.`objId`, t2.`lang` from `tiki_translated_objects` as t1, `tiki_translated_objects` as t2 where t1.`traId`=t2.`traId` and t1.`type`=? and  t1.`objId`=?";
		$result = $this->query($query, array($type, (string) $objId));
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
		if ($type == 'wiki page') {
			$query = "select t2.`objId`, t2.`lang`, p.`pageName`as `objName` from `tiki_translated_objects` as t1, `tiki_translated_objects` as t2 LEFT JOIN `tiki_pages` p ON p.`page_id`=t2.`objId` where t1.`traId`=t2.`traId` and t2.`objId`!= t1.`objId` and t1.`type`=? and  t1.`objId`=?";
		}
		elseif ($long) {
			$query = "select t2.`objId`, t2.`lang`, a.`title` as `objName` from `tiki_translated_objects` as t1, `tiki_translated_objects` as t2, `tiki_articles` as a where t1.`traId`=t2.`traId` and t2.`objId`!= t1.`objId` and t1.`type`=? and  t1.`objId`=? and a.`articleId`=t2.`objId`";
		}
		else {
			$query = "select t2.`objId`, t2.`lang` from `tiki_translated_objects` as t1, `tiki_translated_objects` as t2 where t1.`traId`=t2.`traId` and t2.`objId`!= t1.`objId` and t1.`type`=? and  t1.`objId`=?";
		}
		$result = $this->query($query, array($type, $objId));
		$ret = array();
		if ($long) {
			$l = $this->format_language_list(array($objLang));
			$ret0 = array('objId'=>$objId, 'objName'=>$objName, 'lang'=> $objLang, 'langName'=>$l[0]['name']);
			while ($res = $result->fetchRow()) {
				$l = $this->format_language_list(array($res['lang']));
				$res['langName'] = $l[0]['name'];
				$ret[] = $res;
			}
		}
		else {
			$l = $this->format_language_list(array($objLang), 'y');
			$ret0 = array('objId'=>$objId, 'objName'=>$objName, 'lang'=> $objLang, 'langName'=>$l[0]['name']);
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
	function updatePageLang($type, $objId, $lang, $optimisation = false) {
		if ($this->getTranslation($type, $objId, $lang))
			return 'alreadyTrad';
		if (!$optimisation) {
			if ($type == 'wiki page')
				$query = "update `tiki_pages` set `lang`=? where `page_id`=?";
			else
				$query = "update `tiki_articles` set `lang`=? where `articleId`=?";
			$this->query($query,array($lang, $objId));
		}

		$query = "update `tiki_translated_objects` set `lang`=? where `objId`=?";
		$this->query($query,array($lang, $objId));
		return null;
	}

	/* @brief: detach one translation
	 */
	function detachTranslation($type, $objId) {
		global $prefs;
		if ($type == 'wiki page' && $prefs['feature_wikiapproval'] == 'y') {			
			$objPageName = $this->get_page_name_from_id($objId);
			if (substr($objPageName, 0, strlen($prefs['wikiapproval_prefix'])) != $prefs['wikiapproval_prefix']) {				
				$objStagingPageName = $prefs['wikiapproval_prefix'] . $objPageName;
				if ($this->page_exists($objStagingPageName)) {
					$this->detachTranslation($type, $this->get_page_id_from_name($objStagingPageName));
				}
			}
		}
		$query = "delete from `tiki_translated_objects` where `type`= ? and `objId`=?";
		$this->query($query,array($type, $objId));
//@@TODO: delete the set if only one remaining object - not necesary but will clean the table
	}
	
	/* @brief : test if val exists in a list of objects
	 */
	function exist($tab, $val, $col) {
		foreach ($tab as $t) {
			if ($t[$col] == $val)
				return true;
		}
		return false;
	}

        function getSystemLanguage(){
              
              $query = "select `lang` from `tiki_languages`";
              $result=$this->query($query);
              $languages = array();
              while ($row = $result->fetchRow())
                      $languages[] = $row["lang"];
              return $languages;
        }


	/* @brief : returns an ordered list of prefered languages
	 * @param $langContext: optional the language the user comes from
	 */
	function preferedLangs($langContext = null,$include_browser_lang=TRUE) {
		global $user, $prefs, $tikilib;
		$langs = array();

		if ($langContext) {
			$langs[] = $langContext;
			if (strchr($langContext, "-")) // add en if en-uk
				$langs[] = $this->rootLang($langContext);
		}
		
		if ($prefs['language'] && !in_array($prefs['language'], $langs)) {
			$langs[] = $prefs['language'];
			$l = $this->rootLang($prefs['language']);
			if (!in_array($l, $langs))
				$langs[] = $l;
		}

		if (isset($prefs['read_language'])) {
			$tok = strtok($prefs['read_language'], ' ');

			while (false !== $tok) {
				if (!in_array($tok, $langs) )
					$langs[] = $tok;
				$l = $this->rootLang($tok);
				if (!in_array($l, $langs))
					$langs[] = $l;

				$tok = strtok(' ');
			}
		}

		if (($include_browser_lang)&&(isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))) {
			$ls = preg_split('/\s*,\s*/', preg_replace('/;q=[0-9.]+/','',$_SERVER['HTTP_ACCEPT_LANGUAGE'])); // browser
			foreach ($ls as $l) {
				if (!in_array($l, $langs)) {
					$langs[] = $l;
					$l = $this->rootLang($l);
					if (!in_array($l, $langs))
						$langs[] = $l;
				}
			}
		}

		$l = $prefs['site_language'];
		if (!in_array($l, $langs)) {
			$langs[] = $l; // site language
			$l = $this->rootLang($l);
			if (!in_array($l, $langs))
				$langs[] = $l;
		}
		return $langs;	
	}
	/* @brief : return the root language ex: en-uk returns en
	 */
	function rootLang($lang) {
		return ereg_replace("(.*)-(.*)", "\\1", $lang);
	}

	/* @brief : fitler a list of object to have only one objet in the set of translations with the best language
	 */
	function selectLangList($type, $listObjs, $langContext = null) {
		if (!$listObjs || count($listObjs) <= 1)
			return $listObjs;
		$langs = $this->preferedLangs($langContext);
//echo "<pre>";print_r($langs);echo "</pre>";
		$max = count($listObjs);
		for ($i = 0; $i < $max; ++$i) {
			if (!isset($listObjs[$i]) || !isset($listObjs[$i]['lang']))
				continue; // previously withdrawn or no language
			if ($type == 'wiki page')
				$objId = $listObjs[$i]['page_id'];
			else if ($type == 'objId')
				$objId = $listObjs[$i]['objId'];
			else
				$objId = $listObjs[$i]['articleId'];
			$trads = $this->getTrads($type, $objId);
			if (!$trads)
				continue;
			for ($j = $i + 1; $j < $max; ++$j) {
				if (!isset($listObjs[$j]))
					continue;
				if ($type == 'wiki page')
					$objId2 = $listObjs[$j]['page_id'];
				else if ($type == 'objId')
					$objId2 = $listObjs[$j]['objId'];
				else
					$objId2 = $listObjs[$j]['articleId'];
				if ($this->exist($trads, $objId2, 'objId')) {
					$iord = array_search($listObjs[$i]['lang'] , $langs);
					if (!$iord && strchr($listObjs[$i]['lang'], "-"))
						$iord = array_search($this->rootLang($listObjs[$i]['lang']), $langs);
					$jord = array_search($listObjs[$j]['lang'] , $langs);
					if (!$jord && strchr($listObjs[$j]['lang'], "-"))
						$jord = array_search($this->rootLang($listObjs[$j]['lang']), $langs);
					if ($jord === false) {
						unset($listObjs[$j]); // not in the pref langs
					}
					else if ($iord === false) {
						unset($listObjs[$i]);
						break;
					}
					else if ($iord > $jord) {
						unset($listObjs[$i]);
						break;
					}
					else {
						unset($listObjs[$j]);
					}
					// if none in the pref lang, pick the first (sorted by date)
				}
			}
		}
		return array_merge($listObjs);// take away the unset rows
	}

	/* @brief : select the object with the best language from another object
	 */
	function selectLangObj($type, $objId, $langContext = null) {
		$trads = $this->getTrads($type, $objId);
		if (!$trads)
			return $objId;
		$langs = $this->preferedLangs($langContext);
		foreach ($langs as $l) {
			foreach ($trads as $trad) {
				if ($trad['lang'] == $l)
					return $trad['objId'];
			}
		}
		return $objId;
	}
	function getInteractiveTag($content){
		
		if (!isset($_SESSION['interactive_translation_mode'])||($_SESSION['interactive_translation_mode']=='off')|| (strlen($content)<2) )
			return "";

		$urcontent=urlencode($content);
		$ret= "<span  onclick=\"window.open('tiki-interactive_trans.php?content=$urcontent','traduction','toolbar=no,location=no,scrollbars=yes,directories=no,status=no,menubar=no,resizable=no,copyhistory=no,width=600,height=300,left=10,top=10');return false;\">°</span>";
		return $ret;
	}

	function getSupportedTranslationBitFlags() {
		return array( 'critical' );
	}

	function normalizeTranslationBitFlags( $flags ) {
		if( !is_array( $flags ) )
			$flags = explode( ',', $flags );

		// Add supported flags as they get added
		return array_intersect( $flags, $this->getSupportedTranslationBitFlags() );
	}

	function createTranslationBit($type, $objId, $version = 0, $flags = array()) {
		if( $type != 'wiki page' )
			die('Translation sync only available for wiki pages.');

		$flags = $this->normalizeTranslationBitFlags( $flags );
		$flags = implode( ',', $flags );

		if( $version == 0 ) {
			$info = $this->get_page_info_from_id( $objId );
			$version = $info['version'];
		}

		$this->query(
			"INSERT
			INTO tiki_pages_translation_bits (`page_id`, `version`,`flags` ) 
			VALUES(?, ?, ?)",
			array( (int) $objId, (int) $version, $flags ) );
	}

	function propagateTranslationBits( $type, $sourceId, $targetId, $sourceVersion = 0, $targetVersion = 0 )
	{
		if( $type != 'wiki page' )
			die('Translation sync only available for wiki pages.');

		// TODO : Add a check to make sure both pages are in the same translation set
		
		$sourceId = (int) $sourceId;
		$sourceVersion = (int) $sourceVersion;
		$targetId = (int) $targetId;
		$targetVersion = (int) $targetVersion;

		if( $sourceVersion == 0 ) {
			$info = $this->get_page_info_from_id( $sourceId );
			$sourceVersion = (int) $info['version'];
		}

		if( $targetVersion == 0 ) {
			$info = $this->get_page_info_from_id( $targetId );
			$targetVersion = (int) $info['version'];
		}

		/*
			Fetch the list of translation bits from the source available in
			the selected version. From the list, exclude those that originated
			from the target or were already incorporated in a previous update.
		*/
		$result = $this->query( "
			SELECT translation_bit_id, original_translation_bit, flags
			FROM tiki_pages_translation_bits
			WHERE 
				page_id = ? 
				AND version <= ? 
				AND original_translation_bit IS NULL
				AND translation_bit_id NOT IN(
					SELECT original_translation_bit 
					FROM tiki_pages_translation_bits 
					WHERE page_id = ? AND original_translation_bit IS NOT NULL
				)
			UNION
				SELECT translation_bit_id, original_translation_bit, flags
				FROM tiki_pages_translation_bits
				WHERE 
					page_id = ? 
					AND version <= ? 
					AND original_translation_bit IS NOT NULL 
					AND original_translation_bit NOT IN(
						SELECT translation_bit_id
						FROM tiki_pages_translation_bits 
						WHERE page_id = ?
					)
					AND original_translation_bit NOT IN(
						SELECT original_translation_bit
						FROM tiki_pages_translation_bits 
						WHERE page_id = ? AND original_translation_bit IS NOT NULL
					)
			",
			array( $sourceId, $sourceVersion, $targetId, $sourceId, $sourceVersion, $targetId, $targetId ) );

		$query = "
		INSERT INTO tiki_pages_translation_bits (
			page_id, 
			version, 
			source_translation_bit, 
			original_translation_bit, 
			flags)
		VALUES( ?, ?, ?, ?, ? )";
		while( $row = $result->fetchRow() ) {
			if( empty( $row['original_translation_bit'] ) ) {
				// The translation bit is the original one
				$this->query( $query, array(
					$targetId, 
					$targetVersion, 
					$row['translation_bit_id'], 
					$row['translation_bit_id'], 
					$row['flags'] ) );
			} else {
				// The transation bit was propagated to the source
				$this->query( $query, array(
					$targetId, 
					$targetVersion, 
					$row['translation_bit_id'], 
					$row['original_translation_bit'], 
					$row['flags'] ) );
			}
		}
	}

	function getMissingTranslationBits( $type, $objId, $flags = array() ) {
		if( $type != 'wiki page' )
			die('Translation sync only available for wiki pages.');

		$objId = (int) $objId;
		$flags = $this->normalizeTranslationBitFlags( $flags );

		$conditions = array( '1 = 1' );
		foreach( $flags as $flag )
			$conditions[] = "( FIND_IN_SET('$flag', bits.flags) > 0 )";

		$conditions = implode( ' AND ', $conditions );
		$result = $this->query( "
			SELECT
				bits.translation_bit_id
			FROM
				tiki_translated_objects a
				INNER JOIN tiki_translated_objects b ON a.traId = b.traId AND a.objId <> b.objId
				INNER JOIN tiki_pages_translation_bits bits ON b.objId = bits.page_id
				LEFT JOIN tiki_pages_translation_bits self
					ON bits.translation_bit_id = self.original_translation_bit AND self.page_id = ?
			WHERE
				a.objId = ?
				AND bits.original_translation_bit IS NULL
				AND self.original_translation_bit IS NULL
				AND $conditions
		", array( $objId, $objId ) );

		$bits = array();
		while( $row = $result->fetchRow() )
			$bits[] = $row['translation_bit_id'];

		return $bits;
	}

	function getTranslationsWithBit( $translationBit )
	{
		$result = $this->query( "
			SELECT
				pageName page, lang
			FROM
				tiki_pages_translation_bits bits
				INNER JOIN tiki_pages pages ON pages.page_id = bits.page_id
			WHERE
				translation_bit_id = ?
				OR original_translation_bit = ?
		", array( $translationBit, $translationBit ) );

		$pages = array();
		while( $row = $result->fetchRow() ) {
			// add pagename of approved page if it is a staging page
			global $prefs;			
			if ( $prefs['feature_wikiapproval'] == 'y' && substr($row['page'], 0, strlen($prefs['wikiapproval_prefix'])) == $prefs['wikiapproval_prefix'] ) {
				$row['approvedPage'] = substr($row['page'], strlen($prefs['wikiapproval_prefix']));
			}
			$pages[] = $row;
		}

		return $pages;
	}

	function getSourceHistory( $pageId )
	{
		$result = $this->query( "
			SELECT DISTINCT
				target.version as `group`,
				page.page_id,
				page.pageName as page,
				source.version as version
			FROM
				tiki_pages_translation_bits source
				INNER JOIN tiki_pages_translation_bits target ON source.translation_bit_id = target.source_translation_bit
				INNER JOIN tiki_pages page ON source.page_id = page.page_id
			WHERE
				target.page_id = ?",
			array( $pageId ) );

		$list = array();

		while( $row = $result->fetchRow() ) {
			$group = $row['group'];

			if( ! array_key_exists( $group, $list ) )
				$list[$group] = array();

			$list[$group][] = $row;
		}

		return $list;
	}

	function getTargetHistory( $pageId )
	{
		$result = $this->query( "
			SELECT DISTINCT
				source.version as `group`,
				page.page_id,
				page.pageName as page,
				target.version as version
			FROM
				tiki_pages_translation_bits source
				INNER JOIN tiki_pages_translation_bits target ON source.translation_bit_id = target.source_translation_bit
				INNER JOIN tiki_pages page ON target.page_id = page.page_id
			WHERE
				source.page_id = ?",
			array( $pageId ) );

		$list = array();

		while( $row = $result->fetchRow() ) {
			$group = $row['group'];

			if( ! array_key_exists( $group, $list ) )
				$list[$group] = array();

			$list[$group][] = $row;
		}

		return $list;
	}

	function getBetterPages( $pageId )
	{
		$pageId = (int) $pageId;

		$result = $this->query( "
			SELECT DISTINCT
				page.pageName page,
				IFNULL( (
					SELECT MAX(source.version)
					FROM
						tiki_pages_translation_bits source
						INNER JOIN tiki_pages_translation_bits target
							ON source.translation_bit_id = target.source_translation_bit
					WHERE
						source.page_id = b.objId
						AND target.page_id = page.page_id
				), 1) last_update,
				page.version current_version,
				page.lang
			FROM
				tiki_translated_objects a
				INNER JOIN tiki_translated_objects b ON a.traId = b.traId AND a.objId <> b.objId
				INNER JOIN tiki_pages page ON page.page_id = a.objId
				INNER JOIN tiki_pages_translation_bits candidate ON candidate.page_id = page.page_id
			WHERE
				b.objId = ?
				AND IFNULL( candidate.original_translation_bit, candidate.translation_bit_id ) NOT IN(
					SELECT IFNULL( original_translation_bit, translation_bit_id )
					FROM tiki_pages_translation_bits
					WHERE page_id = b.objId
				)
		", array( $pageId ) );

		$pages = array();
		while( $row = $result->fetchRow() ) {
			$pages[] = $row;
		}

		return $pages;
	}

	function getWorstPages( $pageId )
	{
		$pageId = (int) $pageId;

		$result = $this->query( "
			SELECT DISTINCT
				page.pageName page,
				IFNULL( (
					SELECT MAX(source.version)
					FROM
						tiki_pages_translation_bits source
						INNER JOIN tiki_pages_translation_bits target ON source.translation_bit_id = target.source_translation_bit
					WHERE
						source.page_id = a.objId
						AND target.page_id = b.objId
				), 1) last_update,
				page.lang
			FROM
				tiki_pages page
				INNER JOIN tiki_translated_objects a ON a.objId = page.page_id
				INNER JOIN tiki_translated_objects b ON a.traId = b.traId AND a.objId <> b.objId
			WHERE
				b.objId = ?
				 AND (
					SELECT COUNT(*)
					FROM tiki_pages_translation_bits
					WHERE page_id = b.objId
				) > (
					SELECT COUNT(*)
					FROM
						tiki_pages_translation_bits self
						INNER JOIN tiki_pages_translation_bits candidate
							ON IFNULL(self.original_translation_bit, self.translation_bit_id)
							 = IFNULL(candidate.original_translation_bit, candidate.translation_bit_id)
					WHERE
						self.page_id = b.objId
						AND candidate.page_id = a.objId
				)
		", array( $pageId ) );

		$pages = array();
		while( $row = $result->fetchRow() ) {
			$pages[] = $row;
		}

		return $pages;
	}
	
	function get_page_bit_flags( $pageId, $version ) {
		$query = "select distinct `flags` from `tiki_pages_translation_bits` where `page_id`=? and `version`=?";
		$result = $this->query($query, array($pageId, $version));
		$flags = array();	
		while( $row = $result->fetchRow() ) {
			$flags[] = $row['flags'];
		}
		return $flags;
	}
	
}
global $dbTiki;
$multilinguallib = new MultilingualLib($dbTiki);
?>
