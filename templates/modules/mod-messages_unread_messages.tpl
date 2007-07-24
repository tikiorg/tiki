{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-messages_unread_messages.tpl,v 1.15 2007-07-24 14:40:38 jyhem Exp $ *}

{if $user and $feature_messages eq 'y' and $tiki_p_messages eq 'y'}
{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}Messages{/tr}"}{/if}
{tikimodule title=$tpl_module_title name="messages_unread_messages" flip=$module_params.flip decorations=$module_params.decorations}
{if $modUnread > 0}
	<a class="linkmodule" href="messu-mailbox.php"><span class="highlight">
	{tr}You have{/tr} {$modUnread} {if $modUnread !== '1'}{tr}New Messages{/tr}{else}{tr}New Message{/tr}{/if}</span>
{else}
	<a class="linkmodule" href="messu-mailbox.php">
	{tr}You have 0 new messages{/tr}
{/if}
</a>
{/tikimodule}
{/if}
