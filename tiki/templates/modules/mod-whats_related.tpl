<div class="box">
<div class="box-title">
{include file="module-title.tpl" module_title="{tr}Whats related{/tr}" module_name="whats_related"}
</div>
<div class="box-data">
<table  border="0" cellpadding="0" cellspacing="0">
{foreach key=key item=item from=$WhatsRelated}
<tr><td class="module"><a class="linkmodule" href="{$key}">{$item}</a></td></tr>
{foreachelse}
<tr><td class="module">&nbsp;</td></tr>
{/foreach}
</table>
</div>
</div>
