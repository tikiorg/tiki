{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-hw_teacher_assignments.tpl,v 1.5 2004-03-19 18:09:59 ggeller Exp $ *}
{* George G. Geller *}

<!-- templates/tiki-hw_teacher_assignments.tpl start -->

{section name=ix loop=$listassignments}
  <div class="article">
    <div class="articletitle">
      <span class="titlea">{$listassignments[ix].title}</span><br />
      <span class="titleb">
        <span style="color: rgb(0, 0, 255);">
          {tr}Due Date:{/tr} {$listassignments[ix].dueDate|tiki_short_datetime}
        </span>
      </span><br />
    </div>
    <div class="articleheading">
      <table  cellpadding="0" cellspacing="0">
        <tr>
          <td valign="top">
            <div class="articleheadingtext">{$listassignments[ix].parsed_heading}</div>
          </td>
        </tr>
      </table>
    </div>
    <div class="articletrailer">
      <table class="wikitopline">
        <tr>
          <td>
            <a href="tiki-hw_teacher_assignment.php?assignmentId={$listassignments[ix].assignmentId}" class="trailer">{tr}Details{/tr}</a>
          </td>
          {if $listassignments[ix].iGradingQueue eq 0}
            <td style="text-align:center;"><a class="trailer" href="tiki-hw_grading_queue.php?assignmentId={$listassignments[ix].assignmentId}">{tr}Grading Queue {/tr}({tr}empty{/tr})</a></td>
          {elseif $listassignments[ix].iGradingQueue eq 1}
            <td style="text-align:center;"><a class="trailer" href="tiki-hw_grading_queue.php?assignmentId={$listassignments[ix].assignmentId}">{tr}Grading Queue {/tr}(1 {tr}paper{/tr})</a></td>
          {else}
            <td style="text-align:center;"><a class="trailer" href="tiki-hw_grading_queue.php?assignmentId={$listassignments[ix].assignmentId}">{tr}Grading Queue {/tr}({$listassignments[ix].iGradingQueue} {tr}papers{/tr})</a></td>
          {/if}
          <td style="text-align:right;">
            {if $tiki_p_hw_teacher eq 'y'}
              <a class="trailer" href="tiki-hw_teacher_assignment_edit.php?assignmentId={$listassignments[ix].assignmentId}"><img src='img/icons/edit.gif' border='0' alt='{tr}Edit{/tr}' title='{tr}Edit{/tr}' /></a>
              <a class="trailer" href="tiki-hw_teacher_assignments.php?remove={$listassignments[ix].assignmentId}"><img src='img/icons2/delete.gif' border='0' alt='{tr}Remove{/tr}' title='{tr}Remove{/tr}' /></a>
            {/if}
          </td>
        </tr>
      </table>
    </div>
  </div>
{/section}

<a class="linkbut" href="tiki-hw_teacher_assignment_edit.php">{tr}create assignment{/tr}</a>

<!-- templates/tiki-hw_teacher_assignments.tpl end -->
