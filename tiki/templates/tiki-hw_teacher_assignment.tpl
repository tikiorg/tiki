{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-hw_teacher_assignment.tpl,v 1.3 2004-06-19 15:19:04 ohertel Exp $ *}
{* Copyright 2004 George G. Geller *}

<!-- templates/tiki-hw_teacher_assignment.tpl start -->

<div class="articletitle">
  <span class="titlea">{$title}</span><br />
  <span class="titleb">
    <span style="color: rgb(0, 0, 255);">
      {tr}Due Date{/tr}: {$dueDate|tiki_long_datetime}
    </span><br/ >
  </span>
</div>

<div class="articleheading">
  <table  cellpadding="0" cellspacing="0">
    <tr>
      <td  valign="top">
        <div class="articleheadingtext">{$parsed_heading}</div>
      </td>
    </tr>
  </table>
</div>

<div class="articletrailer">
  <table class="wikitopline">
    <tr>
      <td><a href="tiki-hw_teacher_assignments.php" class="trailer">{tr}Assignments List{/tr}</a></td>
      <td>
      {if $nGradingQueue eq 0}
        <td style="text-align:left;"><a class="trailer" href="tiki-hw_grading_queue.php?assignmentId={$assignmentId}">{tr}Grading Queue {/tr}({tr}empty{/tr})</a></td>
      {elseif $nGradingQueue eq 1}
        <td style="text-align:left;"><a class="trailer" href="tiki-hw_grading_queue.php?assignmentId={$assignmentId}">{tr}Grading Queue {/tr}(1 {tr}paper{/tr})</a></td>
      {else}
        <td style="text-align:left;"><a class="trailer" href="tiki-hw_grading_queue.php?assignmentId={$assignmentId}">{tr}Grading Queue {/tr}({$nGradingQueue} {tr}papers{/tr})</a></td>
      {/if}
      </td>
      <td style="text-align:right;">
        {* TODO IN VERSION2
          <a class="trailer" href="tiki-hw_teacher_assignment_print.php?assignmentId={$listpages[ix].articleId}"><img src='img/icons/ico_print.gif' border='0' alt='{tr}Print{/tr}' title='{tr}Print{/tr}' /></a>
        TODO IN VERSION 2 *}
      </td>
    </tr>
  </table>
</div>

<div class="articlebody">
  {$parsed_body}
</div>

<!-- templates/tiki-hw_teacher_assignment.tpl end -->
