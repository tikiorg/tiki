<h1>
  <a  href="tiki-hw_student_assignment.php?assignmentId={$assignmentId}" class="pagetitle">
    {$assignmentTitle}
  </a>
  {if $lock}
    <img src="img/icons/lock_topic.gif" alt="{tr}locked{/tr}" title="{tr}locked by{/tr} {$page_user}" />
  {/if}
</h1>

<table class="wikibar">
  <tr>
    <td>
      <small>{$assignmentHeading}</small>
    </td>
    <td style="text-align:right;">
      {* GGG We may want to adapt something like this eventually
        {if !$lock and $tiki_p_edit eq 'y' and $beingEdited ne 'y'}
          <a title="{tr}edit{/tr}" href="tiki-editpage.php?page={$page|escape:"url"}"><img border="0" src="img/icons/edit.gif" alt='{tr}edit{/tr}' /></a>
        {/if}
        {if $print_page ne 'y'}
          <a title="{tr}print{/tr}" href="tiki-print.php?page={$page|escape:"url"}"><img border="0" src="img/icons/ico_print.gif" alt='{tr}print{/tr}' /></a>
        {/if}
      GGG *}
    </td> 
  </tr>
  <tr>
    <td> 
      <span style="color: rgb(0, 0, 255);">
        {tr}Due Date:{/tr} {$dueDate|tiki_long_datetime}
      </span>
    </td> 
  </tr>
</table>

{if $beingEdited eq 'y'}
  {popup_init src="lib/overlib.js"}
  <span class="tabbut"><a style="background: #FFAAAA;" href="tiki-hw_editpage.php?id={$id|escape:"url"}" class="tablink" {popup text="$semUser" width="-1"}>{tr}edit{/tr}</a></span>
{else}
  <span class="tabbut"><a href="tiki-hw_editpage.php?id={$id|escape:"url"}" class="tablink">{tr}edit{/tr}</a></span>
{/if}
<span class="tabbut"><a href="tiki-hw_pagehistory.php?id={$id|escape:"url"}" class="tablink">{tr}history{/tr}</a></span>

<div class="wikitext">
  {$parsed}
</div>

<p class="editdate"> {tr}Comment{/tr}: {$comment} </p>

<p class="editdate"> {tr}last modification{/tr}: {$lastModif|tiki_long_datetime} {tr}by{/tr} {$lastUser}</p>

{if $nGradingQueue eq 0}
  {if $user eq $studentName}
    <input type="submit" class="wikiaction" name="submit for grading" value="{tr}submit for grading{/tr}" /> <a class="link" href="tiki-hw_page.php?submit={$id}"></a>  &nbsp 
  {/if}
  ({tr}This page is not in a grading queue.{/tr})
{else}
  (This page is number {$nGradingQueue} in the grading queue for this assignment.)
{/if}
