{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-hw_student_assignments.tpl,v 1.3 2004-03-12 20:58:25 ggeller Exp $ *}
{* tiki-hw_student_assignments.tpl *}
{* March 11, 2004 *}
{* George G. Geller *}

<!-- tiki-hw_student_assignments.tpl start -->

{section name=ix loop=$listpages}
 <div class="article">
  <div class="articletitle">
   <span class="titlea">{$listpages[ix].title}
   </span><br />
   <span class="titleb">
    <span style="color: rgb(0, 0, 255);">
      {tr}Due Date:{/tr} {$listpages[ix].dueDate|tiki_short_datetime}
    </span>
   </span>
   <br />
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
      <a href="tiki-hw_student_assignment.php?assignmentId={$listpages[ix].assignmentId}" class="trailer">{tr}Details{/tr}</a>
     </td>
     <td>
      <a href="tiki-hw_page.php?assignmentId={$listpages[ix].assignmentId}" class="trailer">{tr}My Work{/tr}</a>
     </td>
     <td style="text-align:right;">
       {* TODO IN VERSION2
          <a class="trailer" href="tiki-hw_student_assignment_print.php?assignmentId={$listpages[ix].assignmentId}"><img src='img/icons/ico_print.gif' border='0' alt='{tr}Print{/tr}' title='{tr}Print{/tr}' /></a>
        TODO IN VERSION 2 *}
     </td>
    </tr>
   </table>
  </div>
 </div>
{/section}

<!-- tiki-hw_student_assignments.tpl end -->
