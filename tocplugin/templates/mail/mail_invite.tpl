{* $Id$ *}{tr}You are invited by:{/tr} {$prefs.mail_template_custom_text}{$user|username}
{if !empty($groups)}
	{tr}You join this group:{/tr}
	{foreach from=$groups item=group}
		{$group.groupName|escape}{if $group.groupDescription} ({$group.groupDescription|escape}){/if}

	{/foreach}
{/if}
{if $message}
---------------------------
{$message|escape}

{/if}
{if $new_user}
{$machine}/tiki-change_password.php?user={$invite|escape:'url'}&actpass={$codedPassword}
{else}
{$machine}
{/if}