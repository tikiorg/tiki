<?php

class GeoLib
{
	
	function geocode($where) {
		global $prefs;
		$where = stripslashes($where);
		$whereurl = urlencode($where);
		$googlekey = $prefs["gmap_key"];
		if (!$googlekey) {
			return false;
		}
		$location = file("http://maps.google.com/maps/geo?q=$whereurl&output=csv&key=$googlekey");
		list ($stat,$acc,$north,$east) = explode(",",$location[0]);
		$ret = array(
			'status' => $stat,
			'accuracy' => $acc,
			'lat' => $north,
			'lon' => $east,
		);
		if ($stat != '200') {
			return false;
		}
		return $ret;
	}
	
	function geofudge($geo) {
		if (!$geo) {
			return false;
		}
		if (empty($geo["lon"]) || empty($geo["lat"])) {
			return array("lon" => 0, "lat" => 0);
		}
		$geo["lon"] = $geo["lon"] + rand(0, 10000) / 8000;
		$geo["lat"] = $geo["lat"] + rand(0, 10000) / 10000;
		return $geo;
	}
	
	function setTrackerGeo($itemId, $geo) {
		global $prefs, $trklib;
		if (!is_object($trklib)) {
			include_once('lib/trackers/trackerlib.php');
		}
		$item = $trklib->get_tracker_item($itemId);
		$fields = $trklib->list_tracker_fields($item['trackerId']);
		foreach ($fields["data"] as $f) {
			if ($f["type"] == 'G' && $f["options_array"][0] == 'y') {
				$fieldId = $f["fieldId"];
				$options_array = $f["options_array"];
				$pointx = $geo['lon'];
				$pointy = $geo['lat'];
				$pointz = $prefs["gmap_defaultz"];
				break;
			}
		}
		if (isset($fieldId)) {
			$ins_fields["data"][$fieldId] = array('fieldId' => $fieldId, 'options_array' => $options_array, 'value' => "$pointx,$pointy,$pointz", 'type' => 'G');
			$res = $trklib->replace_item($item['trackerId'], $itemId, $ins_fields);
		}
	}

}

$geolib = new GeoLib;