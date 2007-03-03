{*
@author: Javier Reyes Gomez (jreyes@escire.com)
@date: 27/01/2006
@copyright (C) 2006 Javier Reyes Gomez (eScire.com)
@license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*}
<br clear=all style='page-break-before:always'/>
<div class="printpage">
<a name="{$strupage.pageName}"></a>
<div class="page_head">
<table width=100%>
<tr>
<td>
<h3>{$strupage.pos} {$strupage.info.description}</h3><br/>
<b>{$strupage.pageName}</b>&nbsp; <i>(V.{$strupage.info.version})</i>
</td>
<td width=250 align="rigth">
{include file="tiki-page_bar.tpl" page=$strupage.pageName editable=$strupage.editable tiki_p_edit=$strupage.edit}
</td>
</tr>
</table>
</div>
<BR/>
{$strupage.pdata}
<BR/>
{php}
global $smarty;
$strupage = $smarty->get_template_vars("strupage");
$pagename = $strupage["pageName"];
$smarty->assign("page",$pagename);
{/php}
{*
{include_php file="aulawiki-include_category.php"}
*}
</div>
