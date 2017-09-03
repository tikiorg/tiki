{* $Id$ *}

{title help="Sitemap" admpage="General#contentadmin_general-3"}{tr}Sitemap{/tr}{/title}

{button href="tiki-admin_sitemap.php?rebuild=1" _icon_name="sitemap" class="btn btn-default" _text="{tr}Rebuild sitemap{/tr}"}

<br />
<h2>{tr}Submit the Sitemap{/tr}</h2>
{if $sitemapAvailable}
<div class="alert alert-info">
	{tr}You can submit the sitemap for processing in all major search engines using the following URL:{/tr}
	<br><br>
	<a href="{$url}" target="_blank">{$xml}</a>
</div>
{else}
<div class="alert alert-info">
	{tr}The URL that you will need to use for submitting the sitemap will be available after you rebuild the sitemap.{/tr}
</div>
{/if}
