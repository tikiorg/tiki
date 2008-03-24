{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-admin-include-ads.tpl,v 1.1.2.1 2008-03-16 16:57:51 luciash Exp $ *}

<div class="cbox">
	<div class="cbox-title">
	<h3>{tr}{$crumbs[$crumb]->title}{/tr}
	{help crumb=$crumbs[$crumb]}</h3>
	</div>

	<form action="tiki-admin.php?page=ads" class="admin" method="post">
		<div class="heading button" style="text-align: right">
			<input type="submit" name="looksetup" value="{tr}Apply{/tr}" />
			<input type="reset" name="looksetupreset" value="{tr}Reset{/tr}" />
		</div>

{if $prefs.feature_tabs eq 'y'}
	{cycle name=tabs values="1,2,3,4" print=false advance=false reset=true}
		<div class="tabs">
			<span	id="tab{cycle name=tabs advance=false assign=tabi}{$tabi}" 
					class="tabmark tabinactive"><a 
					href="#theme"
					onclick="javascript:tikitabs({cycle name=tabs},4); return false;">{tr}Site Ads and Banners{/tr}</a></span>
		</div>
	{cycle name=content values="1,2,3,4" print=false advance=false reset=true}
{/if}

		<fieldset{if $prefs.feature_tabs eq 'y'} class="tabcontent" id="content{cycle name=content assign=focustab}{$focustab}"{/if}>
{if $prefs.feature_tabs neq 'y'}			<legend class="heading" id="tab{cycle name=tabs advance=false assign=tabi}{$tabi}"><a href="#siteads" name="siteads" onclick="flip('siteads'); return false;"><span>{tr}Site Ads and Banners{/tr}</span></a></legend>
			<div id="siteads" style="display:{if !isset($smarty.session.tiki_cookie_jar.show_siteads) and $smarty.session.tiki_cookie_jar.show_siteads neq 'y'}none{else}block{/if};">{/if}
				<table class="admin" width="100%">
					<tr>
						<td class="form"><label for="feature_sitead">{tr}Activate{/tr}:</label></td>
						<td><input type="checkbox" name="feature_sitead" id="feature_sitead"{if $prefs.feature_sitead eq 'y'} checked="checked"{/if} /></td>
					</tr>
					<tr>
						<td class="form"><label for="sitead">{tr}Content{/tr}:</label></td>
						<td><textarea name="sitead" rows="6" style="width: 90%" id="sitead">{$prefs.sitead|escape}</textarea>
						<br />
						<small><em>{tr}Example{/tr}</em>: {literal}{banner zone='{/literal}{tr}Test{/tr}{literal}'}{/literal}</small></td>
					</tr>
					<tr>
						<td class="form"><label for="sitead_publish">{tr}Publish{/tr}:</label></td>
						<td><input type="checkbox" name="sitead_publish" id="sitead_publish"{if $prefs.sitead_publish eq 'y'} checked="checked"{/if} /></td>
					</tr>
				</table>
{if $prefs.feature_tabs neq 'y'}			</div>{/if}
		</fieldset>

		<div class="button" style="text-align: center"><input type="submit" name="adssetup" value="{tr}Apply{/tr}" /></div>
	</form>
</div><!-- cbox end -->
