{tikimodule title="{tr}Whats related{/tr}" name="whats_related" flip=$module_params.flip decorations=$module_params.decorations}
<table  border="0" cellpadding="0" cellspacing="0">
{foreach key=key item=item from=$WhatsRelated}
<tr><td class="module"><a class="linkmodule" href="{$key}">{$item}</a></td></tr>
{foreachelse}
<tr><td class="module">&nbsp;</td></tr>
{/foreach}
</table>
{/tikimodule}
