{title help="FAQs" admpage="faqs"}{tr}FAQs{/tr}{/title}

{tabset name='tabs_list_faqs'}
	{tab name="{tr}Available FAQs{/tr}"}
		<h2>{tr}Available FAQs{/tr}</h2>

		{if $channels or ($find ne '')}
			{include file='find.tpl'}
		{/if}

		<div class="table-responsive">
			<table class="table normal">
				<tr>
					<th>
						<a href="tiki-list_faqs.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'title_desc'}title_asc{else}title_desc{/if}">{tr}Title{/tr}</a>
					</th>
					<th style="text-align:right;">
						<a href="tiki-list_faqs.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'hits_desc'}hits_asc{else}hits_desc{/if}">{tr}Visits{/tr}</a>
					</th>
					<th style="text-align:right;">
						<a href="tiki-list_faqs.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'questions_desc'}questions_asc{else}questions_desc{/if}">{tr}Questions / Suggested{/tr}</a>
					</th>
					{if $tiki_p_admin_faqs eq 'y'}
						<th></th>
					{/if}
				</tr>

				{section name=user loop=$channels}
					<tr>
						<td class="text">
							<a class="tablename" href="tiki-view_faq.php?faqId={$channels[user].faqId}">{$channels[user].title|escape}</a>
							<div class="subcomment">
								{$channels[user].description|escape|nl2br}
							</div>
						</td>
						<td class="integer">
							<span class="badge">{$channels[user].hits}</span>
						</td>
						<td class="integer">
							<span class="badge">{$channels[user].questions}</span> /  <span class="badge">{$channels[user].suggested}</span>
						</td>
						{if $tiki_p_admin_faqs eq 'y'}
							<td class="action">
								{capture name=faq_actions}
									{strip}
										<a href="tiki-list_faqs.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;faqId={$channels[user].faqId}">
											{icon name='edit' _menu_text='y' _menu_icon='y' alt="{tr}Edit{/tr}"}
										</a>
										<a href="tiki-faq_questions.php?faqId={$channels[user].faqId}">
											{icon name='help' _menu_text='y' _menu_icon='y' alt="{tr}Questions{/tr}"}
										</a>
										<a href="tiki-list_faqs.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].faqId}">
											{icon name='remove' _menu_text='y' _menu_icon='y' alt="{tr}Remove{/tr}"}
										</a>
									{/strip}
								{/capture}
								<a class="tips"
								   title="{tr}Actions{/tr}"
								   href="#" {popup delay="0|2000" fullhtml="1" center=true text=$smarty.capture.faq_actions|escape:"javascript"|escape:"html"}
								   style="padding:0; margin:0; border:0"
										>
									{icon name='wrench'}
								</a>
							</td>
						{/if}
					</tr>
				{sectionelse}
					{if $tiki_p_admin_faqs eq 'y'}{norecords _colspan=5}{else}{norecords _colspan=4}{/if}
				{/section}
			</table>
		</div>

		{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}
	{/tab}

	{if $tiki_p_admin_faqs eq 'y'}
		{tab name="{tr}Edit/Create{/tr}"}
			{if $faqId > 0}
				<h2>{tr}Edit this FAQ:{/tr} {$title}</h2>
				<div class="t_navbar">
					{button href="tiki-list_faqs.php" class="btn btn-default" _text="{tr}Create new FAQ{/tr}"}
				</div>
			{else}
				<h2>{tr}Create New FAQ:{/tr}</h2>
			{/if}

			<form action="tiki-list_faqs.php" method="post">
				<input type="hidden" name="faqId" value="{$faqId|escape}">
				<table class="formcolor">
					<tr>
						<td>
							{tr}Title:{/tr}
						</td>
						<td>
							<input type="text" name="title" value="{$title|escape}">
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
							<input type="checkbox" name="canSuggest" {if $canSuggest eq 'y'}checked="checked"{/if}>
						</td>
					</tr>
					<tr>
						<td>&nbsp;

						</td>
						<td>
							<input type="submit" class="btn btn-primary btn-sm" name="save" value="{tr}Save{/tr}">
						</td>
					</tr>
				</table>
			</form>
		{/tab}
	{/if}
{/tabset}

