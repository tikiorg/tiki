<div class="box">
<div class="box-title">
{$user_title}
</div>
<div class="box-data" id="{$module_name}" {if $smarty.cookies.$module_name ne 'c'}style="display:block;"{else}style="display:none;"{/if}>
{eval var=$user_data}
</div>
</div>

