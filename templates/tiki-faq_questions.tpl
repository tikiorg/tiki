{title}{tr}Admin FAQ:{/tr} {$faq_info.title}{/title}

<div class="t_navbar margin-bottom-md">
	{button href="tiki-list_faqs.php" class="btn btn-default" _text="{tr}List FAQs{/tr}"}
	{button href="tiki-view_faq.php?faqId=$faqId" class="btn btn-default" _text="{tr}View FAQ{/tr}"}
	{button href="tiki-list_faqs.php?faqId=$faqId" class="btn btn-default" _text="{tr}Edit this FAQ{/tr}"}
	{button href="tiki-faq_questions.php?faqId=$faqId" class="btn btn-default" _text="{tr}New Question{/tr}"}
</div>

<h2>{if $questionId}{tr}Edit FAQ question{/tr}{else}{tr}Add FAQ question{/tr}{/if}</h2>
<br>
<form action="tiki-faq_questions.php" method="post" id="editpageform" class="form-horizontal">
	<input type="hidden" name="questionId" value="{$questionId|escape}">
	<input type="hidden" name="faqId" value="{$faqId|escape}">

	<div class="form-group">
		<label class="col-sm-3 control-label">{tr}Question{/tr}</label>
		<div class="col-sm-7">
      		<textarea type="text" rows="2" cols="80" name="question" class="form-control" tabindex="1">{$question|escape}</textarea>
  		</div>
    </div>
    <div class="form-group">
		<label class="col-sm-3 control-label">{tr}Answer{/tr}</label>
		<div class="col-sm-7">
      		{toolbars area_id="faqans"}
			<textarea id='faqans' type="text" rows="8" cols="80" name="answer" class="form-control" tabindex="2">{$answer|escape}</textarea>
  		</div>
    </div>
    <div class="form-group">
		<label class="col-sm-3 control-label"></label>
		<div class="col-sm-7">
      		<input type="submit" class="btn btn-primary btn-sm" name="save" value="{tr}Save{/tr}" tabindex="3">
  		</div>
    </div>
</form>

{* This is the area for choosing questions from the db... it really should support choosing options from the answers, but only show if there are existing questions *}
{if $allq}
<h2> {tr}Use a question from another FAQ{/tr}</h2>
<br>
<form action="tiki-faq_questions.php" method="post" class="form-horizontal">
	<input type="hidden" name="questionId" value="{$questionId|escape}">
	<input type="hidden" name="faqId" value="{$faqId|escape}">
	<div class="form-group">
		<label class="col-sm-3 control-label">{tr}Filter{/tr}</label>
		<div class="col-sm-7">
			<div class="input-group">
	      		<input type="text" name="filter" id="filter" value="{$filter|escape}" class="form-control input-sm">
				<div class="input-group-btn">
					<input type="submit" class="btn btn-default btn-sm" name="filteruseq" value="{tr}Filter{/tr}">
				</div>
			</div>
  		</div>
    </div>
    <div class="form-group">
		<label class="col-sm-3 control-label">{tr}Question{/tr}</label>
		<div class="col-sm-7">
      		<select name="usequestionId" class="form-control">
				{section name=ix loop=$allq}
					{* Ok, here's where you change the truncation field for this field *}
					<option value="{$allq[ix].questionId|escape|truncate:20:"":true}">{$allq[ix].question|escape|truncate:110:"":true}</option>
				{/section}
			</select>
  		</div>
    </div>
    <div class="form-group">
		<label class="col-sm-3 control-label"></label>
		<div class="col-sm-7">
      		<input type="submit" class="btn btn-default btn-sm" name="useq" value="{tr}Use{/tr}">
  		</div>
    </div>
</form>
{/if}
<br>

{* next big chunk *}
<br>
<h2>{tr}FAQ questions{/tr}</h2>
{if $channels or ($find ne '')}
	{include file='find.tpl'}
{/if}

{* Use css menus as fallback for item dropdown action menu if javascript is not being used *}
{if $prefs.javascript_enabled !== 'y'}
	{$js = 'n'}
	{$libeg = '<li>'}
	{$liend = '</li>'}
{else}
	{$js = 'y'}
	{$libeg = ''}
	{$liend = ''}
{/if}
<div class="{if $js === 'y'}table-responsive{/if}"> {* table-responsive class cuts off css drop-down menus *}
	<table class="table table-striped table-hover">
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
						{$libeg}<a href="tiki-faq_questions.php?faqId={$faqId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;questionId={$channels[user].questionId}">
							{icon name='edit' _menu_text='y' _menu_icon='y' alt="{tr}Edit{/tr}"}
						</a>{$liend}
						{$libeg}<a href="tiki-faq_questions.php?faqId={$faqId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].questionId}">
							{icon name='remove' _menu_text='y' _menu_icon='y' alt="{tr}Remove{/tr}"}
						</a>{$liend}
					{/strip}
				{/capture}
				{if $js === 'n'}<ul class="cssmenu_horiz"><li>{/if}
				<a
					class="tips"
					title="{tr}Actions{/tr}"
					href="#"
					{if $js === 'y'}{popup fullhtml="1" center=true text=$smarty.capture.faq_actions|escape:"javascript"|escape:"html"}{/if}
					style="padding:0; margin:0; border:0"
				>
					{icon name='wrench'}
				</a>
				{if $js === 'n'}
					<ul class="dropdown-menu" role="menu">{$smarty.capture.faq_actions}</ul></li></ul>
				{/if}
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
	<div class="{if $js === 'y'}table-responsive{/if}"> {* table-responsive class cuts off css drop-down menus *}
		<table class="table table-striped table-hover">
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
								{$libeg}<a href="tiki-faq_questions.php?faqId={$faqId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;approve_suggested={$suggested[ix].sfqId}">
									{icon name='ok' _menu_text='y' _menu_icon='y' alt="{tr}Approve{/tr}"}
								</a>{$liend}
								{$libeg}<a href="tiki-faq_questions.php?faqId={$faqId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove_suggested={$suggested[ix].sfqId}">
									{icon name='remove' _menu_text='y' _menu_icon='y' alt="{tr}Remove{/tr}"}
								</a>{$liend}
							{/strip}
						{/capture}
						{if $js === 'n'}<ul class="cssmenu_horiz"><li>{/if}
						<a
							class="tips"
							title="{tr}Actions{/tr}"
							href="#"
							{if $js === 'y'}{popup fullhtml="1" center=true text=$smarty.capture.faq2_actions|escape:"javascript"|escape:"html"}{/if}
							style="padding:0; margin:0; border:0"
						>
							{icon name='wrench'}
						</a>
						{if $js === 'n'}
							<ul class="dropdown-menu" role="menu">{$smarty.capture.faq2_actions}</ul></li></ul>
						{/if}
					</td>
				</tr>
			{/section}
		</table>
	</div>
{else}
	<h2>{tr}No suggested questions{/tr}</h2>
{/if}
