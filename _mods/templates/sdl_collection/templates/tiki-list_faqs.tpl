<a class="pagetitle" href="tiki-list_faqs.php" title="Frequentley Asked Questions">{tr}FAQs{/tr}</a>

{if $feature_help eq 'y'}
<!-- the help link info -->
<a href="http://tikiwiki.org/tiki-index.php?page=FAQ" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}FAQ{/tr}">
<img border='0' src='img/icons/help.gif' alt='help' /></a>{/if}

<!-- link to tpl -->

      {if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=templates/tiki-list_faqs.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}list faqs tpl{/tr}">
<img border='0' src='img/icons/info.gif' alt='edit tpl' /></a>{/if}

<!-- begin -->

<!-- beginning of next bit -->

<br /><br />
{if $tiki_p_admin_faqs eq 'y'}
{if $faqId > 0}
<h2>{tr}Edit This FAQ:{/tr} {$title}</h2>
<a href="tiki-list_faqs.php" class="linkbut">{tr}Create New FAQ{/tr}</a>
{else}
<h2>{tr}Create New FAQ:{/tr}</h2>
{/if}
<form action="tiki-list_faqs.php" method="post">
<input type="hidden" name="faqId" value="{$faqId|escape}" />
<table class="normal">
<tr><td class="formcolor">{tr}Title{/tr}:</td><td class="formcolor"><input type="text" name="title" value="{$title|escape}" /></td></tr>
<tr><td class="formcolor">{tr}Description{/tr}:</td><td class="formcolor"><textarea name="description" rows="4" cols="40">{$description|escape}</textarea></td></tr>
{include file=categorize.tpl}
<tr><td class="formcolor">{tr}Users can suggest questions{/tr}:</td><td class="formcolor"><input type="checkbox" name="canSuggest" {if $canSuggest eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td  class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>
{/if}
<h2>{tr}Available FAQs{/tr}</h2>
<div  align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Search: {/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-list_faqs.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}Go{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-list_faqs.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'title_desc'}title_asc{else}title_desc{/if}">{tr}Title{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-list_faqs.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'description_desc'}description_asc{else}description_desc{/if}">{tr}Description{/tr}</a></td>
<!--<td class="heading"><a class="tableheading" href="tiki-list_faqs.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}created{/tr}</a></td>-->
<td style="text-align:right;" class="heading"><a class="tableheading" href="tiki-list_faqs.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'hits_desc'}hits_asc{else}hits_desc{/if}">{tr}Visits{/tr}</a></td>
<td style="text-align:right;" class="heading"><a class="tableheading" href="tiki-list_faqs.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'questions_desc'}questions_asc{else}questions_desc{/if}">{tr}Questions{/tr}</a></td>
{if $tiki_p_admin_faqs eq 'y'}
<td class="heading">{tr}Action{/tr}</td>
{/if}
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$channels}
<tr>
<td class="{cycle advance=false}"><a class="tablename" href="tiki-view_faq.php?faqId={$channels[user].faqId}">{$channels[user].title}</a></td>
<td class="{cycle advance=false}">{$channels[user].description}</td>
<!--<td class="{cycle advance=false}">{$channels[user].created|tiki_short_datetime}</td>-->
<td style="text-align:right;" class="{cycle advance=false}">{$channels[user].hits}</td>
<td style="text-align:right;"  class="{cycle advance=false}">{$channels[user].questions} ({$channels[user].suggested})</td>
{if $tiki_p_admin_faqs eq 'y'}
<td  class="{cycle}">
   <a class="link" href="tiki-list_faqs.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;faqId={$channels[user].faqId}"><img src='img/icons/edit.gif' alt='{tr}edit{/tr}' title='{tr}edit{/tr}' border='0' /></a>
   <a class="link" href="tiki-faq_questions.php?faqId={$channels[user].faqId}"><img src='img/icons/question.gif' alt='{tr}questions{/tr}' title='{tr}questions{/tr}' border='0' /></a>
   <a class="link" href="tiki-list_faqs.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].faqId}" onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this question area?{/tr}')"><img src='img/icons2/delete.gif' alt='{tr}remove{/tr}' title='{tr}remove{/tr}' border='0' /></a>
</td>
{/if}
</tr>
{/section}
</table>
<br />
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-list_faqs.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-list_faqs.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-list_faqs.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>