{* below is redundant in pages like browse freetags *}
<script src="lib/cpaint/cpaint2.inc.js" type="text/javascript"></script>

<script type="text/javascript" language="JavaScript" src="lib/chat/im.js"></script>

{if $feature_ajax eq 'y' && $feature_friends eq 'y'}

  {tikimodule title='My friends' name="instant_messaging" flip=$module_params.flip decorations=$module_params.decorations}

  {/tikimodule}
{/if}