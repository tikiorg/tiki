
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
        {$boxtitle|default:"{tr}Opinion-Network admin interface (under development){/tr}"}
        </div>
        <div class="cbox-data">
        <br />
	
{*here comes the real thing :)*}
<table class="normalnoborder" cellpadding="0" cellspacing="0">
	<tr><td>
		<div class="cbox">
			<div class="cbox-title">
				{tr}Create Question Forms{/tr}
			</div>
			<div class="cbox-data">
				<div align="left"> <h3> {tr}Create a new type of question form{/tr} </h3> <br/> </div>
				<table>
				
				<tr> <td class="form">
				<form method="post" action="tiki-opnet_admin.php">
				{tr}Name of the new form: {/tr}<input type="text" name="addformtype"/>
				<input type="submit" value="{tr}Add form{/tr}"/>
				</form>
				</td> </tr>
				
				</table>
				<br/>
				<div align="left"> <h3> {tr}Add a new question to a question form{/tr} </h3> <br/> </div>
				<table>
				
				<tr> <td class="form">
				<form method="post" action="tiki-opnet_admin.php">
				
				{tr}Select the question form to add your question to:{/tr}
				<select name="parentform">
				{section name=chooseformtype loop=$available_formtype}
				<option value="{$available_formtype[chooseformtype].id|escape}" {if $available_formtype[chooseformtype].id eq $parentform}selected="selected"{/if}>{$available_formtype[chooseformtype].name}</option>
				{/section}
				</select>
				<br/>
				{tr}Enter the question: {/tr}<input type="text" name="question_str"/>
				<input type="submit" value="{tr}Add question{/tr}"/>
				</form>
				</td> </tr>
				
				</table>
				
				
			</div>
		</div>
		
		<div class="cbox">
			<div class="cbox-title">
				{tr}Delete Question Forms{/tr}
			</div>
			<div class="cbox-data">
				<div align="left"> <h3> {tr}Delete a question form with all of it's questions{/tr} </h3> <br/> </div>
				<table>
				
				<tr> <td class="form">
				<form method="post" action="tiki-opnet_admin.php">
				
				{tr}Select the question form to delete:{/tr}
				<select name="parentform">
				{section name=chooseformtype loop=$available_formtype}
				<option value="{$available_formtype[chooseformtype].id|escape}">{$available_formtype[chooseformtype].name}</option>
				{/section}
				</select>
				<br/>
				<input type="submit" value="{tr}Delete form{/tr}" name="formdelete"/>
				</form>
				</td> </tr>
				
				</table>
				
				
			</div>
		</div>
	</td></tr>
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