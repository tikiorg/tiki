{* based on /cvsroot/tikiwiki/tiki/templates/modules/mod-eph.tpl,v 1.5.10.3 2005/02/23 21:12:46 michael_davey *}

{tikimodule title="{tr}Ephemerides{/tr}</a>" name="eph" flip=$module_params.flip decorations=$module_params.decorations}
{if $modephdata}
  {if $modephdata.filesize}
    <div style="text-align:center" class="module"><img src="tiki-view_eph.php?ephId={$modephdata.ephId}" alt="{tr}image{/tr}" />
	</div>
  {/if}
  <div class="module">{$modephdata.textdata}</div>
{/if}
<a href="tiki-eph.php">{tr}More{/tr} . . .</a>
{/tikimodule}
