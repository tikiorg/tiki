{* $Id$ *}
{strip}
<table>
	{foreach item=prop key=propname from=$fgal_listing_conf}
		{if isset($item.key)}
			{assign var=propkey value=$item.key}
		{else}
			{assign var=propkey value="show_$propname"}
		{/if}
		{if isset($file.$propname)}
			{if $propname == 'share' && isset($file.share.data)}
				{$email = []}
				{foreach item=tmp_prop key=tmp_propname from=$file.share.data}
					{$email[]=$tmp_prop.email}
				{/foreach}
				{assign var=propval value=$email|implode:','}
			{else}
				{assign var=propval value=$file.$propname}
			{/if}
		{/if}
		{* Format property values *}
		{if $propname eq 'created' or $propname eq 'lastModif' or $propname eq 'lastDownload'}
			{assign var=propval value=$propval|tiki_long_date}
		{elseif $propname eq 'last_user' or $propname eq 'author' or $propname eq 'creator'}
			{assign var=propval value=$propval|username}
		{elseif $propname eq 'size'}
			{assign var=propval value=$propval|kbsize:true}
		{elseif $propname eq 'description'}
			{assign var=propval value=$propval|nl2br}
		{/if}

		{if isset($gal_info.$propkey)
			and $propval neq ''
			and ($propname neq 'name' or $view eq 'page')
			and ($gal_info.$propkey eq 'a' or $gal_info.$propkey eq 'o'
					or ($view eq 'page' and ($gal_info.$propkey neq 'n' or $propname eq 'name'))
				)
		}
			<tr>
				<td class="pull-right">
					<b>{$fgal_listing_conf.$propname.name}</b>
				</td>
				<td style="padding-left:5px">
					<span class="pull-left">{$propval}</span>
				</td>
			</tr>
		{/if}
	{/foreach}
</table>
{/strip}