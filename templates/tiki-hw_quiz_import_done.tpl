{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-hw_quiz_import_done.tpl,v 1.1 2004-04-28 00:49:25 ggeller Exp $ *}
{* George G. Geller *}
{* All Rights Reserved. See copyright.txt for details and a complete list of authors. *}
{* Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details. *}

<!-- templates/tiki-quiz_import_done.tpl start -->


<br /> <br />
<form enctype="multipart/form-data" method="post" action="sort_roster.php">
  <table class="normal">
    <tr>
      <td class="formcolor" colspan=2>The imported question appears below.  The question is not stored in the present version of the application.  This is just a prototype for testing.</td>
    </tr>
    <tr>
      <td class="formcolor" colspan=2>Imported Question:</td>
    </tr>

    <tr>
      <td class="formcolor">
        <textarea class="wikiedit" name="input_data" rows="30" cols="80" id='subheading' wrap="virtual" >{$OKOK}</textarea>
      </td>
    </tr>
  </table>
</form>
<br />

<!-- templates/tiki-quiz_import_done.tpl end -->
