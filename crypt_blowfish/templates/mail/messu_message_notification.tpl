{$mail_body|truncate:$mail_truncate:"..."}

{tr}A new message was posted to you.{/tr} <a href={$mail_machine}?msgId={$messageid}>{tr}Click here to read the full message and / or reply{/tr}</a><br/>

{tr}Date:{/tr} {$mail_date|tiki_short_datetime:"":"n"}
