{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-hw_grading_queue.tpl,v 1.1 2004-03-19 18:09:59 ggeller Exp $ *}
{* George G. Geller *}

<!-- templates/tiki-hw_grading_queue.tpl start -->

{section name=ix loop=$listPages}
  <div class="article">
    <div class="articletitle">
      <span class="titlea">Student: {$listPages[ix].studentName}</span><br />
      <span class="titleb">
        <span style="color: rgb(0, 0, 255);">
          {tr}Last Edit:{/tr} {$listPages[ix].lastModif|tiki_short_datetime} {tr}by{/tr}{$listPages[ix].user}
        </span>
      </span>
      <br />
    </div>
    <div class="articleheading">
      <table  cellpadding="0" cellspacing="0">
        <tr>
          <td valign="top">
            <div class="articleheadingtext">{$listPages[ix].data}</div>
          </td>
        </tr>
      </table>
    </div>
    <div class="articletrailer">
      <table class="wikitopline">
        <tr>
          <td style="text-align:center;"><a class="trailer" href="tiki-hw_editpage.php?id={$listPages[ix].id}">{tr}Grade Paper{/tr}</a></td>
        </tr>
      </table>
    </div>
  </div>
{/section}

<!-- templates/tiki-hw_grading_queue.tpl end -->
