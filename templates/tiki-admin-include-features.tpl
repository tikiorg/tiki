{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-admin-include-features.tpl,v 1.52 2004-06-17 21:44:37 teedog Exp $ *}

{* this is the very top most box of the feature section in tiki-admin.php?page=features,
 * each td is a cell,each tr is a row, not to be confused with tr-smarty-tag which means translate...
 * there are five cells for every row, the middle cell is empty to keep feature and ckboxes separate
 *}

<form action="tiki-admin.php?page=features" method="post">
  <div class="cbox">
    <div class="cbox-title">
      {if $feature_help eq 'y'}<a href="{$helpurl}FeatureSettings" target="tikihelp" class="tikihelp" title="{tr}Features{/tr}">{/if}
			{tr}Features{/tr}
			{if $feature_help eq 'y'}</a>{/if}
			<br />
    </div>
{* the heading of the  box *}
    <div class="cbox-data">
      <table class="admin" width="100%"><tr>
        <td class="heading" colspan="5" align="center">{tr}Tiki sections and features{/tr}</td>
      </tr>
{* top left wiki ck box ... each of the function option boxes here begin with  td class form *}
			<tr>
        <td class="form" >
				{if $feature_help eq 'y'}<a href="{$helpurl}Wiki" target="tikihelp" class="tikihelp" title="{tr}Wiki{/tr}">{/if}
				{tr}Wiki{/tr}
				{if $feature_help eq 'y'}</a>{/if}
				:</td>
        <td ><input type="checkbox" name="feature_wiki"
            {if $feature_wiki eq 'y'}checked="checked"{/if}/></td>
{* here is the blank cell *}
        <td >&nbsp;</td>
{* here is the beginning of the new cell for blogs followed by a check box cell *}
				<td class="form" >
				{if $feature_help eq 'y'}<a href="{$helpurl}Blog" target="tikihelp" class="tikihelp" title="{tr}Wiki{/tr}">{/if}
				{tr}Blogs{/tr}
				{if $feature_help eq 'y'}</a>{/if}
				:</td>
        <td><input type="checkbox" name="feature_blogs"
            {if $feature_blogs eq 'y'}checked="checked"{/if}/></td>
      </tr>
{* end of the first row *}
			<tr>
        <td class="form">
				{if $feature_help eq 'y'}<a href="{$helpurl}ImageGallery" target="tikihelp" class="tikihelp" title="{tr}Image Galleries{/tr}">{/if}
				{tr}Image Galleries{/tr}
				{if $feature_help eq 'y'}</a>{/if}
				:</td>
        <td><input type="checkbox" name="feature_galleries"
            {if $feature_galleries eq 'y'}checked="checked"{/if}/></td>
        <td>&nbsp;</td>
        
				<td class="form">
				{if $feature_help eq 'y'}<a href="{$helpurl}FileGallery" target="tikihelp" class="tikihelp" title="{tr}File Galleries{/tr}">{/if}
				{tr}File Galleries{/tr}
				{if $feature_help eq 'y'}</a>{/if}
				:</td>
        <td><input type="checkbox" name="feature_file_galleries"
            {if $feature_file_galleries eq 'y'}checked="checked"{/if}/></td>
      </tr>
			
			<tr>
        <td class="form">
				{if $feature_help eq 'y'}<a href="{$helpurl}Article" target="tikihelp" class="tikihelp" title="{tr}Articles{/tr}">{/if}
				{tr}Articles{/tr}
				{if $feature_help eq 'y'}</a>{/if}
				:</td>
        <td><input type="checkbox" name="feature_articles"
            {if $feature_articles eq 'y'}checked="checked"{/if}/></td>
        <td>&nbsp;</td>
        <td class="form">
				{if $feature_help eq 'y'}<a href="{$helpurl}Forums" target="tikihelp" class="tikihelp" title="{tr}Forums{/tr}">{/if}
				{tr}Forums{/tr}
				{if $feature_help eq 'y'}</a>{/if}
				:</td>
        <td><input type="checkbox" name="feature_forums"
            {if $feature_forums eq 'y'}checked="checked"{/if}/></td>
      </tr>
			
			<tr>
        <td class="form">
				{if $feature_help eq 'y'}<a href="{$helpurl}Faq" target="tikihelp" class="tikihelp" title="{tr}FAQs{/tr}">{/if}
				{tr}FAQs{/tr}
				{if $feature_help eq 'y'}</a>{/if}
				:</td>
        <td><input type="checkbox" name="feature_faqs"
            {if $feature_faqs eq 'y'}checked="checked"{/if}/></td>
        <td>&nbsp;</td>
				
				<td class="form">
				{if $feature_help eq 'y'}<a href="{$helpurl}Shoutbox" target="tikihelp" class="tikihelp" title="{tr}Shoutbox{/tr}">{/if}
				{tr}Shoutbox{/tr}
				{if $feature_help eq 'y'}</a>{/if}
				:</td>
        <td><input type="checkbox" name="feature_shoutbox"
            {if $feature_shoutbox eq 'y'}checked="checked"{/if}/></td>
      </tr>
			
			<tr>
        <td class="form">
				{if $feature_help eq 'y'}<a href="{$helpurl}Chat" target="tikihelp" class="tikihelp" title="{tr}Chat{/tr}">{/if}
				{tr}Chat{/tr}
				{if $feature_help eq 'y'}</a>{/if}
				:</td>
        <td><input type="checkbox" name="feature_chat"
            {if $feature_chat eq 'y'}checked="checked"{/if}/></td>
        <td>&nbsp;</td>
        
				<td class="form">
				{if $feature_help eq 'y'}<a href="{$helpurl}Trackers" target="tikihelp" class="tikihelp" title="{tr}Trackers{/tr}">{/if}
				{tr}Trackers{/tr}
				{if $feature_help eq 'y'}</a>{/if}
				:</td>
        <td><input type="checkbox" name="feature_trackers"
            {if $feature_trackers eq 'y'}checked="checked"{/if}/></td>
      </tr>
			
			<tr>
        <td class="form">
				{if $feature_help eq 'y'}<a href="{$helpurl}Directory" target="tikihelp" class="tikihelp" title="{tr}Directory{/tr}">{/if}
				{tr}Directory{/tr}
				{if $feature_help eq 'y'}</a>{/if}
				:</td>
        <td><input type="checkbox" name="feature_directory"
            {if $feature_directory eq 'y'}checked="checked"{/if}/></td>
        <td>&nbsp;</td>
        
				<td class="form">
				{if $feature_help eq 'y'}<a href="{$helpurl}Webmail" target="tikihelp" class="tikihelp" title="{tr}Webmail{/tr}">{/if}
				{tr}Webmail{/tr} :
				{if $feature_help eq 'y'}</a>{/if}
				</td>
        <td><input type="checkbox" name="feature_webmail"
            {if $feature_webmail eq 'y'}checked="checked"{/if}/></td>
      </tr>
			
			<tr>
        <td class="form">
				{if $feature_help eq 'y'}<a href="{$helpurl}Newsreader" target="tikihelp" class="tikihelp" title="{tr}Newsreader{/tr}">{/if}
				{tr}Newsreader{/tr} :
				{if $feature_help eq 'y'}</a>{/if}
				</td>
        <td><input type="checkbox" name="feature_newsreader"
            {if $feature_newsreader eq 'y'}checked="checked"{/if}/></td>
        <td>&nbsp;</td>
        
				<td class="form">
				{if $feature_help eq 'y'}<a href="{$helpurl}Surveys" target="tikihelp" class="tikihelp" title="{tr}Surveys{/tr}">{/if}
				{tr}Surveys{/tr}
				{if $feature_help eq 'y'}</a>{/if}
				:</td>
        <td><input type="checkbox" name="feature_surveys"
            {if $feature_surveys eq 'y'}checked="checked"{/if}/></td>
      </tr>
			
			<tr>
        <td class="form">
				{if $feature_help eq 'y'}<a href="{$helpurl}Polls" target="tikihelp" class="tikihelp" title="{tr}Polls{/tr}">{/if}
				{tr}Polls{/tr}
				{if $feature_help eq 'y'}</a>{/if}
				:</td>
        <td><input type="checkbox" name="feature_polls"
            {if $feature_polls eq 'y'}checked="checked"{/if}/></td>
        <td>&nbsp;</td>
        
				<td class="form">
				{if $feature_help eq 'y'}<a href="{$helpurl}Ephemerides" target="tikihelp" class="tikihelp" title="{tr}Ephemerides{/tr}">{/if}
				{tr}Ephemerides{/tr}
				{if $feature_help eq 'y'}</a>{/if}
				:</td>
        <td><input type="checkbox" name="feature_eph"
            {if $feature_eph eq 'y'}checked="checked"{/if}/></td>
      </tr>
			
			<tr>
        <td class="form">
				{if $feature_help eq 'y'}<a href="{$helpurl}Quizzes" target="tikihelp" class="tikihelp" title="{tr}Quizzes{/tr}">{/if}
				{tr}Quizzes{/tr}
				{if $feature_help eq 'y'}</a>{/if}
				:</td>
        <td><input type="checkbox" name="feature_quizzes"
            {if $feature_quizzes eq 'y'}checked="checked"{/if}/></td>
        <td>&nbsp;</td>
        
				<td class="form">
	        	{if $feature_help eq 'y'}<a href="{$helpurl}Search" target="tikihelp" class="tikihelp" title="{tr}Search{/tr}">{/if}				
				{tr}Search{/tr}
				{if $feature_help eq 'y'}</a>{/if}
				:</td>
        <td ><input type="checkbox" name="feature_search"
            {if $feature_search eq 'y'}checked="checked"{/if}/></td>
      </tr>
			
			<tr>
        <td class="form">
				{if $feature_help eq 'y'}<a href="{$helpurl}FeaturedLinks" target="tikihelp" class="tikihelp" title="{tr}Featured Help{/tr}">{/if}
				{tr}Featured links{/tr}
				{if $feature_help eq 'y'}</a>{/if}
				:</td>
        <td><input type="checkbox" name="feature_featuredLinks"
            {if $feature_featuredLinks eq 'y'}checked="checked"{/if}/></td>
        <td>&nbsp;</td>
        <td class="form">
        	{if $feature_help eq 'y'}<a href="{$helpurl}Banners" target="tikihelp" class="tikihelp" title="{tr}Banners{/tr}">{/if}
        	{tr}Banners{/tr}
        	{if $feature_help eq 'y'}</a>{/if}
        	:</td>
        <td><input type="checkbox" name="feature_banners"
            {if $feature_banners eq 'y'}checked="checked"{/if}/></td>
      </tr>
			<tr>
        <td class="form">
	        	{if $feature_help eq 'y'}<a href="{$helpurl}Games" target="tikihelp" class="tikihelp" title="{tr}Games{/tr}">{/if}
	        	{tr}Games{/tr}
	        	{if $feature_help eq 'y'}</a>{/if}
	        	:</td>
        <td><input type="checkbox" name="feature_games" 
            {if $feature_games eq 'y'}checked="checked"{/if}/></td>
        <td>&nbsp;</td>
        <td class="form">
	        	{if $feature_help eq 'y'}<a href="{$helpurl}Workflow" target="tikihelp" class="tikihelp" title="{tr}Workflow{/tr}">{/if}
        		{tr}Workflow engine{/tr}
        		{if $feature_help eq 'y'}</a>{/if}
        		:</td>
        <td><input type="checkbox" name="feature_workflow"
            {if $feature_workflow eq 'y'}checked="checked"{/if}/></td>
      </tr>
			
			<tr>
        <td class="form">
		        	{if $feature_help eq 'y'}<a href="{$helpurl}Newsletters" target="tikihelp" class="tikihelp" title="{tr}Newsletters{/tr}">{/if}
	        		{tr}Newsletters{/tr}
	        		{if $feature_help eq 'y'}</a>{/if}
	        		:</td>
        <td><input type="checkbox" name="feature_newsletters"
            {if $feature_newsletters eq 'y'}checked="checked"{/if}/></td>
        <td>&nbsp;</td>
        
				<td class="form">
		        	{if $feature_help eq 'y'}<a href="{$helpurl}LiveSupport" target="tikihelp" class="tikihelp" title="{tr}Live Support{/tr}">{/if}
					{tr}Live support system{/tr}
					{if $feature_help eq 'y'}</a>{/if}
					:</td>
        <td><input type="checkbox" name="feature_live_support"
            {if $feature_live_support eq 'y'}checked="checked"{/if}/></td>

      </tr><tr>
{* beginning of mini calendar function option *}
        <td class="form">
	        	{if $feature_help eq 'y'}<a href="{$helpurl}MiniCalendar" target="tikihelp" class="tikihelp" title="{tr}Mini Calendar{/tr}">{/if}
        		{tr}Mini Calendar{/tr}
        		{if $feature_help eq 'y'}</a>{/if}
        		:</td>
        <td><input type="checkbox" name="feature_minical"
            {if $feature_minical eq 'y'}checked="checked"{/if}/></td>
        <td>&nbsp;</td>
{* here is the categories option *}
        <td class="form">
	        	{if $feature_help eq 'y'}<a href="{$helpurl}Categories" target="tikihelp" class="tikihelp" title="{tr}Categories{/tr}">{/if}
        		{tr}Categories{/tr}
        		{if $feature_help eq 'y'}</a>{/if}
        		:</td>
        <td><input type="checkbox" name="feature_categories"
                {if $feature_categories eq 'y'}checked="checked"{/if}/></td>
      </tr>
{* Calendar option on left side of first row of table*}
<tr>

                                                                                                       
<td class="form">
	        	{if $feature_help eq 'y'}<a href="{$helpurl}HelpSystemFutureConcept" target="tikihelp" class="tikihelp" title="{tr}Help System{/tr}">{/if}
        		{tr}Help System{/tr}
        		{if $feature_help eq 'y'}</a>{/if}
        		:</td>
        <td><input type="checkbox" name="feature_help"
            {if $feature_help eq 'y'}checked="checked"{/if}/></td>
        <td>&nbsp;</td>

        <td class="form">

	        	{if $feature_help eq 'y'}<a href="{$helpurl}TikiMap" target="tikihelp" class="tikihelp" title="{tr}Maps{/tr}">{/if}
        		{tr}Maps{/tr}
        		{if $feature_help eq 'y'}</a>{/if}
        		:</td>
        <td><input type="checkbox" name="feature_maps"
            {if $feature_maps eq 'y'}checked="checked"{/if}/></td>

      </tr>
      <tr>

        <td class="form">
	        	{if $feature_help eq 'y'}<a href="{$helpurl}ShowCategoryPath" target="tikihelp" class="tikihelp" title="{tr}Show Category Path{/tr}">{/if}
        		{tr}Show Category Path{/tr}
        		{if $feature_help eq 'y'}</a>{/if}
        		:</td>
        <td><input type="checkbox" name="feature_categorypath"
            {if $feature_categorypath eq 'y'}checked="checked"{/if}/></td>

        <td>&nbsp;</td>

        <td class="form">
	        	{if $feature_help eq 'y'}<a href="{$helpurl}Babelfish" target="tikihelp" class="tikihelp" title="{tr}Show Babelfish Translation URLs{/tr}">{/if}
        		{tr}Show Babelfish Translation URLs{/tr}
        		{if $feature_help eq 'y'}</a>{/if}
        		:</td>
        <td><input type="checkbox" name="feature_babelfish"
            {if $feature_babelfish eq 'y'}checked="checked"{/if}/></td>

      </tr>
      <tr>
        
        <td class="form">
	        	{if $feature_help eq 'y'}<a href="{$helpurl}ShowCategoryObjects" target="tikihelp" class="tikihelp" title="{tr}Show Category Objects{/tr}">{/if}
        		{tr}Show Category Objects{/tr}
        		{if $feature_help eq 'y'}</a>{/if}
        		:</td>
        <td><input type="checkbox" name="feature_categoryobjects"
            {if $feature_categoryobjects eq 'y'}checked="checked"{/if}/></td>

        <td>&nbsp;</td>

        <td class="form">
	        	{if $feature_help eq 'y'}<a href="{$helpurl}Babelfish" target="tikihelp" class="tikihelp" title="{tr}Show Babelfish Translation Logo{/tr}">{/if}
        		{tr}Show Babelfish Translation Logo{/tr}
        		{if $feature_help eq 'y'}</a>{/if}
        		:</td>
        <td><input type="checkbox" name="feature_babelfish_logo"
            {if $feature_babelfish_logo eq 'y'}checked="checked"{/if}/></td>

      </tr>
      <tr>
        
        <td class="form">
	        	{if $feature_help eq 'y'}<a href="{$helpurl}ShowModuleControls" target="tikihelp" class="tikihelp" title="{tr}Show Module Controls{/tr}">{/if}
        		{tr}Show Module Controls{/tr}
        		{if $feature_help eq 'y'}</a>{/if}
        		:</td>
        <td><input type="checkbox" name="feature_modulecontrols"
            {if $feature_modulecontrols eq 'y'}checked="checked"{/if}/></td>

        <td>&nbsp;</td>

        <td class="form">
					{if $feature_help eq 'y'}<a href="{$helpurl}Calendar" target="tikihelp" class="tikihelp" title="{tr}Calendar{/tr}">{/if}
					{tr}Tiki Calendar{/tr}
					{if $feature_help eq 'y'}</a>{/if}
					:</td>
				<td><input type="checkbox" name="feature_calendar"
					{if $feature_calendar eq 'y'}checked="checked"{/if}/></td>

      </tr>
            <tr>
        
        <td class="form">
	        	{if $feature_help eq 'y'}<a href="{$helpurl}AutoLinks" target="tikihelp" class="tikihelp" title="{tr}AutoLinks{/tr}">{/if}
        		{tr}AutoLinks{/tr}
        		{if $feature_help eq 'y'}</a>{/if}
        		:</td>
        <td><input type="checkbox" name="feature_autolinks"
            {if $feature_autolinks eq 'y'}checked="checked"{/if}/></td>

        <td>&nbsp;</td>
        <td class="form">
            	{if $feature_help eq 'y'}<a href="{$helpurl}SmartyTplEditingDev" target="tikihelp" class="tikihelp" title="{tr}Template Viewing{/tr}">{/if}
        		{tr}Tiki Template Viewing{/tr}
        		{if $feature_help eq 'y'}</a>{/if}
        		:</td>
        <td><input type="checkbox" name="feature_view_tpl"
            {if $feature_view_tpl eq 'y'}checked="checked"{/if}/></td>
        
      </tr>

            <tr>
        
        <td class="form">
	        	{if $feature_help eq 'y'}<a href="{$helpurl}TikiIntegrator" target="tikihelp" class="tikihelp" title="{tr}Integrator{/tr}">{/if}
        		{tr}Integrator{/tr}
        		{if $feature_help eq 'y'}</a>{/if}
        		:</td>
        <td><input type="checkbox" name="feature_integrator"
            {if $feature_integrator eq 'y'}checked="checked"{/if}/></td>

        <td>&nbsp;</td>
        <td class="form">
				{if $feature_help eq 'y'}<a href="{$helpurl}PhpLayers" target="tikihelp" class="tikihelp" title="{tr}PHPLayers{/tr}">{/if}
				{tr}PhpLayers Dynamic menus{/tr}
				{if $feature_help eq 'y'}</a>{/if}
				:</td>
        <td><input type="checkbox" name="feature_phplayers" {if $feature_phplayers eq 'y'}checked="checked"{/if}/></td>

      </tr>

            <tr>
        
        <td class="form">
	        	{if $feature_help eq 'y'}<a href="{$helpurl}JsCalendar" target="tikihelp" class="tikihelp" title="{tr}JsCalendar{/tr}">{/if}
        		{tr}JsCalendar{/tr}
        		{if $feature_help eq 'y'}</a>{/if}
        		:</td>
        <td><input type="checkbox" name="feature_jscalendar" {if $feature_jscalendar eq 'y'}checked="checked"{/if}/></td>

        <td>&nbsp;</td>
        <td class="form">
				{if $feature_help eq 'y'}<a href="{$helpurl}Tabs" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}:
{tr}TikiTabs{/tr}">{/if}
				{tr}Use Tabs{/tr}
				{if $feature_help eq 'y'}</a>{/if}
				:</td>
        <td><input type="checkbox" name="feature_tabs" {if $feature_tabs eq 'y'}checked="checked"{/if}/></td>

      </tr>

            <tr>
        
        <td class="form">
	        	{if $feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=Homework" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}Homework{/tr}">{/if}
        		{tr}Homework{/tr}
        		{if $feature_help eq 'y'}</a>{/if}
        		:</td>
        <td><input type="checkbox" name="feature_homework" {if $feature_homework eq 'y'}checked="checked"{/if}/></td>

	<td>&nbsp;</td>
	<td class="form">
			{if $feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=Jukebox" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}Jukebox{/tr}">{/if}
			{tr}Jukebox{/tr}
			{if $feature_help eq 'y'}</a>{/if}
			:</td>
	<td><input type="checkbox" name="feature_jukebox" {if $feature_jukebox eq 'y'}checked="checked"{/if} /></td>



      </tr>
      <tr>
        
        <td class="form">
	        	{if $feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=TikiSheet" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}TikiSheet{/tr}">{/if}
        		{tr}Tiki Sheet{/tr}
        		{if $feature_help eq 'y'}</a>{/if}
        		:</td>
        <td><input type="checkbox" name="feature_sheet" {if $feature_sheet eq 'y'}checked="checked"{/if}/></td>
	<td>&nbsp;</td>
	<td class="form">{if $feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=TikiMultilingual" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}Multilingual{/tr}">{/if}{tr}Multilingual{/tr}{if $feature_help eq 'y'}</a>{/if}:</td>
	<td><input type="checkbox" name="feature_multilingual" {if $feature_multilingual eq 'y'}checked="checked"{/if}/
	</td>



      </tr>
      <tr>
        
        <td class="form">
	        	{if $feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=FriendshipNetwork" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}Friendship Network{/tr}">{/if}
        		{tr}Friendship Network{/tr}
        		{if $feature_help eq 'y'}</a>{/if}
        		:</td>
        <td><input type="checkbox" name="feature_friends" {if $feature_friends eq 'y'}checked="checked"{/if}/></td>
	<td>&nbsp;</td>
	<td class="form">{if $feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=ScoreSystem" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}Score{/tr}">{/if}{tr}Score{/tr}{if $feature_help eq 'y'}</a>{/if}:</td>
	<td><input type="checkbox" name="feature_score" {if $feature_score eq 'y'}checked="checked"{/if}/
	</td>



      </tr>
			<tr>
				<td class="form">
	      	{if $feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=TikiSiteIdentity" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}Site Logo and Identity{/tr}">{/if}{tr}Site Logo and Identity{/tr}{if $feature_help eq 'y'}</a>{/if}&nbsp;:
				</td>
        <td>
					<input type="checkbox" name="feature_siteidentity" {if $feature_siteidentity eq 'y'}checked="checked"{/if}/>
				</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>



{* ---------- Content features ------------ *}


      <tr>

        <td class="heading" colspan="5"
            align="center">{tr}Content Features{/tr}</td>

      </tr><tr>
        <td class="form">
	        	{if $feature_help eq 'y'}<a href="{$helpurl}Hotwords" target="tikihelp" class="tikihelp" title="{tr}Hotwords{/tr}">{/if}
        		{tr}Hotwords{/tr}
        		{if $feature_help eq 'y'}</a>{/if}
        		:</td>
        <td><input type="checkbox" name="feature_hotwords" 
            {if $feature_hotwords eq 'y'}checked="checked"{/if}/></td>
        <td>&nbsp;</td>
        <td class="form">
	        	{if $feature_help eq 'y'}<a href="{$helpurl}EditTemplates" target="tikihelp" class="tikihelp" title="{tr}Edit Templates{/tr}">{/if}
        		{tr}Edit Templates{/tr}
        		{if $feature_help eq 'y'}</a>{/if}
        		:</td>
        <td><input type="checkbox" name="feature_edit_templates"
            {if $feature_edit_templates eq 'y'}checked="checked"{/if}/></td>
      </tr><tr>
        <td class="form">
	        	{if $feature_help eq 'y'}<a href="{$helpurl}HotwordsInNewWindows" target="tikihelp" class="tikihelp" title="{tr}Hotwords in New Windows{/tr}">{/if}
        		{tr}Hotwords in New Windows{/tr}
        		{if $feature_help eq 'y'}</a>{/if}
        		:</td>
        <td><input type="checkbox" name="feature_hotwords_nw"
            {if $feature_hotwords_nw eq 'y'}checked="checked"{/if}/></td>
        <td>&nbsp;</td>
        <td class="form">
	        	{if $feature_help eq 'y'}<a href="{$helpurl}EditCss" target="tikihelp" class="tikihelp" title="{tr}Edit CSS{/tr}">{/if}
        		{tr}Edit CSS{/tr}
        		{if $feature_help eq 'y'}</a>{/if}
        		:</td>
        <td><input type="checkbox" name="feature_editcss"
            {if $feature_editcss eq 'y'}checked="checked"{/if}/></td>
      </tr><tr>
        <td class="form">
	        	{if $feature_help eq 'y'}<a href="{$helpurl}CustomHome" target="tikihelp" class="tikihelp" title="{tr}Custom Home{/tr}">{/if}
        		{tr}Custom Home{/tr}
        		{if $feature_help eq 'y'}</a>{/if}
        		:</td>
        <td><input type="checkbox" name="feature_custom_home"
            {if $feature_custom_home eq 'y'}checked="checked"{/if}/></td>
        <td>&nbsp;</td>
        <td class="form">
	        	{if $feature_help eq 'y'}<a href="{$helpurl}HtmlPages" target="tikihelp" class="tikihelp" title="{tr}HTML Pages{/tr}">{/if}
        		{tr}HTML pages{/tr}
        		{if $feature_help eq 'y'}</a>{/if}
        		:</td>
        <td><input type="checkbox" name="feature_html_pages"
            {if $feature_html_pages eq 'y'}checked="checked"{/if}/></td>
      </tr><tr>
        <td class="form">
	        	{if $feature_help eq 'y'}<a href="{$helpurl}Drawings" target="tikihelp" class="tikihelp" title="{tr}Drawings{/tr}">{/if}
				{tr}Drawings{/tr}
				{if $feature_help eq 'y'}</a>{/if}
				:</td>
        <td><input type="checkbox" name="feature_drawings"
            {if $feature_drawings eq 'y'}checked="checked"{/if}/></td>
        <td>&nbsp;</td>
        <td class="form">
	        	{if $feature_help eq 'y'}<a href="{$helpurl}DynamicContent" target="tikihelp" class="tikihelp" title="{tr}Dynamic Content System{/tr}">{/if}
        		{tr}Dynamic Content System{/tr}
        		{if $feature_help eq 'y'}</a>{/if}
        		:</td>
        <td><input type="checkbox" name="feature_dynamic_content"
            {if $feature_dynamic_content eq 'y'}checked="checked"{/if}/></td>
      </tr><tr>
        <td class="form">
	        	{if $feature_help eq 'y'}<a href="{$helpurl}Charts" target="tikihelp" class="tikihelp" title="{tr}Charts{/tr}">{/if}
		        {tr}Charts{/tr}
		        {if $feature_help eq 'y'}</a>{/if}
		        :</td>
        <td><input type="checkbox" name="feature_charts"
            {if $feature_charts eq 'y'}checked="checked"{/if}/></td>
        <td>&nbsp;</td>
        <td class="form">
	        	{if $feature_help eq 'y'}<a href="{$helpurl}AllowSmileys" target="tikihelp" class="tikihelp" title="{tr}Allow Smileys{/tr}">{/if}        
		        {tr}Allow Smileys{/tr}
		        {if $feature_help eq 'y'}</a>{/if}
		        :</td>
        <td><input type="checkbox" name="feature_smileys"
            {if $feature_smileys eq 'y'}checked="checked"{/if}/></td>
      </tr><tr>

        <td class="heading" colspan="5" 
            align="center">{tr}Administration Features{/tr}</td>
      </tr><tr>
        <td class="form">
	        	{if $feature_help eq 'y'}<a href="{$helpurl}BanningSystem" target="tikihelp" class="tikihelp" title="{tr}Banning System{/tr}">{/if}
        		{tr}Banning system{/tr}
        		{if $feature_help eq 'y'}</a>{/if}
        		:</td>
        <td><input type="checkbox" name="feature_banning"
            {if $feature_banning eq 'y'}checked="checked"{/if}/></td>
        <td>&nbsp;</td>
        <td class="form">
	        	{if $feature_help eq 'y'}<a href="{$helpurl}DebuggerConsole" target="tikihelp" class="tikihelp" title="{tr}Debugger Console{/tr}">{/if}
        		{tr}Debugger Console{/tr}
        		{if $feature_help eq 'y'}</a>{/if}
        		:</td>
        <td><input type="checkbox" name="feature_debug_console"
            {if $feature_debug_console eq 'y'}checked="checked"{/if}/></td>
      </tr><tr>
        <td class="form">
	        	{if $feature_help eq 'y'}<a href="{$helpurl}Stats" target="tikihelp" class="tikihelp" title="{tr}Stats{/tr}">{/if}
        		{tr}Stats{/tr}
        		{if $feature_help eq 'y'}</a>{/if}
        		:</td>
        <td><input type="checkbox" name="feature_stats"
            {if $feature_stats eq 'y'}checked="checked"{/if}/></td>
        <td>&nbsp;</td>
        <td class="form">
	        	{if $feature_help eq 'y'}<a href="{$helpurl}Communications" target="tikihelp" class="tikihelp" title="{tr}Communications (send/receive objects){/tr}">{/if}
		        {tr}Communications (send/receive objects){/tr}
		        {if $feature_help eq 'y'}</a>{/if}
		        :</td>
        <td><input type="checkbox" name="feature_comm"
            {if $feature_comm eq 'y'}checked="checked"{/if}/></td>
      </tr><tr>
        <td class="form">
	        	{if $feature_help eq 'y'}<a href="{$helpurl}ThemeControl" target="tikihelp" class="tikihelp" title="{tr}Theme Control{/tr}">{/if}
        		{tr}Theme Control{/tr}
        		{if $feature_help eq 'y'}</a>{/if}
        		:</td>
        <td><input type="checkbox" name="feature_theme_control"
            {if $feature_theme_control eq 'y'}checked="checked"{/if}/></td>
        <td>&nbsp;</td>
        <td class="form">
	        	{if $feature_help eq 'y'}<a href="{$helpurl}XmlrpcApi" target="tikihelp" class="tikihelp" title="{tr}XMLRPC API{/tr}">{/if}
        		{tr}XMLRPC API{/tr}
        		{if $feature_help eq 'y'}</a>{/if}
        		:</td>
        <td><input type="checkbox" name="feature_xmlrpc"
            {if $feature_xmlrpc eq 'y'}checked="checked"{/if}/></td>
      </tr><tr>
        <td class="form">
	        	{if $feature_help eq 'y'}<a href="{$helpurl}RefererStats" target="tikihelp" class="tikihelp" title="{tr}Referer Stats{/tr}">{/if}
        		{tr}Referer Stats{/tr}
        		{if $feature_help eq 'y'}</a>{/if}
        		:</td>
        <td><input type="checkbox" name="feature_referer_stats"
            {if $feature_referer_stats eq 'y'}checked="checked"{/if}/></td>
        <td>&nbsp;</td>
        <td class="form">
	        	{if $feature_help eq 'y'}<a href="{$helpurl}ContactUs" target="tikihelp" class="tikihelp" title="{tr}Contact Us{/tr}">{/if}
        		{tr}Contact Us{/tr}
        		{if $feature_help eq 'y'}</a>{/if}
        		:</td>
        <td><input type="checkbox" name="feature_contact"
            {if $feature_contact eq 'y'}checked="checked"{/if}/></td>
        </tr><tr>
        <td class="form">
	        	{if $feature_help eq 'y'}<a href="{$helpurl}ContactUs" target="tikihelp" class="tikihelp" title="{tr}Contact Us{/tr}">{/if}
        		{tr}Contact Us (Anonymous){/tr}
        		{if $feature_help eq 'y'}</a>{/if}
        		:</td>
        <td><input type="checkbox" name="contact_anon"
            {if $contact_anon eq 'y'}checked="checked"{/if}/></td>            
        <td>&nbsp;</td>
	<td class="form">
	        	{if $feature_help eq 'y'}<a href="{$helpurl}SearchStats" target="tikihelp" class="tikihelp" title="{tr}SearchStats{/tr}">{/if}
			{tr}Search stats{/tr}
			{if $feature_help eq 'y'}</a>{/if}
			:</td>
        <td><input type="checkbox" name="feature_search_stats"
            {if $feature_search_stats eq 'y'}checked="checked"{/if}/></td>

        </tr><tr>

        <td class="heading" colspan="5"
            align="center">{tr}User Features{/tr}</td>
      </tr><tr>
        <td class="form">
	        	{if $feature_help eq 'y'}<a href="{$helpurl}UserPreferences" target="tikihelp" class="tikihelp" title="{tr}User Preferences Screen{/tr}">{/if}
        		{tr}User Preferences Screen{/tr}
        		{if $feature_help eq 'y'}</a>{/if}
        		:</td>
        <td><input type="checkbox" name="feature_userPreferences"
            {if $feature_userPreferences eq 'y'}checked="checked"{/if}/></td>
        <td>&nbsp;</td>
        <td class="form">
	        	{if $feature_help eq 'y'}<a href="{$helpurl}UsersConfigureModules" target="tikihelp" class="tikihelp" title="{tr}Users can Configure Modules{/tr}">{/if}
        		{tr}Users can Configure Modules{/tr}
        		{if $feature_help eq 'y'}</a>{/if}
        		:</td>
        <td><input type="checkbox" name="user_assigned_modules"
            {if $user_assigned_modules eq 'y'}checked="checked"{/if}/></td>
      </tr><tr>
        <td class="form">
	        	{if $feature_help eq 'y'}<a href="{$helpurl}UserBookmarks" target="tikihelp" class="tikihelp" title="{tr}User Bookmarks{/tr}">{/if}
        		{tr}User Bookmarks{/tr}
        		{if $feature_help eq 'y'}</a>{/if}
        		:</td>
        <td><input type="checkbox" name="feature_user_bookmarks"
            {if $feature_user_bookmarks eq 'y'}checked="checked"{/if}/></td>
        <td>&nbsp;</td>
        <td class="form">
	        	{if $feature_help eq 'y'}<a href="{$helpurl}UserMenu" target="tikihelp" class="tikihelp" title="{tr}User Menu{/tr}">{/if}
        		{tr}User Menu{/tr}
        		{if $feature_help eq 'y'}</a>{/if}
        		:</td>
        <td><input type="checkbox" name="feature_usermenu"
            {if $feature_usermenu eq 'y'}checked="checked"{/if}/></td>
      </tr><tr>
        <td class="form">
	        	{if $feature_help eq 'y'}<a href="{$helpurl}UserWatches" target="tikihelp" class="tikihelp" title="{tr}User Watches{/tr}">{/if}
        		{tr}User Watches{/tr}
        		{if $feature_help eq 'y'}</a>{/if}
        		:</td>
        <td><input type="checkbox" name="feature_user_watches"
            {if $feature_user_watches eq 'y'}checked="checked"{/if}/></td>
        <td>&nbsp;</td>
        <td class="form">
	        	{if $feature_help eq 'y'}<a href="{$helpurl}UserMessages" target="tikihelp" class="tikihelp" title="{tr}User Messages{/tr}">{/if}
        		{tr}User Messages{/tr}
        		{if $feature_help eq 'y'}</a>{/if}
        		:</td>
        <td><input type="checkbox" name="feature_messages"
            {if $feature_messages eq 'y'}checked="checked"{/if}/></td>
      </tr><tr>
        <td class="form">
	        	{if $feature_help eq 'y'}<a href="{$helpurl}UserTasks" target="tikihelp" class="tikihelp" title="{tr}User Tasks{/tr}">{/if}
        		{tr}User Tasks{/tr}
        		{if $feature_help eq 'y'}</a>{/if}
        		:</td>
        <td><input type="checkbox" name="feature_tasks"
            {if $feature_tasks eq 'y'}checked="checked"{/if}/></td>
        <td>&nbsp;</td>
        <td class="form">
	        	{if $feature_help eq 'y'}<a href="{$helpurl}UserNotepad" target="tikihelp" class="tikihelp" title="{tr}User Notepad{/tr}">{/if}
        		{tr}User Notepad{/tr}
        		{if $feature_help eq 'y'}</a>{/if}
        		:</td>
        <td><input type="checkbox" name="feature_notepad"
            {if $feature_notepad eq 'y'}checked="checked"{/if}/></td>
      </tr><tr>
        <td class="form">
	        	{if $feature_help eq 'y'}<a href="{$helpurl}UserFiles" target="tikihelp" class="tikihelp" title="{tr}User Files{/tr}">{/if}
        		{tr}User Files{/tr}
        		{if $feature_help eq 'y'}</a>{/if}
        		:</td>
        <td><input type="checkbox" name="feature_userfiles"
            {if $feature_userfiles eq 'y'}checked="checked"{/if}/></td>
        <td>&nbsp;</td>
        <td class="form" colspan="2">&nbsp;</td>
      </tr><tr>

        <td class="heading" colspan="5" 
            align="center">{tr}General Layout options{/tr}</td>
      </tr><tr>
        <td class="form">{tr}Left column{/tr} :</td>
        <td><input type="checkbox" name="feature_left_column"
            {if $feature_left_column eq 'y'}checked="checked"{/if}/></td>
        <td>&nbsp;</td>
        <td class="form">{tr}Layout per section{/tr} :</td>
        <td><input type="checkbox" name="layout_section"
            {if $layout_section eq 'y'}checked="checked"{/if}/></td>
      </tr><tr>
        <td class="form">{tr}Right column{/tr} :</td>
        <td><input type="checkbox" name="feature_right_column"
            {if $feature_right_column eq 'y'}checked="checked"{/if}/></td>
        <td>&nbsp;</td>
        <td align="center" colspan="2"><a href="tiki-admin_layout.php" 
            class="link">{tr}Admin layout per section{/tr}</a></td>
      </tr><tr>
        <td class="form">{tr}Top bar{/tr} :</td>
        <td><input type="checkbox" name="feature_top_bar"
            {if $feature_top_bar eq 'y'}checked="checked"{/if}/></td>
        <td colspan="3">&nbsp;</td>
      </tr><tr>
        <td class="form">{tr}Bottom bar{/tr} :</td>
        <td><input type="checkbox" name="feature_bot_bar"
            {if $feature_bot_bar eq 'y'}checked="checked"{/if}/></td>
        <td colspan="3">&nbsp;</td>
      </tr><tr>
        <td colspan="5" class="button">
          <input type="submit" name="features" value="{tr}Change preferences{/tr}" />
        </td>
      </tr></table>
    </div>
  </div>
</form>
