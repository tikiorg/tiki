<h1><a class="pagetitle" href="tiki-faq_questions.php?faqId={$faqId}">{tr}Admin FAQ{/tr}: {$faq_info.title}</a></h1>
<a href="tiki-list_faqs.php" class="linkbut">{tr}List FAQs{/tr}</a>
<a href="tiki-view_faq.php?faqId={$faqId}" class="linkbut">{tr}View FAQ{/tr}</a>
<a href="tiki-list_faqs.php?faqId={$faqId}" class="linkbut">{tr}Edit this FAQ{/tr}</a>
<a class="linkbut" href="tiki-faq_questions.php?faqId={$faqId}">{tr}New Question{/tr}</a><br />
<br />
<h2>{if $questionId}{tr}Edit FAQ question{/tr}{else}{tr}Add FAQ question{/tr}{/if}</h2>
<form action="tiki-faq_questions.php" method="post" id="editpageform">
<input type="hidden" name="questionId" value="{$questionId|escape}" />
<input type="hidden" name="faqId" value="{$faqId|escape}" />

{* begin table *}
<table class="normal">
  <tr class="formcolor">
    <td class="formcolor">{tr}Question{/tr}:</td>
    <td class="formcolor" >
      <textarea type="text" rows="2" cols="80" name="question">{$question|escape}</textarea>
    </td>
  </tr>

  <tr class="formcolor">
    <td class="formcolor">{tr}Answer{/tr}:
      <br /> 
      {include file="textareasize.tpl" area_name='faqans' formId='editpageform'}
      {if $prefs.quicktags_over_textarea neq 'y'}
        <br />
        {include file=tiki-edit_help_tool.tpl area_name="faqans"}
      {/if}
    </td>
    <td class="formcolor" >
      {if $prefs.quicktags_over_textarea eq 'y'}
        {include file=tiki-edit_help_tool.tpl area_name="faqans"}
      {/if}
      <textarea id='faqans' type="text" rows="8" cols="80" name="answer">{$answer|escape}</textarea>
    </td>
  </tr>

  <tr class="formcolor">
    <td  class="formcolor">&nbsp;</td>
    <td class="formcolor" >
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
<table class="normal">
<tr>
<td class="formcolor">{tr}Filter{/tr}</td>
<td class="formcolor">
<input type="text" name="filter" value="{$filter|escape}" />
<input type="submit" name="filteruseq" value="{tr}Filter{/tr}" />
</td>
</tr>
<tr>
<td class="formcolor">{tr}Question{/tr}:</td>
<td class="formcolor" >
<select name="usequestionId">
{section name=ix loop=$allq}
{* Ok, here's where you change the truncation field for this field *}
<option value="{$allq[ix].questionId|escape|truncate:20:"":true}">{$allq[ix].question|escape|truncate:110:"":true}</option>
{/section}
</select>
</td>
</tr>
<tr>
<td class="formcolor">&nbsp;</td>
<td class="formcolor">
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
<div align="center">
{if $channels or ($find ne '')}
  {include file='find.tpl' _sort_mode='y'}
{/if}

<table class="normal">
<tr>
<td class="heading">
<a class="tableheading" href="tiki-faq_questions.php?faqId={$faqId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'questionId_desc'}questionId_asc{else}questionId_desc{/if}">{tr}ID{/tr}</a>
</td>
<td class="heading"><a class="tableheading" href="tiki-faq_questions.php?faqId={$faqId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'question_desc'}question_asc{else}question_desc{/if}">{tr}Question{/tr}</a>
</td>
<td class="heading">{tr}Action{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$channels}
<tr>
<td class="{cycle advance=false}">{$channels[user].questionId}</td>
<td class="{cycle advance=false}">{$channels[user].question}</td>
<td class="{cycle}" width="80px">
   <a class="link" href="tiki-faq_questions.php?faqId={$faqId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;questionId={$channels[user].questionId}">{icon _id='page_edit'}</a>
   <a class="link" href="tiki-faq_questions.php?faqId={$faqId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].questionId}">{icon _id='cross' alt='{tr}Remove{/tr}'}</a>
</td>
</tr>
{sectionelse}
<tr><td class="odd" colspan="3">{tr}No records{/tr}</td></tr>
{/section}
</table>

<br />
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-faq_questions.php?find={$find}&amp;faqId={$faqId}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}Prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-faq_questions.php?find={$find}&amp;faqId={$faqId}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}Next{/tr}</a>]
{/if}
{if $prefs.direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
<a class="prevnext" href="tiki-faq_questions.php?find={$find}&amp;faqId={$faqId}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>
{if count($suggested) > 0}

{* this is the next section *}

<h2>{tr}Suggested questions{/tr}</h2>
<table class="normal">
<tr>
  <th class="heading">{tr}Question{/tr}</th>
  <th class="heading">{tr}Answer{/tr}</th>
  <th class="heading">{tr}Action{/tr}</th>
</tr>
{cycle values="odd,even" print=false}
{section name=ix loop=$suggested}
<tr>
  <td class="{cycle advance=false}">{$suggested[ix].question} </td>
  <td class="{cycle advance=false}">{$suggested[ix].answer}</td>
  <td class="{cycle}" width='80px'>
  <a class="link" href="tiki-faq_questions.php?faqId={$faqId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;approve_suggested={$suggested[ix].sfqId}" alt="{tr}Approve{/tr}">{icon _id=accept alt="{tr}Approve{/tr}"}</a>
  <a class="link" href="tiki-faq_questions.php?faqId={$faqId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove_suggested={$suggested[ix].sfqId}">{icon _id=cross alt="{tr}Remove{/tr}"}</a> 
  </td>
</tr>
{/section}
</table>
{else}
<h2>{tr}No suggested questions{/tr}</h2>
{/if}

