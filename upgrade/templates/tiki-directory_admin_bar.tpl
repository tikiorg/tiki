<div class="navbar">
	<span class="button2">
		<a href="tiki-directory_browse.php">{tr}Browse{/tr}</a>
	</span>
	<span class="button2">
		<a href="tiki-directory_admin.php">{tr}Admin{/tr}</a>
	</span>

	{if $tiki_p_admin_directory_cats eq 'y'}
		<span class="button2">
			<a href="tiki-directory_admin_categories.php">{tr}Categories{/tr}</a>
		</span>
	{/if}

	{if $tiki_p_admin_directory_cats eq 'y'}
		<span class="button2">
			<a href="tiki-directory_admin_related.php">{tr}Related{/tr}</a>
		</span>
	{/if}

	{if $tiki_p_admin_directory_sites eq 'y'}
		<span class="button2">
			<a href="tiki-directory_admin_sites.php">{tr}Sites{/tr}</a>
		</span>
	{/if}

	{if $tiki_p_validate_links eq 'y'}
		<span class="button2">
			<a href="tiki-directory_validate_sites.php">{tr}Validate{/tr}</a>
		</span>
	{/if}
</div>
