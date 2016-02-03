<div class="t_navbar margin-bottom-md">
    <button class='btn btn-default'>{self_link print='y'}{icon name='print' text="{tr}Print{/tr}"}{/self_link}</button>

	{if $mid ne "tiki-directory_browse.tpl"}
		{button href="tiki-directory_browse.php" class="btn btn-default" _icon_name="view" _text="{tr}Browse{/tr}"}
	{/if}

	{if $tiki_p_admin_directory_cats eq 'y' or $tiki_p_admin_directory_sites eq 'y' or $tiki_p_validate_links eq 'y'}
		{button href="tiki-directory_admin.php" class="btn btn-default" _icon_name="cog" _text="{tr}Admin{/tr}"}
	{/if}
	{button href="tiki-directory_ranking.php?sort_mode=created_desc" class="btn btn-default" _icon_name="star" _text="{tr}New Sites{/tr}"}

	{if $prefs.directory_cool_sites eq "y"}
		{button href="tiki-directory_ranking.php?sort_mode=hits_desc" class="btn btn-default" _icon_name="ok" _text="{tr}Popular Sites{/tr}"}
	{/if}

	{if $tiki_p_submit_link eq 'y' or $tiki_p_autosubmit_link eq 'y'}
		{if isset($addtocat)}
			{button href="tiki-directory_add_site.php?addtocat=$addtocat" class="btn btn-default" _icon_name="add" _text="{tr}Add a site{/tr}"}
		{else}
			{button href="tiki-directory_add_site.php" class="btn btn-default" _icon_name="add" _text="{tr}Add a site{/tr}"}
		{/if}

		{if $tiki_p_admin_directory_cats eq 'y'}
			{button href="tiki-directory_admin_categories.php" class="btn btn-default" _icon_name="add" _text="{tr}Add a Directory Category{/tr}"}
		{/if}
	{/if}
</div>
