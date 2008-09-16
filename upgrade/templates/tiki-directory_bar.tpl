<div class="navbar">
		{if $tiki_p_admin_directory_cats eq 'y' or $tiki_p_admin_directory_sites eq 'y' or $tiki_p_validate_links eq 'y'}
			<span class="button2">
				<a href="tiki-directory_admin.php" class="linkbut">{tr}Admin{/tr}</a>
			</span>
		{/if}

		{if $mid ne "tiki-directory_browse.tpl"}
			<span class="button2">
				<a href="tiki-directory_browse.php" class="linkbut">{tr}Browse{/tr}</a>
			</span>
		{/if}

			<span class="button2">
				<a href="tiki-directory_ranking.php?sort_mode=created_desc" class="linkbut">{tr}New Sites{/tr}</a>
			</span>
	
	{if $prefs.directory_cool_sites eq "y"}
			<span class="button2">
				<a href="tiki-directory_ranking.php?sort_mode=hits_desc" class="linkbut">{tr}Cool Sites{/tr}</a>
			</span>
	{/if}

	{if $tiki_p_submit_link eq 'y' or $tiki_p_autosubmit_link eq 'y'}
			<span class="button2">
				<a href="tiki-directory_add_site.php{if isset($addtocat)}?addtocat={$addtocat}{/if}" class="linkbut">{tr}Add a Site{/tr}</a>
			</span>
		{if $tiki_p_admin_directory_cats eq 'y'}
			<span class="button2">
				<a href="tiki-directory_admin_categories.php" class="linkbut">{tr}Add a Category{/tr}</a>
			</span>
		{/if}
	{/if}
</div>
