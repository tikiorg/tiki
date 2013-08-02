{* $Id$ *}
{strip}
	{* Simple remarks box used by Smarty entity block.remarksbox.php & wikiplugin_remarksbox.php *}
	<div class="clearfix rbox {$rbox_params.type}">
		{if $rbox_params.close eq 'y' and $rbox_params.type ne 'errors' and $rbox_params.type ne 'confirm'}
			{icon _id='close' class='rbox-close' onclick='$(this).parent().fadeOut(); return false;'}
		{/if}
		{if $rbox_params.title ne ''}
			<div class="rbox-title">
				{if $rbox_params.icon ne 'none'}
					{icon _id=$rbox_params.icon alt='{tr}$rbox_params.type{/tr}'}
				{/if}
				<span>{$rbox_params.title|escape}</span>
			</div>
		{/if}
		<div class="rbox-data {$rbox_params.highlight}"{if !empty($rbox_params.width)} style="width:{$rbox_params.width}"{/if}>
			{$remarksbox_content}
		</div>
	</div>
{/strip}