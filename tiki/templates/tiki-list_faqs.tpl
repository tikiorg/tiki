<a class="pagetitle" href="tiki-list_faqs.php">{tr}FAQs{/tr}</a><br/>
{if $tiki_p_admin_faqs eq 'y'}
<h2>{tr}Create/edit Faq{/tr}</h2>
<form action="tiki-list_faqs.php" method="post">
<input type="hidden" name="faqId" value="{$faqId}" />
<table class="normal">
<tr><td class="formcolor">{tr}Title{/tr}:</td><td class="formcolor"><input type="text" name="title" value="{$title}" /></td></tr>
<tr><td class="formcolor">{tr}Description{/tr}:</td><td class="formcolor"><textarea name="description" rows="4" cols="40">{$description}</textarea></td></tr>
{include file=categorize.tpl}
<tr><td class="formcolor">{tr}Users can suggest questions{/tr}:</td><td class="formcolor"><input type="checkbox" name="canSuggest" {if $canSuggest eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td  class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>
{/if}
<h2>{tr}Available FAQs{/tr}</h2>
<div  align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-list_faqs.php">
     <input type="text" name="find" value="{$find}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode}" />
   </form>
   </td>
</tr>
</table>
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-list_faqs.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'title_desc'}title_asc{else}title_desc{/if}">{tr}title{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-list_faqs.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'description_desc'}description_asc{else}description_desc{/if}">{tr}description{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-list_faqs.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}created{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-list_faqs.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'hits_desc'}hits_asc{else}hits_desc{/if}">{tr}visits{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-list_faqs.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'questions_desc'}questions_asc{else}questions_desc{/if}">{tr}questions{/tr}</a></td>
<td class="heading">{tr}suggested{/tr}</td>
{if $tiki_p_admin_faqs eq 'y'}
<td class="heading">{tr}action{/tr}</td>
{/if}
</tr>
{section name=user loop=$channels}
{if $smarty.section.user.index % 2}
<tr>
<td class="odd"><a class="tablename" href="tiki-view_faq.php?faqId={$channels[user].faqId}">{$channels[user].title}</a></td>
<td class="odd">{$channels[user].description}</td>
<td class="odd">{$channels[user].created|tiki_short_datetime}</td>
<td class="odd">{$channels[user].hits}</td>
<td class="odd">{$channels[user].questions}</td>
<td class="odd">{$channels[user].suggested}</td>
{if $tiki_p_admin_faqs eq 'y'}
<td class="odd">
   <a class="link" href="tiki-list_faqs.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].faqId}">{tr}remove{/tr}</a>
   <a class="link" href="tiki-list_faqs.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;faqId={$channels[user].faqId}">{tr}edit{/tr}</a>
   <a class="link" href="tiki-faq_questions.php?faqId={$channels[user].faqId}">{tr}questions{/tr}</a>
</td>
{/if}
</tr>
{else}
<tr>
<td class="even"><a class="tablename" href="tiki-view_faq.php?faqId={$channels[user].faqId}">{$channels[user].title}</a></td>
<td class="even">{$channels[user].description}</td>
<td class="even">{$channels[user].created|tiki_short_datetime}</td>
<td class="even">{$channels[user].hits}</td>
<td class="even">{$channels[user].questions}</td>
<td class="even">{$channels[user].suggested}</td>
{if $tiki_p_admin_faqs eq 'y'}
<td class="even">
   <a class="link" href="tiki-list_faqs.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].faqId}">{tr}remove{/tr}</a>
   <a class="link" href="tiki-list_faqs.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;faqId={$channels[user].faqId}">{tr}edit{/tr}</a>
   <a class="link" href="tiki-faq_questions.php?faqId={$channels[user].faqId}">{tr}questions{/tr}</a>
</td>
{/if}
</tr>
{/if}
{/section}
</table>
<br/>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-list_faqs.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-list_faqs.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br/>
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-list_faqs.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>

