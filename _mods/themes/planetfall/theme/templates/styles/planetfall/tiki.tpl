{include file="header.tpl"}
{if $feature_bidi eq 'y'}
<div dir="rtl">
{/if}
<table width="77%" border="0" cellspacing="0" cellpadding="0" align="center"><tr><td>
<div id="tiki-main">
         	{if $feature_top_bar eq 'y'}
          	<div id="tiki-top">
	  	<table border="0" cellspacing="0" cellpadding="0" width="100%">
	    		<tr>
	     		<td height="11"id="top_bar_td" width="100%"></td></tr><tr>
	     		<td>
	       			<table cellspacing="0" cellpadding="0" width="100%"><tr>
	  				<td id="planetfall_header" width="100%">
	  					<div id="planetfall_header_module">{include file="modules/mod-search_box.tpl"}</div>
	  					<div id="planetfall_logo">&nbsp;</div></td></tr>
	        		</table>
	        	</td>
	        	</tr>
	    		 <tr>
	     		<td>
	        		<table width="100%" cellspacing="0" cellpadding="0" height="26" background="/styles/planetfall/silver_bg.jpg"><tr>
	  				<td align="left"><div id="horiz_menu">{phplayers id=43 type=horiz}</div></td>
	  				<td id="tiki-top_bar_td">
	           				<div id="tiki-top_bar">{include file="tiki-top_bar.tpl"}</div></td>
	           		</tr>
	        		</table>
	     		</td>
	     		</tr>
	 	 </table>
	  	</div>
	    	{/if}
	    	{* end tiki-top *}  
	   {*  {if $feature_right_column eq 'user'}
	              <span style="float: left"><a class="flip" href="javascript:icntoggle('rightcolumn');">
	              <img name="rightcolumnicn" class="colflip" src="img/icons/ofo.gif" border="0" alt="+/-" />&nbsp;{tr}Show/Hide Column{/tr}&nbsp;</a>
	              </span>
	        {/if}
	        *}
	      
	    <div id="tiki-mid">
	    <table id="tiki-midtbl" border="0" cellpadding="0" cellspacing="0" width="100%"  valign="top">
	    
	  {* {if $feature_left_column eq 'user' or $feature_right_column eq 'user'}
	      <tr><td id="tiki-columns" colspan="0" width="100%">
	       {if $feature_left_column eq 'user'}
	          <span style="float: left"><a class="flip" href="javascript:icntoggle('leftcolumn');">
	          <img name="leftcolumnicn" class="colflip" src="img/icons/ofo.gif" border="0" alt="+/-" />&nbsp;{tr}Show/Hide Left Menus{/tr}&nbsp;</a>
	          </span>
	        {/if}
	        
	         </td></tr>
	    {/if}*}
	      <tr>
	      <td id="centercolumn" valign="top">
	     <div id="tiki-center">{include file=$mid}
	            {if $show_page_bar eq 'y'}
	            {include file="tiki-page_bar.tpl"}
	        {/if}
	      </div>
	        </td>
	        {if $feature_left_column ne 'n'}
	        <td id="leftcolumn" valign="top" width="180">
	        {section name=homeix loop=$left_modules}
	        {$left_modules[homeix].data}
	        {/section}
	           {* {if $feature_left_column eq 'user'}
	              <img src="blank.gif" width="100%" height="0px">
	              {literal}
	                <script language="Javascript" type="text/javascript">
	                  setfolderstate("leftcolumn");
	                </script>
	              {/literal}
	            {/if}*}
	        </td>
	        {/if}
	       {if $feature_right_column ne 'n'}
	              <td id="rightcolumn" valign="top"width="180">
	              {section name=homeix loop=$right_modules}
	              {$right_modules[homeix].data}
	              {/section}
	                {*  {if $feature_right_column eq 'user'}
	                    <img src="blank.gif" width="100%" height="0px">
	                    {literal}
	                      <script language="Javascript" type="text/javascript"> 
	                        setfolderstate("rightcolumn");
	                      </script>
	                    {/literal}
	                  {/if}*}
	              </td>
	        {/if}
	        
	        </div>
	        </td>
	        
	      </tr>
	      </table>
  </div>        
          {if $feature_bot_bar eq 'y'}
	    <div id="tiki-bot">
	      {include file="tiki-bot_bar.tpl"}
	    </div>
  {/if}
         
{if $feature_bidi eq 'y'}
</div>
</td></tr></table>
{/if}
{include file="footer.tpl"}