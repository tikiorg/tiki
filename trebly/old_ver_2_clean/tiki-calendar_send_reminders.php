<?php

require_once('lib/tikilib.php');

Main();

function Main()
{
    header("Content-Type: text/plain");

    include_once "db/local.php";

    $connection = mysql_connect($host_tiki, $user_tiki, $pass_tiki);

    if (!$connection)
    {
        die(mysql_error());
    }

    if (!mysql_select_db($dbs_tiki))
    {
        die(mysql_error());
    }

    ProcessCalendarItems($connection);

    mysql_close($connection);
}

function ProcessCalendarItems($connection)
{
    $query = "SELECT * FROM tiki_calendar_items i LEFT JOIN custom_calendar_reminder r ON i.calitemId = r.calendar_item_id WHERE i.start > " . (time() - 3600) . " AND i.end < " . (time() + 28 * 86400);

    echo $query . "\n";

    $resultset = mysql_query($query, $connection);
    
    if (!$resultset)
    {
        die(mysql_error());
    }
    
    while ($row = mysql_fetch_assoc($resultset))
    {
        if ($row["last_sent"] == 0)
        {
            switch ($row["reminder_type"])
            {
                case 1:
                    ProcessFixedDateReminder($connection, $row);
                    break;
                case 2:
                    ProcessTimeOffsetReminder($connection, $row);
                    break;
            }
        }
    }

    mysql_free_result($resultset);
}

function ProcessFixedDateReminder($connection, $item)
{
    $datetime = $item["fixed_date"];
    
    if ($datetime < time())
    {
        SendEmailNotification($connection, $item);
        UpdateReminder($connection, $item);
    }
}

function ProcessTimeOffsetReminder($connection, $item)
{
    $basetime = $item["related_to"] == "S" ? $item["start"] : $item["end"];
    $offset = $item["time_offset"] * ($item["when_run"] == "B" ? -1 : 1);

    if (($basetime + $offset) < time())
    {
        SendEmailNotification($connection, $item);
        UpdateReminder($connection, $item);
    }
}

function SendEmailNotification($connection, $item)
{
    $tikilib = new TikiLib;

    $subject = "TikiWiki Calendar Reminder :: " . $item["name"];
    $subject = "=?UTF-8?B?" . base64_encode($subject) . "?=";
    
    $content = "<html>"
             . "<head>"
             . "<style type=\"text/css\">"
             . "body { font-family: Calibri, Tahoma, Arial; background-color: white; }"
             . "table { border-collapse: collapse; border: 2px solid black; width: 100%; }"
             . "th { text-align: right; border: 1px solid black; padding: 2px 5px 2px 5px; }"
             . "td { border: 1px solid black; padding: 2px 5px 2px 5px; }"
             . "</style>"
             . "<body>"
             . "<h1>" . $item["name"] . "</h1>"
             . "<p>"
             . "<a href=\"http://adrasteia/tikiwiki/tiki-calendar.php\">Open Calendar</a>"
             . "&nbsp;&nbsp;"
             . "<a href=\"http://adrasteia/tikiwiki/tiki-calendar_edit_item.php?viewcalitemId=" . $item["calitemId"] . "\">View Event</a>"
             . "</p>"
             . "<table>"
             . "<col width=\"15%\" /><col width=\"*\" />"
             . "<tr><th>Description:</th><td>" . $tikilib->parse_data($item["description"]) . "</td></tr>"
             . "<tr><th>Start:</th><td>" . date(DATE_RFC2822, $item["start"]) . "</td></tr>"
             . "<tr><th>End:</th><td>" . date(DATE_RFC2822, $item["end"]) . "</td></tr>"
             . "</p>"
             . "</body>"
             . "</html>";

    $headers = "From: tikiwiki@ampsoft.cz" . "\r\n" .
               "Reply-To: tikiwiki@ampsoft.cz" . "\r\n" .
               "Content-Type: text/html; charset=UTF-8";

    $users = SelectUsersToNotify($connection, $item);

    foreach ($users as $address)
    {
        echo "SEND: " . $item["calitemId"] . " # '" . $item["name"] . "' # " . $address . "\n";
        mail($address, $subject, $content, $headers);
    }
}

function SelectUsersToNotify($connection, $item)
{
    $users = GetItemParticipants($connection, $item);

    if (count($users) == 0)
    {
        $users = GetAlertGroupMembers($connection, $item);
    }
    if (count($users) == 0)
    {
        $users = GetCalendarOwner($connection, $item);
    }

    return $users;
}

function GetItemParticipants($connection, $item)
{
    $query = "SELECT IFNULL(u.email, r.username) FROM tiki_calendar_roles r LEFT JOIN users_users u ON r.username = u.login WHERE r.calitemId = " . $item["calitemId"];

    echo $query . "\n";

    $resultset = mysql_query($query, $connection);

    if (!$resultset)
    {
        die(mysql_error());
    }

    $users = array();

    while ($row = mysql_fetch_row($resultset))
    {
        $email = $row[0];

        if (strpos($email, "@") !== false)
        {
            $users[] = $email;
        }
    }

    mysql_free_result($resultset);

    return array_unique($users);
}

function GetAlertGroupMembers($connection, $item)
{
    $query = "SELECT groupName FROM tiki_groupalert WHERE objectType = 'calendar' AND objectId = " . $item["calendarId"];

    echo $query . "\n";

    $resultset = mysql_query($query, $connection);

    if (!$resultset)
    {
        die(mysql_error());
    }

    $groups = array();

    while ($row = mysql_fetch_row($resultset))
    {
        $groups[] = $row[0];
    }

    mysql_free_result($resultset);

    $users = array();

    foreach ($groups as $name)
    {
        $query = "SELECT u.email FROM users_usergroups ug LEFT JOIN users_users u ON ug.userId = u.userId WHERE ug.groupName = '" . $name . "'";

        echo $query . "\n";

        $resultset = mysql_query($query, $connection);

        if (!$resultset)
        {
            die(mysql_error());
        }

        while ($row = mysql_fetch_row($resultset))
        {
            $users[] = $row[0];
        }

        mysql_free_result($resultset);
    }

    return array_unique($users);
}

function GetCalendarOwner($connection, $item)
{
    $query = "SELECT u.email FROM tiki_calendars c LEFT JOIN users_users u ON c.user = u.login WHERE c.personal = 'y' AND c.calendarId = " . $item["calendarId"];

    echo $query . "\n";

    $resultset = mysql_query($query, $connection);

    if (!$resultset)
    {
        die(mysql_error());
    }

    $users = array();

    while ($row = mysql_fetch_row($resultset))
    {
        $users[] = $row[0];
    }

    mysql_free_result($resultset);

    return array_unique($users);
}

function UpdateReminder($connection, $item)
{
    $query = "UPDATE custom_calendar_reminder SET last_sent = " . time() . ", times_sent = times_sent + 1 WHERE reminder_id = " . $item["reminder_id"];

    echo $query . "\n";

    if (!mysql_query($query, $connection))
    {
        die(mysql_error());
    }
}

