<div class="fckeditzone" style="width:{$fck->Width};height:{$fck->Height};">
	{if $fck->Compat} 
		<input type="hidden" id="{$fck->id}" name="{$fck->InstanceName}" value="{$fck->Meat|escape}" style="display:none" />
		<input type="hidden" id="{$fck->id}___Config" value="{$fck->ConfigString}" style="display:none" />
		<iframe id="{$fck->id}___Frame" src="{$fck->LinkFile}" width="100%" height="100%" frameborder="0" scrolling="no"></iframe>
	{else}
		<textarea id='editwiki' class="wikiedit" name="{$fck->InstanceName}" rows="4" cols="40" style="width:100%;height:100%;">{$fck->HtmlMeat}</textarea>
	{/if}
</div>
