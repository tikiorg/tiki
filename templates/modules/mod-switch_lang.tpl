<div class="box">
<div class="box-title">
{tr}Language: {/tr}{$language}
</div>
<div class="box-data">
<form method="get" action="tiki-switch_lang.php" target="_self">
       <select name="language" size="1" onchange="this.form.submit();">
        {section name=ix loop=$languages}
        <option value="{$languages[ix].value|escape}"
          {if $language eq $languages[ix].value}selected="selected"{/if}>
          {$languages[ix].name}
        </option>
        {/section}
        </select>
</form>
</div>
</div>
