{*
@author: Javier Reyes Gomez (jreyes@escire.com)
@date: 27/01/2006
@copyright (C) 2006 Javier Reyes Gomez (eScire.com)
@license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*}

<div class="wsbox box-{$module_name|escape}">
<div class="wsbox-title box-title-{$module_name|escape}">
{if $module_flip eq 'y'}
<table class="wsbox-title">
  <tr>
    <td ondblclick="javascript:icntoggle('mod-{$module_name|escape}','mo.png');">
{/if}
<span class="wsbox-titletext">{$module_title}</span>
{if $module_flip eq 'y'}
    </td>
    <td width="11">
      <a title="{tr}Hide module contents{/tr}" class="flipmodtitle" href="javascript:icntoggle('mod-{$module_name|escape}','mo.png');"><img name="mod-{$module_name|escape}icn" class="flipmodimage" src="img/icons/omo.png" border="0" alt="[{tr}hide{/tr}]" /></a>
</td>
</tr>
</table>
{/if}
</div><div id="mod-{$module_name|escape}" style="display: block" class="wsbox-data {$module_style_data}">
{$module_content}
{$module_error}
{if $module_flip eq 'y'}
{literal}
<script language="Javascript" type="text/javascript">
  setsectionstate('mod-{/literal}{$module_name|escape}{literal}','mo.png');
</script>
{/literal}
{/if}
</div></div>
