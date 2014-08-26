{* $Id$ *}

<div class="adminWizardIconleft"><img src="img/icons/large/wizard_profiles48x48.png" alt="{tr}Configuration Profiles Wizard{/tr}" title="{tr}Configuration Profiles Wizard{/tr}" /></div>
{tr}Each of these profiles create a working instance of some features, such as wiki structures, forums, trackers and wiki pages, customized for specific purposes{/tr}. <br><br>
{remarksbox type="warning" title="{tr}Warning{/tr}"}
    <div target="tikihelp" class="tikihelp" style="float:right" title="{tr}Demo Profiles:{/tr}
		{tr}They are initially intended for testing environments, so that, after you have played with the feature, you don't have to deal with removing the created objects, nor with restoring the potentially changed settings in your site{/tr}. 
		<br/><br/>
		{tr}Once you know what they do, you can also apply them in your production site, in order to have working templates of the underlying features, that you can further adapt to your site later on{/tr}.">
        <img src="img/icons/help.png" alt="" width="16" height="16" class="icon" />
    </div>
{tr}They are not to be initially applied in production environments since they cannot be easily reverted and changes and new objects in your site are created for real{/tr}
{/remarksbox}
<div class="adminWizardContent">
    <fieldset>
        <legend>{tr}Profiles:{/tr}</legend>
        <table style="width:100%">
            <tr>
                <td style="width:48%">
                    <b>{tr}Structured Master Documents{/tr}</b> (<a href="tiki-admin.php?profile=Structured+Master+Documents&show_details_for=Structured+Master+Documents&categories%5B%5D={$tikiMajorVersion}.x&repository=http%3a%2f%2fprofiles.tiki.org%2fprofiles&page=profiles&preloadlist=y&list=List#step2" target="_blank">{tr}apply profile now{/tr}</a>)
                    <br>
                    {tr}This profile will get you started with Wiki Structures, containing multiple wiki pages with a hierarchical order, like master documents from office suites, and more{/tr}.
                    <br/><a href="https://doc.tiki.org/Structures" target="tikihelp" class="tikihelp" title="{tr}Structured Master Documents{/tr}:
           	        {tr}More details{/tr}:
           	        <ul>
    		            <li>{tr}Many pages are pre-created to let you easily set up several wiki structures{/tr}</li>
    		            <li>{tr}A common navigation menu is created and shown at the top of pages in the structure{/tr}</li>
	                    <li>{tr}You can easily print (export) them all together in a single html{/tr}</li>
	                    <li>{tr}Permissions or Monitoring can be applied in bulk to the whole structure or substructures{/tr}</li>
	                </ul>
           	        {tr}Click to read more{/tr}">
                        <img src="img/icons/help.png" alt="" width="16" height="16" class="icon" />
                    </a>
                    <div style="display:block; margin-left:auto; margin-right:auto; width:202px;">
                        <a href="http://tiki.org/display588" class="internal" rel="box" title="{tr}Click to expand{/tr}">
                            <img src="img/profiles/profile_thumb_structured_master_documents.png"  width="200" style="display:block; margin-left:auto; margin-right:auto;border:1px solid darkgray;" alt="Click to expand" class="regImage pluginImg" title="{tr}Click to expand{/tr}" />
                        </a>
                        <div class="mini" style="width:100px;">
                            <div class="thumbcaption text-center">{tr}Click to expand{/tr}</div>
                        </div>
                    </div>
                <td style="width:4%">
                    &nbsp;
                </td>
                <td style="width:48%">
                    <b>{tr}Consensus Forums{/tr}</b> (<a href="tiki-admin.php?profile=Consensus+Forums&show_details_for=Consensus+Forums&categories%5B%5D={$tikiMajorVersion}.x&repository=http%3a%2f%2fprofiles.tiki.org%2fprofiles&page=profiles&preloadlist=y&list=List#step2" target="_blank">{tr}apply profile now{/tr}</a>)
                    <br>
                    {tr}This profile sets up the configuration needed to facilitate forums that help their users to seek consensus on the discussion topics held{/tr}.
                    <br/><a href="https://doc.tiki.org/Rating#Users_ratings_in_Forums" target="tikihelp" class="tikihelp" title="{tr}Users Ratings in Forums{/tr}:
           	        {tr}More details{/tr}:
        	        <ul>
	                    <li>{tr}Topics with less agreement can be easily identified from the topic list{/tr}</li>
    		            <li>{tr}The current rating of each user to the thread topic is shown each time, so that further attention and explanations can be given where needed to help reaching a higher degree of consensus{/tr}</li>
	                    <li>{tr}Replies can also be rated, but without affecting the topic rating average{/tr}</li>
	                    <li>{tr}Profile instructions are translated to several languages. Therefore, some settings related to internationalization of wiki pages are enabled by the profile{/tr}</li>
	                </ul>
           	        {tr}Click to read more{/tr}">
                        <img src="img/icons/help.png" alt="" width="16" height="16" class="icon" />
                    </a>
                    <div style="display:block; margin-left:auto; margin-right:auto; width:202px;">
                        <a href="http://tiki.org/display587" class="internal" rel="box" title="{tr}Click to expand{/tr}">
                            <img src="img/profiles/profile_thumb_consensus_forums.png"  width="200" style="display:block; margin-left:auto; margin-right:auto;border:1px solid darkgray;" alt="Click to expand" class="regImage pluginImg" title="{tr}Click to expand{/tr}" />
                        </a>
                        <div class="mini" style="width:100px;">
                            <div class="thumbcaption text-center">{tr}Click to expand{/tr}</div>
                        </div>
                    </div>
                    <br/>
                </td>
            </tr>
            <tr>
                <td style="width:48%">
                    <b>{tr}Barter Market{/tr}</b>  (<a href="tiki-admin.php?profile=Barter_Market&show_details_for=Barter_Market&categories%5B%5D={$tikiMajorVersion}.x&repository=http%3a%2f%2fprofiles.tiki.org%2fprofiles&page=profiles&preloadlist=y&list=List#step2" target="_blank">{tr}apply profile now{/tr}</a>)
                    <br>
                    {tr}This profile creates three trackers with some demo data to showcase a basic setup for a barter market of linked offers and wants of goods, services and knowledge.{/tr}
                    <br/><a href="http://profiles.tiki.org/Barter_Market" target="tikihelp" class="tikihelp" title="{tr}Barter Market{/tr}:
                    {tr}More details{/tr}:
                    <ul>
                            <li>{tr}minimal number of fields in these trackers, which can be extended{/tr}</li>
                            <li>{tr}tracker items are categorized{/tr}</li>
                            <li>{tr}a few modules added, including a wiki page menu{/tr}</li>
                            <li>{tr}best display if using just one column (right, for instance){/tr}</li>
                    </ul>
                    {tr}Click to read more{/tr}">
                        <img src="img/icons/help.png" alt="" width="16" height="16" class="icon" />
                    </a>
                    <div style="display:block; margin-left:auto; margin-right:auto; width:202px;">
                        <a href="http://tiki.org/display586" class="internal" rel="box" title="{tr}Click to expand{/tr}">
                            <img src="img/profiles/profile_thumb_barter_market.png"  width="200" style="display:block; margin-left:auto; margin-right:auto;border:1px solid darkgray;" alt="Click to expand" class="regImage pluginImg" title="{tr}Click to expand{/tr}" />
                        </a>
                        <div class="mini" style="width:100px;">
                            <div class="thumbcaption text-center">{tr}Click to expand{/tr}</div>
                        </div>
                    </div>
                </td>
                <td style="width:4%">
                    &nbsp;
                </td>
                <td style="width:48%">
                </td>
            </tr>
        </table>
    </fieldset>
    <br>
</div>

