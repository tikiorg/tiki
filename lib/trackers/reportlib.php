<?php
// $Header: /cvsroot/tikiwiki/tiki/lib/trackers/Attic/reportlib.php,v 1.1.2.1 2008/07/11 17:44:01 kerrnel22 Exp $
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class ReportLib extends TrackerLib {

	function ReportLib($db) {
		if (!$db) {
			die ("Invalid db object passed to importerlib constructor.");
		}
		$this->db = $db;
	}

	// return select list of trackers
	function getTrackers() {
		$trackers = array();

		$query = "select `trackerId`, `name`, `description` from `tiki_trackers`";
		$result = $this->query($query);
		while( $r = $result->fetchRow() ) {
			/* Add this section back in later....not relevant yet
			if (!isset($tracker["trackerId"])) {
				$tracker["trackerId"] = -1;
				$tracker["name"] = 'All';
				$tracker["description"] = 'All trackers.';
				array_push($trackers, $tracker);
			}*/ 

			$tracker["trackerId"] = $r["trackerId"];
			$tracker["name"] = $r["name"];
			$tracker["description"] = $r["description"];
			array_push($trackers, $tracker);
		}

		return $trackers;
	}

	// return list of fields for a tracker
	function getTrackerFields($trackerId) {
		$fields = '';

		$query = "select `fieldId`, `name`, `type` from `tiki_tracker_fields` where `trackerId` = ?";
		$result = $this->query($query, array($trackerId));
		while ($r = $result->fetchRow()) {
			switch($r["type"]) {
				case 'f':		// date & time
				case 'F':		// date only
				case 'j':		// javascript date & time
				case 'n':		// numeric
					$tf["fieldId"] = $r["fieldId"];
					$tf["name"] = $r["name"];
					$tf["type"] = $r["type"];
					array_push($fields["list"], $tf);
					break;
				default:
			}


		}

		return $fields;
	}

	// Generate CSV Report
	function GetCSVReport($trackerId, $dateRange, $cFrom = null, $cTo = null) {
		$data = '';
		$dateFilter = '';

		// Figure out Date Ranges
		$today = getdate();
		$q = (int)($today['mon'] / 3);
		$qm = $today['mon'] % 3;
		if ($qm > 0) {
			$q++;
		}

		switch($dateRange) {
			case 'ytd':		// Year-to-Date
				$ad = mktime(0,0,0,1,1,$today['year']);
				$zd = 'current';
				break;
			case 'qtd':		// This Quarter
				if ($q > 1) {
					$tm = ($q - 1) * 3 + 1;
				} else {
					$tm = 1;
				}
				$ad = mktime(0,0,0,$tm,1,$today['year']);
				$zd = 'current';
				break;
			case 'mtd':		// This Month
				$ad = mktime(0,0,0,$today['mon'],1,$today['year']);
				$zd = 'current';
				break;
			case 'wtd':		// This Week
				$td = $today['mday'] - $today['wday'];
				$tm = $today['mon'];
				$ty = $today['year'];
				if ($td < 1) {
					$tod = getdate(time - 86400);
					$tm = $tod['mon'];
					$ty = $tod['year'];
					$td = $tod['mday'];
				}
				$ad = mktime(0,0,0,$tm,$td,$ty);
				$zd = 'current';
				break;
			case 'ly':		// Last Year
				$ad = mktime(0,0,0,1,1,$today['year'] - 1);
				$zd = mktime(0,0,0,1,1,$today['year']);
				break;
			case 'lq':		// Last Quarter
				$lq = $q - 1;
				if ($lq < 1) {
					$ly = $today['year'] - 1;
					$lm = 10;
					$ty = $today['year'];
					$ad = mktime(0,0,0,10,1,$ly);
					$zd = mktime(0,0,0,1,1,$ty) - 60;
				} else {
					$ty = $today['year'];
					$lm = ($lq - 1) * 3 + 1;
					$tm = $lq * 3 + 1;
					$ad = mktime(0,0,0,$lm,1,$ty);
					$zd = mktime(0,0,0,$tm,1,$ty) - 60;
				}
				break;
			case 'lm':		// Last Month
				$tm = $today['mon'] - 1;
				$ty = $today['year'];
				if ($tm < 1) {
					$ty = $today['year'] - 1;
					$tm = 12;
				}
				$ad = mktime(0,0,0,$tm,1,$ty);
				$zd = mktime(0,0,0,$today['mon'],1,$today['year']);
				break;
			case 'lw':		// Last Week
				$tw = $today['wday'];
				$twoff = $tw * 86400;
				$td = $today['mday'];
				$tm = $today['mon'];
				$ty = $today['year'];
				$ad = mktime(0,0,0,$tm,$td,$ty) - $twoff - 604800;
				$zd = mktime(23,59,59,$tm,$td,$ty) - $twoff;
				break;
			case 'custom':	// Custom Range
				$ad = (int) $cFrom;
				$zd = (int) $cTo;
				break;
			default:		// Entire History

		}

		// Build filter from date range
		if (isset($zd) && $zd == 'current') {
			$dateFilter = "and `created` > $ad ";
		} else if (isset($zd)) {
			$dateFilter = "and (`created` > $ad and `created` < $zd) ";
		} else {
			echo "MOO\n";
		}

		$query = "select * from `tiki_tracker_fields` where `trackerId` = ? order by `position`";
		$result = $this->query($query, array($trackerId));
		$count = 0;
		while ($r = $result->fetchRow()) {
			// Ignore non-input and special fields.
			if ($r["type"] != "h" && $r["type"] != "s") {
				$fn[$r["fieldId"]] = $r["name"];
				$fo[$count] = $r["fieldId"];
				$fi[$r["fieldId"]] = $count;
				$ft[$r["fieldId"]] = $r["type"];
				$count++;
			}
		}

		// Display field headings in output data
		for ($count = 0; $count < count($fo); $count++) {
			$data .= $fn[$fo[$count]] . ',';
		}
		$data .= "\n";

		// Get all itemId's associated with target tracker
		$query = "select * from `tiki_tracker_items` where `trackerId` = ? " . $dateFilter . "order by `created`";
		$result = $this->query($query, array($trackerId));

//echo "$dateFilter :: $query ::: $cFrom ($ad) ::: $cTo :::" . mktime() . "\n";

		while ($r = $result->fetchRow()) {
			$query2 = "select * from `tiki_tracker_item_fields` where `itemId` = ?";
			$result2 = $this->query($query2, array($r["itemId"]));

			// Print row data
			while ($r2 = $result2->fetchRow()) {
				if (isset($fi[$r2["fieldId"]])) {
					// Date fields
					if (ereg("F|f|j", $ft[$r2["fieldId"]])) {
						if ($r2["value"] == 0) {
							$d[$fi[$r2["fieldId"]]] = '';
						} else {
							$d[$fi[$r2["fieldId"]]] = date("n/j/Y", $r2["value"]);
						}

					// Text and drop-downs
					} else if (ereg("t|d", $ft[$r2["fieldId"]])) {
						$d[$fi[$r2["fieldId"]]] = '"' . $r2["value"] . ' "';

					// Numeric and all other field types
					} else {
						$d[$fi[$r2["fieldId"]]] = $r2["value"];
					}
				}
			}

			for ($count = 0; $count < count($d); $count++) {
				if (isset($d[$count])) {
					$data .= $d[$count] . ',';
				}
			}
			$data .= "\n";

			unset($d);
		}
		$dataresult = "ok";

		return array($dataresult, $data);
	}


}

?>
