<?php
/* ***** BEGIN LICENSE BLOCK *****
 * Version: MPL 1.1/GPL 2.0/LGPL 2.1
 *
 * The contents of this file are subject to the Mozilla Public License Version
 * 1.1 (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Software distributed under the License is distributed on an "AS IS" basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
 * for the specific language governing rights and limitations under the
 * License.
 *
 * The Original Code is SUMO tools
 *
 * The Initial Developer of the Original Code is
 * Mozilla Corporation.
 * Portions created by the Initial Developer are Copyright (C) 2009
 * the Initial Developer. All Rights Reserved.
 *
 * Contributor(s):
 *  Paul Craciunoiu <pcraciunoiu@mozilla.com>
 *
 * Alternatively, the contents of this file may be used under the terms of
 * either the GNU General Public License Version 2 or later (the "GPL"), or
 * the GNU Lesser General Public License Version 2.1 or later (the "LGPL"),
 * in which case the provisions of the GPL or the LGPL are applicable instead
 * of those above. If you wish to allow use of your version of this file only
 * under the terms of either the GPL or the LGPL, and not to allow others to
 * use your version of this file under the terms of the MPL, indicate your
 * decision by deleting the provisions above and replace them with the notice
 * and other provisions required by the GPL or the LGPL. If you do not delete
 * the provisions above, a recipient may use your version of this file under
 * the terms of any one of the MPL, the GPL or the LGPL.
 *
 * ***** END LICENSE BLOCK ***** */
/**
 * Metrics Dashboard Library class
 * 
 * This implements the metrics library functions, used in
 * tiki-metrics.php, tiki-admin_metrics.php, metrics-tab.php
 * @author Paul Craciunoiu <pcraciunoiu@mozilla.com>
 * @version 1.0
 * @package metrics
 */

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: /index.php");
  exit;
}

/**
 * Global variables for datatype and range.
 */
$AR_DATATYPE = array(
			'i' => tra('Integer (i)')
			, '%' => tra('Percentage (%)')
			, 'f' => tra('Float (f)')
			, 'L' => tra('List (L)')
			);
$AR_RANGE = array(
			'+' => tra('Daily (+)')
		, '@' => tra('Monthly and weekly (@)')
		, '-' => tra('Weekly (-)')
		);

class MetricsLib extends TikiDb_Bridge
{
    
	/**
	 * Get all existing metrics from the SQL table metrics_metric
	 * See SQL table structure for details.
	 * @return associated array $key => $value
	 * 		metric_id => associative array of SQL table representing metric
	 */
	function getAllMetrics() {
		$query = "SELECT *
					FROM metrics_metric";
		$result = $this->query($query,array());
		$ret = array();
		while ($res = $result->fetchRow()) {
			$res = $this->_metricConvertValues($res);
			$ret[$res['metric_id']] = $res;
		}
	
		return $ret;	
	}
	
	/**
	 * Get all existing assigned metrics from the SQL table metrics_assigned
	 * See SQL table structure for details.
	 * @return associated array $key => $value
	 * 		assigned_id => associative array of SQL table representing 
	 * 		assigned metric
	 */
	function getAllMetricsAssigned() {
		$query = "SELECT *
					FROM metrics_assigned";
		$result = $this->query($query,array());
		$ret = array();
		while ($res = $result->fetchRow()) {
			$ret[$res['assigned_id']] = $res;
		}
	
		return $ret;	
	}

	/**
	 * Get all existing tabs from the SQL table metrics_tab, ordered
	 * See SQL table structure for details.
	 * @return associated array $key => $value
	 * 		tab_id => associative array of SQL table representing tab
	 */
	function getAllTabs() {
		$query = "SELECT *
					FROM metrics_tab ORDER BY tab_order";
		$result = $this->query($query,array());
		$ret = array();
		while ($res = $result->fetchRow()) {
			$ret[$res['tab_id']] = $res;
		}
		return $ret;	
	}
	

	/**
	 * Get range array.
	 * @return global $AR_RANGE
	 */
	function getMetricsRangeAll() {
		global $AR_RANGE;
		return $AR_RANGE;
	}
	
	/**
	 * Get datatype array.
	 * @return global $AR_DATATYPE
	 */
	function getMetricsDatatypeAll() {
		global $AR_DATATYPE;
		return $AR_DATATYPE;
	}
	
	/**
	 * Creates or updates a metric. If $metric_id is specified,
	 * 		the function updates an existing metric. Otherwise,
	 * 		a new metric is created.
	 * @param integer $metric_id (optional)
	 * @param string $metric_name name of metric
	 * @param string $metric_range one letter range value
	 * 		see $AR_RANGE for details
	 * @param string $metric_datatype one letter datatype value
	 * 		see $AR_DATATYPE for details
	 * @param string $metric_query SQL query string
	 * @return query result
	 */
	function createUpdateMetric($metric_id, $metric_name, $metric_range, $metric_datatype, $metric_query, $metric_dsn = 'local') {
		$values = array($metric_name, $metric_range, $metric_datatype, $metric_query, $metric_dsn);
		
		if (empty($metric_id) || (!is_numeric($metric_id))) {
			$query = "INSERT INTO `metrics_metric` (`metric_id`, `metric_name`, `metric_range`, `metric_datatype`, `metric_lastupdate`, `metric_query`, `metric_dsn`)
					VALUES (NULL, ?, ?, ?, CURRENT_TIMESTAMP, ?, ?)";
		}
		else {
			$query = "UPDATE `metrics_metric` SET `metric_name` = ?, `metric_range` = ?, `metric_datatype` = ?, `metric_lastupdate` = CURRENT_TIMESTAMP, `metric_query` = ?, `metric_dsn` = ? WHERE `metric_id` = ?";
			$values[] = $metric_id;
		}
		
		$res = $this->query($query, $values);
		return $res;
	}

	/**
	 * Remove a metric by id.
	 * @param integer $metric_id id of metric to remove
	 * @return query result
	 */
	function removeMetricById($metric_id) {
		$query = "DELETE FROM `metrics_metric` WHERE `metric_id` = ?";
		return $this->query($query, array($metric_id), 1);
	}
	
	/**
	 * Remove an assigned metric by id.
	 * @param integer $assigned_id id of assigned metric to remove
	 * @return query result
	 */
	function removeMetricAssignedById($assigned_id) {
		$query = "DELETE FROM `metrics_assigned` WHERE `assigned_id` = ?";
		return $this->query($query, array($assigned_id), 1);
	}

	/**
	 * Creates or updates a tab. If $tab_id is specified,
	 * 		the function updates an existing tab. Otherwise,
	 * 		a new tab is created.
	 * @param integer $tab_id (optional) id of tab
	 * @param string $tab_name name of metric
	 * @param integer $tab_order integer order of tab
	 * @param string $tab_content content, will be
	 * 		smarty parsed
	 * @return query result
	 */
	function createUpdateTab($tab_id, $tab_name, $tab_order, $tab_content) {
		$values = array($tab_name, $tab_order, $tab_content);
		
		if (empty($tab_id) || (!is_numeric($tab_id))) {
			$query = "INSERT INTO `metrics_tab` (`tab_id`, `tab_name`, `tab_order`, `tab_content`)
					VALUES (NULL, ?, ?, ?)";
		}
		else {
			$query = "UPDATE `metrics_tab` SET `tab_name` = ?, `tab_order` = ?, `tab_content` = ? WHERE `tab_id` = ?";
			$values[] = $tab_id;
		}
		$res = $this->query($query, $values);
		return $res;
	}

	/**
	 * Creates or updates an assigned metric. If $assigned_id is specified,
	 * 		the function updates an existing assigned metric. Otherwise,
	 * 		a new assigned metric is created.
	 * @param integer $assigned_id (optional)
	 * @param integer $metric_id id of metric
	 * @param integer $tab_id id of tab
	 * @return query result
	 */
	function createUpdateMetricAssigned($metric_assigned_id, $metric_id, $tab_id) {
		$values = array($metric_id, $tab_id);
		
		if (empty($metric_assigned_id) || (!is_numeric($metric_assigned_id))) {
			$query = "INSERT INTO `metrics_assigned` (`assigned_id`, `metric_id`, `tab_id`)
					VALUES (NULL, ?, ?)";
		}
		else {
			$query = "UPDATE `metrics_assigned` SET `metric_id` = ?, `tab_id` = ? WHERE `assigned_id` = ?";
			$values[] = $metric_assigned_id;
		}
		$res = $this->query($query, $values);
		return $res;
	}

	/**
	 * Remove a tab by id.
	 * @param integer $tab_id id of tab to remove
	 * @return query result
	 */
	function removeTabById($tab_id) {
		$query = "DELETE FROM metrics_tab WHERE `tab_id` = ?";
		return $this->query($query, array($tab_id), 1);
	}
	
	/**
	 * Get a metric by id.
	 * @param integer $metric_id id of metric
	 * @return associative array representing SQL row in table.
	 */
	function getMetricById($metric_id) {
		$query = "SELECT *
				FROM metrics_metric
				WHERE metric_id = ?";
		$result = $this->query($query, array($metric_id), 1);
		if ($res = $result->fetchRow()) {
			$res = $this->_metricConvertValues($res);
		}
		return $res;
	}

	/**
	 * Get a metric by name.
	 * @param integer $metric_name name of metric
	 * @return associative array representing SQL row in table.
	 */
	function getMetricByName($metric_name) {
		$query = "SELECT *
				FROM metrics_metric
				WHERE metric_name = ?";
		$res = $this->query($query, array($metric_name), 1);
		if ($res = $result->fetchRow()) {
			$res = $this->_metricConvertValues($res);
		}
		return $res;
	}

	/**
	 * Get a tab by id.
	 * @param integer $tab_id id of tab
	 * @return associative array representing SQL row in table.
	 */
	function getTabById($tab_id) {
		$query = "SELECT *
				FROM metrics_tab
				WHERE tab_id = ?";
		$result = $this->query($query, array($tab_id), 1);
		$res = $result->fetchRow();
		return $res;
	}

	/**
	 * Get a tabb by name.
	 * @param integer $tab_name name of tab
	 * @return associative array representing SQL row in table.
	 */
	function getTabByName($tab_name) {
		$query = "SELECT *
				FROM metrics_tab
				WHERE tab_name = ?";
		$result = $this->query($query, array($tab_name), 1);
		$res = $result->fetchRow();
		return $res;
	}

	/**
	 * Get a assigned metric by id.
	 * @param integer $assigned_id id of assigned metric
	 * @return associative array representing SQL row in table.
	 */
	function getMetricAssignedById($assigned_id) {
		$query = "SELECT *
				FROM metrics_assigned
				WHERE assigned_id = ?";
		$result = $this->query($query, array($assigned_id), 1);
		$res = $result->fetchRow();
		return $res;
	}
	
	/**
	 * Get a assigned metrics by tab id.
	 * @param integer $tab_id id of tab
	 * @return associative array representing SQL row in table.
	 */
	function getAssignedMetricsByTabId($tab_id) {
		$query = "SELECT *
				FROM metrics_assigned NATURAL JOIN metrics_metric
				WHERE tab_id = ?";
		$result = $this->query($query,array($tab_id));
		$ret = array();
		while ($res = $result->fetchRow()) {
			$res = $this->_metricConvertValues($res);
			$ret[$res['metric_id']] = $res;
		}
	
		return $ret;	
	}
	
	/**
	 * Convert values in associative array to keep both the codename 
	 * and the wording for range and datatype
	 * @param array $res associative array of row
	 * @return same associative array, modified
	 */
	function _metricConvertValues($res) {
		global $AR_DATATYPE, $AR_RANGE;
		$res['metric_range_id'] = $res['metric_range'];
		$res['metric_datatype_id'] = $res['metric_datatype'];
		$res['metric_range'] = $AR_RANGE[$res['metric_range']];
		$res['metric_datatype'] = $AR_DATATYPE[$res['metric_datatype']];
		return $res;
	}
	
	function getMetricsData( $tab_info, $range_type, $converted_range, $date_field = 'date_field' ) {
		global $prefs, $tikilib;

		$date_from = $converted_range['date_from'];
		$date_to = $converted_range['date_to'];
		$range_group = $converted_range['range_group'];
		$timeperiod = $converted_range['timeperiod'];

		//build the date range
		//handle pastresults
		if ($prefs['metrics_pastresults'] == 'y') {
			$date_from_past = date(DEFAULT_DATE_FORMAT, strtotime("-" . $prefs['metrics_pastresults_count'] . ' '
					. $timeperiod, strtotime($date_from)));
			$date_range = "(`$date_field` >= '$date_from_past' AND `$date_field` <= '$date_to')";
		} else {
			$date_range = "(`$date_field` <= '$date_to')";
		}

		//get assigned metrics
		$metrics = $this->getAssignedMetricsByTabId( $tab_info['tab_id'] );

		$m = array();
		$m_id = array();
		//main part of this file, runs the SQL queries for assigned metrics
		foreach ($metrics as $metric_id => $metric) {
			//skip metrics that shouldn't show up for different ranges
			if (($range_type == 'custom') && ($metric['metric_range_id'] == '@'))
				continue;
			if (($range_type == 'custom' || $range_type == 'monthof') && ($metric['metric_range_id'] == '-'))
				continue;
			$n = $metric['metric_name'];
			$q = $metric['metric_query'];
			if (strpos($q, '$date_range$')) {
				$q = str_replace('$date_range$', $date_range, $q);
				$range_groupby = strpos($q, 'GROUP BY') == FALSE ? 'GROUP BY ' : '';
				$range_groupby .= $range_group;
				$q = str_replace('$range_groupby$', $range_groupby, $q);
			}
			
			if( $db = $tikilib->get_db_by_name( $metric['metric_dsn'] ) ) {
				$temp_result = $db->fetchAll( $q );
				$m[$n]['result'] = $temp_result;
				$m[$n]['range'] = $metric['metric_range'];
				$m[$n]['range_id'] = $metric['metric_range_id'];
				$m[$n]['datatype'] = $metric['metric_datatype'];
				$m[$n]['datatype_id'] = $metric['metric_datatype_id'];
				$m_id[$metric_id] = $m[$n];
				$m[$n]['metric_id'] = $metric_id;
				$m_id[$metric_id]['metric_name'] = $n;
			}
		}

		return array(
			'ids' => $m_id,
			'data' => $m,
		);
	}
}
/* Editor configuration
Local Variables:
   tab-width: 4
   c-basic-offset: 4
End:
* vim: fdm=marker tabstop=4 shiftwidth=4 noet:
*/
