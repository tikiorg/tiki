{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/matrix/tiki-debug_console.tpl,v 1.1 2003-07-13 00:03:47 zaufi Exp $ *}

<div class="debugconsole" id="debugconsole" style="{$debugconsole_style}">

{* Command prompt form *}
<form method="post" action="{$console_father}">
<table border="0" width="100%">
  <tr><td colspan="3" align="right">
    <b>Tiki Debuger Console<b>
    <a class="separator" href="javascript:toggle('debugconsole');">
      <small>x</small>
    </a>
  </td></tr>
  <tr>
    <td class="formcolor"><small>Current URL:</small></td>
    <td class="formcolor">{$console_father}</td>
  </tr>
  <tr>
    <td class="formcolor">{tr}Command{/tr}:</td>
    <td class="formcolor"><input type="text" name="command" size=90 value='{$command|escape:"quotes"}'></td>
  </tr>
  <tr>
    <td class="formcolor"></td>
    <td class="formcolor">
      <input type="submit" name="exec" value="{tr}exec{/tr}"> &nbsp;&nbsp;&nbsp;&nbsp;
      <small>{tr}Type <code>help</code> to get list of available commands{/tr}</small>
    </td>
  </tr>
</table>
</form>


{* Display command results if we have smth to show... *}
{if $result_type ne NO_RESULT}

  <pre>&gt;&nbsp;{$command|escape:"html"}</pre>

  {if    $result_type == TEXT_RESULT }

    {* Show text in PRE section *}
    <pre>
{$command_result|escape:"html"|replace:"\n":"<br/>"}
    </pre>

  {elseif $result_type == HTML_RESULT }

    {* Type HTML as is *}
    {$command_result}

  {elseif $result_type == TPL_RESULT && strlen($result_tpl) > 0}

    {* Result have its own template *}
    {include file=$result_tpl}

  {/if}{* Check result type *}

{/if}{* We have smth to show as result *}


<br/>
</div><!-- debug console -->
