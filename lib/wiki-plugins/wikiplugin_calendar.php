<?php

function wikiplugin_calendar_help() {
        $help = tra("Includes a calendar");
        $help .="<br />";
        $help .= tra("~np~{CALENDAR(calIds=>1)}{CALENDAR}");
        $help .= "<br />";
        $help .= tra("Id is optional. If not given, all calendars viewable by default are used.");

        return $help;
}

function wikiplugin_calendar_info() {
	return array(
		'name' => tra('Calendar'),
		'documentation' => 'PluginCalendar',
		'description' => tra('Includes a calendar within the page.'),
		'prefs' => array( 'feature_calendar', 'wikiplugin_calendar' ),
		'params' => array(
			'calIds' => array(
				'required' => false,
				'name' => tra('Calendar ID'),
				'description' => tra('The calendar number (Id) to display. Default value refers to all visible calendars.'),
				'filter' => 'digits',
			),
			'viewmode' => array(
				'required' => false,
				'name' => tra('View Mode'),
				'description' => tra('The view mode for the Calendar.'),
				'filter' => 'alpha',
			),
			'viewlist' => array(
				'required' => false,
				'name' => tra('View as a List of Events'),
				'description' => tra('Show the list of events at the bottom.'),
				'filter' => 'alpha',
			),
			'withviewevents' => array(
				'required' => false,
				'name' => tra('View the List of Events'),
				'description' => tra('Decide or not to show the list of events at the bottom.'),
				'filter' => 'alpha',
			),
			'viewnavbar' => array(
				'required' => false,
				'name' => tra('View the navigation bar'),
				'description' => tra('Decide or not to show the navigation bar.'),
				'filter' => 'alpha',
			),

		),
	);
}

function wikiplugin_calendar($data, $params) {
    global $smarty, $tikilib, $prefs, $tiki_p_admin, $tiki_p_view_calendar;
    global $dbTiki, $dc, $user, $calendarlib;

    require_once("lib/calendar/calendarlib.php");

    //extract ($params,EXTR_SKIP);

		if ( empty($params['calIds']) ) {
			$params['calIds'] = array(1);
		} else {
			$params['calIds'] = explode(',',$params['calIds']);
		}
		if ( empty($params['viewlist']) ) {
			$params['viewlist'] = 'table';
		}
		if ( empty($params['viewmode']) ) {
			$params['viewmode'] = 'month';
		}
		if ( empty($params['viewnavbar']) ) {
			$params['viewnavbar'] = 'n';
		}

    $module_reference = array(
      'moduleId' => null,
      'name' => 'calendar_new',
      'params' => array( 'calIds' => $params['calIds'], 'viewnavbar'=> $params['viewnavbar'],
												 'viewlist'=> $params['viewlist'],
												 'viewmode' => $params['viewmode'] ),
      'position' => null,
      'ord' => null,
    );

    global $modlib; require_once 'lib/modules/modlib.php';
    $out = $modlib->execute_module( $module_reference );

		if ( !empty($params['withviewevents']) ) {
			$module_reference['params']['viewlist'] = 'list';
    	$out .= "<div>".$modlib->execute_module( $module_reference )."</div>";
		}
		

    return "<div>$out</div>";

}
