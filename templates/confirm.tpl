{* $Id$ *}
<div class="cbox">
  {if !empty($confirmation_text)}<div class="cbox-title">{icon _id=information style="vertical-align:middle"} {$confirmation_text}</div>{/if}
  <br />
  <div class="cbox-data">
    <form action="{$confirmaction}" method="post">
      {if $ticket}<input value="{$ticket}" name="ticket" type="hidden" />{/if}
      {query _type='form_input'}
      <input type="submit" name="daconfirm" value="{tr}Click here to confirm your action{/tr}" />
      <span class="button2">
      {if $prefs.feature_ajax eq 'y' and isset($last_mid_template)}
        {self_link _class='linkbut' _template=$last_mid_template}{tr}Go back{/tr}{/self_link}
      {else}
        <a href="javascript:history.back()" class="linkbut">{tr}Go back{/tr}</a>
      {/if}
      </span>
      <span class="button2"><a href="{$prefs.tikiIndex}" class="linkbut">{tr}Return to home page{/tr}</a></span>
    </form>
  </div>
</div><br />
</div>
