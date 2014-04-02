{$mail_body|truncate:$mail_truncate:"..."}

---
{tr}A new message was posted to you.{/tr} {tr}Click here to read the full message and / or reply{/tr}:
{$mail_machine}?msgId={$messageid}

{tr}Date:{/tr} {$mail_date|tiki_short_datetime:"":"n"}
