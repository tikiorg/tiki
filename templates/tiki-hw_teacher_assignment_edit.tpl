{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-hw_teacher_assignment_edit.tpl,v 1.6 2004-03-11 17:12:32 ggeller Exp $ *}
{* tiki-hw_teacher_assignment_edit.tpl *}
{* George G. Geller *}

<!-- templates/tiki-hw_teacher_assignment_edit.tpl start -->

{if $preview}
  {include file="tiki-hw_teacher_assignment_edit_preview.tpl"}
{/if}

<a class="pagetitle" href="tiki-hw_teacher_assignment_edit.php">
  {tr}Edit Assignment{/tr}: {$assignment_data.title}
</a>

{if $feature_help eq 'y'}
  <a href="http://tikiwiki.org/tiki-index.php?page=HWAssignmentEdit" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}Edit Assignment{/tr}">
    <img border='0' src='img/icons/help.gif' alt='{tr}help{/tr}' />
  </a>
{/if}

<br /> <br />
<a class="linkbut" href="tiki-hw_teacher_assignments.php">{tr}list assignments{/tr}</a>
<br /> <br />
<form enctype="multipart/form-data" method="post" action="tiki-hw_teacher_assignment_edit.php" id='editassignmentform'>
  <input type="hidden" name="assignmentId" value="{$assignment_data.assignmentId|escape}" />
  <table class="normal">

    <tr>
      <td class="formcolor">{tr}Due Date{/tr}</td>
      <td class="formcolor">
        {html_select_date time=$assignment_data.dueDate start_year="+0" end_year="+1"} {tr}at{/tr}
        <span dir="ltr">
          {html_select_time time=$assignment_data.dueDate display_seconds=false}
          &nbsp;
          {$siteTimeZone}
        </span>
      </td>
    </tr>

    <tr>
      <td class="formcolor">
        {tr}Title{/tr}
      </td>
      <td class="formcolor">
        <input type="text" name="title" value="{$assignment_data.title|escape}" size="80" />
      </td>
    </tr>

    <tr>
      <td class="formcolor">
        {tr}Summary{/tr}
      </td>
      <td class="formcolor">
        <textarea class="wikiedit" name="heading" rows="5" cols="80" id='subheading' wrap="virtual" >{$assignment_data.heading|escape}</textarea>
      </td>
    </tr>

    <tr>
      <td class="formcolor">
        {tr}Details{/tr}
        <br />
        <br />
        {include file="textareasize.tpl" area_name='body' formId='editpageform'}
        <br />
        {include file=tiki-edit_help_tool.tpl area_name="body"}
      </td>
      <td class="formcolor">
        <textarea class="wikiedit" id="body" name="body" rows="{$rows}" cols="{$cols}" wrap="virtual">{$assignment_data.body|escape}</textarea>
        <input type="hidden" name="rows" value="{$rows}"/>
        <input type="hidden" name="cols" value="{$cols}"/>
      </td>
    </tr>
  </table>
  <div align="center">
    <input type="submit" class="wikiaction" name="preview" value="{tr}preview{/tr}" />
    <input type="submit" class="wikiaction" name="save" value="{tr}save{/tr}" />
  </div>
</form>
<br />

<!-- templates/tiki-hw_teacher_assignment_edit.tpl end -->
