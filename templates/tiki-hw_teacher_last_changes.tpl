{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-hw_teacher_last_changes.tpl,v 1.1 2004-03-12 20:58:25 ggeller Exp $ *}
{* Copyright (c) 2004 George G. Geller *}

<!-- tiki-hw_teacher_last_changes.tpl start -->

<h1>
  <a href="tiki-hw_teacher_last_changes.php?" class="pagetitle">{tr}Last Changes{/tr}</a>
</h1>
<br />
<div align="left">
  <table class="normal">
    <tr>
      <td class="heading" bgcolor="#bbbbbb"><a class="tableheading" href="tiki-hw_teacher_last_changes.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'lastModif_desc'}lastModif_asc{else}lastModif_desc{/if}">{tr}Last Modified{/tr}</a></td>
      <td class="heading" bgcolor="#bbbbbb"><a class="tableheading" href="tiki-hw_teacher_last_changes.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'Assignment_desc'}Assignment_asc{else}Assignment_desc{/if}">{tr}Assignment{/tr}</a></td>
      <td class="heading" bgcolor="#bbbbbb"><a class="tableheading" href="tiki-hw_teacher_last_changes.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'pageName_desc'}pageName_asc{else}pageName_desc{/if}">{tr}Due Date{/tr}</a></td>
      <td class="heading" bgcolor="#bbbbbb"><a class="tableheading" href="tiki-hw_teacher_last_changes.php?days={$days}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'user_desc'}user_asc{else}user_desc{/if}">{tr}User{/tr}</a></td>
      <td class="heading" bgcolor="#bbbbbb"><a class="tableheading" href="tiki-hw_teacher_last_changes.php?days={$days}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'comment_desc'}comment_asc{else}comment_desc{/if}">{tr}Comment{/tr}</a></td>
    </tr>
    {cycle values="odd,even" print=false}
    {section name=changes loop=$lastchanges}
      <tr>
        <td class="{cycle advance=false}">&nbsp;{$lastchanges[changes].lastModif|tiki_short_datetime}&nbsp;</td>
        <td class="{cycle advance=false}">&nbsp;{$lastchanges[changes].title}&nbsp;</td>
        <td class="{cycle advance=false}">&nbsp;{$lastchanges[changes].dueDate|tiki_short_datetime}&nbsp;</td>
        <td class="{cycle advance=false}">&nbsp;{$lastchanges[changes].user}&nbsp;</td>
        <td class="{cycle}">&nbsp;{$lastchanges[changes].comment}&nbsp;</td>
      </tr>
    {sectionelse}
      <tr>
        <td class="even" colspan="6">
          <b>{tr}No records found{/tr}</b>
        </td>
      </tr>
    {/section}
  </table>
  <br />
</div>
<div class="mini" align="center">
  {* WTF is this? {if $prev_offset >= 0}
    [<a class="prevnext" href="tiki-hw_student_last_changes.php?find={$find}&amp;days={$days}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
  {/if} *}
  {tr}Page{/tr}: {$actual_page}/{$cant_pages}
  {if $next_offset >= 0}
    &nbsp;[<a class="prevnext" href="tiki-lastchanges.php?find={$find}&amp;days={$days}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
  {/if}
  {if $direct_pagination eq 'y'}
    <br />
    {section loop=$cant_pages name=foo}
      {assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
      <a class="prevnext" href="tiki-lastchanges.php?find={$find}&amp;days={$days}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
      {$smarty.section.foo.index_next}</a>&nbsp;
    {/section}
  {/if}
</div>

<!-- tiki-hw_teacher_last_changes.tpl end -->
