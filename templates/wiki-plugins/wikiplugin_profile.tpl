<form method="post" action="">
	<p>
		<input type="hidden" name="{if $profile_is_installed}reinstall{else}install{/if}" value="{$profile_key|escape}">
		<input type="submit" class="btn btn-default btn-sm" title="{tr}Apply Changes{/tr}"value="{if $profile_is_installed}{tr}Re-Apply{/tr}{else}{tr}Apply{/tr}{/if}">
	</p>
</form>
