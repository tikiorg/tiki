{strip}
{* $Id$ *} 
<a class="linkbut" href="tiki-index.php?page={$params.page|escape:url}
{if $prefills}
&amp;prefills=
{foreach from=$prefills item=field name=foo}
{if !$smarty.foreach.foo.first}:{/if}
{$field.fieldId}
{/foreach}
{/if}
{foreach from=$prefills item=field}
&amp;values[]={$field.value|escape:"url"}
{/foreach}
">{if $params.label}{tr}{$params.label}{/tr}{else}{tr}Go{/tr}{/if}</a>
{/strip}
