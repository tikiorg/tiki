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
{/if}
{* If js is enabled, the layouts/internal/modal.tpl will be used which already has buttons *}
