
{include file="tiki-opnetheader.tpl"}
{* Index we display a wiki page here *}
{if $feature_bidi eq 'y'}
<table dir="rtl" ><tr><td>
{/if}

<div id="tiki-main">
  {if $feature_top_bar eq 'y'}
  <div id="tiki-top">
    {include file="tiki-top_bar.tpl"}
  </div>
  {/if}
  <div id="tiki-mid">
    <table border="0" cellpadding="0" cellspacing="0" id="tikimidtbl">
    <tr>
      {if $feature_left_column eq 'y'}
      <td id="leftcolumn">
      {section name=homeix loop=$left_modules}
      {$left_modules[homeix].data}
      {/section}
      </td>
      {/if}
      <td id="centercolumn"><div id="tiki-center">
      <br />
        <div class="cbox">
        <div class="cbox-title">
        {$boxtitle|default:"{tr}Opinion-Network{/tr}"}
        </div>
        <div class="cbox-data">
        <br />

</form>
<table class="normalnoborder" cellpadding="0" cellspacing="0">
	<tr><td>
		{*the setup part*}
		<div class="cbox">
			<div class="cbox-title">
				{tr}Here you can see the average of the results of the question-forms which were filled about you{/tr}
			</div>
			<div class="cbox-data">
				<table>
				<form method="get" action="tiki-opnet_result.php">
				<tr> <td class="form">
				Select the question-form:
				<select name="parentform">
				{section name=chooseformtype loop=$available_formtype}
				<option value="{$available_formtype[chooseformtype].id|escape}" {if $available_formtype[chooseformtype].id eq $parentform}selected="selected"{/if}>{$available_formtype[chooseformtype].name}</option>
				{/section}
				</select>
				<input type="submit" value="{tr}Apply{/tr}" name="youcango"/>
				</form>
				</td> </tr>
				</table>
				
			</div>
		</div>
		
		

	</td></tr>
	{* if the user sends the above form, we can display this form*}
	{if $youcango}
	<tr><td>
		<div class="cbox">
			<div class="cbox-title">
				{tr}The question form{/tr} {if $formname neq ""} - {$formname} {/if}
			</div>
			<div class="cbox-data">
			<div align="center">
				{$message}
				<form method="post" action="tiki-opnet.php" width=80%>
				<table>
					<tr>
						<td class="form"><input type="hidden" value="{$about_who}" name="about_who"/></td>
						<td class="form"><input type="hidden" value="{$whichform}" name="whichform"/></td>
					</tr>
				{section name=displayform loop=$question}
					<tr> 
					<td class="form">
						{$question[displayform].question}
					</td>
					<td class="form">
						{$question[displayform].slider}
					</td>
					</tr>
				{/section}
				</table>
				</form>
			</div>
			</div>
		</div>
	</td></tr>
	{/if}
</table>
{*and here it ends*}


	{if $page and !$nocreate and ($tiki_p_admin eq 'y' or  $tiki_p_admin_wiki eq 'y')}<b><a href="tiki-editpage.php?page={$page}" class="linkmenu">{tr}Click here to create it{/tr}!</a></b>{/if}
        <br /><br />
        <a href="javascript:history.back()" class="linkmenu">{tr}Go back{/tr}</a><br />
        <a href="{$tikiIndex}" class="linkmenu">{tr}Return to home page{/tr}</a>
        </div>
        </div>
      </div></td>
      {if $feature_right_column eq 'y'}
      <td id="rightcolumn">
      {section name=homeix loop=$right_modules}
      {$right_modules[homeix].data}
      {/section}
      </td>
      {/if}
    </tr>
    </table>
  </div>
  {if $feature_bot_bar eq 'y'}
  <div id="tiki-bot">
    {include file="tiki-bot_bar.tpl"}
  </div>
  {/if}
</div>
{if $feature_bidi eq 'y'}
</td></tr></table>
{/if}
{include file="footer.tpl"}
