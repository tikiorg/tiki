{tr}The page {$mail_page} was commented by {$mail_user} at {$mail_date|tiki_short_datetime}{/tr}

{tr}You can view the page following this link:{/tr}
{$mail_machine}?page={$mail_page|escape:"url"}
{tr}Title{/tr}: {$mail_title}
{tr}Comment:{/tr} {$mail_comment}

{tr}If you don't want to receive these notifications follow this link:{/tr}
{$mail_machine_raw}/tiki-user_watches.php?hash={$mail_hash}
