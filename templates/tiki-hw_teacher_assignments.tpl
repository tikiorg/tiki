{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-hw_teacher_assignments.tpl,v 1.1 2004-02-20 22:58:28 ggeller Exp $ *}
{* tiki-hw_teacher_assignments.tpl *}
{* Adapted from tiki-view_articles.tpl *}
{* January 21, 2004 *}
{* George G. Geller *}

<!-- templates/tiki-hw_teacher_assignments start -->

{section name=ix loop=$listpages}
  {if $listpages[ix].disp_article eq 'y'}
    <div class="article">
      <div class="articletitle">
        <span class="titlea">{$listpages[ix].title}</span><br />
        <span class="titleb">
          <span style="color: rgb(0, 0, 255);">
            {tr}Due Date:{/tr} {$listpages[ix].expireDate|tiki_short_datetime}
          </span>
        </span><br />
      </div>
      <div class="articleheading">
        <table  cellpadding="0" cellspacing="0">
          <tr>
            <td valign="top">
              <div class="articleheadingtext">{$listpages[ix].parsed_heading}</div>
            </td>
          </tr>
        </table>
      </div>
      <div class="articletrailer">
        <table class="wikitopline">
          <tr>
            {if $listpages[ix].size > 0 }
              <td>
                <a href="tiki-hw_teacher_assignment.php?assignmentId={$listpages[ix].articleId}" class="trailer">{tr}Details{/tr}</a>
              </td>
              <td style="text-align:center;">
                {tr}Grading Queue (Empty){/tr}
              </td>
            {/if}
            <td style="text-align:right;">
              {if $tiki_p_hw_teacher eq 'y'}
                <a class="trailer" href="tiki-hw_teacher_assignment_edit.php?assignmentId={$listpages[ix].articleId}"><img src='img/icons/edit.gif' border='0' alt='{tr}Edit{/tr}' title='{tr}Edit{/tr}' /></a>
              {/if}
              <a class="trailer" href="tiki-hw_teacher_assignment_print.php?assignmentId={$listpages[ix].articleId}"><img src='img/icons/ico_print.gif' border='0' alt='{tr}Print{/tr}' title='{tr}Print{/tr}' /></a>
              {if $tiki_p_hw_teacher eq 'y'}
                <a class="trailer" href="tiki-hw_teacher_assignments.php?remove={$listpages[ix].articleId}"><img src='img/icons2/delete.gif' border='0' alt='{tr}Remove{/tr}' title='{tr}Remove{/tr}' /></a>
              {/if}
            </td>
          </tr>
        </table>
      </div>
    </div>
  {/if}
{/section}

<!-- templates/tiki-hw_teacher_assignments end -->
