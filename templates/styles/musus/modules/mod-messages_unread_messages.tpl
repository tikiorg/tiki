{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/musus/modules/mod-messages_unread_messages.tpl,v 1.1 2004-01-07 04:31:24 musus Exp $ *}

{if $user and $feature_messages eq 'y' and $tiki_p_messages eq 'y'}
{tikimodule title="{tr}Messages{/tr}" name="messages_unread_messages"}
{if $modUnread > 0}
	<a class="linkmodule" href="messu-mailbox.php"><span class="highlight">
	{tr}You have{/tr} {$modUnread} {if $modUnread !== '1'}{tr}new messages{/tr}{else}{tr}new message{/tr}</span>{/if}
{else}
	<a class="linkmodule" href="messu-mailbox.php">
	{tr}You have 0 new messages{/tr}
{/if}
</a>
{/tikimodule}
{/if}
