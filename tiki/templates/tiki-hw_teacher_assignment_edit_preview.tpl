{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-hw_teacher_assignment_edit_preview.tpl,v 1.5 2004-03-11 17:12:32 ggeller Exp $ *}
{* George G. Geller *}

<!-- tiki-hw_teacher_assignment_edit_preview.tpl start -->

<h2>{tr}Preview{/tr}: {$page}</h2>
<div class="article">
  <div class="articletitle">
    <span class="titlea">{$assignment_data.parsed_title}</span><br/>
    <span style="color: rgb(0, 0, 255);">{tr}Due{/tr} {tr}on:{/tr} {$assignment_data.dueDate|tiki_short_datetime} </span>
  </div>
  <div class="articleheading">
    <table  cellpadding="0" cellspacing="0">
      <tr>
        <td  valign="top"></td>
        <td  valign="top">
          <span class="articleheading">{$assignment_data.parsed_heading}</span>
        </td>
      </tr>
    </table>
  </div>
  <div class="articlebody">{$assignment_data.parsed_body}</div>
</div>

<!-- tiki-hw_teacher_assignment_edit_preview.tpl end -->
