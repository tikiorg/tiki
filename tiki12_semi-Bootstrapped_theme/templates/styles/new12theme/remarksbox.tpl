{* $Id: remarksbox.tpl 49126 2013-12-17 15:38:01Z sept_7 $ *}
{strip}
	{* Simple remarks box used by Smarty entity block.remarksbox.php & wikiplugin_remarksbox.php *}
	<div class="alert alert-{$rbox_params.type} rbox {$rbox_params.type} panel" id="{$rbox_guid}">
		{if $rbox_params.close and $rbox_params.type ne 'errors' and $rbox_params.type ne 'confirm'}
			{icon _id='close' class='rbox-close' onclick=$rbox_close_click|default:''}
		{/if}
		{if $rbox_params.title ne ''}
			<h4>
				{if $rbox_params.icon ne 'none'}
                    <img src="img/icons/{$rbox_params.icon}.png" alt="{tr}{$rbox_params.type}{/tr}" class="icon">
				{/if}
				<span>&nbsp;{$rbox_params.title|escape}</span>
			</h4>
		{/if}
		{$remarksbox_content}
	</div>
{/strip}