{* $Id$ *}
{tr}{$mail_action} article post:{/tr} {tr}{$mail_title} by {$mail_user|username} at{/tr} {$mail_date|tiki_short_datetime:"":"n"}

{if $mail_action neq 'Delete'}{tr}View the article at:{/tr}
{$mail_machine_raw}/{$mail_postid|sefurl:article}
{/if}

{tr}If you don't want to receive these notifications follow this link:{/tr}
{$mail_machine_raw}/tiki-user_watches.php?id={$watchId}

{tr}Title:{/tr} {$mail_title}

{tr}Publish Date:{/tr} {$mail_current_publish_date|tiki_short_datetime:"":"n"}
{tr}Expiration Date:{/tr} {$mail_current_expiration_date|tiki_short_datetime:"":"n"}
***********************************************************
{tr}Content{/tr}
***********************************************************
{$mail_current_data}
{if isset($mail_old_data)}

***********************************************************
{tr}The old article follows.{/tr}
***********************************************************
{tr}Title:{/tr} {$mail_old_title}

{tr}Publish Date:{/tr} {$mail_old_publish_date|tiki_short_datetime:"":"n"}
{tr}Expiration Date:{/tr} {$mail_old_expiration_date|tiki_short_datetime:"":"n"}
***********************************************************
{tr}Content{/tr}
***********************************************************
{$mail_old_data}
{/if}