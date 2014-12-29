{assign var=thispageName value=$pageName|escape:"url"}

{if $type eq 'd'}
	<iframe width='0' height='0' frameborder="0" src="tiki-page_loader.php?refresh={$refresh}&amp;pageName={$thispageName}">{tr}Browser not supported{/tr}</iframe>
{/if}

{$parsed}

{if $tiki_p_edit_html_pages eq 'y'}
	{button href="tiki-admin_html_pages.php?pageName=$thispageName" _text="{tr}Edit{/tr}"}
	{if $type eq 'd'}
		{button href="tiki-admin_html_page_content.php?pageName=$thispageName" _text="{tr}Content{/tr}"}
	{/if}
{/if}
