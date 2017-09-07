{* $Id$ *}

{title help="Sitemap" admpage="General#contentadmin_general-3"}{tr}Sitemap{/tr}{/title}

{button href="tiki-admin_sitemap.php?rebuild=1" _icon_name="sitemap" class="btn btn-default" _text="{tr}Rebuild sitemap{/tr}"}

<br/><h2>{tr}Submit the Sitemap{/tr}</h2>
{remarksbox type="info" title="{tr}Submit the Sitemap{/tr}" close="n"}
{if $sitemapAvailable}
	{tr}You can submit the sitemap for processing in all major search engines using the following URL:{/tr}
	<br>
	<br>
	<a href="{$url}" target="_blank">{$url}</a>
{else}
	{tr}The URL that you will need to use for submitting the sitemap will be available after you rebuild the sitemap.{/tr}
{/if}
{/remarksbox}
{remarksbox type="info" title="{tr}Automate Sitemap generation{/tr}" close="n"}
	<p>
		{tr}You can automate the sitemap generation by using the scheduler functionality:
			<a href="https://doc.tiki.org/Scheduler">https://doc.tiki.org/Scheduler</a>
		{/tr}
	</p>
	<p>
		{tr}Or you can use directly the command line:{/tr} <code>php console.php sitemap:generate {$base_host}</code>
	</p>
{/remarksbox}
