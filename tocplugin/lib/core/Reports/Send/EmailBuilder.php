<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Build an periodic report e-mail with the changes
 * in Tiki for the objects a user is watching.
 *
 * @package Tiki
 * @subpackage Reports
 */
class Reports_Send_EmailBuilder
{
    /**
     * @param TikiLib $tikilib
     * @return null
     */
    public function __construct(TikiLib $tikilib, Reports_Send_EmailBuilder_Factory $factory)
    {
        $this->tikilib = $tikilib;
        $this->factory = $factory;
    }

    public function emailBody($user_data, $report_preferences, $report_cache)
    {
        global $prefs;
        include_once('lib/smarty_tiki/modifier.username.php');

        if (isset($report_cache[0])) {
            $base_url = $report_cache[0]['data']['base_url'];
        } else {
            $base_url = "http://" . $prefs['cookie_domain'] . "/"; // TODO: better handling for https and such
        }

        $smarty = TikiLib::lib('smarty');

        $smarty->assign('report_preferences', $report_preferences);
        $smarty->assign('report_user', ucfirst(smarty_modifier_username($user_data['login'])));
        $smarty->assign('report_interval', ucfirst($report_preferences['interval']));
        $smarty->assign('report_date', date("l d.m.Y"));
        $smarty->assign('report_site', $this->tikilib->get_preference('browsertitle'));

        if ($report_preferences['last_report'] != '0000-00-00 00:00:00') {
            $smarty->assign('report_last_report_date', TikiLib::date_format($this->tikilib->get_preference('long_date_format'), strtotime($report_preferences['last_report'])));
        }

        $smarty->assign('report_total_changes', count($report_cache));

        $smarty->assign('report_body', $this->makeEmailBody($report_cache, $report_preferences));

        $userWatchesUrl = $base_url . 'tiki-user_watches.php';

        if ($report_preferences['type'] == 'html') {
            $userWatchesUrl = "<a href=\"{$userWatchesUrl}\">{$userWatchesUrl}</a>";
        }

        $smarty->assign('userWatchesUrl', $userWatchesUrl);

        $mail_data = $smarty->fetch("mail/report.tpl");

        return $mail_data;
    }

    /**
     * Organize $report_cache array by event type
     *
     * @param array $report_cache
     * @return array new array with events organized by type
     */
    private function makeChangeArray(array $report_cache)
    {
        $change_array = array();

        foreach ($report_cache as $change) {
            $indexIdentifier = $change['event'];

            if (isset($change['data']['action'])) {
                $indexIdentifier .= $change['data']['action'];
            }

            if (isset($change['data']['galleryId'])) {
                $indexIdentifier .= '_' . $change['data']['galleryId'];
            } else if (isset($change['data']['pageName'])) {
                $indexIdentifier .= '_' . $change['data']['pageName'];
            } else if (isset($change['data']['categoryId'])) {
                $indexIdentifier .= '_' . $change['data']['categoryId'];
            }

            $change_array[$indexIdentifier][] = $change;
        }

        return $change_array;
    }

    /**
     * Generate the e-mail body
     *
     * @param array $report_cache changes for objects the user is watching
     * @param array $report_preferences user preferences for receiving the report
     * @return string email body string
     */
    public function makeEmailBody(array $report_cache, array $report_preferences)
    {
		$userlib = TikiLib::lib('user');

        $change_array = $this->makeChangeArray($report_cache);
        $body = '';

        $morechanges = 0;
        foreach ($change_array as $eventName => $changes) {

            $eventObject = $this->factory->build($changes[0]['event']);

            $body .= '<b>' . $eventObject->getTitle() . "</b><br />\n";

            foreach ($changes as $key => $change) {
                if ($report_preferences['view']=="short" AND $key>0) {
                    $morechanges++;
                } elseif ($report_preferences['view']=="detailed" OR $key==0) {
                    if ($morechanges > 0) {
                        $body .= "   " . tr('and %0 more changes of the same type.', $morechanges) . "<br>\n";
                        $morechanges = 0;
                    }

                    if ($report_preferences['type'] == 'plain') {
                        $body .= "   ";
                    } else {
                        $body .= "&nbsp;&nbsp;&nbsp;";
                    }

                    $body .= $this->tikilib->get_short_datetime(strtotime($change['time'])) . ": ";

                    if (isset($change['data']['user'])) {
                        include_once('lib/smarty_tiki/modifier.username.php');
                        $change['data']['user'] = smarty_modifier_username($change['data']['user']);
                    }

                    if (isset($change['data']['editUser'])) {
                        include_once('lib/smarty_tiki/modifier.username.php');
                        $change['data']['editUser'] = smarty_modifier_username($change['data']['editUser']);
                    }

                    if (isset($change['user'])) {
                        include_once('lib/smarty_tiki/modifier.username.php');
                        $change['user'] = smarty_modifier_username($change['user']);
                    }
                    
                    $body .= $eventObject->getOutput($change);

                    $body .= "<br>\n";
                }
            }
        }

        if ($report_preferences['type'] == 'plain') {
            $body = strip_tags($body);
        }

        if (empty($change_array)) {
            return tr('Nothing has happened.');
        } else {
            return $body;
        }
    }
}
