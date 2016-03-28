{* $Id$ *}

{* Use css menus as fallback for item dropdown action menu if javascript is not being used *}
{if $prefs.javascript_enabled != 'y'}
	{$js = 'n'}
	{$libeg = '<li>'}
	{$liend = '</li>'}
{else}
	{$js = 'y'}
	{$libeg = ''}
	{$liend = ''}
{/if}

{if !$tsOn && ($cant_pages > 1 or $initial or $find)}
	{initials_filter_links}
{/if}

{if $tiki_p_remove eq 'y' or $prefs.feature_wiki_multiprint eq 'y'}
	{if isset($checkboxes_on) and $checkboxes_on eq 'n'}
		{assign var='checkboxes_on' value='n'}
	{else}
		{assign var='checkboxes_on' value='y'}
	{/if}
{else}
	{assign var='checkboxes_on' value='n'}
{/if}

{if $find ne '' and $listpages|@count ne '0'}
	<p>{tr}Found{/tr} &quot;{$find|escape}&quot; {tr}in{/tr} {$listpages|@count} {tr}pages{/tr}.</p>
{/if}


{if isset($checkboxes_on) and $checkboxes_on eq 'y'}
	<form name="checkboxes_on" method="post">
{/if}

{assign var='pagefound' value='n'}
<div id="{$ts_tableid}-div" class="{if $js === 'y'}table-responsive{/if} ts-wrapperdiv" {if $tsOn}style="visibility:hidden;"{/if}> {*the table-responsive class cuts off dropdown menus *}
	<table id="{$ts_tableid}" class="table normal table-striped table-hover" data-count="{$cant|escape}">
		<thead>
			<tr>
				{if isset($checkboxes_on) and $checkboxes_on eq 'y'}
					<th id="checkbox">
						{select_all checkbox_names='checked[]'}
					</th>
					{assign var='cntcol' value='1'}
				{else}
					{assign var='cntcol' value='0'}
				{/if}

				{if $prefs.wiki_list_id eq 'y'}
					{assign var='cntcol' value=$cntcol+1}
					<th id="pageid">
						{self_link _sort_arg='sort_mode' _sort_field='page_id'}{tr}Id{/tr}{/self_link}
					</th>
				{/if}

				{if $prefs.wiki_list_name eq 'y'}
					{assign var='cntcol' value=$cntcol+1}
					<th id="pagename">
						{self_link _sort_arg='sort_mode' _sort_field='pageName'}{tr}Page{/tr}{/self_link}
					</th>
				{/if}

				{if isset($wplp_used)}
					{foreach from=$wplp_used key=lc item=ln}
						<th>{$ln|escape}</th>
					{/foreach}
				{/if}
				{if $prefs.wiki_list_hits eq 'y'}
					{assign var='cntcol' value=$cntcol+1}
					<th id="hits">{self_link _sort_arg='sort_mode' _sort_field='hits'}{tr}Hits{/tr}{/self_link}</th>
				{/if}

				{if $prefs.wiki_list_lastmodif eq 'y' or $prefs.wiki_list_comment eq 'y'}
					{assign var='cntcol' value=$cntcol+1}
					<th id="lastmodif">
						{assign var='lastmod_sortfield' value='lastModif'}
						{assign var='lastmod_shorttitle' value="{tr}Last modification{/tr}"}
						{if $prefs.wiki_list_lastmodif eq 'y' and $prefs.wiki_list_comment eq 'y'}
							{assign var='lastmod_title' value="{tr}Last modification{/tr} / {tr}Comment{/tr}"}
						{elseif $prefs.wiki_list_lastmodif eq 'y'}
							{assign var='lastmod_title' value="{tr}Last modification{/tr}"}
						{else}
							{assign var='lastmod_title' value="{tr}Comment{/tr}"}
							{assign var='lastmod_sortfield' value='comment'}
							{assign var='lastmod_shorttitle' value="{tr}Comment{/tr}"}
						{/if}
						{self_link _sort_arg='sort_mode' _sort_field=$lastmod_sortfield _title=$lastmod_title}{$lastmod_shorttitle}{/self_link}
					</th>
				{/if}

				{if $prefs.wiki_list_creator eq 'y'}
					{assign var='cntcol' value=$cntcol+1}
					<th id="creator">
						{self_link _sort_arg='sort_mode' _sort_field='creator' _title="{tr}Page creator{/tr}"}{tr}Creator{/tr}{/self_link}
					</th>
				{/if}

				{if $prefs.wiki_list_user eq 'y'}
					{assign var='cntcol' value=$cntcol+1}
					<th id="lastauthor">
						{self_link _sort_arg='sort_mode' _sort_field='user' _title="{tr}Last author{/tr}"}{tr}Last author{/tr}{/self_link}
					</th>
				{/if}

				{if $prefs.wiki_list_lastver eq 'y'}
					{assign var='cntcol' value=$cntcol+1}
					<th id="version">
						{self_link _sort_arg='sort_mode' _sort_field='version' _title="{tr}Last version{/tr}"}{tr}Last version{/tr}{/self_link}
					</th>
				{/if}

				{if $prefs.wiki_list_status eq 'y'}
					{assign var='cntcol' value=$cntcol+1}
					<th id="status" style="text-align:center;">
						{self_link _sort_arg='sort_mode' _sort_field='flag' _icon_name='lock'}{tr}Status of the page{/tr}{/self_link}
					</th>
				{/if}

				{if $prefs.wiki_list_versions eq 'y'}
					{assign var='cntcol' value=$cntcol+1}
					<th id="versions">
						{self_link _sort_arg='sort_mode' _sort_field='versions' _title="{tr}Versions{/tr}"}{tr}Version{/tr}{/self_link}
					</th>
				{/if}

				{if $prefs.wiki_list_links eq 'y'}
					{assign var='cntcol' value=$cntcol+1}
					<th id="links">
						{self_link _sort_arg='sort_mode' _sort_field='links' _title="{tr}Links to other items in page{/tr}"}{tr}Links{/tr}{/self_link}
					</th>
				{/if}

				{if $prefs.wiki_list_backlinks eq 'y'}
					{assign var='cntcol' value=$cntcol+1}
					<th id="backlinks">
						{self_link _sort_arg='sort_mode' _sort_field='backlinks' _title="{tr}Links to this page in other pages{/tr}"}{tr}Backl.{/tr}{/self_link}
					</th>
				{/if}

				{if $prefs.wiki_list_size eq 'y'}
					{assign var='cntcol' value=$cntcol+1}
					<th id="size">
						{self_link _sort_arg='sort_mode' _sort_field='page_size' _title="{tr}Page size{/tr}"}{tr}Size{/tr}{/self_link}
					</th>
				{/if}

				{if $prefs.wiki_list_language eq 'y'}
					{assign var='cntcol' value=$cntcol+1}
					<th id="language">
						{self_link _sort_arg='sort_mode' _sort_field='lang' _title="{tr}Language{/tr}"}{tr}Lang.{/tr}{/self_link}
					</th>
				{/if}

				{if $prefs.wiki_list_categories eq 'y'}
					{assign var='cntcol' value=$cntcol+1}
					<th id="categories">{tr}Categories{/tr}</th>
				{/if}

				{if $prefs.wiki_list_categories_path eq 'y'}
					{assign var='cntcol' value=$cntcol+1}
					<th id="catpaths">{tr}Categories{/tr}</th>
				{/if}

				{if $prefs.wiki_list_rating eq 'y'}
					{assign var='cntcol' value=$cntcol+1}
					<th id="rating">
						{self_link _sort_arg='sort_mode' _sort_field='rating' _title="{tr}Ratings{/tr}"}{tr}Ratings{/tr}{/self_link}
					</th>
				{/if}

				{if $show_actions eq 'y'}
					{assign var='cntcol' value=$cntcol+1}
					<th id="actions"></th>
				{/if}
			</tr>
		</thead>

		<tbody>

			{section name=changes loop=$listpages}

				{if isset($mapview) and $mapview}
					<div class="listpagesmap" style="display:none;">{object_link type="wiki page" id="`$listpages[changes].pageName|escape`"}</div>
				{/if}

				{if $find eq $listpages[changes].pageName}
					{assign var='pagefound' value='y'}
				{/if}

				<tr>
				
					{if $checkboxes_on eq 'y'}
						<td class="checkbox-cell">
							<input type="checkbox" name="checked[]" value="{$listpages[changes].pageName|escape}">
						</td>
					{/if}

					{if $prefs.wiki_list_id eq 'y'}
						<td class="integer">
							<a href="{$listpages[changes].pageName|sefurl}" class="link tips" title="{$listpages[changes].pageName|escape}:{tr}View page{/tr}">
								{$listpages[changes].page_id}
							</a>
						</td>
					{/if}

					{if $prefs.wiki_list_name eq 'y'}
						<td class="text">
							{* 
								The variant of the object link below adds the baseurl as received by the request to the href attribute generated.
								I.e. "http://192.168.1.10/tiki-listpages.php?page=MyPage" instead of "tiki-listpages.php?page=MyPage"
								This leads to trouble when using a reverse proxy that takes an external fqdn and maps it to a local address.
								Other templates do not use this object_link but an simple <a href></a>. See i.e tiki_lastchanges.tpl so we use it here as well.
								Same for the link generated for the page id (wiki_list_id) above.
							*}
							{* 
								{object_link type=wiki id=$listpages[changes].pageName url=$listpages[changes].pageName|sefurl:'wiki':'':$all_langs title=$listpages[changes].pageName|truncate:$prefs.wiki_list_name_len:"...":true}
							*}
							<a href="{$listpages[changes].pageName|sefurl}" class="link tips" title="{$listpages[changes].pageName|escape}:{tr}View page{/tr}">
								{$listpages[changes].pageName|truncate:$prefs.wiki_list_name_len:"...":true}
							</a>
							{if $prefs.wiki_list_description eq 'y' && $listpages[changes].description neq ""}
								<div class="subcomment">
									{$listpages[changes].description|truncate:$prefs.wiki_list_description_len:"...":true}
								</div>
							{/if}
							{if !empty($listpages[changes].snippet)}
								<div class="subcomment">{$listpages[changes].snippet}</div>
							{/if}
						</td>
					{/if}

					{if isset($wplp_used)}
						{foreach from=$wplp_used key=lc item=ln}
							<td class="text">
								{if $listpages[changes].translations[$lc]}
									<a href="{$listpages[changes].translations[$lc]|sefurl}" class="link" title="{tr}View page{/tr}&nbsp;{$listpages[changes].translations[$lc]|escape}">
										{$listpages[changes].translations[$lc]|escape}
									</a>
								{/if}
							</td>
						{/foreach}
					{/if}

					{if $prefs.wiki_list_hits eq 'y'}
						<td class="integer">
							{$listpages[changes].hits}
						</td>
					{/if}

					{if $prefs.wiki_list_lastmodif eq 'y' or $prefs.wiki_list_comment eq 'y'}
						<td class="date">
							{if $prefs.wiki_list_lastmodif eq 'y'}
								<div>{$listpages[changes].lastModif|tiki_short_datetime}</div>
							{/if}
							{if $prefs.wiki_list_comment eq 'y' && $listpages[changes].comment neq ""}
								<div>
									<i>{$listpages[changes].comment|truncate:$prefs.wiki_list_comment_len:"...":true}</i>
								</div>
							{/if}
						</td>
					{/if}

					{if $prefs.wiki_list_creator eq 'y'}
						<td class="username">
							{$listpages[changes].creator|userlink}
						</td>
					{/if}

					{if $prefs.wiki_list_user eq 'y'}
						<td class="username">
							{$listpages[changes].user|userlink}
						</td>
					{/if}

					{if $prefs.wiki_list_lastver eq 'y'}
						<td class="integer">
							{$listpages[changes].version}
						</td>
					{/if}

					{if $prefs.wiki_list_status eq 'y'}
						<td class="icon">
							{if $listpages[changes].flag eq 'locked'}
								{icon name='lock' alt="{tr}Locked{/tr}"}
							{else}
								{icon name='unlock' alt="{tr}unlocked{/tr}"}
							{/if}
						</td>
					{/if}

					{if $prefs.wiki_list_versions eq 'y'}
						{if $prefs.feature_history eq 'y' and $tiki_p_wiki_view_history eq 'y'}
							<td class="integer">
								<a class="link" href="tiki-pagehistory.php?page={$listpages[changes].pageName|escape:"url"}">
									{$listpages[changes].version}
								</a>
							</td>
						{else}
							<td class="integer">
								{$listpages[changes].version}
							</td>
						{/if}
					{/if}

					{if $prefs.wiki_list_links eq 'y'}
						<td class="integer">
							{$listpages[changes].links}
						</td>
					{/if}

					{if $prefs.wiki_list_backlinks eq 'y'}
						{if $prefs.feature_backlinks eq 'y'}
							<td class="integer">
								<a class="link" href="tiki-backlinks.php?page={$listpages[changes].pageName|escape:"url"}">
									{$listpages[changes].backlinks}
								</a>
							</td>
						{else}
							<td class="integer">{$listpages[changes].backlinks}</td>
						{/if}
					{/if}

					{if $prefs.wiki_list_size eq 'y'}
						<td class="integer">{$listpages[changes].len|kbsize}</td>
					{/if}

					{if $prefs.wiki_list_language eq 'y'}
						<td class="text">
							{$listpages[changes].lang}
						</td>
					{/if}

					{if $prefs.wiki_list_categories eq 'y'}
						<td class="text">
							{foreach $listpages[changes].categname as $categ}
								{if !$categ@first}<br>{/if}
								{$categ|escape}
							{/foreach}
						</td>
					{/if}

					{if $prefs.wiki_list_categories_path eq 'y'}
						<td class="text">
							{foreach $listpages[changes].categpath as $categpath}
								{if !$categpath@first}<br>{/if}
								{$categpath|escape}
							{/foreach}
						</td>
					{/if}

					{if $prefs.wiki_list_rating eq 'y'}
						<td class="integer">
							{$listpages[changes].rating}
						</td>
					{/if}

					{if $show_actions eq 'y'}
						<td class="action">
							{capture name=page_actions}
								{strip}
									{if $listpages[changes].perms.tiki_p_edit eq 'y'}
										{$libeg}<a href="tiki-editpage.php?page={$listpages[changes].pageName|escape:"url"}">
											{icon name='edit' _menu_text='y' _menu_icon='y' alt="{tr}Edit{/tr}"}
										</a>{$liend}
										{$libeg}<a href="tiki-copypage.php?page={$listpages[changes].pageName|escape:"url"}&amp;version=last">
											{icon name='copy' _menu_text='y' _menu_icon='y' alt="{tr}Copy{/tr}"}
										</a>{$liend}
									{/if}
									{if $prefs.feature_history eq 'y' and $listpages[changes].perms.tiki_p_wiki_view_history eq 'y'}
										{$libeg}<a href="tiki-pagehistory.php?page={$listpages[changes].pageName|escape:"url"}">
											{icon name='history' _menu_text='y' _menu_icon='y' alt="{tr}History{/tr}"}
										</a>{$liend}
									{/if}

									{if $listpages[changes].perms.tiki_p_assign_perm_wiki_page eq 'y'}
										{$libeg}{permission_link mode=text type="wiki page" permType=wiki id=$listpages[changes].pageName}{$liend}
									{/if}

									{if $listpages[changes].perms.tiki_p_remove eq 'y'}
										{$libeg}<a href="tiki-removepage.php?page={$listpages[changes].pageName|escape:"url"}&amp;version=last">
											{icon name='remove' _menu_text='y' _menu_icon='y' alt="{tr}Remove{/tr}"}
										</a>{$liend}
									{/if}
								{/strip}
							{/capture}
							{if $js === 'n'}<ul class="cssmenu_horiz"><li>{/if}
							<a
								class="tips"
								title="{tr}Actions{/tr}"
								href="#"
								{if $js === 'y'}{popup fullhtml="1" center=true text=$smarty.capture.page_actions|escape:"javascript"|escape:"html"}{/if}
								style="padding:0; margin:0; border:0"
							>
								{icon name='wrench'}
							</a>
							{if $js === 'n'}
								<ul class="dropdown-menu" role="menu">{$smarty.capture.page_actions}</ul></li></ul>
							{/if}
						</td>
					{/if}
				</tr>
			{sectionelse}
				{capture assign='find_htmlescaped'}{$find|escape}{/capture}
				{capture assign="intro"}{if $exact_match ne 'n'}{tr}No page:{/tr}{else}{tr}No pages found with:{/tr}{/if}{/capture}
				{if $find ne '' && $aliases_were_found == 'y'}
					{norecords _colspan=$cntcol _text="$intro &quot;$find_htmlescaped&quot;. <br/>However, some page aliases fitting the query were found (see Aliases section above)."}
				{elseif $find ne '' && $initial ne '' && $aliases_were_found == 'y'}
					{norecords _colspan=$cntcol _text="$intro &quot;$find_htmlescaped&quot;and starting with &quot; $initial &quote;. <br/>However, some page aliases fitting the query were found (see Aliases section above)."}
				{elseif $find ne '' && $initial ne ''}
					{norecords _colspan=$cntcol _text="$intro &quot;$find_htmlescaped&quot; and starting with &quot; $initial &quot;."}
				{elseif $find ne ''}
					{norecords _colspan=$cntcol _text="$intro &quot;$find_htmlescaped&quot;."}
				{else}
					{norecords _colspan=$cntcol _text="{tr}No pages found.{/tr}"}
				{/if}

			{/section}
		</tbody>
	</table>
</div>
{if !$tsAjax}
	{if $checkboxes_on eq 'y' && count($listpages) > 0} {* what happens to the checked items? *}
		<p align="left"> {*on the left to have it close to the checkboxes*}
			<label for="submit_mult">{tr}Perform action with checked:{/tr}</label>
			<select name="submit_mult" class="form-control" id="submit_mult" onchange="this.form.submit();">
				<option value="" selected="selected">...</option>
				{if $tiki_p_remove eq 'y'}
					<option value="remove_pages" >{tr}Remove{/tr}</option>
				{/if}

				{if $prefs.feature_wiki_multiprint eq 'y'}
					<option value="print_pages" >{tr}Print{/tr}</option>

						{if $prefs.print_pdf_from_url neq 'none'}
						<option value="export_pdf" >{tr}PDF{/tr}</option>
					{/if}
				{/if}

				{if $prefs.feature_wiki_usrlock eq 'y' and ($tiki_p_lock eq 'y' or $tiki_p_admin_wiki eq 'y')}
					<option value="lock_pages" >{tr}Lock{/tr}</option>
					<option value="unlock_pages" >{tr}Unlock{/tr}</option>
				{/if}
				{if $tiki_p_admin eq 'y'}
					<option value="zip">{tr}Xml Zip{/tr}</option>
				{/if}
				{if $tiki_p_admin eq 'y'}
					<option value="title">{tr}Add page name as an h1-size header at the beginning of the page content{/tr}</option>
				{/if}

				{* add here e.g. <option value="categorize" >{tr}categorize{/tr}</option> *}
			</select>
		</p>
		<script type='text/javascript'>
			<!--
			// Fake js to allow the use of the <noscript> tag (so non-js-users can still submit)
			//-->
		</script>
		<noscript>
			<input type="submit" class="btn btn-default btn-sm" value="{tr}OK{/tr}">
		</noscript>
	{/if}

	{if $find and $tiki_p_edit eq 'y' and $pagefound eq 'n' and $alias_found eq 'n'}
		{capture assign='find_htmlescaped'}{$find|escape}{/capture}
		{capture assign='find_urlescaped'}{$find|escape:'url'}{/capture}
		<div class="t_navbar">
			{button _text="{tr}Create Page:{/tr} $find_htmlescaped" href="tiki-editpage.php?page=$find_urlescaped&lang=$find_lang&templateId=$template_id&template_name=$template_name&categId=$create_page_with_categId" class="btn btn-default" _title="{tr}Create{/tr}"}
		</div>
	{/if}

	{if $checkboxes_on eq 'y'}
		</form>
	{/if}

	{if !isset($tsOn) or !$tsOn}
		{pagination_links cant=$cant step=$maxRecords offset=$offset clean=$clean}{/pagination_links}
	{/if}
{/if}