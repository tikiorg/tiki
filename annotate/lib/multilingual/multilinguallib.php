<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
		header("location: index.php");
		exit;
}

class MultilingualLib extends TikiLib
{
	private $tracesOn = false;

	public $mtEnabled = 'y';

	/**
	 * @brief add an object and its transaltion set into the set of translations of another one
	 * @param: type = (idem tiki_categ) 'wiki page'...
	 * @param: srcId = id of the source
	 * @param: srcLang = lang of the source
	 * @param: objId = id of the translation
	 * @param: objLang = lang of the translation
	 * @requirment: no translation of the source in this lang must exist
	 */
	function insertTranslation($type, $srcId, $srcLang, $objId, $objLang) {
		global $prefs;

		$srcTrads = $this->getTrads($type, $srcId);
		$objTrads = $this->getTrads($type, $objId);

		if (!$srcTrads && !$objTrads) {
			$query = "insert into `tiki_translated_objects` (`type`,`objId`,`lang`) values (?,?,?)";
			$this->query($query, array($type, $srcId, $srcLang));
			$query = "select max(`traId`) from `tiki_translated_objects` where `type`=? and `objId`=?";
			$tmp_traId = $this->getOne($query, array( $type, $srcId ) );
			$query = "insert into `tiki_translated_objects` (`type`,`objId`,`traId`,`lang`) values (?,?,?,?)";
			$this->query($query, array($type, $objId, $tmp_traId, $objLang));
			return null;
		} elseif (!$srcTrads) {
			if ($this->exist($objTrads, $srcLang, 'lang')) {
				return "alreadyTrad";
			}
			$query = "insert into `tiki_translated_objects` (`type`,`objId`,`traId`,`lang`) values (?,?,?,?)";
			$this->query($query, array($type, $srcId, $objTrads[0]['traId'], $srcLang));
			return null;
		} elseif (!$objTrads) {
			if ($this->exist($srcTrads, $objLang, 'lang')) {
				return "alreadyTrad";
			}
			$query = "insert into `tiki_translated_objects` (`type`,`objId`,`traId`,`lang`) values (?,?,?,?)";
			$this->query($query, array($type, $objId, $srcTrads[0]['traId'], $objLang));
			return null;
		} elseif ($srcTrads[0]['traId'] == $objTrads[0]['traId']) {
			return "alreadySet";
		} else {
			foreach ($srcTrads as $t) {
				if ($this->exist($objTrads, $t['lang'], 'lang')) {
					return "alreadyTrad";
				}
			}
			$query = "update `tiki_translated_objects`set `traId`=? where `traId`=?";
			$this->query = $this->query($query, array($srcTrads[0]['traId'], $objTrads[0]['traId']));
			return null;
		}
	}

	/**
	 * @brief update the object for the language of a translation set
	 * @param $objId: new object for the translation of $srcId of type $type in the language $objLang
	 */
	function updateTranslation($type, $srcId, $objId, $objLang)
	{
		$query = "update `tiki_translated_objects` set `objId`=? where `type`=? and `srcId`=? and `lang`=?";
		$this->query($query, array($objId, $type, $srcId, $objLang));
	}

	/**
	 * @brief get the translation in a language of an object if exists
	 * @return array(objId, traId)
	 */
	function getTranslation($type, $srcId, $objLang)
	{
		$query = "select t2.`objId`, t2.`traId` from `tiki_translated_objects` as t1, `tiki_translated_objects` as t2 where t1.`traId`=t2.`traId` and t1.`type`=? and t1.`objId`=? and t2.`lang`=?";
		return $this->getOne($query, array($type, $srcId, $objLang));
	}

	function getTrads($type, $objId)
	{
		$query = "select t2.`traId`, t2.`objId`, t2.`lang` from `tiki_translated_objects` as t1, `tiki_translated_objects` as t2 where t1.`traId`=t2.`traId` and t1.`type`=? and t1.`objId`=?";
		$result = $this->query($query, array($type, (string) $objId));
		$ret = array();
		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}
		return $ret;
	}

	/*
	 * @brief gets all the translations of an object
	 * @param type = (idem tiki_categ) 'wiki page'...
	 * @param objId = object Id
	 * @return if long is false: array(objId, lang, langName ) with langName=localized language name
	 * @return if long id true: array(objId, objName, lang, langLongFormat)
	 */
	function getTranslations($type, $objId, $objName='', $objLang='', $long=false)
	{
		if ($type == 'wiki page') {
			$query = "select t2.`objId`, t2.`lang`, p.`pageName`as `objName` from `tiki_translated_objects` as t1, `tiki_translated_objects` as t2 LEFT JOIN `tiki_pages` p ON p.`page_id`= t2.`objId` where t1.`traId`=t2.`traId` and t2.`objId`!= t1.`objId` and t1.`type`=? and t1.`objId`=?";
		} elseif ($long) {
			$query = "select t2.`objId`, t2.`lang`, a.`title` as `objName` from `tiki_translated_objects` as t1, `tiki_translated_objects` as t2, `tiki_articles` as a where t1.`traId`=t2.`traId` and t2.`objId`!= t1.`objId` and t1.`type`=? and t1.`objId`=? and a.`articleId`=t2.`objId`";
		} else {
			$query = "select t2.`objId`, t2.`lang` from `tiki_translated_objects` as t1, `tiki_translated_objects` as t2 where t1.`traId`=t2.`traId` and t2.`objId`!= t1.`objId` and t1.`type`=? and t1.`objId`=?";
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
		} else {
			$l = $this->format_language_list(array($objLang), 'y');
			$ret0 = array('objId'=>$objId, 'objName'=>$objName, 'lang'=> $objLang, 'langName'=>empty($l)?'':$l[0]['name']);
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

	/**
	 * @brief sort function on langName string
	 */
	function compare_lang($l1, $l2)
	{
		return strcmp($l1['langName'], $l2['langName']);
	}

	/**
	 * @brief: update lang in all tiki pages
	 */
	function updateObjectLang($type, $objId, $lang, $optimisation = false)
	{
		if ($this->getTranslation($type, $objId, $lang)) {
			return 'alreadyTrad';
		}

		if (!$optimisation) {
			if ($type == 'wiki page') {
				$query = "update `tiki_pages` set `lang`=? where `page_id`=?";
			} elseif ($type == 'article') {
				$query = "update `tiki_articles` set `lang`=? where `articleId`=?";
			}
			$this->query($query,array($lang, $objId));
		}

		$query = "update `tiki_translated_objects` set `lang`=? where `objId`=? and `type`=?";
		$this->query($query,array($lang, $objId, $type));
		return null;
	}

	/**
	 * @brief: detach one translation
	 */
	function detachTranslation($type, $objId)
	{
		$query = "delete from `tiki_translated_objects` where `type`= ? and `objId`=?";
		$this->query($query,array($type, $objId));
		//@@TODO: delete the set if only one remaining object - not necesary but will clean the table
	}

	/**
	 * @brief : test if val exists in a list of objects
	 */
	function exist($tab, $val, $col)
	{
		foreach ($tab as $t) {
			if ($t[$col] == $val) {
				return true;
			}
		}
		return false;
	}

	/**
	 * @brief : returns an ordered list of preferred languages
	 * @param $langContext: optional the language the user comes from
	 */
	function preferredLangs($langContext = null, $include_browser_lang = null)
	{
		global $user, $prefs, $tikilib;
		$langs = array();

		if ($include_browser_lang === null) {
			$include_browser_lang = ($prefs['feature_detect_language'] === 'y');
		}

		if ($langContext) {
			$langs[] = $langContext;
			if (strchr($langContext, "-")) { // add en if en-uk
				$langs[] = $this->rootLang($langContext);
			}
		}

		if ($prefs['language'] && !in_array($prefs['language'], $langs)) {
			$langs[] = $prefs['language'];
			$l = $this->rootLang($prefs['language']);
			if (!in_array($l, $langs)) {
				$langs[] = $l;
			}
		}

		if (isset($prefs['read_language'])) {
			$tok = strtok($prefs['read_language'], ' ');
			while (false !== $tok) {
				if (!in_array($tok, $langs) ) {
					$langs[] = $tok;
				}
				$l = $this->rootLang($tok);
				if (!in_array($l, $langs)) {
					$langs[] = $l;
				}
				$tok = strtok(' ');
			}
		}

		if (($include_browser_lang)&&(isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))) {
			$ls = preg_split('/\s*,\s*/', preg_replace('/;q=[0-9.]+/','',$_SERVER['HTTP_ACCEPT_LANGUAGE'])); // browser
			foreach ($ls as $l) {
				if (!in_array($l, $langs)) {
					$langs[] = $l;
					$l = $this->rootLang($l);
					if (!in_array($l, $langs)) {
						$langs[] = $l;
					}
				}
			}
		}

		$l = $prefs['site_language'];
		if (!in_array($l, $langs)) {
			$langs[] = $l; // site language
			$l = $this->rootLang($l);
			if (!in_array($l, $langs)) {
				$langs[] = $l;
			}
		}

		if ( $prefs['available_languages'] && $prefs['language_inclusion_threshold'] >= count($prefs['available_languages']) ) {
			foreach ( array_diff( $prefs['available_languages'], $langs ) as $lang ) {
				$langs[] = $lang;
			}
		}

		return $langs;
	}

	/**
	 * @brief : return the root language ex: en-uk returns en
	 */
	function rootLang($lang)
	{
		return preg_replace("/(.*)-(.*)/", '$1', $lang);
	}

	/**
	 * @brief : fitler a list of object to have only one objet in the set of translations with the best language
	 */
	function selectLangList($type, $listObjs, $langContext = null)
	{
		if (!$listObjs || count($listObjs) <= 1) {
			return $listObjs;
		}

		$langs = $this->preferredLangs($langContext);
		$max = count($listObjs);
		for ($i = 0; $i < $max; ++$i) {
			if (!isset($listObjs[$i]) || !isset($listObjs[$i]['lang'])) {
				continue; // previously withdrawn or no language
			}

			if ($type == 'wiki page') {
				$objId = $listObjs[$i]['page_id'];
			} else if ($type == 'objId') {
				$objId = $listObjs[$i]['objId'];
			} else {
				$objId = $listObjs[$i]['articleId'];
			}

			$trads = $this->getTrads($type, $objId);
			if (!$trads) {
				continue;
			}

			for ($j = $i + 1; $j < $max; ++$j) {
				if (!isset($listObjs[$j])) {
					continue;
				}
				if ($type == 'wiki page') {
					$objId2 = $listObjs[$j]['page_id'];
				} else if ($type == 'objId') {
					$objId2 = $listObjs[$j]['objId'];
				} else {
					$objId2 = $listObjs[$j]['articleId'];
				}

				if ($this->exist($trads, $objId2, 'objId')) {
					$iord = array_search($listObjs[$i]['lang'] , $langs);
					if (!$iord && strchr($listObjs[$i]['lang'], "-")) {
						$iord = array_search($this->rootLang($listObjs[$i]['lang']), $langs);
					}

					$jord = array_search($listObjs[$j]['lang'] , $langs);
					if (!$jord && strchr($listObjs[$j]['lang'], "-")) {
						$jord = array_search($this->rootLang($listObjs[$j]['lang']), $langs);
					}

					if ($jord === false) {
						unset($listObjs[$j]); // not in the pref langs
					} else if ($iord === false) {
						unset($listObjs[$i]);
						break;
					} else if ($iord > $jord) {
						unset($listObjs[$i]);
						break;
					} else {
						unset($listObjs[$j]);
					}
					// if none in the pref lang, pick the first (sorted by date)
				}
			}
		}
		return array_merge($listObjs);// take away the unset rows
	}

	/**
	 * @brief : select the object with the best language from another object
	 */
	function selectLangObj($type, $objId, $langContext = null)
	{
		$trads = $this->getTrads($type, $objId);
		if (!$trads) {
			return $objId;
		}
		$langs = $this->preferredLangs($langContext);
		foreach ($langs as $l) {
			foreach ($trads as $trad) {
				if ($trad['lang'] == $l) {
					return $trad['objId'];
				}
			}
		}
		return $objId;
	}

	/**
	 * Determine if the best language should be used for an object, based on request and preference parameters,
	 */
	function useBestLanguage()
	{
		/*
		 * Indicates whether or not content should be displayed in the user's preferred language
		 * (as expressed in either its Tiki or browser language preferences).
		 */
		global $prefs, $_REQUEST;

		if ($prefs['feature_multilingual'] == 'n') {
			return false;
		}

		if ($prefs['feature_best_language'] == 'n' && !isset($_REQUEST['bl'])) {
			// If bl is explicitly set then for backward compatibility reasons let through even if feature is off.
			return false;
		}

		if (isset($_REQUEST['no_bl']) && $_REQUEST['no_bl'] == 'y') {
			return false; // no_bl is the new flag which has to be specified as y to have any effect
		}

		// The following checks below maintained for backward compatibility
		if (isset($_REQUEST['best_lang']) && $_REQUEST['best_lang'] != 'y') {
			return false; // the old best_lang check was default no, if present without y specified
		}

		if (isset($_REQUEST['bl']) && $_REQUEST['bl'] == 'n') {
			return false; // the old bl check was default yes once present
		}

		/*
		 * Alain Désilets (2010-01-12):
		 * 
		 * There is also a bl= argument, but it seems too be used very inconsistenly. 
		 * - Sometimes, the mere presence of bl (no matter its value) s interpreted as meaning
		 *  that bBest Language should be used.
		 * - In other cases, we set bl=n, presumably to signifiy that Best Language should not be used.
		 * - Yet, in in lib/setup/language.php, there is a statement which, if unsets bl, if its value was n, so 
		 *  not clear that all the checks for bl=n are doing anything.
		 * - If the purpose of bl is to indicate that Best Language is to be used (when bl is defined),
		 *  then it's kind of weird, because Best Language cannot be used when multilingual features
		 *  or best_languge or detec_language are inactive. Yet, when one of those is active, Best Language
		 *  is always used, so there is no need to say that with an argument. There may be a need to 
		 *  say that in a particular case, Best Language should NOT be used, but not to say that Best Language
		 *  SHOULD be used.
		 * -- extra note by nkoth: I've cleaned this up - so all the checking is done here now.
		 */

		return true;
	}

	function setUrlNoBestLanguageArg($url, $no_bl_value)
	{
		if (preg_match('/[?&]no_bl=/', $url)) {
			$url = preg_replace('/([?&])no_bl=[yn]{0,1}/', '$1no_bl=$no_bl_value', $url);
		} elseif (!preg_match('/[?&]lang=/', $url)) {
			if (strstr($url, '?')) {
				$url.= '&no_bl=$no_bl_value';
			} else {
				$url.= '?no_bl=$no_bl_value';
			}
		}

		return $url;
	}

	function getSupportedTranslationBitFlags()
	{
		return array( 'critical' );
	}

	function normalizeTranslationBitFlags( $flags )
	{
		if	( !is_array( $flags ) ) {
			$flags = explode( ',', $flags );
		}

		// Add supported flags as they get added
		return array_intersect( $flags, $this->getSupportedTranslationBitFlags() );
	}

	function createTranslationBit($type, $objId, $version = 0, $flags = array())
	{
		if ( $type != 'wiki page' ) {
			die('Translation sync only available for wiki pages.');
		}

		$flags = $this->normalizeTranslationBitFlags( $flags );
		$flags = implode( ',', $flags );

		if ( $version == 0 ) {
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
		if ( $type != 'wiki page' ) {
			die('Translation sync only available for wiki pages.');
		}

		// TODO : Add a check to make sure both pages are in the same translation set

		$sourceId = (int) $sourceId;
		$sourceVersion = (int) $sourceVersion;
		$targetId = (int) $targetId;
		$targetVersion = (int) $targetVersion;

		if ( $sourceVersion == 0 ) {
			$info = $this->get_page_info_from_id( $sourceId );
			$sourceVersion = (int) $info['version'];
		}

		if ( $targetVersion == 0 ) {
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
		while ( $row = $result->fetchRow() ) {
			if ( empty( $row['original_translation_bit'] ) ) {
				// The translation bit is the original one
				$this->query( $query, 
											array($targetId, 
														$targetVersion, 
														$row['translation_bit_id'], 
														$row['translation_bit_id'], 
														$row['flags'] ) 
										);
			} else {
				// The transation bit was propagated to the source
				$this->query( $query, 
											array($targetId, 
														$targetVersion, 
														$row['translation_bit_id'], 
														$row['original_translation_bit'], 
														$row['flags'] ) );
			}
		}
	}

	function getMissingTranslationBits( $type, $objId, $flags = array(), $page_unique = false )
	{
		if ( $type != 'wiki page' ) {
			die('Translation sync only available for wiki pages.');
		}

		$objId = (int) $objId;
		$flags = $this->normalizeTranslationBitFlags( $flags );

		$conditions = array( '1 = 1' );
		foreach ( $flags as $flag ) {
			$conditions[] = "( FIND_IN_SET('$flag', bits.flags) > 0 )";
		}

		$conditions = implode( ' AND ', $conditions );
		$result = $this->query( "
					SELECT
					bits.translation_bit_id, bits.page_id
					FROM
					tiki_translated_objects a
					INNER JOIN tiki_translated_objects b ON a.`traId` = b.`traId` AND a.`objId` <> b.`objId`
					INNER JOIN tiki_pages_translation_bits bits ON b.`objId` = bits.page_id
					LEFT JOIN tiki_pages_translation_bits self
					ON bits.`translation_bit_id` = self.`original_translation_bit` AND self.`page_id` = ?
					WHERE
					a.`type` = 'wiki page'
					AND b.`type` = 'wiki page'
					AND a.`objId` = ?
					AND bits.`original_translation_bit` IS NULL
					AND self.`original_translation_bit` IS NULL
					AND $conditions
					", 
					array( $objId, $objId ) );
		
		$bits = array();
		while ( $row = $result->fetchRow() ) {
			if ($page_unique) {
				$bits[$row['bits.page_id']] = $row['translation_bit_id'];
			} else {
				$bits[] = $row['translation_bit_id'];
			}
		}

		return $bits;
	}

	function getTranslationsWithBit( $translationBit, $pageIdToUpdate )
	{
		$pageIdToUpdate = (int) $pageIdToUpdate;
		$translationBit = (int) $translationBit;

		$result = $this->query( "
					SELECT
					`pageName` page,
					lang,
					" . $this->subqueryObtainUpdateVersion( 'pages.page_id', '?' ) . " last_update,
					pages.version current_version
					FROM
					tiki_pages_translation_bits bits
					INNER JOIN tiki_pages pages ON pages.page_id = bits.page_id
					WHERE
					translation_bit_id = ?
					OR original_translation_bit = ?
					", array( $pageIdToUpdate, $translationBit, $translationBit ) );

		$pages = array();
		global $prefs;			
		while ( $row = $result->fetchRow() ) {
			if ( $row['lang'] == $prefs['site_language'] ) {
				$pages[] = $row;
			}
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
					MAX(source.version) as version
				FROM
					tiki_pages_translation_bits source
					INNER JOIN tiki_pages_translation_bits target ON source.translation_bit_id = target.source_translation_bit
					INNER JOIN tiki_pages page ON source.page_id = page.page_id
				WHERE
					target.page_id = ?
				GROUP BY target.version, page.page_id",
				array( $pageId ) );

		$list = array();

		while ( $row = $result->fetchRow() ) {
			$group = $row['group'];

			if ( ! array_key_exists( $group, $list ) ) {
				$list[$group] = array();
			}
			$list[$group][] = $row;
		}

		return $list;
	}

	function getTargetHistory( $pageId )
	{
		$result = $this->query( "
				SELECT DISTINCT
					MAX(source.version) as `group`,
					page.page_id,
					page.pageName as page,
					target.version as version
				FROM
					tiki_pages_translation_bits source
					INNER JOIN tiki_pages_translation_bits target ON source.translation_bit_id = target.source_translation_bit
					INNER JOIN tiki_pages page ON target.page_id = page.page_id
				WHERE
					source.page_id = ?
				GROUP BY page.page_id, target.version",
				array( $pageId ) );

		$list = array();

		while ( $row = $result->fetchRow() ) {
			$group = $row['group'];

			if ( ! array_key_exists( $group, $list ) ) {
				$list[$group] = array();
			}

			$list[$group][] = $row;
		}

		return $list;
	}

	function subqueryObtainUpdateVersion( $sourcePage, $targetPage )
	{
		// Meant to be inlined in an other query. Useful in many cases.

		/*
			Fetches the lowest version of source containing a bit not present
			in target. 

			-1 is made on the version so the diff is made properly.
			IFNULL defaults to 2 so no result is turned back to 1

			If the actual version returned is 1, 1 should be returned and not 0.
		*/
		return "(
			SELECT 
				IFNULL( IF(MIN(version) = 1, 0, MIN(version)), 0 ) - 1
			FROM
				tiki_pages_translation_bits
			WHERE
				page_id = $sourcePage
				AND IFNULL( original_translation_bit, translation_bit_id ) NOT IN(
						SELECT IFNULL( original_translation_bit, translation_bit_id )
						FROM tiki_pages_translation_bits
						WHERE page_id = $targetPage)
			)";
	}

	function getBetterPages( $pageId )
	{
		$pageId = (int) $pageId;

		$query = "
			SELECT DISTINCT
				page.page_id,
				page.pageName page,
				" . $this->subqueryObtainUpdateVersion( 'a.objId', 'b.objId' ) . " last_update,
				page.version current_version,
				page.lang
					FROM
					tiki_translated_objects a
					INNER JOIN tiki_translated_objects b ON a.traId = b.traId AND a.objId <> b.objId
					INNER JOIN tiki_pages page ON page.page_id = a.objId
					INNER JOIN tiki_pages_translation_bits candidate ON candidate.page_id = page.page_id
				WHERE
					a.type = 'wiki page'
					AND b.type = 'wiki page'
					AND b.objId = ?
					AND IFNULL( candidate.original_translation_bit, candidate.translation_bit_id ) NOT IN(
							SELECT IFNULL( original_translation_bit, translation_bit_id )
							FROM tiki_pages_translation_bits
							WHERE page_id = b.objId
							)
			";
		$result = $this->query($query, array( $pageId ) );

		$pages = array();
		while ( $row = $result->fetchRow() ) {
			$pages[] = $row;
		}

		return $pages;
	}

	function getWorstPages( $pageId )
	{
		$pageId = (int) $pageId;

		$result = $this->query( "
				SELECT DISTINCT
					page.page_id,
					page.pageName page,
					" . $this->subqueryObtainUpdateVersion( 'b.objId', 'a.objId' ) . " last_update,
					page.lang
				FROM
					tiki_pages page
					INNER JOIN tiki_translated_objects a ON a.objId = page.page_id
					INNER JOIN tiki_translated_objects b ON a.traId = b.traId AND a.objId <> b.objId
				WHERE
					a.type = 'wiki page'
					AND b.type = 'wiki page'
					AND b.objId = ?
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
		while ( $row = $result->fetchRow() ) {
			$pages[] = $row;
		}

		return $pages;
	}

	function get_page_bit_flags( $pageId, $version )
	{
		$query = "select distinct `flags` from `tiki_pages_translation_bits` where `page_id`=? and `version`=?";
		$result = $this->query($query, array($pageId, $version));
		$flags = array();	
		while ( $row = $result->fetchRow() ) {
			$flags[] = $row['flags'];
		}

		return $flags;
	}

	function getLangOfPage($pageName)
	{
		$pageInfo = $this->get_page_info($pageName);
		$lang = $pageInfo['lang'];
		return $lang;
	}

	function currentPageSearchLanguage()
	{
		/*
		 * Returns the language to be used for a normal page find.
		 */
		global $_REQUEST, $_SESSION;

		$lang = '';
		// First look in HTTP 'lang' argument
		if (isset($_REQUEST['lang'])) { //lang='' means all languages
			$lang = $_REQUEST['lang'];
		}

		return $lang;
	}

	/**
	 * Returns the language to be used for a Term search (terminology module).
	 *
	 * @access public
	 */
	function currentTermSearchLanguage()
	{
		global $_REQUEST, $_SESSION;

		$lang = '';
		if (isset($_REQUEST['term_src']) && isset($_REQUEST['lang'])) {
			$lang = $_REQUEST['lang'];
		}

		if ($lang == '' && array_key_exists('find_term_last_done_in_lang', $_SESSION)) {
			$lang = $_SESSION['find_term_last_done_in_lang'];
		}
		// Remember language of this term search.
		$this->storeCurrentTermSearchLanguageInSession($lang);

		return $lang;
	}


	function storeCurrentTermSearchLanguageInSession($lang)
	{
		global $_SESSION;
		$_SESSION['find_term_last_done_in_lang'] = $lang;
	}

	function preferredLangsInfo()
	{
		global $tikilib;

		// Get IDs of user's preferred languages
		$userLangIDs = $this->preferredLangs();

		// Get information about ALL languages supported by Tiki
		$allLangsInfo = $tikilib->list_languages(false,'y');

		// Create a map of language ID (ex: 'en') to language info
		$langIDs2Info = array();
		foreach ($allLangsInfo as $someLangInfo) {
			$langIDs2Info[$someLangInfo['value']] = $someLangInfo;
		}

		// Create list of language IDs AND names for user's preferred
		// languages.
		$userLangsInfo = array();
		$lang_index = 0;
		foreach ($userLangIDs as $index => $someUserLangID) {
			if ($langIDs2Info[$someUserLangID] != NULL) {
				$userLangsInfo[$lang_index] = $langIDs2Info[$someUserLangID];
				$lang_index++;
			}
		}

		return $userLangsInfo;
	}

	function getTemplateIDInLanguage($section, $template_name, $language)
	{
		global $templateslib;
		require_once 'lib/templates/templateslib.php';

		$all_templates = $templateslib->list_templates($section, 0, -1, 'name_asc', '');
		$looking_for_templates_named = array("$template_name-$language");
		foreach ($looking_for_templates_named as $looking_for_this_template) {
			$looking_for_this_template = "$template_name-$language";
			foreach ($all_templates['data'] as $a_template) {
				$a_template_name = $a_template['name'];
				if ($a_template_name == $looking_for_this_template) {
					return $a_template['templateId'];
				}
			}
		}

		return null;
	}


	function setMachineTranslationFeatureTo($on_or_off)
	{
		$this->mtEnabled = $on_or_off;
	}

	function getTranslationsInProgressFlags($page_id, $language=NULL)
	{
		$fields = '`page_id`';
		$valuesSpec = "?";
		$values = array($page_id);
		if ($language) {
			$fields .= ', `language`';
			$valuesSpec .= ", ?";
			$values[] = $language;
		}

		$query = "select `language` from `tiki_translations_in_progress` where ($fields)=($valuesSpec)";
		$flags = $this->fetchAll($query, $values);

		return $flags;
	}

	function addTranslationInProgressFlags($page_id, $language)
	{
		//
		// First, make sure that there isn't already a row in the table
		// capturing the fact that this page is being translated from that language
		//
		$translationInProgressForThatLanguage = $this->getTranslationsInProgressFlags($page_id, $language);
		if (count($translationInProgressForThatLanguage) == 0) {
			$query = "insert into `tiki_translations_in_progress` (`page_id`,`language`) values (?,?)";
			$results = $this->query($query, array($page_id, $language));
		}
	}

	function deleteTranslationInProgressFlags($page_id, $language)
	{
		$query =
			"DELETE FROM `tiki_translations_in_progress`\n".
			" WHERE (`page_id`, `language`) = (?, ?)";	
		$results = $this->query($query, array($page_id, $language));
	}

	function sqlTranslationOrphan($objectType, $sqlObjectId, $columnObjectId, $langs, &$join, &$mid, &$bindvars)
	{
		$join .= " left join `tiki_translated_objects` tro on (tro.`type` = '$objectType' AND tro.`objId` = $sqlObjectId.`$columnObjectId`) ";
		$translationOrphan_mid = " tro.`traId` IS NULL OR $sqlObjectId.`lang`IS NULL ";
		foreach ($langs as $i=>$lg) {
			$join .= " left join `tiki_translated_objects` tro_$i on (tro_$i.`traId` = tro.`traId` AND tro_$i.`lang`=?) ";
			$translationOrphan_mid .= " OR tro_$i.`traId` IS NULL ";
			$bindvars[] = $lg;
		}

		if (!empty($mid)) $mid .= ' AND ';
		$mid .= "($translationOrphan_mid)";
		if (count($langs) == 1) {
			$mid .= " AND ($sqlObjectId.`lang` != ? OR $sqlObjectId.`lang` IS NULL) ";
			$bindvars[] = $langs[0];
		}
	}

}

$multilinguallib = new MultilingualLib;
