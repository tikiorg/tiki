{tr}The page {$mail_page} was changed by {$mail_user} at {$mail_date|tiki_short_datetime}{/tr}
{tr}You can edit the page following this link:{/tr}
{$mail_machine}?page={$mail_page}
{tr}Comment:{/tr} {$mail_comment}

{tr}Diff:{/tr} {$mail_machine_raw}/tiki-pagehistory.php?page={$mail_page}&diff2={$mail_last_version}

{tr}If you don't want to receive these notifications follow this link:{/tr}
{$mail_machine_raw}/tiki-user_watches.php?hash={$mail_hash}

{tr}The new page content is:{/tr}
{$mail_pagedata}