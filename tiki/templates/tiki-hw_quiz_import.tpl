{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-hw_quiz_import.tpl,v 1.2 2004-04-28 16:43:41 ggeller Exp $ *}
{* tiki-hw_quiz_import.tpl *}

{* Copyright (c) 2004 George G. Geller *}
{* All Rights Reserved. See copyright.txt for details and a complete list of authors. *}
{* Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details. *}

<!-- templates/sort_roster.tpl start -->

{if $preview}
  {* <h2>{tr}Preview{/tr}: {$homeworkTitle}</h2>
  <div  class="wikitext">{$parsed}</div> *}

  Preview goes here!

{/if}


<br /> <br />
<form enctype="multipart/form-data" method="post" action="tiki-hw_quiz_import.php">
  <table class="normal">
    <tr>
      <td class="formcolor" colspan=2>Instructions:Tpye your multiple-choice question below. The question must be on the first line.  Start answer choices on subsequent lines.  Indicatate correct answers by starting them an "*".  White space before and after text is ignored. Then click on the "Import" button.
      </td>
    </tr>

    <tr>
      <td class="formcolor">
        {tr}Input{/tr}
      </td>
      <td class="formcolor">
        <textarea class="wikiedit" name="input_data" rows="30" cols="80" id='subheading' wrap="virtual" >  Replace this text with your quiz questions?
*the correct answer
an incorrect answer
another incorrect answer
  </textarea>
      </td>
    </tr>
  </table>
  <div align="center">
    <input type="submit" class="wikiaction" name="import" value="Import" />
    <input type="submit" class="wikiaction" name="preview" value="Preview" />
  </div>
</form>
<br />

<!-- templates/tiki-sort_roster.tpl end -->
