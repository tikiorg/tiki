<form method="post">
<h1>{tr}Enable Features{/tr}</h1>
<ul>
{section name=i loop=$features}
<li><input type='checkbox' name='{$features[i].setting_name}' id='{$features[i].setting_name}' {if $features[i].value eq 'y'}checked="checked"{/if}/> <label for="{$features[i].setting_name}">{tr}{$features[i].feature_name}{/tr}</label></li>
{/section}
</ul>
<input type="submit" name="submit"/>
</form>
