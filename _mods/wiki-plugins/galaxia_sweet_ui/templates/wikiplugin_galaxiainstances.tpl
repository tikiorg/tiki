{* attention: the result code will be interpreted as wiki text, not html *}
{$galaxia_title}
{foreach from=$instances item=inst}
{if $link}
* <a href="tiki-index.php?page={$page|escape:"url"}&activityId={$activityId}&iid={$inst.instanceId}">{$inst.properties.$labelProperty|default:"''(no name)''"}{*how to translate that?*}</a>
{else}
* {$inst.properties.$labelProperty|default:"''(no name)''"}
{/if}
{/foreach}