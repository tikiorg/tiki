{* $Id$ *}
{*
 * If you want to change this page, check http://www.tikiwiki.org/tiki-index.php?page=AdministrationDev
 * there you"ll find attached a gimp image containing this page with icons in separated layers
 *}

{remarksbox type="tip" title="{tr}Tip{/tr}"}
	{tr}Enable/disable Tiki features in Tikiwiki, in a simple drag
	an drop click! {/tr}
{/remarksbox}

<div class="addfeatures">
<a href="./tiki-admin.php?page=features" class="button" id="addf">Add more features</a>
</div>


<div class="features-box">
<div class="features-box-content">
	<div class="features-box-separator">
		<div class="features-box-separator-title">
			<p>Main Features</p>
		</div>
		<div class="features-box-separator-content">

			{if $prefs.feature_articles ne 'n'}{self_link page="cms" _class="admbox" _style="background-image: url('pics/large/stock_bold48x48.png')" _selected="'`$prefs.feature_articles`' != 'y'" _selected_class="admbox off"}<img src="pics/trans.png"	alt="{tr}Articles{/tr}" title="{tr}Articles{/tr}" /><span>{tr}Articles{/tr}</span>{/self_link}{/if}

			{if $prefs.feature_blogs ne 'n' }{self_link page="blogs" _class="admbox" _style="background-image: url('pics/large/blogs48x48.png')" }<img src="pics/trans.png" alt="{tr}Blogs{/tr}" title="{tr}Blogs{/tr}" /><span>{tr}Blogs{/tr}</span>{/self_link}{/if}

			{if $prefs.feature_calendar ne 'n'}{self_link page="calendar" _class="admbox" _style="background-image: url('pics/large/date48x48.png')" _selected="'`$prefs.feature_calendar`' != 'y'" _selected_class="admbox off"}<img src="pics/trans.png" alt="{tr}Calendar{/tr}" title="{tr}Calendar{/tr}" /><span>{tr}Calendar{/tr}</span>{/self_link}{/if}

			{if $prefs.feature_community ne 'n'}{self_link page="community" _class="admbox" _style="background-image: url('pics/large/users48x48.png')" _title="{tr}Community{/tr}"}<span>{tr}Community{/tr}</span>{/self_link}{/if}

			{if $prefs.feature_forums ne 'n'}{self_link page="forums" _class="admbox" _style="background-image: url('pics/large/stock_index48x48.png')" _selected="'`$prefs.feature_forums`' != 'y'" _selected_class="admbox off"}<img src="pics/trans.png"	alt="{tr}Forums{/tr}" title="{tr}Forums{/tr}" /><span>{tr}Forums{/tr}</span>{/self_link}{/if}

			{if $prefs.feature_rss ne 'n'}{self_link page="rss" _class="admbox" _style="background-image: url('pics/large/gnome-globe48x48.png')" _title="{tr}RSS{/tr}"}<span>{tr}RSS{/tr}</span>{/self_link}{/if}

			{if $prefs.feature_wiki ne 'n'}{self_link page="wiki" _class="admbox" _style="background-image:	url('pics/large/wikipages48x48.png')" _selected="'`$prefs.feature_wiki`' != 'y'" _selected_class="admbox off"}<img src="pics/trans.png"	alt="{tr}Wiki{/tr}" title="{tr}Wiki{/tr}" /><span>{tr}Wiki{/tr}</span>{/self_link}{/if}

			{if $prefs.feature_file_galleries ne 'n'}{self_link page="fgal"	_class="admbox" _style="background-image: url('pics/large/file-manager48x48.png')" _selected="'`$prefs.feature_file_galleries`' != 'y'"	_selected_class="admbox off"}<img src="pics/trans.png" alt="{tr}File Galleries{/tr}" title="{tr}File Galleries{/tr}" /><span>{tr}File Galleries{/tr}</span>{/self_link}{/if}

			{if $prefs.feature_galleries ne 'n'}{self_link page="gal" _class="admbox" _style="background-image: url('pics/large/stock_select-color48x48.png')" _selected="'`$prefs.feature_galleries`' != 'y'" _selected_class="admbox off"}<img src="pics/trans.png" alt="{tr}Image Galleries{/tr}" title="{tr}Image Galleries{/tr}" /><span>{tr}Image Galleries{/tr}</span>{/self_link}{/if}

			{if $prefs.feature_polls ne 'n'}{self_link page="polls" _class="admbox" _style="background-image: url('pics/large/stock_missing-image48x48.png')" _selected="'`$prefs.feature_polls`' != 'y'" _selected_class="admbox off"}<img src="pics/trans.png" alt="{tr}Polls{/tr}" title="{tr}Polls{/tr}" /><span>{tr}Polls{/tr}</span>{/self_link}{/if}

			{if $prefs.feature_trackers ne 'n'}{self_link page="trackers" _class="admbox" _style="background-image: url('pics/large/gnome-settings-font48x48.png')" _selected="'`$prefs.feature_trackers`' != 'y'" _selected_class="admbox off"}<img src="pics/trans.png" alt="{tr}Trackers{/tr}" title="{tr}Trackers{/tr}" /><span>{tr}Trackers{/tr}</span>{/self_link}{/if}

			{if $prefs.feature_maps ne 'n'}{self_link page="maps" _class="admbox" _style="background-image:	url('pics/large/maps48x48.png')" _selected="'`$prefs.feature_maps`' != 'y'" _selected_class="admbox off"}<img src="pics/trans.png" alt="{tr}Maps{/tr}" title="{tr}Maps{/tr}" /><span>{tr}Maps{/tr}</span>{/self_link}{/if}
	
			{if $prefs.feature_gmap ne 'n'}{self_link page="gmap" _class="admbox" _style="background-image: url('pics/large/google_maps48x48.png')" _selected="'`$prefs.feature_gmap`' != 'y'" _selected_class="admbox off"}<img src="pics/trans.png" alt="{tr}Google Maps{/tr}" title="{tr}Google Maps{/tr}" /><span>{tr}Google Maps{/tr}</span>{/self_link}{/if}

			{if $prefs.feature_score ne 'n'}{self_link page="score" _class="admbox" _style="background-image: url('pics/large/stock_about48x48.png')" _selected="'`$prefs.feature_score`' != 'y'" _selected_class="admbox off"}<img src="pics/trans.png" alt="{tr}Score{/tr}" title="{tr}Score{/tr}" /><span>{tr}Score{/tr}</span>{/self_link}{/if}	

			{if $prefs.feature_search ne 'n'}{self_link page="search" _class="admbox" _style="background-image: url('pics/large/xfce4-appfinder48x48.png')" _selected="'`$prefs.feature_search`' != 'y'" _selected_class="admbox off"}<img src="pics/trans.png" alt="{tr}Search{/tr}" title="{tr}Search{/tr}" /><span>{tr}Search{/tr}</span>{/self_link}{/if}
	
			{if $prefs.feature_directory ne 'n'}{self_link page="directory"	_class="admbox" _style="background-image: url('pics/large/gnome-fs-server48x48.png')" _selected="'`$prefs.feature_directory`' != 'y'" _selected_class="admbox off"}<img src="pics/trans.png" alt="{tr}Directory{/tr}" title="{tr}Directory{/tr}" /><span>{tr}Directory{/tr}</span>{/self_link}{/if}

			{if $prefs.feature_multimedia ne 'n'}{self_link page="multimedia" _class="admbox" _style="background-image: url('pics/large/multimedia48x48.png')" _selected="'`$prefs.feature_multimedia`' != 'y'" _selected_class="admbox off"}<img src="pics/trans.png" alt="{tr}Multimedia{/tr}" title="{tr}Multimedia{/tr}" /><span>{tr}Multimedia{/tr}</span>{/self_link}{/if}

			{if $prefs.feature_copyright ne 'n'}{self_link page="copyright" _class="admbox" _style="background-image: url('pics/large/copyright48x48.png')" _selected="'`$prefs.feature_copyright`' != 'y'" _selected_class="admbox off"}<img src="pics/trans.png" alt="{tr}Copyright{/tr}" title="{tr}Copyright{/tr}" /><span>{tr}Copyright{/tr}</span>{/self_link}{/if}

			{if $prefs.feature_messages ne 'n'}{self_link page="messages" _class="admbox" _style="background-image: url('pics/large/messages48x48.png')" _selected="'`$prefs.feature_messages`' != 'y'" _selected_class="admbox off"}<img src="pics/trans.png" alt="{tr}Messages{/tr}" title="{tr}Messages{/tr}" /><span>{tr}Messages{/tr}</span>{/self_link}{/if}
	
			{if $prefs.feature_userfiles ne 'n'}{self_link page="userfiles" _class="admbox" _style="background-image: url('pics/large/userfiles48x48.png')" _selected="'`$prefs.feature_userfiles`' != 'y'" _selected_class="admbox off"}<img src="pics/trans.png" alt="{tr}User files{/tr}" title="{tr}User files{/tr}" /><span>{tr}User files{/tr}</span>{/self_link}{/if}
	
			{if $prefs.feature_webmail ne 'n'}{self_link page="webmail" _class="admbox" _style="background-image: url('pics/large/evolution48x48.png')" _selected="'`$prefs.feature_webmail`' != 'y'" _selected_class="admbox off"}<img src="pics/trans.png" alt="{tr}Webmail{/tr}" title="{tr}Webmail{/tr}" /><span>{tr}Webmail{/tr}</span>{/self_link}{/if}
		</div>
	</div>

<div class="features-box-content">
	<div class="features-box-separator">
		<div class="features-box-separator-title">
			<p>Site Global Features</p>
		</div>
		<div class="features-box-separator-content">

			{if $prefs.feature_categories ne 'n'}{self_link page="category" _class="admbox" _style="background-image: url('pics/large/categories48x48.png')" _selected="'`$prefs.feature_categories`' != 'y'" _selected_class="admbox off"}<img src="pics/trans.png" alt="{tr}Categories{/tr}" title="{tr}Categories{/tr}" /><span>{tr}Categories{/tr}</span>{/self_link}{/if}

			{if $prefs.feature_freetags ne 'n'}{self_link page="freetags" _class="admbox" _style="background-image: url('pics/large/vcard48x48.png')" _selected="'`$prefs.feature_freetags`' != 'y'" _selected_class="admbox off"}<img src="pics/trans.png" alt="{tr}Freetags{/tr}" title="{tr}Freetags{/tr}" /><span>{tr}Freetags{/tr}</span>{/self_link}{/if}

			{if $prefs.feature_sefurl ne 'n'}{self_link page="sefurl" _class="admbox" _style="background-image: url('pics/large/goto48x48.png')" _selected="'`$prefs.feature_sefurl`' != 'y'" _selected_class="admbox off"}<img src="pics/trans.png" alt="{tr}Search engine friendly url{/tr}" title="{tr}Search engine friendly url{/tr}" /><span>{tr}Search engine friendly url{/tr}</span>{/self_link}{/if}


			{self_link page="i18n" _class="admbox" _style="background-image: url('pics/large/i18n48x48.png')" _title="{tr}i18n{/tr}"}<span>{tr}i18n{/tr}</span>{/self_link}	
		</div>
	</div>
</div>


<div class="features-box-content">
	<div class="features-box-separator">
		<div class="features-box-separator-title">
			<p>Additional Features</p>
		</div>
		<div class="features-box-separator-content">

			{if $prefs.feature_banners ne 'n'}{self_link page="ads" _class="admbox" _style="background-image: url('pics/large/ads48x48.png')" _selected="'`$prefs.feature_banners`' != 'y'" _selected_class="admbox off"}<img src="pics/trans.png" alt="{tr}Site Ads an Banners{/tr}" title="{tr}Site Ads and Banners{/tr}" /><span>{tr}Site Ads and Banners{/tr}</span>{/self_link}{/if}

			{if $prefs.feature_intertiki ne 'n'}{self_link page="intertiki" _class="admbox" _style="background-image: url('pics/large/intertiki48x48.png')" _selected="'`$prefs.feature_intertiki`' != 'y'" _selected_class="admbox off"}<img src="pics/trans.png" alt="{tr}InterTiki{/tr}" title="{tr}InterTiki{/tr}" /><span>{tr}InterTiki{/tr}</span>{/self_link}{/if}

			{if $prefs.feature_semantic ne 'n'}{self_link page="semantic" _class="admbox" _style="background-image: url('pics/large/semantic48x48.png')" _selected="'`$prefs.feature_semantic`' != 'y'" _selected_class="admbox off"}<img src="pics/trans.png" alt="{tr}Semantic links{/tr}" title="{tr}Semantic links{/tr}" /><span>{tr}Semantic links{/tr}</span>{/self_link}{/if}

			{if $prefs.feature_webservices}{self_link page="webservices" _class="admbox" _style="background-image: url('pics/large/webservices48x48.png')" _selected="'`$prefs.feature_webservices`' != 'y'" _selected_class="admbox off"}<img src="pics/trans.png" alt="{tr}Webservices{/tr}" title="{tr}Webservices{/tr}"	/><span>{tr}Webservices{/tr}</span>{/self_link}{/if}

			{if $prefs.feature_faqs ne 'n'}{self_link page="faqs" _class="admbox" _style="background-image: url('pics/large/stock_dialog_question48x48.png')" _selected="'`$prefs.feature_faqs`' != 'y'" _selected_class="admbox off"}<img src="pics/trans.png" alt="{tr}FAQs{/tr}" title="{tr}FAQs{/tr}" /><span>{tr}FAQs{/tr}</span>{/self_link}{/if}

		</div>
	</div>
</div>

<div class="features-box-content">
	<div class="features-box-separator">
		<div class="features-box-separator-title">
			<p>Configuration</p>
		</div>
		<div class="features-box-separator-content">
				{self_link page="general" _class="admbox" _style="background-image: url('pics/large/icon-configuration48x48.png')" _title="{tr}General{/tr}"}<span>{tr}General{/tr}</span>{/self_link}

	{self_link page="features" _class="admbox" _style="background-image: url('pics/large/boot48x48.png')" _title="{tr}Features{/tr}"}<span>{tr}Features{/tr}</span>{/self_link}

	{self_link page="login" _class="admbox" _style="background-image: url('pics/large/stock_quit48x48.png')" _title="{tr}Login{/tr}"}<span>{tr}Login{/tr}</span>{/self_link}

	{self_link page="textarea" _class="admbox" _style="background-image: url('img/icons/admin_textarea.png')" _title="{tr}Editing and Plugins{/tr}"}<span>{tr}Editing and Plugins{/tr}</span>{/self_link}

	{self_link page="profiles" _class="admbox" _style="background-image: url('pics/large/profiles48x48.png')" _title="{tr}Profiles{/tr}"}<span>{tr}Profiles{/tr}</span>{/self_link}


		</div>
	</div>
</div>


</div>
</div>

{* $Id$ *}
