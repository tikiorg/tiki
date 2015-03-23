{title}{tr}Admin FAQ:{/tr} {$faq_info.title}{/title}

<div class="t_navbar margin-bottom-md">
	{button href="tiki-list_faqs.php" class="btn btn-default" _text="{tr}List FAQs{/tr}"}
	{button href="tiki-view_faq.php?faqId=$faqId" class="btn btn-default" _text="{tr}View FAQ{/tr}"}
	{button href="tiki-list_faqs.php?faqId=$faqId" class="btn btn-default" _text="{tr}Edit this FAQ{/tr}"}
	{button href="tiki-faq_questions.php?faqId=$faqId" class="btn btn-default" _text="{tr}New Question{/tr}"}
</div>

<h2>{if $questionId}{tr}Edit FAQ question{/tr}{else}{tr}Add FAQ question{/tr}{/if}</h2>

<form action="tiki-faq_questions.php" method="post" id="editpageform">
	<input type="hidden" name="questionId" value="{$questionId|escape}">
	<input type="hidden" name="faqId" value="{$faqId|escape}">

	{* begin table *}
	<table class="formcolor">
		<tr>
			<td>{tr}Question:{/tr}</td>
			<td >
				<textarea type="text" rows="2" cols="80" name="question">{$question|escape}</textarea>
			</td>
		</tr>

		<tr>
			<td>{tr}Answer:{/tr}
			</td>
			<td >
				{toolbars area_id="faqans"}
				<textarea id='faqans' type="text" rows="8" cols="80" name="answer">{$answer|escape}</textarea>
			</td>
		</tr>

		<tr>
			<td >&nbsp;</td>
			<td >
				<input type="submit" class="btn btn-primary btn-sm" name="save" value="{tr}Save{/tr}">
				{* set your changes and save 'em *}
			</td>
		</tr>
	</table>
	{* end table *}
</form>

{* This is the area for choosing questions from the db... it really should support choosing options from the answers, but only show if there are existing questions *}
{if $allq}
<h2> {tr}Use a question from another FAQ{/tr}</h2>
<form action="tiki-faq_questions.php" method="post">
	<input type="hidden" name="questionId" value="{$questionId|escape}">
	<input type="hidden" name="faqId" value="{$faqId|escape}">
	<table class="formcolor">
		<tr>
			<td>{tr}Filter{/tr}</td>
			<td>
				<input type="text" name="filter" value="{$filter|escape}">
				<input type="submit" class="btn btn-default btn-sm" name="filteruseq" value="{tr}Filter{/tr}">
			</td>
		</tr>
		<tr>
			<td>{tr}Question:{/tr}</td>
			<td >
				<select name="usequestionId">
					{section name=ix loop=$allq}
						{* Ok, here's where you change the truncation field for this field *}
						<option value="{$allq[ix].questionId|escape|truncate:20:"":true}">{$allq[ix].question|escape|truncate:110:"":true}</option>
					{/section}
				</select>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<input type="submit" class="btn btn-default btn-sm" name="useq" value="{tr}Use{/tr}">
			</td>
		</tr>
	</table>
	</form>
{/if}
<br>

{* next big chunk *}
<br>
<h2>{tr}FAQ questions{/tr}</h2>
{if $channels or ($find ne '')}
	{include file='find.tpl'}
{/if}

<div class="table-responsive">
	<table class="table normal table-striped table-hover">
		<tr>
			<th>
				<a href="tiki-faq_questions.php?faqId={$faqId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'questionId_desc'}questionId_asc{else}questionId_desc{/if}">{tr}ID{/tr}</a>
			</th>
			<th>
				<a href="tiki-faq_questions.php?faqId={$faqId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'question_desc'}question_asc{else}question_desc{/if}">{tr}Question{/tr}</a>
			</th>
			<th>{tr}Action{/tr}</th>
		</tr>

		{section name=user loop=$channels}
		<tr>
			<td class="id">{$channels[user].questionId}</td>
			<td class="text">{$channels[user].question|escape}</td>
			<td class="action">
				{capture name=faq_actions}
					{strip}
						<a href="tiki-faq_questions.php?faqId={$faqId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;questionId={$channels[user].questionId}">
							{icon name='edit' _menu_text='y' _menu_icon='y' alt="{tr}Edit{/tr}"}
						</a>
						<a href="tiki-faq_questions.php?faqId={$faqId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].questionId}">
							{icon name='remove' _menu_text='y' _menu_icon='y' alt="{tr}Remove{/tr}"}
						</a>
					{/strip}
				{/capture}
				<a class="tips"
				   title="{tr}Actions{/tr}"
				   href="#" {popup trigger="click" fullhtml="1" center=true text=$smarty.capture.faq_actions|escape:"javascript"|escape:"html"}
				   style="padding:0; margin:0; border:0"
						>
					{icon name='wrench'}
				</a>
			</td>
		</tr>
		{sectionelse}
			{norecords _colspan=3}
		{/section}
	</table>
</div>

{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}

{if count($suggested) > 0}

	<h2>{tr}Suggested questions{/tr}</h2>
	<div class="table-responsive">
		<table class="table normal table-striped table-hover">
			<tr>
				<th>{tr}Question{/tr}</th>
				<th>{tr}Answer{/tr}</th>
				<th></th>
			</tr>

			{section name=ix loop=$suggested}
				<tr>
					<td class="text">{$suggested[ix].question|escape} </td>
					<td class="text">{$suggested[ix].answer|escape}</td>
					<td class="action">
						{capture name=faq2_actions}
							{strip}
								<a href="tiki-faq_questions.php?faqId={$faqId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;approve_suggested={$suggested[ix].sfqId}">
									{icon name='ok' _menu_text='y' _menu_icon='y' alt="{tr}Approve{/tr}"}
								</a>
								<a href="tiki-faq_questions.php?faqId={$faqId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove_suggested={$suggested[ix].sfqId}">
									{icon name='remove' _menu_text='y' _menu_icon='y' alt="{tr}Remove{/tr}"}
								</a>
							{/strip}
						{/capture}
						<a class="tips"
						   title="{tr}Actions{/tr}"
						   href="#" {popup trigger="click" fullhtml="1" center=true text=$smarty.capture.faq2_actions|escape:"javascript"|escape:"html"}
						   style="padding:0; margin:0; border:0"
								>
							{icon name='wrench'}
						</a>
					</td>
				</tr>
			{/section}
		</table>
	</div>
{else}
	<h2>{tr}No suggested questions{/tr}</h2>
{/if}
