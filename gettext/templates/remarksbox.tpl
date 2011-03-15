{strip}
{* Simple remarks box used by Smarty entity block.remarksbox.php & wikiplugin_remarksbox.php *}
<div class="clearfix rbox {$remarksbox_type}">
{if $remarksbox_close eq 'y' and $remarksbox_type ne 'errors' and $remarksbox_type ne 'confirm'}
	{icon _id='close' class='rbox-close' onclick='$(this).parent().fadeOut();'}
{/if}
{if $remarksbox_title ne ''}
	<div class="rbox-title">
{if $remarksbox_icon ne 'none'}
	{capture name='alt'}{tr}{$remarksbox_type}{/tr}{/capture}
	{icon _id=$remarksbox_icon alt=$smarty.capture.alt}
{/if}
		<span>{$remarksbox_title}</span>
	</div>
{/if}
	<div class="rbox-data{$remarksbox_highlight}"{if !empty($remarksbox_width)} style="width:{$remarksbox_width}"{/if}>
		{$remarksbox_content}
	</div>
</div>
{/strip}
