{* $Id$ *}{if $mail_action eq 'deleted'}{tr}Tracker item {$mail_itemId} was deleted in the tracker {tr}{$mail_trackerName}{/tr} by {tr}{$mail_user|username}{/tr} on {tr}{$mail_date|tiki_short_datetime:"":"n"}{/tr} {/tr}
{elseif $mail_action eq 'status'}{tr}New status for ItemID {$mail_itemId} {$mail_item_desc} for the {$prefs.mail_template_custom_text}tracker {tr}{$mail_trackerName}{/tr}:{/tr} {if $status eq 'o'}{tr}open{/tr}{elseif $status eq 'p'}{tr}pending{/tr}{elseif $status eq 'c'}{tr}closed{/tr}{/if}
{else}{$mail_action}

{tr}View the {$prefs.mail_template_custom_text}tracker item at:{/tr}
	{$mail_machine_raw}/{$mail_itemId|sefurl:'trackeritem'}
{/if}

{if $mail_action eq 'deleted'}
{if $mail_fields}
{tr}The last content before deletion was as follows:{/tr}

Status: {$mail_field_status}
{foreach from=$mail_fields key=id item=item}
{if $item.value}
----------
[{tr}{$item.name}{/tr}]:
{$item.value}
{/if}
{/foreach}
----------
{/if}
{else}
{tr}Author:{/tr} {$mail_user|username}
{tr}Date:{/tr} {$mail_date|tiki_short_datetime:"":"n"}
{/if}

{$mail_data|replace:'-[':''|replace:']-':''}{* TODO: translate these -[...]- marked strings in $mail_data by watcher language *}
{* {$mail_data|replace:"\n\n":"\n"|replace:":\n":": "} to reduce the number of line *}

{if isset($mail_attId)}
	{tr}Download the file at:{/tr} {$mail_machine_raw}/tiki-download_item_attachment.php?attId={$mail_attId}
{/if}

