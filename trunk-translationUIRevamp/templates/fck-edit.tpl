<div class="fckeditzone">
{if $fck->Compat} 
<input type="hidden" id="{$fck->id}" name="{$fck->InstanceName}" value="{$fck->Meat|escape}" style="display:none" />
<input type="hidden" id="{$fck->id}___Config" value="{$fck->ConfigString}" style="display:none" />
<iframe id="{$fck->id}___Frame" src="{$fck->LinkFile}" width="{$fck->Width|replace:'px':''}" height="{$fck->Height|replace:'px':''}" frameborder="0" scrolling="no"></iframe>
{else}
<textarea id='editwiki' class="wikiedit" name="{$fck->InstanceName}" rows="4" cols="40" style="width:{$fck->Width};height:{$fck->Height};">{$fck->HtmlMeat}</textarea>
{/if}
</div>
