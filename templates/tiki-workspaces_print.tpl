{*
@author: Javier Reyes Gomez (jreyes@escire.com)
@date: 27/01/2006
@copyright (C) 2006 Javier Reyes Gomez (eScire.com)
@license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*}
{include file="header.tpl"}
<div class="structure_tree">
{include file="tiki-workspaces_structure_tree.tpl" subtree=$subtree}
</div>
<div class="printstructure">
{section name=ix loop=$subtree}
   {if $subtree[ix].pos eq '' || !$subtree[ix].last}
	  {include file="aulawiki-print_page.tpl" strupage=$subtree[ix]}
	{/if}
{/section}
</div>
{include file="footer.tpl"}
