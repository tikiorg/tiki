{if $wiki_authors_style eq 'business'}
	{tr}Last edited by{/tr} {$lastUser|userlink}
	{section name=author loop=$contributors}
		{if $smarty.section.author.first}
			, {tr}based on work by{/tr}
		{else}
			{if !$smarty.section.author.last}
				,
			{else}
				{tr}and{/tr}
			{/if}
		{/if}
		{$contributors[author]|userlink}
	{/section}.
	<br />
	{tr}Page last modified on{/tr} {$lastModif|tiki_long_datetime}. {if $prefs.wiki_show_version eq 'y'}({tr}Version{/tr} {$lastVersion}){/if}
{elseif $wiki_authors_style eq 'collaborative'}
	{tr}Contributors to this page{/tr}: {$lastUser|userlink}
	{section name=author loop=$contributors}
		{if !$smarty.section.author.last}
			,
		{else} 
			{tr}and{/tr}
		{/if}
		{$contributors[author]|userlink}
	{/section}.
	<br />
	{tr 0=$lastModif|tiki_long_datetime 1=$lastUser|userlink}Page last modified on %0 by %1{/tr}. 
	{if $prefs.wiki_show_version eq 'y'}
		({tr}Version{/tr} {$lastVersion})
	{/if}

{elseif $wiki_authors_style eq 'lastmodif'}
	{tr}Page last modified on{/tr} {$lastModif|tiki_long_datetime}
{else}
	{tr 0=$creator|userlink}Created by %0{/tr}.
	{tr 0=$lastModif|tiki_long_datetime 1=$lastUser|userlink}Last Modification: %0 by %1{/tr}. 
	{if $prefs.wiki_show_version eq 'y'}
		({tr}Version{/tr} {$lastVersion})
	{/if}
{/if}
