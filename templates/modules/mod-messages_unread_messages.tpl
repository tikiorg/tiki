{* $Id$ *}

{if $user and $prefs.feature_messages eq 'y' and $tiki_p_messages eq 'y'}
{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}Messages{/tr}"}{/if}
{tikimodule title=$tpl_module_title name="messages_unread_messages" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
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
