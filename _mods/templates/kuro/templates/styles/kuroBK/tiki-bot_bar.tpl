<!-- tiki-bot_bar.tpl /-->
<tr>
  <td colspan="3">
    <div id="tiki-bot">
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td id="hr2"></td>
        </tr>      
        <tr>        
          <td id="hr3">

	    <div id="power" style="text-align: center">
              <a href="RSS" title="RSS Feeds"><img style="border: 0; vertical-align: middle" alt="RSS {tr}Feeds{/tr}" src="http://wiki.splitbrain.org/images/button-rss.png" /></a>
              <a href="http://freshmeat.net/projects/tiki" title="TikiWiki"><img style="border: 0; vertical-align: middle" alt="{tr}Powered by{/tr} TikiWiki" src="styles/kuroBK/tag-tw" /></a>
              <a href="http://jigsaw.w3.org/css-validator/check/referer"><img style="border: 0; vertical-align: middle" alt="{tr}Made with{/tr} CSS" src="styles/kuroBK/tag-css" /></a>
              <a href="http://validator.w3.org/check/referer"><img style="border: 0; vertical-align: middle" alt="{tr}Valid{/tr} XHTML 1.0!" src="styles/kuroBK/tag-xhtml" /></a>
              <a href="http://pear.php.net/"><img style="border: 0; vertical-align: middle" alt="{tr}powered by{/tr} pear" src="styles/kuroBK/tag-pear" /></a>
              <a href="http://adodb.sourceforge.net/"><img style="border: 0; vertical-align: middle" alt="{tr}powered by{/tr} adodb" src="styles/kuroBK/tag-adodb" /></a>
              <a href="http://www.php.net"><img style="border: 0; vertical-align: middle" alt="{tr}powered by{/tr} PHP" src="styles/kuroBK/tag-php" /></a>
              <a href="http://smarty.php.net/"><img style="border: 0; vertical-align: middle" alt="{tr}powered by{/tr} smarty" src="http://marisil.org/img/smarty.png"  /></a>
{if function_exists("mmcache")}
              <a href="http://turck-mmcache.sourceforge.net/"><img style="border: 0; vertical-align: middle" alt="{tr}powered by{/tr} RDF" src="http://images.google.co.uk/images?q=tbn:pDybGgXdUbUJ:www.globware.com/images/turck2.gif"  /></a>
{/if}
              <a href="http://www.w3.org/RDF/"><img style="border: 0; vertical-align: middle" alt="{tr}powered by{/tr} RDF" src="styles/kuroBK/tag-rdf"  /></a>
              <a href="http://creativecommons.org/licenses/by-nc-sa/2.0/"><img style="border: 0; vertical-align: middle" alt="{tr}license{/tr} CC" src="http://wiki.splitbrain.org/images/button-cc.gif"  /></a>
	    </div>

            <div id="loadstats" style="text-align: center">
              <small>[ {tr}Execution time{/tr}: {elapsed} {tr}secs{/tr} ] &nbsp; [ {tr}Memory usage{/tr}: {memusage} ] &nbsp; [ {$num_queries} {tr}database queries used{/tr} ] &nbsp; [ GZIP {$gzip} ] &nbsp; [ {tr}Server load{/tr}: {$server_load} ]</small>
            </div>

          </td>
        </tr>
      </table>
    </div>
  </td>
</tr>

<!-- /tiki-bot_bar.tpl /-->
