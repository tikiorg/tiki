<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Event_Function_EventNotify extends Math_Formula_Function
{
    private $recorder;

    function __construct($recorder)
    {
        $this->recorder = $recorder;
    }

    function evaluate( $element )
    {
		$monitorlib = TikiLib::lib('monitor');

        $event = $this->evaluateChild($element[0]);
        $arguments = $this->evaluateChild($element[1]);

        $priority = $this->evaluateChild($element[2]);
        $userpath = $this->evaluateChild($element[3]);

        $users = $this->getUsers($userpath, $arguments);

		/*goes through all returned users to send a direct notification*/
        foreach ($users as $user) {
            $userId = TikiLib::lib('tiki')->get_user_id($user);
            $monitorlib->directNotification($priority,$userId,$event,$arguments);
        }

        return 1;
    }

	/**
	 * Returns an array of users that will receive the activity notification
	 * @param $userpath is used to identify how to retrieve a user who is getting the notification
	 * @param $arguments are passed from the activity to help gather the information needed
	 * @return array|string array of user names
	 * @throws Exception if user not found
	 */
    function getUsers($userpath, $arguments){
        $userarr = explode(":",$userpath);

        switch($userarr[0]){
            case "argument":
                break;
            case "object":
                $users[]=$arguments['object'];
                break;
            case "user":
                $users[]=$arguments['user'];
                break;
            case "parent_comment_user":
                $users[]=$arguments['parent_comment_user'];
                break;
            case "trackeritem":
                $lib = TikiLib::lib('trk');

				//get tracker id
				$io = $lib->get_item_info($arguments['object']);
				$tracker_id=$io["trackerId"];

				//get the second parameter in the user path and set as field
				$field_id = $lib->get_field_id($tracker_id, $userarr[1]);
                $result = $lib->get_tracker_item((string) $arguments['object']);
                $users[] = $result[$field_id];
                break;
            case "groupmember":
                $userlib = TikiLib::lib('user');
                $users = $userlib->get_members($arguments['groupname']);
                break;
            case "groupmanagers":
                $api = new TikiAddons_Api_Group;
                $group = $arguments['groupname'];
                if ($api->isOrganicGroup($group)) {
                    $users = $api->getOrganicGroupLeaders($group);
                }
                break;
            default:
                TikiLib::lib('errorreport')->report(tr(
                    'Problem finding the associated user. %0 is not recognized. See EventNotify.php',
                    $userarr[0]
                ));
                break;
        }

        return $users;

    }
}