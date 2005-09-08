{* $Header: /cvsroot/tikiwiki/_mods/modules/plazes/mod-plazes.tpl,v 1.1 2005-09-08 03:04:51 damosoft Exp $ *}
{tikimodule title="{tr}www.plazes.com{/tr}" name="plazes" flip=$module_params.flip decorations=$module_params.decorations}
<div align="center">
{if $key eq ""}
Error: No valid key was passed through module parameters
{else}
<script type="text/javascript"><!--
	plazeskey = {$key};
	plazesmap = {$map};
	plazeswidth = 175;
	plazesheight = 184;
//--></script>
<script type="text/javascript" 
src="http://beta.plazes.com/plugin/plazesplugin_2.js">
</script>
{/if}
</div>
{/tikimodule}