<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_piwik.php 56291 2015-09-22 02:32:43Z bsfez $

function wikiplugin_piwik_info()
{
    return array(
        'name' => tra('Piwik'),
        'documentation' => 'PluginPiwik',
        'description' => tr('Embed a Piwik preformatted report (widget module) - Piwik Analytics is required.
                            To use this plugin you have to grant in your Piwik view permission to anonymous for the selected "Site Id" or to add a token authentification parameter.'),
        'prefs' => array( 'wikiplugin_piwik' ),
        'iconname' => 'chart',
        'introduced' => 15,
        'tags' => array( 'basic' ),
        'params' => array(
            'piwikserverurl' => array(
                'required' => true,
                'name' => tra('Piwik server url'),
                'description' => tr('The url to your Piwik Server, where data for the report are collected and available.') . ' <code>http(s)://yourpiwik.tld/index.php?</code> ' . '<br />'
                                    . tr('In your Piwik, your selected site (Site Id) MUST have view permission for anonymous OR you can insert in your Piwik server url a token authentification parameter.') . '<br />'
                                    . '<code>http(s)://yourpiwik.tld/index.php&token_auth=yourtokencode</code> ' . tr('Important : token_auth is visible in the html code and must be used in private page accessible to trusted users.'),
                'since' => '15',
                'default' => '',
            ),

            'idSite' => array(
                'required' => true,
                'name' => tra('Site Id'),
                'description' => tr('The ID of your website in Piwik. To be improved.'),
                'since' => '15',
                'filter' => 'digits',
                'default' => '',
            ),

            'moduleToWidgetize' => array(
                'required' => false,
                'name' => tra('Module To Widgetize'),
                'description' => tr('Piwik widget module to be used (as described in the widget section of your Piwik server).'),
                'since' => '15',
                'default' => 'Live',
                'options' => array(
                    array('text' => tra('Visits Time'), 'value' => 'VisitTime'),
                    array('text' => tra('Visits Summary'), 'value' => 'VisitsSummary'),
                    array('text' => tra('User Country Map'), 'value' => 'UserCountryMap'),
                    array('text' => tra('Live'), 'value' => 'Live'),
                    array('text' => tra('Visitors Interest'), 'value' => 'VisitorInterest'),
                    array('text' => tra('Visits Frequency'), 'value' => 'VisitFrequency'),
                    array('text' => tra('Visitor Resolution'), 'value' => 'Resolution'),
                    array('text' => tra('User Language'), 'value' => 'UserLanguage'),
                    array('text' => tra('Visitor Devices'), 'value' => 'DevicesDetection'),
                    array('text' => tra('Actions'), 'value' => 'Actions'),
                    array('text' => tra('Referrers'), 'value' => 'Referrers'),
                    array('text' => tra('SEO'), 'value' => 'SEO'),
                ),
            ),

            'actionToWidgetize' => array(
                'required' => false,
                'name' => tra('Action To Widgetize'),
                'description' => tr('Piwik action type to reports (as described in the widget section of your Piwik server).'),
                'since' => '15',
                'default' => 'widget',
                'options' => array(
                    // Module visitTime
                    array('text' => tra('By day of the week'), 'value' => 'getByDayOfWeek'),
                    array('text' => tra('Visit Information Per Local Time'), 'value' => 'getVisitInformationPerLocalTime'),
                    array('text' => tra('Visit Information Per Server Time'), 'value' => 'getVisitInformationPerServerTime'),
                    // Module VisitsSummary
                    array('text' => tra('Evolution Graph'), 'value' => 'getEvolutionGraph'),
                    array('text' => tra('Index'), 'value' => 'index'),
                    // Module UserCountryMap
                    array('text' => tra('RealTime Map'), 'value' => 'realtimeMap'),
                    array('text' => tra('Visitor Map'), 'value' => 'visitorMap'),
                    array('text' => tra('Country'), 'value' => 'getCountry'),
                    array('text' => tra('Continent'), 'value' => 'getContinent'),
                    // Module Live
                    array('text' => tra('Simple Last Visit Count'), 'value' => 'getSimpleLastVisitCount'),
                    array('text' => tra('Widget'), 'value' => 'widget'),
                    array('text' => tra('Visitor Profile Popup'), 'value' => 'getVisitorProfilePopup'),
                    // Module VisitorInterest
                    array('text' => tra('Number Of Visits Per Visit Duration'), 'value' => 'getNumberOfVisitsPerVisitDuration'),
                    array('text' => tra('Number Of Visits Per Page'), 'value' => 'getNumberOfVisitsPerPage'),
                    array('text' => tra('Number Of Visits By Visit Count'), 'value' => 'getNumberOfVisitsByVisitCount'),
                    array('text' => tra('Number Of Visits By Days Since Last'), 'value' => 'getNumberOfVisitsByDaysSinceLast'),
                    // Module VisitFrequency
                    array('text' => tra('Evolution Graph'), 'value' => 'getEvolutionGraph'), //Duplicate from line 77
                    array('text' => tra('Sparklines'), 'value' => 'getSparklines'),
                    // Module Resolution
                    array('text' => tra('Screen Resolution'), 'value' => 'getResolution'),
                    // Module UserLanguage
                    array('text' => tra('Language'), 'value' => 'getLanguage'),
                    array('text' => tra('Language Code'), 'value' => 'getLanguageCode'),
                    // Module DevicesDetection
                    array('text' => tra('Device type'), 'value' => 'getType'),
                    array('text' => tra('Browsers'), 'value' => 'getBrowsers'),
                    array('text' => tra('Browser Versions'), 'value' => 'getBrowserVersions'),
                    array('text' => tra('Os Families'), 'value' => 'getOsFamilies'),
                    array('text' => tra('Os Versions'), 'value' => 'getOsVersions'),
                    // Module Actions
                    array('text' => tra('Pages'), 'value' => 'getPageUrls'),
                    array('text' => tra('Entry pages'), 'value' => 'getEntryPageUrls'),
                    array('text' => tra('Exit pages'), 'value' => 'getExitPageUrls'),
                    array('text' => tra('Outlinks'), 'value' => 'getOutlinks'),
                    // Module Referrers
                    array('text' => tra('All referrers'), 'value' => 'getAll'),
                    array('text' => tra('Search Engines'), 'value' => 'getSearchEngines'),
                    // Module SEO
                    array('text' => tra('SEO Ranking (slow)'), 'value' => 'getRank'),
                ),
            ),

            'period' => array(
                'required' => false,
                'name' => tra('Statistics period'),
                'description' => tr('Data display duration. If range is selected you must enter the start and end the date.'),
                'since' => '15',
                'default' => 'day',
                'options' => array(
                    array('text' => tra('Day'), 'value' => 'day'),
                    array('text' => tra('Week'), 'value' => 'week'),
                    array('text' => tra('Month'), 'value' => 'month'),
                    array('text' => tra('Year'), 'value' => 'year'),
                    array('text' => tra('Range'), 'value' => 'range'),
                ),
            ),

            'startdate' => array(
                'required' => false,
                'name' => tra('Date or Start date'),
                'description' => tr('Enter date or start date for the data to be displayed (yesterday by default). Possible values are: today, yesterday, and yyyy-mm-dd.'),
                'since' => '15',
                'default' => 'yesterday',
            ),

            'enddate' => array(
                'required' => false,
                'name' => tra('End date'),
                'description' => tr('Enter end date (format yyyy-mm-dd) for the data to be displayed (only if range period is selected).'),
                'since' => '15',
                'default' => '',
            ),

            'width' => array(
                'required' => false,
                'name' => tra('Module width'),
                'description' => tr('Optional, width of the module in px or % (100% by default).'),
                'since' => '15',
                'default' => '100%',
            ),

            'height' => array(
                'required' => false,
                'name' => tra('Module height'),
                'description' => tr('Optional, height of the module in px.'), // Would be nice to have this as auto - checking if number of rows is applicable.
                'since' => '15',
                'default' => '',
            ),

            'scrolling' => array(
                'required' => false,
                'name' => tra('Iframe Scrolling'),
                'description' => tr('Optional, scrolling of the iframe that contain the module (no by default).'),
                'since' => '15',
                'default' => 'no',
                'options' => array(
                    array('text' => tra('No'), 'value' => 'no'),
                    array('text' => tra('Yes'), 'value' => 'yes'),
                ),
            ),
        )
    );
}

function wikiplugin_piwik($data, $params)
{
    global $tikilib;

    $plugininfo = wikiplugin_piwik_info();
    $default = array();
    foreach ($plugininfo['params'] as $key => $param) {
        $default["$key"] = $param['default'];
    }
    $params = array_merge($default, $params);

    if (empty($params['piwikserverurl'])) {
        return '' . tra('Plugin TikiPiwik error: Piwik server url.');
    }

    if (empty($params['idSite'])) {
        return '' . tra('Missing Site Id error: Site Id is required.');
    }


// Issue with date range
// If ($params['period']) = range) the enddate parameter should be added as well as a ',' is to be inserted between the 2 date value so it look as follow; &date='.$params['startdate'].','.$params['enddate'].'

    $iframe = ('<iframe src="'.$params['piwikserverurl'].'&module=Widgetize&action=iframe&widget=1&moduleToWidgetize='.$params['moduleToWidgetize'].'&actionToWidgetize='.$params['actionToWidgetize'].'&idSite='.$params['idSite'].'&period='.$params['period'].'&date='.$params['startdate'].'&disableLink=1&widget=1" scrolling="'.$params['scrolling'].'" frameborder="0" marginheight="0" marginwidth="0" width="'.$params['width'].'" height="'.$params['height'].'"></iframe>');

    return '~np~' . $iframe . '~/np~';
}
