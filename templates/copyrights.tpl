<h2>{tr}Copyrights{/tr}: <a href="tiki-index.php?page={$page|escape:"url"}">{$page}</a></h2>

<table border="0" width="100%">
{section name=i loop=$copyrights}
  <tr><td class="formcolor">
    <form action="copyrights.php?page={$page}" method="post">
    <input type="hidden" name="page" value="{$page|escape}"/>
    <input type="hidden" name="copyrightId" value="{$copyrights[i].copyrightId|escape}"/>
    <table border="0">
      <tr><td class="formcolor">{tr}Title{/tr}:</td><td><input size="40" class="wikitext" type="text" name="copyrightTitle" value="{$copyrights[i].title|escape}" /></td></tr>
      <tr><td class="formcolor">{tr}Year{/tr}:</td><td><input size="4" class="wikitext" type="text" name="copyrightYear" value="{$copyrights[i].year|escape}" /></td></tr>
      <tr><td class="formcolor">{tr}Authors{/tr}:</td><td><input size="40" class="wikitext" type="text" name="copyrightAuthors" value="{$copyrights[i].authors|escape}" /></td></tr>
    </table>
  </td><td class="formcolor" align="right">
    <input type="submit" name="editcopyright" value="{tr}edit{/tr}"/><br />
    <a href="copyrights.php?page={$page|escape:"url"}&action=delete&copyrightId={$copyrights[i].copyrightId}">x</a>
    <a href="copyrights.php?page={$page|escape:"url"}&action=up&copyrightId={$copyrights[i].copyrightId}">up</a>
    <a href="copyrights.php?page={$page|escape:"url"}&action=down&copyrightId={$copyrights[i].copyrightId}">down</a>
    </form>
  </td></tr>
{/section}
<form action="copyrights.php?page={$page}">
<tr><td class="formcolor">
    <table border="0">
      <tr><td class="formcolor">{tr}Title{/tr}:</td><td><input size="40" class="wikitext" type="text" name="copyrightTitle" value="{$copyrights[i].title|escape}" /></td></tr>
      <tr><td class="formcolor">{tr}Year{/tr}:</td><td><input size="4" class="wikitext" type="text" name="copyrightYear" value="{$copyrights[i].year|escape}" /></td></tr>
      <tr><td class="formcolor">{tr}Authors{/tr}:</td><td><input size="40" class="wikitext" type="text" name="copyrightAuthors" value="{$copyrights[i].authors|escape}" /></td></tr>
    </table><input type="hidden" name="page" value="{$page|escape}"/>
</td><td class="formcolor"><input type="submit" name="addcopyright" value="{tr}add{/tr}"/> </td></tr>
</form>
</table>
