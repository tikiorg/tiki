{if isset($modUnread)}
{tikimodule error=$module_params.error title=$tpl_module_title name="messages_unread_messages" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{if $modUnread > 0}
	 <a class="linkmodule" href="messu-mailbox.php">{icon _id=information style="float:left"}
	<span class="highlight">{tr}You have{/tr} {$modUnread} {if $modUnread !== '1'}{tr}new messages{/tr}{else}{tr}new message{/tr}{/if}.</span>
{else}
	<a class="linkmodule" href="messu-mailbox.php">
	{tr}You have 0 new messages{/tr}
{/if}
</a>
{/tikimodule}
{/if}
