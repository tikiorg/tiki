<div class="box">
<div class="box-title">
{tr}Online users{/tr}
</div>
<div class="box-data">
{section name=ix loop=$online_users}
{if $online_users[ix].user_information eq 'public'}
<a class="linkmodule" href="tiki-user_information.php?view_user={$online_users[ix].user}">{$online_users[ix].user}</a><br/>
{else}
{$online_users[ix].user}<br/>
{/if}
{/section}
</div>
</div>

