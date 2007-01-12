<div class="cssmenu">
<ul>
{assign var=lasttype value='o'}
{foreach key=pos item=chdata from=$channels}
{if $chdata.type eq 's'}
{if $lasttype eq 'o'}</ul></ul>{/if}
<ul><li><h2>
<img src="pics/icons/folder.png" width="16" height="16" border="0" align="left" />
{tr}{$chdata.name}{/tr}<br clear="both" /></h2>
{elseif $chdata.type eq 'o'}
{if $lasttype eq 's'}<ul>{/if}
<li>
<a href="{$chdata.url|escape}" class="linkmenu">{tr}{$chdata.name}{/tr}</a>
{/if}
{assign var=lasttype value=$chdata.type}
{/foreach}
</ul>
</div>
