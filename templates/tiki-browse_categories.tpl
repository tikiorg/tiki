{* $Id$ *}

{title}{if $parentId ne 0}{tr}Category{/tr} {$p_info.name}{else}{tr}Categories{/tr}{/if}{/title}

{if $parentId and $p_info.description}
	<div class="description">{$p_info.description|escape|nl2br}</div>
{/if}
<div class="categbar">
	{button href="tiki-edit_categories.php" _text="{tr}Organize Objects{/tr}" _title="{tr}Organize Objects{/tr}"}
	{if $tiki_p_admin_categories eq 'y'}
		{button href="tiki-admin_categories.php?parentId=$parentId" _text="{tr}Admin Category{/tr}" _title="{tr}Admin the Category System{/tr}"}
	{/if}
</div>

<div class="navbar">
	{tr}Browse in:{/tr}
	
	<span class="button">
		<a {if $type eq ''} id="highlight"{/if} href="tiki-browse_categories.php?find={$find|escape:"url"}&amp;deep={$deep}&amp;parentId={$parentId|escape:"url"}&amp;sort_mode={$sort_mode|escape:"url"}">{tr}All{/tr}</a>
	</span>

	{if $prefs.feature_wiki eq 'y'}
		<span class="button">
			<a {if $type eq 'wiki page'} id="highlight"{/if} href="tiki-browse_categories.php?find={$find|escape:"url"}&amp;deep={$deep|escape:"url"}&amp;type=wiki+page&amp;parentId={$parentId|escape:"url"}&amp;sort_mode={$sort_mode|escape:"url"}">{tr}Wiki pages{/tr}</a>
		</span>
	{/if}

	{if $prefs.feature_galleries eq 'y'}
		<span class="button">
			<a {if $type eq 'image gallery'} id="highlight"{/if} href="tiki-browse_categories.php?find={$find|escape:"url"}&amp;deep={$deep|escape:"url"}&amp;type=image+gallery&amp;parentId={$parentId|escape:"url"}&amp;sort_mode={$sort_mode|escape:"url"}">{tr}Image galleries{/tr}</a>
		</span>
		<span class="button">
			<a {if $type eq 'image'} id="highlight"{/if} href="tiki-browse_categories.php?find={$find|escape:"url"}&amp;deep={$deep|escape:"url"}&amp;type=image&amp;parentId={$parentId|escape:"url"}&amp;sort_mode={$sort_mode|escape:"url"}">{tr}Images{/tr}</a>
		</span>
	{/if}

	{if $prefs.feature_file_galleries eq 'y'}
		<span class="button">
			<a {if $type eq 'file gallery'} id="highlight"{/if} href="tiki-browse_categories.php?find={$find|escape:"url"}&amp;deep={$deep|escape:"url"}&amp;type=file+gallery&amp;parentId={$parentId|escape:"url"}&amp;sort_mode={$sort_mode|escape:"url"}">{tr}File Galleries{/tr}</a>
		</span>

		<span class="button">
			<a {if $type eq 'file'} id="highlight"{/if} href="tiki-browse_categories.php?find={$find|escape:"url"}&amp;deep={$deep|escape:"url"}&amp;type=file&amp;parentId={$parentId|escape:"url"}&amp;sort_mode={$sort_mode|escape:"url"}">{tr}Files{/tr}</a>
		</span>
	{/if}

	{if $prefs.feature_blogs eq 'y'}
		<span class="button">
			<a {if $type eq 'blog'} id="highlight"{/if} href="tiki-browse_categories.php?find={$find|escape:"url"}&amp;deep={$deep|escape:"url"}&amp;type=blog&amp;parentId={$parentId|escape:"url"}&amp;sort_mode={$sort_mode|escape:"url"}">{tr}Blogs{/tr}</a>
		</span>
	{/if}

	{if $prefs.feature_trackers eq 'y'}
		<span class="button">
			<a {if $type eq 'tracker'} id="highlight"{/if} href="tiki-browse_categories.php?find={$find|escape:"url"}&amp;deep={$deep|escape:"url"}&amp;type=tracker&amp;parentId={$parentId|escape:"url"}&amp;sort_mode={$sort_mode|escape:"url"}">{tr}Trackers{/tr}</a>
		</span>
		<span class="button">
			<a {if $type eq 'trackeritem'} id="highlight"{/if} href="tiki-browse_categories.php?find={$find|escape:"url"}&amp;deep={$deep|escape:"url"}&amp;type=trackeritem&amp;parentId={$parentId|escape:"url"}&amp;sort_mode={$sort_mode|escape:"url"}">{tr}Trackers Items{/tr}</a>
		</span>
	{/if}

	{if $prefs.feature_quizzes eq 'y'}
		<span class="button">
			<a {if $type eq 'quiz'} id="highlight"{/if} href="tiki-browse_categories.php?find={$find|escape:"url"}&amp;deep={$deep|escape:"url"}&amp;type=quiz&amp;parentId={$parentId|escape:"url"}&amp;sort_mode={$sort_mode|escape:"url"}">{tr}Quizzes{/tr}</a>
		</span>
	{/if}

	{if $prefs.feature_polls eq 'y'}
		<span class="button">
			<a {if $type eq 'poll'} id="highlight"{/if} href="tiki-browse_categories.php?find={$find|escape:"url"}&amp;deep={$deep|escape:"url"}&amp;type=poll&amp;parentId={$parentId|escape:"url"}&amp;sort_mode={$sort_mode|escape:"url"}">{tr}Polls{/tr}</a>
		</span>
	{/if}

	{if $prefs.feature_surveys eq 'y'}
		<span class="button">
			<a {if $type eq 'survey'} id="highlight"{/if} href="tiki-browse_categories.php?find={$find|escape:"url"}&amp;deep={$deep|escape:"url"}&amp;type=survey&amp;parentId={$parentId|escape:"url"}&amp;sort_mode={$sort_mode|escape:"url"}">{tr}Surveys{/tr}</a>
		</span>
	{/if}

	{if $prefs.feature_directory eq 'y'}
		<span class="button">
			<a {if $type eq 'directory'} id="highlight"{/if} href="tiki-browse_categories.php?find={$find|escape:"url"}&amp;deep={$deep|escape:"url"}&amp;type=directory&amp;parentId={$parentId|escape:"url"}&amp;sort_mode={$sort_mode|escape:"url"}">{tr}Directory{/tr}</a>
		</span>
	{/if}

	{if $prefs.feature_faqs eq 'y'}
		<span class="button">
			<a {if $type eq 'faq'} id="highlight"{/if} href="tiki-browse_categories.php?find={$find|escape:"url"}&amp;deep={$deep|escape:"url"}&amp;type=faq&amp;parentId={$parentId|escape:"url"}&amp;sort_mode={$sort_mode|escape:"url"}">{tr}FAQs{/tr}</a>
		</span>
	{/if}

	{if $prefs.feature_sheet eq 'y'}
		<span class="button">
			<a {if $type eq 'sheet'} id="highlight"{/if} href="tiki-browse_categories.php?find={$find|escape:"url"}&amp;deep={$deep|escape:"url"}&amp;type=sheet&amp;parentId={$parentId|escape:"url"}&amp;sort_mode={$sort_mode|escape:"url"}">{tr}Sheets{/tr}</a>
		</span>
	{/if}

	{if $prefs.feature_articles eq 'y'}
		<span class="button">
			<a {if $type eq 'article'} id="highlight"{/if} href="tiki-browse_categories.php?find={$find|escape:"url"}&amp;deep={$deep|escape:"url"}&amp;type=article&amp;parentId={$parentId|escape:"url"}&amp;sort_mode={$sort_mode|escape:"url"}">{tr}Articles{/tr}</a>
		</span>
	{/if}

	{if $prefs.feature_forums eq 'y'}
		<span class="button">
			<a {if $type eq 'forum'} id="highlight"{/if} href="tiki-browse_categories.php?find={$find|escape:"url"}&amp;deep={$deep|escape:"url"}&amp;type=forum&amp;parentId={$parentId|escape:"url"}&amp;sort_mode={$sort_mode|escape:"url"}">{tr}Forums{/tr}</a>
		</span>
	{/if}
</div>

<form method="post" action="tiki-browse_categories.php">
	<label>{tr}Find:{/tr} {if $parentId ne 0}{$p_info.name|escape} {/if}<input type="text" name="find" value="{$find|escape}" size="35"></label>
	{help url="#" desc="{tr}Find in:{/tr} <ul><li>{tr}Name{/tr}</li><li>{tr}Description{/tr}</li></ul>"}
	<input type="submit" class="btn btn-default" value="{tr}Find{/tr}" name="search">
	<label>{tr}in the current category - and its subcategories: {/tr}<input type="checkbox" name="deep" {if $deep eq 'on'}checked="checked"{/if}></label>
	<input type="hidden" name="parentId" value="{$parentId|escape}">
	<input type="hidden" name="type" value="{$type|escape}">
	
	<input type="hidden" name="sort_mode" value="{$sort_mode|escape}">
</form>

{if $deep eq 'on'}
	<a class="link" href="tiki-browse_categories.php?find={$find|escape:"url"}&amp;type={$type|escape:"url"}&amp;parentId={$parentId|escape:"url"}&amp;sort_mode={$sort_mode|escape:"url"}">{tr}Hide subcategories objects{/tr}</a>
{else}
	<a class="link" href="tiki-browse_categories.php?find={$find|escape:"url"}&amp;type={$type|escape:"url"}&amp;deep=on&amp;parentId={$parentId|escape:"url"}&amp;sort_mode={$sort_mode|escape:"url"}">{tr}Show subcategories objects{/tr}</a>
{/if}

<br><br>

{if isset($p_info)}
	<div class="treetitle">{tr}Current category:{/tr}
		<a href="tiki-browse_categories.php?parentId=0&amp;deep={$deep|escape:"url"}&amp;type={$type|escape:"url"}" class="categpath">{tr}Top{/tr}</a>
		{foreach $p_info.tepath as $id=>$name}
			&nbsp;{$prefs.site_crumb_seper}&nbsp;
			<a class="categpath" href="tiki-browse_categories.php?parentId={$id}&amp;deep={$deep|escape:"url"}&amp;type={$type|escape:"url"}">{$name|escape}</a>
		{/foreach}
			{$eyes_curr}
	</div>
     
	{if $parentId ne '0'}
		<div>
			<a class="catname" href="tiki-browse_categories.php?parentId={$father|escape:"url"}&amp;deep={$deep|escape:"url"}&amp;type={$type|escape:"url"}" title="{tr}Upper level{/tr}">..</a>
		</div>
	{/if}
{/if}
<div class="cattree">{$tree}</div>
<div class="catobj">
	{if $cant_pages > 0}
		<table class="table normal">
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
				<tr class="{cycle}">
					<td class="text">
						<a href={if empty($objects[ix].sefurl)}"{$objects[ix].href}"{else}"{$objects[ix].sefurl}"{/if} class="catname">{$objects[ix].name|escape|default:'&nbsp;'}</a>
						<div class="subcomment">{$objects[ix].description|escape|nl2br}</div>
					</td>
					<td class="text">
						{tr}{$objects[ix].type|replace:"wiki page":"wiki"|replace:"trackeritem":"tracker item"}{/tr}
					</td>
					{if $deep eq 'on'}
						<td class="text">
							{$objects[ix].categName|tr_if|escape}
						</td>
					{/if}
				</tr>
			{sectionelse}
				{if $deep eq 'on'}
					{norecords _colspan=3}
				{else}
					{norecords _colspan=2}
				{/if}
			{/section}
		</table>
		<br>
	{/if}
</div>

{pagination_links cant=$cant_pages step=$maxRecords offset=$offset}{/pagination_links}
