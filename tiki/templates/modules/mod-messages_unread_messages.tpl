{if $user and $feature_messages eq 'y' and $tiki_p_messages eq 'y'}
<div class="box">
<div class="box-title">
{tr}Messages{/tr}
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
