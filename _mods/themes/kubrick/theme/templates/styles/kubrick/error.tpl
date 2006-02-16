{include file="header.tpl"}
{* Index we display a wiki page here *}
{if $feature_bidi eq 'y'}
<div dir="rtl">
{/if}
<div id="tiki-main"><table width="100%" border="0" cellpadding="0" cellspacing="0"><tr><td>
	{if $feature_top_bar eq 'y'}
	<div id="tiki-top">
		<div id="headerright"></div>
		<div id="headerleft"></div>
		<div id="headercenter">
			<div id="logo"></div>
			<div id="tiki-top_bar">{include file="tiki-top_bar.tpl"}</div>
			<div id="horiz_menu">{phplayers id=43 type=horiz}</div>
		</div>
	</div>
	{/if}</td></tr>
	<tr><td>
	<div id="tiki-mid">
	<table id="tiki-midtbl" border="0" cellpadding="0" cellspacing="0"><tr>
			{* flip stuff deleted *}
		<td id="centercolumn" valign="top">
      				<div id="tiki-center">
				      <br />
				        <div class="cbox">
				        <div class="cbox-title">
				        {$errortitle|default:"{tr}Error{/tr}"}
				        </div>
				        <div class="cbox-data">
				        <br />
				        {if ($errortype eq "404")}
				           {if $likepages}
				          {tr}Perhaps you were looking for:{/tr}
				          <ul>
				          {section name=back loop=$likepages}
				          <li><a  href="tiki-index.php?page={$likepages[back]|escape:"url"}" class="wiki">{$likepages[back]}</a></li>
				          {/section}
				          </ul>
				          {else}
				          {tr}There are no wiki pages similar to '{$page}'{/tr}
				          <br /><br />
				          {/if}
				          {include
				            file="tiki-searchindex.tpl"
				            searchNoResults="true"                 
				            searchStyle="menu"
				            searchOrientation="horiz"
				            words="$page"
				          }
				          <br />
				        {else}
				        {$msg}
				        <br /><br />
				        {/if}
				        {if $page and $create eq 'y' and ($tiki_p_admin eq 'y' or $tiki_p_admin_wiki eq 'y'  or $tiki_p_edit eq 'y')}<a href="tiki-editpage.php?page={$page}" class="linkmenu">{tr}Create this page{/tr}</a> {tr}(page will be orphaned){/tr}<br /><br />{/if}
				        <a href="javascript:history.back()" class="linkmenu">{tr}Go back{/tr}</a><br /><br />
				        <a href="{$tikiIndex}" class="linkmenu">{tr}Return to home page{/tr}</a>
				        </div>
				        </div>
      </div>
		</td>
		<td id="sidecolumn" valign="top">
      			{if $feature_right_column ne 'n'}
      			{section name=homeix loop=$right_modules}
      			{$right_modules[homeix].data}
      			{/section}
         		{* rightcol setfolderstate stuff deleted *}
      			{/if}
               		{if $feature_left_column ne 'n'}
             		{section name=homeix loop=$left_modules}
            		{$left_modules[homeix].data}
            		{/section}
              		{*  leftcol setfolderstate stuff deleted *}
         		{/if}
		</td>
		</tr>
		</table>
      	</div>
		{* end tiki-mid *}
		{if $feature_bot_bar eq 'y'}	
	<div id="tiki-bot">
		<div id="footer">
			<div id="footerright"></div>
			<div id="footerleft"></div>
			<div id="footermid">
				{include file="tiki-bot_bar.tpl"}</div>
		</div>
	</div>
		
		{/if}
	</td></tr></table>
</div>

{if $feature_bidi eq 'y'}
</div>
{/if}
{include file="footer.tpl"}
