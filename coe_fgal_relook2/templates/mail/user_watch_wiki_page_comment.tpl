{tr}The page {$mail_page} was commented by {$mail_user|username} at {$mail_date|tiki_short_datetime}{/tr}

{tr}You can view the page by following this link:{/tr}
{$mail_machine_raw}/tiki-index.php?page={$mail_page|escape:"url"}#comments
{tr}Title:{/tr} {$mail_title}
{tr}Comment:{/tr} {$mail_comment}

{if $watchId}
{tr}If you don't want to receive these notifications follow this link:{/tr}
{$mail_machine_raw}/tiki-user_watches.php?id={$watchId}
{/if}

