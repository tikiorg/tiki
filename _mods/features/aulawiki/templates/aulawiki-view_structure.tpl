<div class="structure_tree">
{include file="aulawiki-structure_tree.tpl" subtree=$subtree}
</div>
<div class="printstructure">
{section name=ix loop=$subtree}
   {if $subtree[ix].pos eq '' || !$subtree[ix].last}
	  {include file="aulawiki-print_page.tpl" strupage=$subtree[ix]}
	{/if}
{/section}
</div>

