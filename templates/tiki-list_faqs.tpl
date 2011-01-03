{title help="FAQs" admpage="faqs"}{tr}FAQs{/tr}{/title}

{tabset name='tabs_list_faqs'}
{tab name="{tr}Available FAQs{/tr}"}

{if $channels or ($find ne '')}
  {include file='find.tpl'}
{/if}

<table class="normal">
	<tr>
		<th>
			<a href="tiki-list_faqs.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'title_desc'}title_asc{else}title_desc{/if}">{tr}Title{/tr}</a>
		</th>
		<th style="text-align:right;">
			<a href="tiki-list_faqs.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'hits_desc'}hits_asc{else}hits_desc{/if}">{tr}Visits{/tr}</a>
		</th>
		<th style="text-align:right;">
			<a href="tiki-list_faqs.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'questions_desc'}questions_asc{else}questions_desc{/if}">{tr}Questions{/tr}</a>
		</th>
		{if $tiki_p_admin_faqs eq 'y'}
			<th>{tr}Action{/tr}</th>
		{/if}
	</tr>
	{cycle values="odd,even" print=false}
	{section name=user loop=$channels}
		<tr class="{cycle}">
			<td>
				<a class="tablename" href="tiki-view_faq.php?faqId={$channels[user].faqId}">{$channels[user].title|escape}</a>
				<div class="subcomment">
					{$channels[user].description|escape|nl2br}
				</div>
			</td>
			<td style="text-align:right;">
				{$channels[user].hits}
			</td>
			<td style="text-align:right;">
				{$channels[user].questions} ({$channels[user].suggested})
			</td>
			{if $tiki_p_admin_faqs eq 'y'}
				<td style="text-align:right">
					<a class="link" href="tiki-list_faqs.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;faqId={$channels[user].faqId}">{icon _id='page_edit'}</a>
					<a class="link" href="tiki-faq_questions.php?faqId={$channels[user].faqId}">{icon _id='help' alt="{tr}Questions{/tr}"}</a>
					<a class="link" href="tiki-list_faqs.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].faqId}">{icon _id='cross' alt="{tr}Remove{/tr}"}</a>
				</td>
			{/if}
		</tr>
	{sectionelse}
		<tr>
			<td class="odd" colspan="{if $tiki_p_admin_faqs eq 'y'}5{else}4{/if}">
				<strong>{tr}No records found.{/tr}</strong>
			<td>
		</tr>
	{/section}
</table>

{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}
{/tab}

{if $tiki_p_admin_faqs eq 'y'}
{tab name="{tr}Edit/Create{/tr}"}
  {if $faqId > 0}
		<h2>{tr}Edit this FAQ:{/tr} {$title}</h2>
		<div class="navbar">
			{button href="tiki-list_faqs.php" _text="{tr}Create new FAQ{/tr}"} 
		</div>
  {else}
		<h2>{tr}Create New FAQ:{/tr}</h2>
	{/if}

	<form action="tiki-list_faqs.php" method="post">
		<input type="hidden" name="faqId" value="{$faqId|escape}" />
		<table class="formcolor">
			<tr>
				<td>
					{tr}Title:{/tr}
				</td>
				<td>
					<input type="text" name="title" value="{$title|escape}" />
				</td>
			</tr>
			<tr>
				<td>
					{tr}Description:{/tr}
				</td>
				<td>
					<textarea name="description" rows="4" cols="40">{$description|escape}</textarea>
				</td>
			</tr>
			{include file='categorize.tpl'}
			<tr>
				<td>
					{tr}Users can suggest questions:{/tr}
				</td>
				<td>
					<input type="checkbox" name="canSuggest" {if $canSuggest eq 'y'}checked="checked"{/if} />
				</td>
			</tr>
			<tr>
				<td>
					&nbsp;
				</td>
				<td>
					<input type="submit" name="save" value="{tr}Save{/tr}" />
				</td>
			</tr>
		</table>
	</form>
{/tab}
{/if}
{/tabset}

