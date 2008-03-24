{if $cant_pages > 1}
<br />
<div class="mini">

{if $prev_offset >= 0}
<a class="prevnext" href="{$smarty.server.PHP_SELF}?offset={$prev_offset}{foreach key=arg item=val from=$urlquery}{if $val}&amp;{$arg|escape:"url"}={$val|escape:"url"}{/if}{/foreach}">[{tr}Prev{/tr}]</a>&nbsp;
{/if}

{tr}Page{/tr}: {$actual_page} / {$cant_pages}

{if $next_offset >= 0}
&nbsp;<a class="prevnext" href="{$smarty.server.PHP_SELF}?offset={$next_offset}{foreach key=arg item=val from=$urlquery}{if $val}&amp;{$arg|escape:"url"}={$val|escape:"url"}{/if}{/foreach}">[{tr}Next{/tr}]</a>
{/if}

{if $prefs.direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
<a class="prevnext" href="{$smarty.server.PHP_SELF}?offset={$selector_offset}{foreach key=arg item=val from=$urlquery}{if $val}&amp;{$arg|escape:"url"}={$val|escape:"url"}{/if}{/foreach}">{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}

</div>
{/if}

