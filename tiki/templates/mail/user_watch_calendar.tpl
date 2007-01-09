{$mail_data.name}
{$mail_data.description}
{$mail_data.start|tiki-long_datetime}-{$mail_data.end|tiki-long_datetime}

{tr}View item calendar at:{/tr}
{$mail_machine}/tiki-calendar.php?calitemId={$mail_calitemId}&amp;calIds[]={$mail_data.calendarId}
