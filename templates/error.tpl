{if $close_window eq 'y'}
<script type="text/javascript">
<!--//--><![CDATA[//><!--
close();
//--><!]]>
</script>
{/if}
{capture assign=mid_data}
      {if ($errortype eq "402")}
      <center>
      {include file=tiki-login.tpl}
      </center>
      {else}
        <br />
        <div class="cbox">
        <div class="cbox-title">{icon _id=exclamation alt={tr}Error{/tr} style=vertical-align:middle} {$errortitle|default:"{tr}Error{/tr}"}</div>
        <div class="cbox-data">
        <br />
        {if ($errortype eq "404")}
          {if $prefs.feature_likePages eq 'y'}
           {if $likepages}
          {tr}Perhaps you were looking for:{/tr}
          <ul>
          {section name=back loop=$likepages}
          <li><a  href="tiki-index.php?page={$likepages[back]|escape:"url"}" class="wiki">{$likepages[back]}</a></li>
          {/section}
          </ul>
          <br />
          {else}
          {tr}There are no wiki pages similar to '{$page}'{/tr}
          <br /><br />
          {/if}
          {/if}
		  
		  {if $prefs.feature_search eq 'y'}
          {include
            file="tiki-searchindex.tpl"
            searchNoResults="true"                 
            searchStyle="menu"
            searchOrientation="horiz"
            words="$page"
          }
          {/if}
		  
		  <br />
        {else}
        {$msg}
        <br /><br />
        {/if}
        {if $page and $create eq 'y' and ($tiki_p_admin eq 'y' or $tiki_p_admin_wiki eq 'y'  or $tiki_p_edit eq 'y')}<a href="tiki-editpage.php?page={$page}" class="linkmenu">{tr}Create this page{/tr}</a> {tr}(page will be orphaned){/tr}<br /><br />{/if}
        <a href="javascript:history.back()" class="linkmenu">{tr}Go back{/tr}</a><br /><br />
        <a href="{$prefs.tikiIndex}" class="linkmenu">{tr}Return to home page{/tr}</a>
        </div>
        </div>
      {/if}
{/capture}
{include file=tiki.tpl}
