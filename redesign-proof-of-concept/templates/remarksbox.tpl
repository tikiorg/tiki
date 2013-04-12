{strip}
{* $Id$ *}
{* Simple remarks box used by Smarty entity block.remarksbox.php & wikiplugin_remarksbox.php *}
<div class="alert {$remarksbox_type|escape}">
	{if $remarksbox_close eq 'y' and $remarksbox_type ne 'alert-error' and $remarksbox_type ne 'alert-success'}
		<a href="#" class="icon-remove" style="float: right;" onclick="$(this).parent().fadeOut();return false;"></a>
	{/if}
	<h5>{$remarksbox_title|escape}</h5>
	{$remarksbox_content}
</div>
{/strip}
