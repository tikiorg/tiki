{if $feature_tail eq 'y'}
{popup_init src="lib/overlib.js"}
<div class="box">
<div class="box-title">
{$tailtitle}
</div>
<div class="box-data">
{section name=ix loop=$tail}
<div class="module">{$tail[ix]}</div>
{/section}
</div>
</div>
{/if}
