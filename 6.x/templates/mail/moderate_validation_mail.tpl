{tr}Hi{/tr},

{if $mail_again}
{tr}{$mail_user} <{$mail_email}> has requested a new password on {$mail_site}, but you need to validate his account first{/tr}
{else}
{$mail_user} <{$mail_email}> {tr}has requested an account on{/tr} {$mail_site}
{if isset($chosenGroup)}
{tr}Group:{/tr} {$chosenGroup}{/if}
{/if}


{tr}To validate that account, please follow the link:{/tr}
{$mail_machine}?user={$mail_user|escape:'url'}&pass={$mail_apass}

{tr}Assign to a group:{/tr} {$mail_machine_assignuser}?assign_user={$mail_user|escape:'url'}
{tr}View user's data:{/tr} {$mail_machine_userprefs}?view_user={$mail_user|escape:'url'}


{tr}Best regards{/tr}

{if isset($item)}
{tr}User Tracker{/tr}
{foreach item=field_value from=$item.field_values}
	{$field_value.name}: {include file="tracker_item_field_value.tpl" list_mode='csv' showlinks='n'}
{/foreach}
{/if}

