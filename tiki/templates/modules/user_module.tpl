<div class="box">
<div class="box-title">
{include file="module-title.tpl"}
</div>
<div class="box-data" id="{$module_name}" {if $smarty.cookies.$module_name ne 'c'}style="display:block;"{else}style="display:none;"{/if}>
{eval var=$module_data}
</div>
</div>

