{if $prefs.feature_ajax eq 'y' && $prefs.feature_friends eq 'y' && !empty($user)}

{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}My friends{/tr}"}{/if}
{tikimodule title=$tpl_module_title name="instant_messaging" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}

  <div id="im"></div>

  {/tikimodule}


{* below is redundant in pages like browse freetags *}
<script src="lib/cpaint/cpaint2.inc.compressed.js" type="text/javascript"></script>
<script src="lib/cpaint/tiki-ajax.js" type="text/javascript"></script>

<script type="text/javascript" src="lib/chat/im_ui.js"></script>
<script type="text/javascript" src="lib/chat/im.js"></script>

{/if}
