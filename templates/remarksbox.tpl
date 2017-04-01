{* $Id$ *}
{strip}
{* Simple remarks box used by Smarty entity block.remarksbox.php & wikiplugin_remarksbox.php *}
<div {if $remarksbox_id}id="{$remarksbox_id|escape}"{/if} class="alert {$remarksbox_class|escape} {if $remarksbox_close}alert-dismissable{/if} {if $remarksbox_highlight}{$remarksbox_highlight}{/if}{if $remarksbox_hidden} hide{/if}">
	{if $remarksbox_close}
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	{/if}
	<h4>
		{icon name="$remarksbox_icon"}
		&nbsp;
		{$remarksbox_title|escape}
	</h4>
	{$remarksbox_content}
</div>
{/strip}

{if $remarksbox_cookie}
{jq}
$("button.close", "#{{$remarksbox_id|escape}}").click(function() {
	setCookie("{{$remarksbox_cookiehash}}", "1", "rbox");
});
{/jq}
{/if}
