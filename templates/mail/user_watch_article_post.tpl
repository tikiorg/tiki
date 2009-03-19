{* $Id$ *}
{if $mail_action eq 'new'}{tr}New{/tr}{elseif $mail_action eq 'edit'}{tr}Edited{/tr}{elseif $mail_action eq 'delete'}{tr}Deleted{/tr}{/if} {tr}article{/tr}: {$mail_title} {tr}by{/tr} {$mail_user} {tr}at{/tr} {$mail_date|tiki_short_datetime}

{if $mail_action neq 'delete'}{tr}View the article at:{/tr}
{$mail_machine_raw}/{$mail_postid|sefurl:article}
{/if}
{if $mail_user ne 'admin'}

{tr}If you don't want to receive these notifications follow this link:{/tr}
{$mail_machine_raw}/tiki-user_watches.php?hash={$mail_hash}
{/if}
{$mail_data}

