{* tiki-hw_student_assignments.tpl *}
{* Adapted from tiki-view_articles.tpl *}
{* January 22, 2004 *}
{* George G. Geller *}

{section name=ix loop=$listpages}
 {if $listpages[ix].disp_article eq 'y'}
  <div class="article">
   <div class="articletitle">
    <span class="titlea">{$listpages[ix].title}
    </span><br />
    <span class="titleb">
      <span style="color: rgb(0, 0, 255);">
       {tr}Due Date:{/tr} {$listpages[ix].expireDate|tiki_short_datetime}
      </span>
    </span><br />
   </div>
   <div class="articleheading">
    <table  cellpadding="0" cellspacing="0">
     <tr>
      <div class="articleheadingtext">{$listpages[ix].parsed_heading}</div>
     </tr>
    </table>
   </div>
   <div class="articletrailer">
    <table class="wikitopline">
     <tr>
      <td>
       <a href="tiki-hw_student_assignment.php?assignmentId={$listpages[ix].articleId}" class="trailer">{tr}Details{/tr}</a>
      </td>
      <td>
       <a href="tiki-hw_page.php?assignmentId={$listpages[ix].articleId}" class="trailer">{tr}My Work{/tr}</a>
      </td>
      <td style="text-align:right;">
        {* TODO IN VERSION2
          <a class="trailer" href="tiki-hw_student_assignment_print.php?assignmentId={$listpages[ix].articleId}"><img src='img/icons/ico_print.gif' border='0' alt='{tr}Print{/tr}' title='{tr}Print{/tr}' /></a>
        TODO IN VERSION 2 *}
      </td>
     </tr>
    </table>
   </div>
  </div>
 {/if}
{/section}

