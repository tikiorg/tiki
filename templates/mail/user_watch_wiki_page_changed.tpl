{tr}The page {$mail_page} was changed by {$mail_user} at {$mail_date|tiki_short_datetime}{/tr}

{if $mail_comment}{tr}Comment:{/tr} {$mail_comment}

{/if}
{tr}You can view the page by following this link:{/tr}
{$mail_machine}/tiki-index.php?page={$mail_page|escape:"url"}

{tr}You can view a diff back to the previous version by following this link:{/tr}
{$mail_machine}/tiki-pagehistory.php?page={$mail_page|escape:"url"}&diff2={$mail_last_version}

{if $mail_hash}{tr}If you don't want to receive these notifications follow this link:{/tr}
{$mail_machine_raw}/tiki-user_watches.php?hash={$mail_hash}

{/if}
{tr}The new page content follows below.{/tr}
***********************************************************

{$mail_pagedata}
