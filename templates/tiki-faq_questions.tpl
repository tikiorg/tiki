{title}{tr}Admin FAQ:{/tr} {$faq_info.title|escape}{/title}

<div class="navbar">
	{button href="tiki-list_faqs.php" _text="{tr}List FAQs{/tr}"}
	{button href="tiki-view_faq.php?faqId=$faqId" _text="{tr}View FAQ{/tr}"}
	{button href="tiki-list_faqs.php?faqId=$faqId" _text="{tr}Edit this FAQ{/tr}"}
	{button href="tiki-faq_questions.php?faqId=$faqId" _text="{tr}New Question{/tr}"}
</div>

<h2>{if $questionId}{tr}Edit FAQ question{/tr}{else}{tr}Add FAQ question{/tr}{/if}</h2>

<form action="tiki-faq_questions.php" method="post" id="editpageform">
<input type="hidden" name="questionId" value="{$questionId|escape}" />
<input type="hidden" name="faqId" value="{$faqId|escape}" />

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
      <input type="submit" name="save" value="{tr}Save{/tr}" />
      {* set your changes and save 'em *}
    </td>
  </tr>
</table>
{* end table *}

</form>
{* This is the area for choosing questions from the db... it really should support choosing options from the answers, but only show if there are existing questions *}
{if $allq}
<br /><h2> {tr}Use a question from another FAQ{/tr}</h2>
<form action="tiki-faq_questions.php" method="post">
<input type="hidden" name="questionId" value="{$questionId|escape}" />
<input type="hidden" name="faqId" value="{$faqId|escape}" />
<table class="formcolor">
<tr>
<td>{tr}Filter{/tr}</td>
<td>
<input type="text" name="filter" value="{$filter|escape}" />
<input type="submit" name="filteruseq" value="{tr}Filter{/tr}" />
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
<input type="submit" name="useq" value="{tr}Use{/tr}" />
</td>
</tr>
</table>
</form>
{/if}
<br />

{* next big chunk *}
<br />
<h2>{tr}FAQ questions{/tr}</h2>
{if $channels or ($find ne '')}
  {include file='find.tpl'}
{/if}

<table class="normal">
<tr>
<th>
<a href="tiki-faq_questions.php?faqId={$faqId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'questionId_desc'}questionId_asc{else}questionId_desc{/if}">{tr}ID{/tr}</a>
</th>
<th><a href="tiki-faq_questions.php?faqId={$faqId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'question_desc'}question_asc{else}question_desc{/if}">{tr}Question{/tr}</a>
</th>
<th>{tr}Action{/tr}</th>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$channels}
<tr class="{cycle}">
<td>{$channels[user].questionId}</td>
<td>{$channels[user].question|escape}</td>
<td width="80px">
   <a class="link" href="tiki-faq_questions.php?faqId={$faqId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;questionId={$channels[user].questionId}">{icon _id='page_edit'}</a>
   <a class="link" href="tiki-faq_questions.php?faqId={$faqId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].questionId}">{icon _id='cross' alt="{tr}Remove{/tr}"}</a>
</td>
</tr>
{sectionelse}
	{norecords _colspan="3}
{/section}
</table>

{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}

{if count($suggested) > 0}

<h2>{tr}Suggested questions{/tr}</h2>
<table class="normal">
<tr>
  <th>{tr}Question{/tr}</th>
  <th>{tr}Answer{/tr}</th>
  <th>{tr}Action{/tr}</th>
</tr>
{cycle values="odd,even" print=false}
{section name=ix loop=$suggested}
<tr class="{cycle}">
  <td>{$suggested[ix].question|escape} </td>
  <td>{$suggested[ix].answer|escape}</td>
  <td width='80px'>
  <a class="link" href="tiki-faq_questions.php?faqId={$faqId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;approve_suggested={$suggested[ix].sfqId}" alt="{tr}Approve{/tr}">{icon _id=accept alt="{tr}Approve{/tr}"}</a>
  <a class="link" href="tiki-faq_questions.php?faqId={$faqId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove_suggested={$suggested[ix].sfqId}">{icon _id=cross alt="{tr}Remove{/tr}"}</a> 
  </td>
</tr>
{/section}
</table>
{else}
<h2>{tr}No suggested questions{/tr}</h2>
{/if}
