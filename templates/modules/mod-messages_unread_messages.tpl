{if $user}
<div class="box">
<div class="box-title">
{tr}Messages{/tr}
</div>
<div class="box-data">
{tr}You have{/tr} {$modUnread} {tr}new message{/tr}{if $modUnread>1}s{/if}
{/if}
</div>
</div>