{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-shoutbox.tpl,v 1.32.2.1 2008-02-01 18:43:19 sylvieg Exp $ *}

{if $prefs.feature_shoutbox eq 'y' and $tiki_p_view_shoutbox eq 'y'}
{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}Shoutbox{/tr}"}{/if}
{tikimodule title=$tpl_module_title name="shoutbox" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
    {if $tiki_p_post_shoutbox eq 'y'}
      {js_maxlength textarea=shout_msg maxlength=255}
      <form action="{$shout_ownurl}" method="post" onsubmit="return verifyForm(this);">
	  {if $shout_error}<div class="highlight">{$shout_error}</div>{/if}
      <div align="center">
        <textarea rows="3" cols="16" class="tshoutbox" name="shout_msg">{$shout_msg|escape:'htmlall'}</textarea>
		{if $prefs.feature_antibot eq 'y' && $user eq ''}
			<table>{include file="antibot.tpl"}</table>
		{/if}
	<input type="submit" name="shout_send" value="{tr}Send{/tr}" />
      </div><br />
      </form>
    {/if}

    {section loop=$shout_msgs name=ix}
      <div class="shoutboxmodmsg">

        {* TODO: IMHO Using 'modifier' is not best solution here
         *       so I forced to hack its result to inject 'title' attribute!
         *}

        {assign var=userlink value=$shout_msgs[ix].user|userlink:"linkmodule"}

        {capture name=date}{strip} {* Print date *}
          {$shout_msgs[ix].timestamp|tiki_short_time}, {$shout_msgs[ix].timestamp|tiki_short_date}
        {/strip}{/capture}

	{* Show user message in style according to 'tooltip' module parameter *}
	{assign var=cdate value=$smarty.capture.date}
	{if $tooltip == 1}
          <b>{strip}{$userlink|replace:" class=":" title='$cdate' class="}{/strip}</b>:
        {else}
          <b>{strip}{$userlink}{/strip}</b>, {$cdate}:
        {/if}
        {$shout_msgs[ix].message}
        {if $tiki_p_admin_shoutbox eq 'y' || $user == $shout_msgs[ix].user }
          [<a href="{$shout_ownurl}shout_remove={$shout_msgs[ix].msgId}" class="linkmodule">x</a>|<a href="tiki-shoutbox.php?msgId={$shout_msgs[ix].msgId}" class="linkmodule">e</a>]
        {/if}
      </div>
    {/section}
      <div style="text-align: center">
        <a href="tiki-shoutbox.php" class="linkmodule">{tr}Read More{/tr}&hellip;</a>
      </div>
{/tikimodule}
{/if}
