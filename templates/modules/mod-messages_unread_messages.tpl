{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-messages_unread_messages.tpl,v 1.9 2003-11-20 23:49:04 mose Exp $ *}

{if $user and $feature_messages eq 'y' and $tiki_p_messages eq 'y'}
<div class="box">
<div class="box-title">
{include file="module-title.tpl" module_title="{tr}Messages{/tr}" module_name="messages_unread_messages"}
</div>
<div class="box-data">
{if $modUnread > 0}
	<a class="linkmodule" href="messu-mailbox.php"><span class="highlight">
	{tr}You have{/tr} {$modUnread} {if $modUnread !== '1'}{tr}new messages{/tr}{else}{tr}new message{/tr}</span>{/if}
{else}
	<a class="linkmodule" href="messu-mailbox.php">
	{tr}You have 0 new messages{/tr}
{/if}
</a>
</div>
</div>
{/if}
