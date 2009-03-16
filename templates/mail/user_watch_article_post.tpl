{* $Id$ *}
{tr}{$mail_action} article post: {$mail_title} by {$mail_user} at {$mail_date|tiki_short_datetime}{/tr}

{if $mail_action neq tr('Delete')}{tr}View the article at:{/tr}
{$mail_machine_raw}/{$mail_postid|sefurl:article}
{/if}
{if $mail_user ne 'admin'}

{tr}If you don't want to receive these notifications follow this link:{/tr}
{$mail_machine_raw}/tiki-user_watches.php?hash={$mail_hash}
{/if}
{$mail_data}

