<?php

function wikiplugin_trackertimeline_info() {
	return array(
		'name' => tra( 'Tracker Timeline' ),
		'description' => tra('Timeline view of a tracker, can be used to display event schedules or gantt charts.'),
		'prefs' => array( 'wikiplugin_trackertimeline', 'feature_trackers' ),
		'params' => array(
			'tracker' => array(
				'required' => true,
				'name' => tra('Tracker ID'),
				'description' => tra('Numeric value'),
			),
			'title' => array(
				'required' => true,
				'name' => tra('Title Field'),
				'description' => tra('Tracker Field ID containing the item title.'),
			),
			'summary' => array(
				'required' => true,
				'name' => tra('Summary Field'),
				'description' => tra('Tracker Field ID containing the summary of the item. The summary will be displayed on the timeline when the item is focused.'),
			),
			'start' => array(
				'required' => true,
				'name' => tra('Start Date'),
				'description' => tra('Tracker Field ID containing the element start date. The field must be a datetime/jscalendar field.'),
			),
			'end' => array(
				'required' => true,
				'name' => tra('End Date'),
				'description' => tra('Tracker Field ID containing the element end date. The field must be a datetime/jscalendar field.'),
			),
			'group' => array(
				'required' => true,
				'name' => tra('Element Group'),
				'description' => tra('Tracker Field ID containing the element\'s group. Elements of a same group are displayed on the same row.'),
			),
			'lower' => array(
				'required' => true,
				'name' => tra('Lower Bound'),
				'description' => tra('Date from which element should be displayed. Date must be provided in YYYY-MM-DD HH:mm:ss format.'),
			),
			'upper' => array(
				'required' => true,
				'name' => tra('Upper Bound'),
				'description' => tra('Date until which element should be displayed. Date must be provided in YYYY-MM-DD HH:mm:ss format.'),
			),
		),
	);
}

function wikiplugin_trackertimeline( $data, $params ) {
	global $trklib, $smarty, $tikilib;
	require_once 'lib/trackers/trackerlib.php';

	if( ! isset( $params['tracker'] ) )
		return "^" . tr("Missing parameter: %0", 'tracker') . "^";

	$start = strtotime( $params['lower'] );
	$end = strtotime( $params['upper'] );
	$size = $end - $start;

	if( $size <= 0 )
		return "^" . tr("Start date after end date.") . "^";
	
	$fieldIds = array( 
		$params['title'] => 'title', 
		$params['summary'] => 'summary', 
		$params['start'] => 'start', 
		$params['end'] => 'end', 
		$params['group'] => 'group',
	);

	$fields = array();
	foreach( $fieldIds as $id => $label )
		$fields[$id] = $trklib->get_tracker_field( $id );

	$items = $trklib->list_items( $params['tracker'], 0, -1, '', $fields );

	$data = array();
	foreach( $items['data'] as $item ) {
		// Collect data
		$detail = array( 'item' => $item['itemId'] );
		foreach( $item['field_values'] as $field ) {
			$detail[ $fieldIds[$field['fieldId']] ] = $field['value'];
		}

		// Filter elements
		if( $detail['start'] >= $detail['end'] )
			continue;
		if( $detail['end'] <= $start || $detail['start'] > $end )
			continue;

		$detail['lstart'] = max( $start, $detail['start'] );
		$detail['lend'] = min( $end, $detail['end'] );
		$detail['lsize'] = round( ( $detail['lend'] - $detail['lstart'] ) / $size * 80 );

		$detail['fstart'] = date( 'H:i', $detail['start'] );
		$detail['fend'] = date( 'H:i', $detail['end'] );
		$detail['psummary'] = $tikilib->parse_data( $detail['summary'] );

		$detail['encoded'] = json_encode( $detail );

		// Add to data list
		if( ! array_key_exists( $detail['group'], $data ) )
			$data[$detail['group']] = array();
		$data[ $detail['group'] ][] = $detail;
	}

	$new = array();
	foreach( $data as $group => &$list ) {
		wp_ttl_organize( $group, $start, $size, $list, $new );
	}
	$data = array_merge( $data, $new );
	ksort($data);

	$smarty->assign( 'wp_ttl_data', $data );

	$layout = array(
		'size' => round( 3600 / $size * 80 ),
		'blocks' => array(
		),
	);

	if( $start % 3600 ) {
		$pos = $start + 3600 - $start % 3600;
		$layout['pad'] = round( (3600-$start%3600) / $size * 80 );
	 } else {
		$pos = $start;
		$layout['pad'] = 0;
	}

	for( $i = $pos; $end > $i; $i += 3600 ) {
		$layout['blocks'][] = date( 'H:i', $i );
	}

	$smarty->assign( 'layout', $layout );

	return $smarty->fetch('wiki-plugins/wikiplugin_trackertimeline.tpl');
}

function wp_ttl_organize( $name, $base, $size, &$list, &$new ) {
	usort( $list, 'wp_ttl_sort_cb' );

	$first = $list;
	$list = array();
	$remaining = array();

	$pos = $base;
	foreach( $first as $item ) {
		if( $item['lstart'] < $pos ) {
			$remaining[] = $item;
			continue;
		}

		$item['lpad'] = round( ( $item['lstart'] - $pos ) / $size * 80 );
		$pos = $item['lend'];

		$list[] = $item;
	}

	if( count( $remaining ) ) {
		wp_ttl_organize( "$name ", $base, $size, $remaining, $new );
		$new["$name "] = $remaining;
	}
}

function wp_ttl_sort_cb( $a, $b ) {
	if( $a['start'] == $b['start'] )
		return 0;
	if( $a['start'] < $b['start'] )
		return -1;
	if( $a['start'] > $b['start'] )
		return 1;
}

?>
