{if $feature_ajax eq 'y' && $feature_friends eq 'y' && !empty($user)}

  {tikimodule title='My friends' name="instant_messaging" flip=$module_params.flip decorations=$module_params.decorations}

  <div id="im"></div>

  {/tikimodule}


{* below is redundant in pages like browse freetags *}
<script src="lib/cpaint/cpaint2.inc.compressed.js" type="text/javascript"></script>
<script src="lib/cpaint/tiki-ajax.js" type="text/javascript"></script>

<script type="text/javascript" language="JavaScript" src="lib/chat/im_ui.js"></script>
<script type="text/javascript" language="JavaScript" src="lib/chat/im.js"></script>

{/if}