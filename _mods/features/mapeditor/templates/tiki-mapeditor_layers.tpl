<h1><a class="pagetitle" href="tiki-mapeditor_layers.php">{tr}Layers{/tr}</a>
  
      {if $feature_help eq 'y'}
<a href="{$helpurl}Layers" target="tikihelp" class="tikihelp" title="{tr}admin Layers{/tr}">
<img src="img/icons/help.gif" border="0" height="16" width="16" alt='{tr}help{/tr}' /></a>{/if}

      {if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-mapeditor_layers.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}admin Layers tpl{/tr}">
<img src="img/icons/info.gif" border="0" height="16" width="16" alt='{tr}edit tpl{/tr}' /></a>{/if}</h1>

<a class="linkbut" href="tiki-mapeditor_edit_layer.php?mapId={$mapId}">{tr}Create layer{/tr}</a>
<a class="linkbut" href="tiki-mapeditor_edit_layergroup.php?mapId={$mapId}">{tr}Create layerGroup{/tr}</a>
<a class="linkbut" href="tiki-mapeditor_maps.php">{tr}See Maps{/tr}</a>

<br /><br />
<div class="tree">{$tree}</div>
