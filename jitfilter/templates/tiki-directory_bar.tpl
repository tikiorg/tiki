<div class="navbar">
		{if $tiki_p_admin_directory_cats eq 'y' or $tiki_p_admin_directory_sites eq 'y' or $tiki_p_validate_links eq 'y'}
			<span class="button2">
				<a href="tiki-directory_admin.php">{tr}Admin{/tr}</a>
			</span>
		{/if}

		{if $mid ne "tiki-directory_browse.tpl"}
			<span class="button2">
				<a href="tiki-directory_browse.php">{tr}Browse{/tr}</a>
			</span>
		{/if}

			<span class="button2">
				<a href="tiki-directory_ranking.php?sort_mode=created_desc">{tr}New Sites{/tr}</a>
			</span>
	
	{if $prefs.directory_cool_sites eq "y"}
			<span class="button2">
				<a href="tiki-directory_ranking.php?sort_mode=hits_desc">{tr}Cool Sites{/tr}</a>
			</span>
	{/if}

	{if $tiki_p_submit_link eq 'y' or $tiki_p_autosubmit_link eq 'y'}
			<span class="button2">
				<a href="tiki-directory_add_site.php{if isset($addtocat)}?addtocat={$addtocat}{/if}">{tr}Add a Site{/tr}</a>
			</span>
		{if $tiki_p_admin_directory_cats eq 'y'}
			<span class="button2">
				<a href="tiki-directory_admin_categories.php">{tr}Add a Category{/tr}</a>
			</span>
		{/if}
	{/if}
</div>
