{* $Header: /cvsroot/tikiwiki/tiki/tests/appmenu/mod-application_menu2.tpl,v 1.1 2003-11-20 02:45:15 gmuslera Exp $ *}

<div class="box">
<div class="box-title">

{include file="modules/module-title.tpl" module_title="<a class=\"flip\" href=\"javascript:flip('mainmenu');\">{tr}Menu{/tr}</a>" module_name="application_menu2"}
</div>
{assign var="curmenu" value=""}
<div id="mainmenu" class="box-data">
{section name=mysec loop=$appmenu}
	{if ($appmenu[mysec].menu ne $curmenu)} 
		{if $curmenu ne ""}
			</div>
		{/if}
		{assign var="curmenu" value=$appmenu[mysec].menu}
		<div class="separator">
		{if $feature_menusfolderstyle eq 'y'}
			<a class="separator" href="javascript:icntoggle('{$curmenu}');">
			<img src="img/icons/fo.gif" style="border: 0" name="{$curmenu}icn" alt="{$curmenu}"/>&nbsp;
			</a>
		{else}
			<a class="separator" href="javascript:toggle('{$curmenu}');">[-]</a>
		{/if}
		<a href="{$appmenu[mysec].link}" class="separator">{$appmenu[mysec].text}</a>
		{if $feature_menusfolderstyle ne 'y'}
			<a class="separator" href="javascript:toggle('{$curmenu}');">[+]</a>
		{/if}
		<br />
		</div>
		<div id="{$curmenu}" style="display:none;">

	{else}
		&nbsp;<a href="{$appmenu[mysec].link}" class="linkmenu">{$appmenu[mysec].text}</a><br />
	{/if}

{/section}
</div>
</div>
{if $feature_menusfolderstyle eq 'y'}
<script type='text/javascript'>
	{assign var="curmenu" value=""}
	{section name=mysec loop=$appmenu}
		{if ($appmenu[mysec].menu ne $curmenu)}
			{assign var="curmenu" value=$appmenu[mysec].menu}
setfoldericonstate('{$curmenu}');
		{/if}
	{/section}

</script>

{/if} 


