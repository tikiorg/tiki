{if $user and $feature_messages eq 'y' and $tiki_p_messages eq 'y'}
<div class="box">
<div class="box-title">
{tr}Messages{/tr}
</div>
<div class="box-data">
<a class="linkmodule" href="messu-mailbox.php">
{tr}You have{/tr} {$modUnread} {if $modUnread>1}{tr}new message{/tr}{else}{tr}new messages{/tr}{/if}
</a>
</div>
</div>
{/if}
