{* $Id$ *}

{if isset($modUnread)}
{tikimodule error=$module_params.error|default:null title=$tpl_module_title name="messages_unread_messages" flip=$module_params.flip|default:null decorations=$module_params.decorations|default:null nobox=$module_params.nobox notitle=$module_params.notitle|default:null}
	{if $modUnread > 0}
		<a class="linkmodule" href="messu-mailbox.php">
			{icon name='information' istyle="float:left"}
			<span class="highlight">
				{tr}You have{/tr} {$modUnread} {if $modUnread !== '1'}{tr}new messages{/tr}{else}{tr}new message{/tr}{/if}.
			</span>
		</a>
	{else}
		<a class="linkmodule" href="messu-mailbox.php">
			{tr}You have 0 new messages{/tr}
		</a>
	{/if}
{/tikimodule}
{/if}
