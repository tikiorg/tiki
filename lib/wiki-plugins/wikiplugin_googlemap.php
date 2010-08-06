<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

function wikiplugin_googlemap_help() {
	return tra("googlemap").":~np~{GOOGLEMAP(type=locator|user|item|objectlist,trackerfield, mode=normal|satellite|hybrid, key=XXXXX name=xxx, width=500, height=400, frameborder=1|0, defaultx=-79.4, defaulty=43.707, defaultz=14, setdefaultxyz=1|0, locateitemtype=wiki page|..., locateitemid=xxx, hideifnone=0|1, togglehidden=0|1, starthidden=0|1, autozoom=14, controls=n, trackerfieldid=xxx, trackerinputid=xxx)}{GOOGLEMAP}~/np~";
}

function wikiplugin_googlemap_info() {
	return array(
		'name' => tra('googlemap'),
		'documentation' => 'PluginGoogleMap',
		'description' => tra("Displays a Google map"),
		'prefs' => array( 'wikiplugin_googlemap' ),
//		'validate' => 'all',
		'params' => array(
			'type' => array(
				'safe' => true,
				'required' => true,
				'name' => tra('Type of items'),
				'description' => tra('Type of items to show on google map'),
			),
			'mode' => array(
				'safe' => true,
				'required' => true,
				'name' => tra('Map display mode'),
				'description' => tra('Map display mode'),
			),
			'key' => array(
					'safe' => true,
					'required' => false,
					'name' => tra('API Key'),
					'description' => tra('Google maps key, if not set in user preferences'),
				),
			'name' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Map ID'),
				'description' => tra('Id suffix of Google map div to avoid conflicts with other maps on same page'),
			),
			'width' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Width'),
				'description' => tra('Pixels or %'),
			),
			'height' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Height'),
				'description' => tra('Pixels or %'),
			),
			'frameborder' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Show Border'),
				'description' => '1|0',
			),
			'defaultx' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Default longitude to center map'),
				'description' => tra('Longitude value e.g. -79.39'),
			),
			'defaulty' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Default latitude to center map'),
				'description' => tra('Latitude value e.g. 43.7'),
			),
			'defaultz' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Default zoom level to view map'),
				'description' => tra('An integer between 0 and 19'),
			),
			'setdefaultxyz' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Allow user to set map view as user default'),
				'description' => tra('1|0, allow user to set current map view as default view for himself only'),
			),
			'locateitemtype' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Type of item being geotagged'),
				'description' => tra('user, wiki page, blog, etc..., will attempt to use current object if not specified'),
			),
			'locateitemid' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('ID of item being geotagged'),
				'description' => tra('Name of page, blog ID, etc..., will attempt to use current object if not specified'),
			),		
			'hideifnone' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Do not show map if there is no '),
				'description' => tra('1|0, allow user to set current map view as default view for himself only'),
			),
			'hideifnone' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Hide map if there are no markers to be shown'),
				'description' => tra('1|0'),
			),
			'togglehidden' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Ability to toggle visibility'),
				'description' => tra('1|0'),
			),
			'starthidden' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Start hidden'),
				'description' => tra('1|0'),
			),		
			'autozoom' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Auto zoom to this level on address find'),
				'description' => tra('An integer between 0 and 19'),
			),
			'controls' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Show controls'),
				'description' => tra('y|n'),
			),
			'trackerfieldid' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('ID of tracker field'),
				'description' => tra('Field ID of tracker field if type is trackerfield or locator and locateitemtype is trackeritem'),
			),
			'trackerinputid' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('HTML ID of tracker field input box'),
				'description' => tra('HTML ID of tracker field input box where value is copied to by Javascript, auto set if used as part of entry forms'),
			),
			'in_form' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Set to 1 if this is embedded inside a form'),
				'description' => tra('1|0, is needed to prevent nested forms which is not allowed in HTML'),
			),
		),
	);
}

function wikiplugin_googlemap($data, $params) {

	global $prefs, $smarty, $tikilib, $access, $ajaxlib, $headerlib;

	$access->check_feature('feature_gmap');
	
	$type = $params["type"];
	$smarty->assign_by_ref('gmaptype', $type); // by ref as may be overridden later

	if ($type == 'locator' || isset($params["setdefaultxyz"]) && $params["setdefaultxyz"]) {
		$access->check_feature('feature_ajax');
		if (!is_object($ajaxlib)) {
			include_once ('lib/ajax/ajaxlib.php');	
		}	
	}
	
	if (isset($params["mode"]) && $params["mode"]) {
		$smarty->assign( 'gmapmode', $params["mode"] );
	} else {
		$smarty->assign( 'gmapmode', '' );
	}
	
	if (isset($params["key"]) && $params["key"]) {
		$gmapkey = $params["key"];
	} elseif ($prefs["gmap_key"]) {
		$gmapkey = $prefs["gmap_key"];
	} else {
		return tra("Google Maps API key not set");
	}

	$headerlib->add_jsfile("http://maps.google.com/maps?file=api&v=2&key=$gmapkey");
	
	if (isset($params["name"]) && $params["name"]) {
		$gmapname = str_replace(' ', '', $params["name"]);
	} else {
		$gmapname = 'default';
	}
	$smarty->assign( 'gmapname',  $gmapname);
	
	if (isset($params["defaultx"])) {
		$defaultx = $params["defaultx"];
	} else {
		$defaultx = $prefs["gmap_defaultx"];
	}
	if (isset($params["defaulty"])) {
		$defaulty = $params["defaulty"];
	} else {
		$defaulty = $prefs["gmap_defaulty"];
	}
	if (isset($params["defaultz"])) {
		$defaultz = $params["defaultz"];
	} else {
		$defaultz = $prefs["gmap_defaultz"];
	}
	$smarty->assign( 'gmap_defaultx', $defaultx );
	$smarty->assign( 'gmap_defaulty', $defaulty );
	$smarty->assign( 'gmap_defaultz', $defaultz );
	
	if (isset($params["controls"])) {
		$smarty->assign( 'gmap_controls', $params["controls"] );
	} else {
		$smarty->assign( 'gmap_controls', 'y' );
	}
	
	if (isset($params["in_form"])) {
		$smarty->assign( 'gmap_in_form', $params["in_form"] );
	} else {
		$smarty->assign( 'gmap_in_form', 0 );
	}
	
	if (isset($params["setdefaultxyz"]) && $params["setdefaultxyz"]) {
		$smarty->assign( 'gmap_defaultset', true) ;
		$ajaxlib->registerFunction('saveGmapDefaultxyz');
	} else {
		$smarty->assign( 'gmap_defaultset', false) ;
	}
	
	if (isset($params["width"]) && $params["width"]) {
		$width = $params["width"];
	} else {
		$width = 500;
	}
	$smarty->assign( 'gmapwidth', $width );
	$smarty->assign( 'gmapaddresslength', floor($width/14));
	
	if (isset($params["height"]) && $params["height"]) {
		$smarty->assign( 'gmapheight', $params["height"] );
	} else {
		$smarty->assign( 'gmapheight', 400 );
	}
	
	if (isset($params["frameborder"]) && $params["frameborder"]) {
		$smarty->assign( 'gmapframeborder', 1 );
	} else {
		$smarty->assign( 'gmapframeborder', 0 );
	}

	if (isset($params["locateitemtype"]) && $params["locateitemtype"]) {
		$locateitemtype = $params["locateitemtype"];
	} else {
		$locateitemtype = '';
	}
	if (isset($params["locateitemid"]) && $params["locateitemid"]) {
		$locateitemid = $params["locateitemid"];
	} else {
		$locateitemid = '';
	}
	
	if (isset($params["togglehidden"]) && $params["togglehidden"]) {
		$smarty->assign( 'gmaptoggle', 1 );
	} else {
		$smarty->assign( 'gmaptoggle', 0 );
	}	
	if (isset($params["hideifnone"]) && $params["hideifnone"]) {
		$hideifnone = true;
	} else {
		$hideifnone = false;
	}
	if (isset($params["starthidden"]) && $params["starthidden"]) {
		$smarty->assign( 'gmaphidden', 1 );
	}
	if (isset($params["autozoom"])) {
		$smarty->assign( 'gmapautozoom', $params["autozoom"] );
	}

	if (isset($params["trackerfieldid"])) {
		$smarty->assign( 'gmaptrackerfieldid', $params["trackerfieldid"] );	
	} else {
		$smarty->assign( 'gmaptrackerfieldid', 0 );
	}
	
	if (isset($params["trackerinputid"])) {
		$smarty->assign( 'gmaptrackerinputid', $params["trackerinputid"] );	
	} else {
		$smarty->assign( 'gmaptrackerinputid', '' );
	}
	
	// defaults for these could perhaps be specified as params (but they might be overridden below)
	$pointx = '';
	$pointy = '';
	$pointz = '';
	$markers = array();
	
	if ($type == 'user') {
		$query = "SELECT `login`, `avatarType`, `avatarLibName`, `userId`, p1.`value` as lon, p2.`value` as lat FROM `users_users` as u ";
		$query.= "left join `tiki_user_preferences` as p1 on p1.`user`=u.`login` and p1.`prefName`=? ";
		$query.= "left join `tiki_user_preferences` as p2 on p2.`user`=u.`login` and p2.`prefName`=? ";
		$result = $tikilib->query($query, array('lon','lat'));
		while ($res = $result->fetchRow()) {
			if ($res['lon'] and $res['lon'] < 180 and $res['lon'] > -180 and $res['lat'] and $res['lat'] < 180 and $res['lat'] > -180) {
				$res['lon'] = number_format($res['lon'],5);
				$res['lat'] = number_format($res['lat'],5);

				$image = $tikilib->get_user_avatar( $res );
				$realName = $tikilib->get_user_preference( $res["login"], 'realName', '' );
				if (!$realName) {
					$nameShow = $res['login'];	
				} else {
					$nameShow = $realName;
				}
				$nameShow = '<a href="tiki-user_information.php?userId=' . $res['userId'] . '">' . $nameShow . '</a>';
				$markers[] = array($res['lat'],$res['lon'],addslashes($image).'&nbsp;'.$nameShow.'<br />Lat: '.$res['lon'].'&deg;<br /> Long: '.$res['lat'].'&deg;');
			}
		}
	}

	if ($type != 'objectlist' && $locateitemtype == 'user') {
		$smarty->assign('gmapitemtype', 'user');
		global $userlib, $user, $tiki_p_admin;
		if (!$locateitemid) {
			$locateitemid = $user;
		}
		if ($locateitemid != $user && !$userlib->user_exists($locateitemid)) {
			return tra("No such user");
		}
		if ($locateitemid != $user && $tiki_p_admin != 'y' && $tikilib->get_user_preference($locateitemid, 'user_information') == 'private') {
			return tra("The user has chosen to make his information private");
		}
		$smarty->assign('gmapitem', $locateitemid);
		$pointx = $tikilib->get_user_preference( $locateitemid, 'lon', '' );
		$pointy = $tikilib->get_user_preference( $locateitemid, 'lat', '' );
		$pointz = $tikilib->get_user_preference( $locateitemid, 'zoom', '' );
		if ($type == 'locator') {
			$ajaxlib->registerFunction('saveGmapUser');
		}
	} elseif ($type != 'objectlist' && $locateitemtype && $locateitemid) {
		global $objectlib, $attributelib, $user;
		include_once('lib/objectlib.php');
		include_once('lib/attributes/attributelib.php'); 
		$objectId = $objectlib->get_object_id($locateitemtype, $locateitemid);
		if (!$objectId) {
			return tra("No such object");
		}
		$viewPermNeeded = $objectlib->get_needed_perm($locateitemtype, 'view');
		if (!$tikilib->user_has_perm_on_object($user, $locateitemid, $locateitemtype, $viewPermNeeded)) {
			return '';
		}
		if ($type == 'locator') {
			$editPermNeeded = $objectlib->get_needed_perm($locateitemtype, 'edit');
			if (!$tikilib->user_has_perm_on_object($user, $locateitemid, $locateitemtype, $editPermNeeded)) {
				// if no perm to edit, even if type is set to locator, locator is disabled
				$type = 'item';
			}
		}
		$smarty->assign('gmapitem', $locateitemid);
		$smarty->assign('gmapitemtype', $locateitemtype);
		$attributes = $attributelib->get_attributes( $locateitemtype, $locateitemid );
		if ($locateitemtype == 'trackeritem' && !empty($params["trackerfieldid"])) {
			// There could be more than one googlemap field in trackers, thus we are not using object attributes for this purpose
			global $trklib;
			if (!is_object($trklib)) {
				include_once('lib/trackers/trackerlib.php');
			}
			$item = $trklib->get_tracker_item($locateitemid);
				
			// double check tracker perms
			if ($item['status'] == 'p' && !$tikilib->user_has_perm_on_object($user, $item['trackerId'], 'tracker', 'tiki_p_view_trackers_pending')
				|| $item['status'] == 'c' && !$tikilib->user_has_perm_on_object($user, $item['trackerId'], 'tracker', 'tiki_p_view_trackers_closed')
				|| $item['status'] == 'o' && !$tikilib->user_has_perm_on_object($user, $item['trackerId'], 'tracker', 'tiki_p_view_trackers')) {
					return '';
			}
			if ($type == 'locator') {
			// if no perm to edit, even if type is set to locator, locator is disabled
				if ($item['status'] == 'p' && !$tikilib->user_has_perm_on_object($user, $item['trackerId'], 'tracker', 'tiki_p_modify_tracker_items_pending')
					|| $item['status'] == 'c' && !$tikilib->user_has_perm_on_object($user, $item['trackerId'], 'tracker', 'tiki_p_modify_tracker_items_closed')
					|| $item['status'] == 'o' && !$tikilib->user_has_perm_on_object($user, $item['trackerId'], 'tracker', 'tiki_p_modify_tracker_items')) {
						$type = 'trackerfield';
				}
			}
			$f_value = explode(',',$item[$params["trackerfieldid"]]);
			if ( !empty($f_value[0]) && !empty($f_value[1]) ) {
				$pointx = $f_value[0];
				$pointy = $f_value[1];
			} else {
				$pointx = $defaultx;
				$pointy = $defaulty;
			}
			if ( !empty($f_value[2]) ) {
				$pointz = $f_value[2];
			} else {
				$pointz = $defaultz;
			}
			$fields = $trklib->list_tracker_fields($item['trackerId']);
			foreach ($fields["data"] as $f) {
				if ($f["fieldId"] == $params["trackerfieldid"]) {
					$options_array = $f["options_array"];
					break;
				}
			}
			if (!empty($options_array[1])) {
				$markertext = '';
				$markerfields = explode('+', $options_array[1]);
				foreach ($markerfields as $k => $m) {
					if (!empty($item[$m])) {
						if ($markertext) {
							$markertext .= '<br /><br />';	
						}
						$markertext .= preg_replace("/[\r\n|\r|\n]/", "<br />", htmlspecialchars($item[$m]));					
					}
				}
			}
			$icon = '';
			$iconx = '';
			$icony = '';
			if (!empty($options_array[2]) && $iconsize = getimagesize($options_array[2])) {
				if (isset($iconsize[0]) && isset($iconsize[1]) && $iconsize[0] && $iconsize[1]) {
					$icon = $options_array[2];
					$icon_x = $iconsize[0];
					$icon_y = $iconsize[1];
				}
			}
			if (!empty($markertext)) {
				$markers[] = array($pointy,$pointx,$markertext,$icon,$icon_x,$icon_y);
			}
			$smarty->assign('pointicon', $icon);
			$smarty->assign('pointiconx', $icon_x);
			$smarty->assign('pointicony', $icon_y);
		} else {
			if ( isset($attributes['tiki.geo.lon']) && isset($attributes['tiki.geo.lat']) ) {
				$pointx = $attributes['tiki.geo.lon'];
				$pointy = $attributes['tiki.geo.lat'];
			} else {
				$pointx = $defaultx;
				$pointy = $defaulty;
			}
			if ( isset($attributes['tiki.geo.google.zoom']) ) {
				$pointz = $attributes['tiki.geo.google.zoom'];
			} else {
				$pointz = $defaultz; 
			}
		}
		if ($type == 'locator') {
			$ajaxlib->registerFunction('saveGmapItem');
		}			
	}	
	
	if ($type == 'objectlist') {
		// An global array of objects with type, id, title, href, text is read  
		// This assumes the objects have already been filtered for permissions
		global $gmapobjectarray;
		foreach ($gmapobjectarray as $obj) {
			global $attributelib;
			include_once('lib/attributes/attributelib.php'); 
			$attributes = $attributelib->get_attributes( $obj["type"], $obj["id"] );
			if ( isset($attributes['tiki.geo.lon']) ) {
				$lon = $attributes['tiki.geo.lon'];
			} else {
				$lon = '';
			}
			if ( isset($attributes['tiki.geo.lat']) ) {
				$lat = $attributes['tiki.geo.lat'];
			} else {
				$lat = '';
			}
			
			$popup = '';
			if (!empty($obj['href'])) {
				$popup .= '<a href="' . $obj['href']  . '">';
			}
			$popup .= htmlspecialchars($obj['title']);
			if (!empty($obj['href'])) {
				$popup .= '</a>';	
			}
			if (!empty($obj['text'])) {
				$popup .= '<br /><br />';
				$popup .= $obj['text'];
			}
			
			$icon = '';
			$icon_x = '';
			$icon_y = '';           
			if (!empty($obj['icon']) && $iconsize = getimagesize($obj['icon'])) {
				if (isset($iconsize[0]) && isset($iconsize[1]) && $iconsize[0] && $iconsize[1]) {
					$icon = $obj['icon'];
					$icon_x = $iconsize[0];
					$icon_y = $iconsize[1];
				}
			}
		
			if ($lat && $lon) { 
				$markers[] = array($lat,$lon,$popup,$icon,$icon_x,$icon_y);
			}
		}
		// free up memory
		if (isset($gmapobjectarray)) {
			unset($gmapobjectarray);	
		}
	}
	
	$smarty->assign('gmapmarkers', $markers);
	$smarty->assign('pointx', $pointx);
	$smarty->assign('pointy', $pointy);
	$smarty->assign('pointz', $pointz);	
	
	if (!$markers && !$pointx && !$pointy && $hideifnone) {
		$smarty->assign('gmaphidden', 1);
	}
			
	$ret = '~np~' . $smarty->fetch('wiki-plugins/wikiplugin_googlemap.tpl') . '~/np~';
	return $ret;

}

function saveGmapDefaultxyz($feedback, $pointx, $pointy, $pointz) {
	global $tikilib, $ajaxlib, $user;
	$objResponse = new xajaxResponse();
	if (!$user) {
		$objResponse->assign($feedback, "innerHTML", tra("Not logged in"));
		return $objResponse;
	}
	if (!is_numeric($pointx) || !is_numeric($pointy) || !is_numeric($pointz) ||
		 !($pointx > -180 && $pointx < 180 && $pointy > -180 && $pointy < 180 && $pointz >= 0 && $pointz < 20) ) {
		$objResponse->assign($feedback, "innerHTML", tra("Error: Invalid Lon. and Lat. values"));
		return $objResponse;		
	}
	$tikilib->set_user_preference($user, 'gmap_defx', $pointx);
	$tikilib->set_user_preference($user, 'gmap_defy', $pointy);
	$tikilib->set_user_preference($user, 'gmap_defz', $pointz);	
	
	$objResponse->assign($feedback, "innerHTML", tra("Map view saved as default for ") . $user);
	return $objResponse;
}

function saveGmapUser($feedback, $pointx, $pointy, $pointz, $u) {
	global $prefs, $tikilib, $ajaxlib, $user, $userlib, $tiki_p_admin_users;
	$objResponse = new xajaxResponse();
	if (!($u == $user || $tiki_p_admin_users == 'y' && $u != $user && $userlib->user_exists($u))) {		
		$objResponse->assign($feedback, "innerHTML", tra("You can only set your own location"));
		return $objResponse;		
	}
	if (!is_numeric($pointx) || !is_numeric($pointy) || !is_numeric($pointz) ||
		 !($pointx > -180 && $pointx < 180 && $pointy > -180 && $pointy < 180 && $pointz >= 0 && $pointz < 20) ) {
		$objResponse->assign($feedback, "innerHTML", tra("Please select a point to set both Lon. and Lat."));
		return $objResponse;		
	}
	$tikilib->set_user_preference($u, 'lon', $pointx);
	$tikilib->set_user_preference($u, 'lat', $pointy);
	$tikilib->set_user_preference($u, 'zoom', $pointz);

	if ($prefs["user_trackersync_geo"] == 'y') {
		$userinfo = $userlib->get_user_info($u);
		$re = $userlib->get_usertracker($userinfo["userId"]);
		global $trklib;
		if (!is_object($trklib)) {
			include_once('lib/trackers/trackerlib.php');
		} 
		$itemId = $trklib->get_item_id($re['usersTrackerId'], $re['usersFieldId'], $u);                $item = $trklib->get_tracker_item($itemId);
		$fields = $trklib->list_tracker_fields($item['trackerId']);
		$fieldId = 0;
		foreach ($fields["data"] as $f) {
			if ($f["type"] == 'G' && $f["options_array"][0] == 'y') {
				$options_array = $f["options_array"];
				$fieldId = $f["fieldId"];
				break;
			}
		}
		if ($fieldId) {
			$ins_fields["data"][$fieldId] = array('fieldId' => $fieldId, 'options_array' => $options_array, 'value' => "$pointx,$pointy,$pointz", 'type' => 'G');
			$res = $trklib->replace_item($re['usersTrackerId'], $itemId, $ins_fields);
		}
	}
        
	$objResponse->assign($feedback, "innerHTML", tra("User location saved for ") . $u);
	return $objResponse;
}

function saveGmapItem($feedback, $pointx, $pointy, $pointz, $type, $itemId, $fieldId) {
	global $tikilib, $ajaxlib, $user, $objectlib, $attributelib;
	$objResponse = new xajaxResponse();
	$res = false;
	include_once('lib/objectlib.php');
	include_once('lib/attributes/attributelib.php');
	if (!is_numeric($pointx) || !is_numeric($pointy) || !is_numeric($pointz) ||
		 !($pointx > -180 && $pointx < 180 && $pointy > -180 && $pointy < 180 && $pointz >= 0 && $pointz < 20) ) {
		$objResponse->assign($feedback, "innerHTML", tra("Please select a point to set both Lon. and Lat."));
		return $objResponse;		
	}
	$editPermNeeded = $objectlib->get_needed_perm($type, 'edit');
	if ($type == 'trackeritem') {
		global $trklib;
		if (!is_object($trklib)) {
			include_once('lib/trackers/trackerlib.php');
		}
		$item = $trklib->get_tracker_item($itemId);
		$fields = $trklib->list_tracker_fields($item['trackerId']);
		foreach ($fields["data"] as $f) {
			if ($f["fieldId"] == $fieldId) {
				$options_array = $f["options_array"];
				break;
			}
		}
		if ($item['status'] == 'p' && !$tikilib->user_has_perm_on_object($user, $item['trackerId'], 'tracker', 'tiki_p_modify_tracker_items_pending')
			|| $item['status'] == 'c' && !$tikilib->user_has_perm_on_object($user, $item['trackerId'], 'tracker', 'tiki_p_modify_tracker_items_closed')
			|| $item['status'] == 'o' && !$tikilib->user_has_perm_on_object($user, $item['trackerId'], 'tracker', 'tiki_p_modify_tracker_items')) {
				$objResponse->assign($feedback, "innerHTML", tra("You cannot edit this object or no such object"));
				return $objResponse;
		}
		$ins_fields["data"][$fieldId] = array('fieldId' => $fieldId, 'options_array' => $options_array, 'value' => "$pointx,$pointy,$pointz", 'type' => 'G');
		$res = $trklib->replace_item($item['trackerId'], $itemId, $ins_fields); 
	} elseif (!$tikilib->user_has_perm_on_object($user, $itemid, $type, $editPermNeeded)) {
		$objResponse->assign($feedback, "innerHTML", tra("You cannot edit this object or no such object"));
		return $objResponse;
	}
	if (!$res) {
		// Only set attributes if not set yet (not tracker item)
		$res = $attributelib->set_attribute($type, $itemId, 'tiki.geo.lon', $pointx);
		$res = $attributelib->set_attribute($type, $itemId, 'tiki.geo.lat', $pointy);
		$res = $attributelib->set_attribute($type, $itemId, 'tiki.geo.google.zoom', $pointz);
	}
	if ($res) {
		$objResponse->assign($feedback, "innerHTML", tra("Location saved for object"));
	} else {
		$objResponse->assign($feedback, "innerHTML", tra("Error saving location"));
	}
	return $objResponse;
}
