{if $smarty.request.diff_style}
	{if $translation_mode ne 'y'}
		<h2>{tr}Comparing version {$old.version} with version {$new.version}{/tr}</h2>		
	{/if}
	<table class="normal diff">
	{if $translation_mode eq 'n'}
		<tr>
			<th colspan="2"><b>{tr}Version:{/tr} <a href="tiki-pagehistory.php?page={$page|escape:"url"}&amp;preview={$old.version}" title="{tr}View{/tr}">{$old.version}</a>{if $old.version == $info.version} ({tr}Current{/tr}){/if}</b></th>
			<th colspan="2"><b>{tr}Version:{/tr} <a href="tiki-pagehistory.php?page={$page|escape:"url"}&amp;preview={$new.version}" title="{tr}View{/tr}">{$new.version}</a>{if $new.version == $info.version} ({tr}Current{/tr}){/if}</b></th>
		</tr>
		<tr>
			<td colspan="2">{if $tiki_p_wiki_view_author ne 'n'}{$old.user|userlink} - {/if}{$old.lastModif|tiki_short_datetime}</td>
			<td colspan="2">{if $tiki_p_wiki_view_author ne 'n'}{$new.user|userlink} - {/if}{$new.lastModif|tiki_short_datetime}</td>
		</tr>
		{if $old.comment || $new.comment}
			<tr>
				<td colspan="2" class="editdate">{if $old.comment}{$old.comment}{else}&nbsp;{/if}</td>
				<td colspan="2" class="editdate">{if $new.comment}{$new.comment}{else}&nbsp;{/if}</td>
			</tr>
		{/if}
		{if $old.description != $new.description}
			<tr>
				<td colspan="2" class="diffdeleted">{if $old.description}{$old.description}{else}&nbsp;{/if}</td>
				<td colspan="2" class="diffadded">{if $new.description}{$new.description}{else}&nbsp;{/if}</td>
			</tr>
		{/if}
	{/if}

	{if $diff_style eq "sideview"}
		<tr>
			<td colspan="2" valign="top" ><div class="wikitext">{$old.data}</div></td>
			<td colspan="2" valign="top" ><div class="wikitext">{$new.data}</div></td>
		</tr>
	{/if}
		<tr>
			{if $smarty.request.oldver_idx + 1 eq $smarty.request.newver_idx or $smarty.request.oldver_idx eq $smarty.request.newver_idx}
				<td colspan="4">
					{if isset($show_all_versions) and $show_all_versions eq "n"}
						{pagination_links cant=$ver_cant offset=$smarty.request.bothver_idx offset_arg="bothver_idx" itemname="{tr}Session{/tr}" show_numbers="n"}{/pagination_links}
					{else}
						{pagination_links cant=$ver_cant offset=$smarty.request.bothver_idx offset_arg="bothver_idx" itemname="{tr}Version{/tr}" show_numbers="n"}{/pagination_links}
					{/if}
				</td>
			{else}
				<td colspan="2">
					{if isset($show_all_versions) and $show_all_versions eq "n"}
						{pagination_links cant=$ver_cant offset=$smarty.request.oldver_idx offset_arg="oldver_idx" itemname="{tr}Old Session{/tr}" show_numbers="n"}{/pagination_links}
					{else}
						{pagination_links cant=$ver_cant offset=$smarty.request.oldver_idx offset_arg="oldver_idx" itemname="{tr}Old Version{/tr}" show_numbers="n"}{/pagination_links}
					{/if}
				</td>
				<td colspan="2">
					{if isset($show_all_versions) and $show_all_versions eq "n"}
						{pagination_links cant=$ver_cant offset=$smarty.request.newver_idx offset_arg="newver_idx" itemname="{tr}New Session{/tr}" show_numbers="n"}{/pagination_links}
					{else}
						{pagination_links cant=$ver_cant offset=$smarty.request.newver_idx offset_arg="newver_idx" itemname="{tr}New Version{/tr}" show_numbers="n"}{/pagination_links}
					{/if}
				</td>
			{/if}
		</tr>
		{if $diff_style eq 'unidiff'}
			 <tr>
			 	<td colspan="4">
					 {if $diffdata}
						{section name=ix loop=$diffdata}
							{if $diffdata[ix].type == "diffheader"}
								{assign var="oldd" value=$diffdata[ix].old}
								{assign var="newd" value=$diffdata[ix].new}
								<br /><div class="diffheader">@@ {tr}-Lines: {$oldd} changed to +Lines: {$newd}{/tr} @@</div>
							{elseif $diffdata[ix].type == "diffdeleted"}
								<div class="diffdeleted">
								{section name=iy loop=$diffdata[ix].data}
									{if not $smarty.section.iy.first}<br />{/if}
									- {$diffdata[ix].data[iy]}
								{/section}
								</div>
							{elseif $diffdata[ix].type == "diffadded"}
								<div class="diffadded">
									{section name=iy loop=$diffdata[ix].data}
										{if not $smarty.section.iy.first}<br />{/if}
										+ {$diffdata[ix].data[iy]}
									{/section}
								</div>
							{elseif $diffdata[ix].type == "diffbody"}
								<div class="diffbody">
								{section name=iy loop=$diffdata[ix].data}
									{if not $smarty.section.iy.first}<br />{/if}
									{$diffdata[ix].data[iy]}
								{/section}
							</div>
							{/if}
						{/section}
					{else}
						<div class="diffheader">{tr}Versions are identical{/tr}</div>
					{/if}
				</td>
			</tr>
		{/if}
		
		{if $diff_style neq 'unidiff' && $diff_style neq 'sideview'}
			{if $diffdata}{$diffdata}{else}<tr><td colspan="4">{tr}Versions are identical{/tr}</td></tr>{/if}
		{/if}
	</table>
{/if}
