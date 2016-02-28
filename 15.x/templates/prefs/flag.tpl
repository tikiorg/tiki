<div class="adminoptionbox preference clearfix {$p.tagstring|escape}{if isset($smarty.request.highlight) and $smarty.request.highlight eq $p.preference} highlight{/if}">
	<div class="adminoption form-group">
		<label class="col-sm-4 control-label">
			{$p.name|escape}
		</label>
        <div class="col-sm-8">
            <input id="{$p.id|escape}" type="checkbox" name="{$p.preference|escape}" {if $p.value eq 'y'}checked="checked" {/if}
                {if ! $p.available}disabled="disabled"{/if} {$p.params}
               data-tiki-admin-child-block="#{$p.preference|escape}_childcontainer"
               data-tiki-admin-child-mode="{$mode|escape}"
                >
        		{include file="prefs/shared-flags.tpl"}
		    {if $p.hint}
		    	<div class="help-block">{$p.hint|simplewiki}</div>
		    {/if}
    	    {include file="prefs/shared-dependencies.tpl"}
        </div>
    </div>
</div>
