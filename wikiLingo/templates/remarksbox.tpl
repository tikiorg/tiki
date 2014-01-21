{* $Id$ *}
{strip}
{* Simple remarks box used by Smarty entity block.remarksbox.php & wikiplugin_remarksbox.php *}
<div class="alert {$remarksbox_class|escape} alert-dismissable">
	{if $remarksbox_close}
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	{/if}
	<h4>
		{glyph name=$remarksbox_icon}
		&nbsp;
		{$remarksbox_title|escape}
	</h4>
	{$remarksbox_content}
</div>
{/strip}
