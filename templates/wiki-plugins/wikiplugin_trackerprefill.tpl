{strip}
{* $Header: /cvsroot/tikiwiki/tiki/templates/wiki-plugins/wikiplugin_trackerprefill.tpl,v 1.1.2.1 2008-02-04 14:01:06 sylvieg Exp $ *} 
<a class="linkbut" href="tiki-index.php?page={$params.page|escape:url}
{if $prefills}
&amp;prefills=
{foreach from=$prefills item=field name=foo}
{if !$smarty.foreach.foo.first}:{/if}
{$field.fieldId}
{/foreach}
{/if}
{foreach from=$prefills item=field}
&amp;values[]={$field.value}
{/foreach}
">{if $params.label}{tr}{$params.label}{/tr}{else}{tr}Go{/tr}{/if}</a>
{/strip}