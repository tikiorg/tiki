<div class="navbar">
	{self_link print='y'}{icon _id='printer' align='right' hspace='1' alt="{tr}Print{/tr}"}{/self_link}
	
	{if $mid ne "tiki-directory_browse.tpl"}
		{button href="tiki-directory_browse.php" _text="{tr}Browse{/tr}"}
	{/if}
	
	{if $tiki_p_admin_directory_cats eq 'y' or $tiki_p_admin_directory_sites eq 'y' or $tiki_p_validate_links eq 'y'}
		{button href="tiki-directory_admin.php" _text="{tr}Admin{/tr}"}
	{/if}
	
	{button href="tiki-directory_ranking.php?sort_mode=created_desc" _text="{tr}New Sites{/tr}"}
	
	{if $prefs.directory_cool_sites eq "y"}
		{button href="tiki-directory_ranking.php?sort_mode=hits_desc" _text="{tr}Cool Sites{/tr}"}
	{/if}
	
	{if $tiki_p_submit_link eq 'y' or $tiki_p_autosubmit_link eq 'y'}
		{if isset($addtocat)}
			{button href="tiki-directory_add_site.php?addtocat=$addtocat" _text="{tr}Add a Site{/tr}"}
		{else}
			{button href="tiki-directory_add_site.php" _text="{tr}Add a Site{/tr}"}
		{/if}
	
		{if $tiki_p_admin_directory_cats eq 'y'}
			{button href="tiki-directory_admin_categories.php" _text="{tr}Add a Directory Category{/tr}"}
		{/if}
	{/if}
</div>
