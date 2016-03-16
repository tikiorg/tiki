<div class="t_navbar margin-bottom-md">
	{button href="tiki-directory_browse.php" _icon_name="binoculars" class="btn btn-link" _type="link" _text="{tr}Browse{/tr}"}
	{button href="tiki-directory_admin.php" _icon_name="gear" class="btn btn-link" _type="link" _text="{tr}Admin{/tr}"}

	{if $tiki_p_admin_directory_cats eq 'y'}
		{button href="tiki-directory_admin_categories.php" _icon_name="sitemap" class="btn btn-link" _type="link" _text="{tr}Directory Categories{/tr}"}
	{/if}

	{if $tiki_p_admin_directory_cats eq 'y'}
		{button href="tiki-directory_admin_related.php" _icon_name="chain" class="btn btn-link" _type="link" _text="{tr}Related{/tr}"}
	{/if}

	{if $tiki_p_admin_directory_sites eq 'y'}
		{button href="tiki-directory_admin_sites.php" _icon_name="list" class="btn btn-link" _type="link" _text="{tr}Sites{/tr}"}
	{/if}

	{if $tiki_p_validate_links eq 'y'}
		{button href="tiki-directory_validate_sites.php" _icon_name="check" class="btn btn-link" _type="link" _text="{tr}Validate{/tr}"}
	{/if}
</div>
