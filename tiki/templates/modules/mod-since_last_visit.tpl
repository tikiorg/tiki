{if $user}
<div class="box">
<div class="box-title">
{tr}Since your last visit{/tr}
</div>
<div class="box-data">
{tr}Since your last visit on{/tr}<br/>
<b>{$nvi_info.lastVisit|tiki_short_datetime}</b><br/>
{$nvi_info.images} {tr}new images{/tr}<br/>
{$nvi_info.pages} {tr}wiki pages changed{/tr}<br/>
{$nvi_info.files} {tr}new files{/tr}<br/>
{$nvi_info.comments} {tr}new comments{/tr}<br/>
{$nvi_info.users} {tr}new users{/tr}<br/>
</div>
</div>
{/if}

