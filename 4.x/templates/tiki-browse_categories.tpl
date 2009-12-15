{* $Id$ *}

{title}{if $parentId ne 0}{tr}Category{/tr} {$p_info.name|escape}{else}{tr}Categories{/tr}{/if}{/title}

{if $parentId and $p_info.description}
	<div class="description">{$p_info.description}</div>
{/if}
{if $tiki_p_admin_categories eq 'y'}
	<div class="categbar">
		{if $user and $prefs.feature_user_watches eq 'y'}
			{if $user_watching_category eq 'n'}
				<a href="tiki-browse_categories.php?parentId={$parentId|escape:"url"}&amp;watch_event=category_changed&amp;watch_object={$parentId|escape:"url"}&amp;deep={$deep}&amp;watch_action=add" class="icon">{icon _id='eye' align='right' alt='{tr}Watch Only This Category{/tr}'}</a>
				<a href="tiki-browse_categories.php?parentId={$parentId|escape:"url"}&amp;watch_event=category_changed&amp;watch_object={$parentId|escape:"url"}&amp;deep={$deep}&amp;watch_action=add_desc" class="icon">{icon _id='eye_arrow_down' align='right' alt='{tr}Watch This Category and Their Descendants{/tr}'}</a>
			{else}
				<a href="tiki-browse_categories.php?parentId={$parentId|escape:"url"}&amp;watch_event=category_changed&amp;watch_object={$parentId|escape:"url"}&amp;deep={$deep}&amp;watch_action=remove">{icon _id='no_eye' align='right' alt='{tr}Stop Watching Only This Category{/tr}'}</a>
				<a href="tiki-browse_categories.php?parentId={$parentId|escape:"url"}&amp;watch_event=category_changed&amp;watch_object={$parentId|escape:"url"}&amp;deep={$deep}&amp;watch_action=remove_desc" class="icon">{icon _id='no_eye_arrow_down' align='right' alt='{tr}Stop Watching This Category and Their Descendants{/tr}'}</a>
			{/if}
		{/if}
		{button href="tiki-admin_categories.php?parentId=$parentId" _text="{tr}Admin Category{/tr}" _title="{tr}Admin the Category System{/tr}"}
	</div>
{/if}

<div class="navbar">
	{tr}Browse in{/tr}:
	
	<span class="button">
		<a {if $type eq ''} id="highlight"{/if} href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{tr}All{/tr}</a>
	</span>

	{if $prefs.feature_wiki eq 'y'}
		<span class="button">
			<a {if $type eq 'wiki page'} id="highlight"{/if} href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=wiki+page&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{tr}Wiki pages{/tr}</a>
		</span>
	{/if}

	{if $prefs.feature_galleries eq 'y'}
		<span class="button">
			<a {if $type eq 'image gallery'} id="highlight"{/if} href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=image+gallery&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{tr}Image galleries{/tr}</a>
		</span>
		<span class="button">
			<a {if $type eq 'image'} id="highlight"{/if} href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=image&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{tr}Images{/tr}</a>
		</span>
	{/if}

	{if $prefs.feature_file_galleries eq 'y'}
		<span class="button">
			<a {if $type eq 'file gallery'} id="highlight"{/if} href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=file+gallery&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{tr}File galleries{/tr}</a>
		</span>

		<span class="button">
			<a {if $type eq 'file'} id="highlight"{/if} href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=file&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{tr}Files{/tr}</a>
		</span>
	{/if}

	{if $prefs.feature_blogs eq 'y'}
		<span class="button">
			<a {if $type eq 'blog'} id="highlight"{/if} href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=blog&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{tr}Blogs{/tr}</a>
		</span>
	{/if}

	{if $prefs.feature_trackers eq 'y'}
		<span class="button">
			<a {if $type eq 'tracker'} id="highlight"{/if} href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=tracker&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{tr}Trackers{/tr}</a>
		</span>
		<span class="button">
			<a {if $type eq 'trackeritem'} id="highlight"{/if} href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=trackeritem&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{tr}Trackers Items{/tr}</a>
		</span>
	{/if}

	{if $prefs.feature_quizzes eq 'y'}
		<span class="button">
			<a {if $type eq 'quiz'} id="highlight"{/if} href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=quiz&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{tr}Quizzes{/tr}</a>
		</span>
	{/if}

	{if $prefs.feature_polls eq 'y'}
		<span class="button">
			<a {if $type eq 'poll'} id="highlight"{/if} href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=poll&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{tr}Polls{/tr}</a>
		</span>
	{/if}

	{if $prefs.feature_surveys eq 'y'}
		<span class="button">
			<a {if $type eq 'survey'} id="highlight"{/if} href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=survey&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{tr}Surveys{/tr}</a>
		</span>
	{/if}

{	if $prefs.feature_directory eq 'y'}
		<span class="button">
			<a {if $type eq 'directory'} id="highlight"{/if} href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=directory&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{tr}Directory{/tr}</a>
		</span>
	{/if}

	{if $prefs.feature_faqs eq 'y'}
		<span class="button">
			<a {if $type eq 'faq'} id="highlight"{/if} href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=faq&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{tr}FAQs{/tr}</a>
		</span>
	{/if}

	{if $prefs.feature_sheet eq 'y'}
		<span class="button">
			<a {if $type eq 'sheet'} id="highlight"{/if} href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=sheet&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{tr}Sheets{/tr}</a>
		</span>
	{/if}

	{if $prefs.feature_articles eq 'y'}
		<span class="button">
			<a {if $type eq 'article'} id="highlight"{/if} href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=article&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{tr}Articles{/tr}</a>
		</span>
	{/if}

	{if $prefs.feature_forums eq 'y'}
		<span class="button">
			<a {if $type eq 'forum'} id="highlight"{/if} href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=forum&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{tr}Forums{/tr}</a>
		</span>
	{/if}
</div>

<form method="post" action="tiki-browse_categories.php">
	<label>{tr}Find:{/tr} {$p_info.name|escape} <input type="text" name="find" value="{$find|escape}" size="35" /></label><input type="submit" value="{tr}Find{/tr}" name="search" />
	<label>{tr}in the current category - and its subcategories: {/tr}<input type="checkbox" name="deep" {if $deep eq 'on'}checked="checked"{/if}/></label>
	<input type="hidden" name="parentId" value="{$parentId|escape}" />
	<input type="hidden" name="type" value="{$type|escape}" />
	
	<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
</form>

{if $deep eq 'on'}
	<a class="link" href="tiki-browse_categories.php?find={$find|escape}&amp;type={$type|escape}&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{tr}Hide subcategories objects{/tr}</a>
{else}
	<a class="link" href="tiki-browse_categories.php?find={$find|escape}&amp;type={$type|escape}&amp;deep=on&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{tr}Show subcategories objects{/tr}</a>
{/if}

<br /><br />

{if $path}
	<div class="treetitle">{tr}Current category{/tr}:
		<a href="tiki-browse_categories.php?parentId=0&amp;deep={$deep}&amp;type={$type|escape}" class="categpath">{tr}Top{/tr}</a>
		{section name=x loop=$path}
			&nbsp;{$prefs.site_crumb_seper}&nbsp;
			<a class="categpath" href="tiki-browse_categories.php?parentId={$path[x].categId}&amp;deep={$deep}&amp;type={$type|escape}">{$path[x].name|tr_if|escape}</a>
		{/section}
	</div>

	{if $parentId ne '0'}
		<div class="treenode">
			<a class="catname" href="tiki-browse_categories.php?parentId={$father}&amp;deep={$deep}&amp;type={$type}" title="{tr}Upper level{/tr}">..</a>
		</div>
	{/if}
{elseif $paths}
	{section name=x loop=$paths}
		{section name=y loop=$paths[x]}
			&nbsp;{$prefs.site_crumb_seper}&nbsp;
			<a class="categpath" href="tiki-browse_categories.php?parentId={$paths[x][y].categId}&amp;deep={$deep}&amp;type={$type|escape}">{$paths[x][y].name|tr_if}</a>
		{/section}
		<br />
	{/section}
{/if}

<table class="admin">
	<tr>
		<td>{$tree}</td>
		<td width="20">&nbsp;</td>
		<td>
			{if $cant_pages > 0}
				<table class="normal">
					<tr>
						<th>
							{tr}Name{/tr}
						</th>
						<th>
							{tr}Type{/tr}
						</th>
						{if $deep eq 'on'}
							<th>
								{tr}Category{/tr}
							</th>
						{/if}
					</tr>

					{cycle values="odd,even" print=false}
					{section name=ix loop=$objects}
						<tr class="{cycle}" >
							<td>
								<a href={if empty($objects[ix].sefurl)}"{$objects[ix].href}"{else}"{$objects[ix].sefurl}"{/if} class="catname">{$objects[ix].name|escape|default:'&nbsp;'}</a>
								<div class="subcomment">{$objects[ix].description}</div>
							</td>
							<td>
								{tr}{$objects[ix].type|replace:"wiki page":"wiki"|replace:"trackeritem":"tracker item"}{/tr}
							</td>
							{if $deep eq 'on'}
								<td>
									{$objects[ix].categName|tr_if|escape}
								</td>
							{/if}
						</tr>
					{sectionelse}
						<tr>
							<td colspan="{if $deep eq 'on'}3{else}2{/if}" class="odd">{tr}No records found{/tr}</td>
						</tr>
					{/section}
				</table>
				<br />
			{/if}
		</td>
	</tr>
</table>

{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
