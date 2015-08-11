{* $Id: layout_view.tpl 48366 2013-11-08 16:12:24Z lphuberdeau $ *}<!DOCTYPE html>
{if isset($confirm) && $confirm === 'y'}
	{$confirm = true}
{else}
	{$confirm = false}
{/if}
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title" id="myModalLabel">{$title|escape}{block name=subtitle}{/block}</h4>
</div>
<div class="modal-body">
	{block name=content}{/block}
	{if $headerlib}
		{$headerlib->output_js_config()}
		{$headerlib->output_js_files()}
		{$headerlib->output_js()}
	{/if}
	{if !empty($smarty.request.show_smarty_debug)}
		{debug}
	{/if}
</div>
<div class="modal-footer">
	{block name=buttons}
		<button type="button" class="btn btn-default btn-dismiss" data-dismiss="modal">{tr}Close{/tr}</button>
		{if $confirm}
			<button type='submit' form="confirm-action" class="btn {if !empty($confirmButtonClass)}{$confirmButtonClass}{else}btn-primary{/if} confirm-action-btn">
				{if !empty($confirmButton)}
					{$confirmButton}
				{else}
					{tr}OK{/tr}
				{/if}
			</button>
		{/if}
	{/block}
</div>
