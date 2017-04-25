{if $prefs.javascript_enabled !== 'y'}
	<div class="modal-footer">
		<a class="btn btn-default" href="{$extra.referer}">
			{tr}Back{/tr}
		</a>
		<button type='submit' form="confirm-action" class="btn {if !empty($confirmButtonClass)}{$confirmButtonClass}{else}btn-primary{/if}">
			{if !empty($confirmButton)}
				{$confirmButton}
			{else}
				{tr}OK{/tr}
			{/if}
		</button>
	</div>
{*If js is enabled, the layouts/internal/modal.tpl will be used which already has buttons*}
{else}
	{jq}
		//needed for ie10/11 since it does not support the form attribute on buttons.
		$('button[form="confirm-action"]').click(function (ev){
			ev.preventDefault();
			$('#confirm-action').submit();
		});

		$('form.confirm-action').submit(function () {
			confirmAction(this);
			return false;
		});
	{/jq}
{/if}
