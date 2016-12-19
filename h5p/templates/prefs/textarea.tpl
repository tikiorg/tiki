{strip}
	<div class="adminoptionbox preference form-group clearfix {$p.tagstring|escape}{if isset($smarty.request.highlight) and $smarty.request.highlight eq $p.preference} highlight{/if}" style="text-align: left;">
		<label class="control-label col-sm-4" for="{$p.id|escape}">{$p.name|escape}</label>
		<div class="col-sm-8">
			<textarea name="{$p.preference|escape}" id="{$p.id|escape}" {if $syntax} data-syntax="{$syntax|escape}" data-codemirror="{$codemirror|escape}" {/if} class="form-control" {if $p.size} rows="{$p.size|escape}"{/if} {$p.params}>
				{$p.value|escape}
			</textarea>
			{include file="prefs/shared-flags.tpl"}
			{if $p.hint}
				<div class="help-block">{$p.hint|simplewiki}</div>
			{/if}
			{include file="prefs/shared-dependencies.tpl"}
		</div>
	</div>
{/strip}
