{title help="FAQs"}{tr}{$faq_info.title}{/tr}{/title}
<div class="description">{$faq_info.description|escape}</div>

<div class="navbar">
	{self_link print='y'}{icon _id='printer' align='right' hspace='1' alt="{tr}Print{/tr}"}{/self_link}
	{button href="tiki-list_faqs.php" _text="{tr}List FAQs{/tr}"}
	
	{if $tiki_p_admin_faqs eq 'y'}
		{button href="tiki-list_faqs.php?faqId=$faqId" _text="{tr}Edit this FAQ{/tr}"}
	{/if}
	{if $tiki_p_admin_faqs eq 'y'}
		{button href="tiki-faq_questions.php?faqId=$faqId" _text="{tr}New Question{/tr}"}
	{/if}
</div>

<h2>{tr}Questions{/tr}</h2>
{if !$channels}
	{tr}There are no questions in this FAQ.{/tr}
{else}
	<div class="faqlistquestions">
		<ol>
			{section name=ix loop=$channels}
				<li>
					<a class="link" href="#q{$channels[ix].questionId}">{$channels[ix].question|escape}</a>
				</li>
			{/section}
		</ol>
	</div>

	<h2>{tr}Answers{/tr}</h2>
	{section name=ix loop=$channels}
		<a name="q{$channels[ix].questionId}"></a>
		<div class="faqqa">
			<div class="faqquestion">
				{if $prefs.faq_prefix neq 'none'}
					<span class="faq_question_prefix">
						{if $prefs.faq_prefix eq 'QA'}
							{tr}Question:{/tr}
						{elseif $prefs.faq_prefix eq 'question_id'}
							{$smarty.section.ix.index_next}.&nbsp;
						{/if}
					</span>
				{/if}
				{$channels[ix].question|escape}
			</div>
			<div class="faqanswer">
				{if $prefs.faq_prefix eq 'QA'}
					<span class="faq_answer_prefix">{tr}Answer{/tr}&nbsp;</span>
				{/if}
				{$channels[ix].parsed}
			</div>
		</div>
	{/section}
{/if}

<div class="navbar">
	{if $faq_info.canSuggest eq 'y' and $tiki_p_suggest_faq eq 'y'}
		{button href="javascript:flip('faqsugg');" _flip_id="faqsugg" _text="{tr}Add Suggestion{/tr}"}
	{/if}

	{if $prefs.feature_faq_comments == 'y'
			&& (($tiki_p_read_comments == 'y'
			&& $comments_cant != 0)
		|| $tiki_p_post_comments == 'y'
		|| $tiki_p_edit_comments == 'y')}
		{include file='comments_button.tpl'}
	{/if}
</div>

{if $faq_info.canSuggest eq 'y' and $tiki_p_suggest_faq eq 'y'}
	<div class="faq_suggestions" id="faqsugg" style="display:{if !empty($error)}block{else}none{/if};">
		{if !empty($error)}
			<br />
			<div class="simplebox highlight">{icon _id=exclamation alt="{tr}Error{/tr}" style="vertical-align:middle"} {$error}</div>
		{/if}
		<br />
		<form action="tiki-view_faq.php" method="post">
			<input type="hidden" name="faqId" value="{$faqId|escape}" />
			<table class="formcolor">
				<tr>
					<td>{tr}Question:{/tr}</td>
					<td>
						<textarea rows="2" cols="80" name="suggested_question" style="width:95%;">{if $pendingquestion}{$pendingquestion}{/if}</textarea>
					</td>
				</tr>
				<tr>
					<td>
						{tr}Answer:{/tr}
					</td>
					<td>
						<textarea rows="2" cols="80" name="suggested_answer" style="width:95%;">{if $pendinganswer}{$pendinganswer}{/if}</textarea>
					</td>
				</tr>
				{if $prefs.feature_antibot eq 'y' && $user eq ''}
					{include file='antibot.tpl'}
				{/if}
				<tr>
					<td>&nbsp;</td>
					<td>
						<input type="submit" name="sugg" value="{tr}Add{/tr}" />
					</td>
				</tr>
			</table>
		</form>
		{if count($suggested) != 0}
			<br />
			<table class="normal">
				<tr>
					<th>{tr}Suggested questions{/tr}</th>
				</tr>
				{cycle values="odd,even" print=false}
				{section name=ix loop=$suggested}
					<tr class="{cycle}">
						<td class="text">{$suggested[ix].question}</td>
					</tr>
				{/section}
			</table>
		{/if}
	</div>
{/if}

{capture name='copyright_section'}
	{include file='show_copyright.tpl'}
{/capture}

{* When copyright section is not empty show it *}
{if $smarty.capture.copyright_section neq ''}
	<p class="editdate">
		{$smarty.capture.copyright_section}
	</p>
{/if}

{if $prefs.feature_faq_comments == 'y'
&& ($tiki_p_read_comments == 'y'
|| $tiki_p_post_comments == 'y'
|| $tiki_p_edit_comments == 'y')}
<div id="comment-container" data-target="tiki-ajax_services.php?controller=comment&amp;action=list&amp;type=faq&amp;objectId={$faqId|escape:'url'}"></div>
{jq}
	var id = '#comment-container';
	$(id).comment_load($(id).data('target'));
{/jq}
{/if}
