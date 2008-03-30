{* $Id$ *}

{if $prefs.feature_shoutbox eq 'y' and $tiki_p_view_shoutbox eq 'y'}
	{tikimodule title="{tr}Shoutbox{/tr}" name="shoutbox" flip=$module_params.flip decorations=$module_params.decorations}
		{if $tiki_p_post_shoutbox eq 'y'}
			{js_maxlength textarea=shout_msg maxlength=255}
			<form action="{$shout_ownurl}" method="post" onsubmit="return verifyForm(this);">
				<div style="text-align: center">
					<textarea rows="5" cols="16" class="tshoutbox" name="shout_msg"></textarea>
					<button type="submit" name="shout_send">{tr}Send{/tr}</button>
				</div>
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
				<strong>{strip}{$userlink|replace:" class=":" title='$cdate' class="}{/strip}</strong>:
			{else}
				<strong>{strip}{$userlink}{/strip}</strong>, {$cdate}:
			{/if}
			{$shout_msgs[ix].message}
			{if $tiki_p_admin_shoutbox eq 'y'}
				[<a href="{$shout_ownurl}shout_remove={$shout_msgs[ix].msgId}" class="linkmodule">x</a>|<a href="tiki-shoutbox.php?msgId={$shout_msgs[ix].msgId}" class="linkmodule">e</a>]
			{/if}
			</div>
		{/section}
		<div style="text-align: center">
			<a href="tiki-shoutbox.php" class="linkmodule">{tr}Read More{/tr}&hellip;</a>
		</div>
	{/tikimodule}
{/if}
