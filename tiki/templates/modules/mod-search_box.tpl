{if $feature_search eq 'y'}
<div class="box">
<div class="box-title">
{tr}Search{/tr}
</div>
<div class="box-data">
    <form class="forms" method="post" action="tiki-searchresults.php">
    &nbsp;<input name="words" size="14" type="text" />
    <input type="submit" class="wikiaction" name="search" value="{tr}search{/tr}"/>
    </form>
</div>
</div>
{/if}