<div class="cbox-title">{$title}</div>
<div class="cbox-data">
{foreach key=t item=i from=$listcat}
<b>{tr}{$t}{/tr}:</b> {section name=o loop=$i}<a href="{$i[o].href}" class="link" title="{tr}Created{/tr} {$i[o].created|tiki_long_date}">{$i[o].name}</a> . {/section}<br />
{/foreach}
</div>

