{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-messages_unread_messages.tpl,v 1.10 2003-11-23 03:53:04 zaufi Exp $ *}

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
