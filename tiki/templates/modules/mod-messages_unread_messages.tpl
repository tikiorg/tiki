{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-messages_unread_messages.tpl,v 1.14 2007-02-18 11:21:17 mose Exp $ *}

{if $user and $feature_messages eq 'y' and $tiki_p_messages eq 'y'}
{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}Messages{/tr}"}{/if}
{tikimodule title=$tpl_module_title name="messages_unread_messages" flip=$module_params.flip decorations=$module_params.decorations}
{if $modUnread > 0}
	<a class="linkmodule" href="messu-mailbox.php"><span class="highlight">
	{tr}You have{/tr} {$modUnread} {if $modUnread !== '1'}{tr}new messages{/tr}{else}{tr}new message{/tr}{/if}</span>
{else}
	<a class="linkmodule" href="messu-mailbox.php">
	{tr}You have 0 new messages{/tr}
{/if}
</a>
{/tikimodule}
{/if}
