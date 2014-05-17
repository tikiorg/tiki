{* $Id: profiles_demo_highly_specialized_confs.tpl 51152 2014-05-05 08:27:09Z xavidp $ *}

<div class="adminWizardIconleft"><img src="img/icons/large/wizard_profiles48x48.png" alt="{tr}Configuration Profiles Wizard{/tr}" title="{tr}Configuration Profiles Wizard{/tr}" /></div>
{tr}Some profiles are highly customized for very specialized use cases, and they are listed in this special category.{/tr} </br></br>
{remarksbox type="warning" title="{tr}Warning{/tr}"}
    <div target="tikihelp" class="tikihelp" style="float:right" title="{tr}Demo Profiles:{/tr}
		{tr}They are initially intended for testing environments, so that, after you have played with the feature, you don't have to deal with removing the created objects, nor with restoring the potentially changed settings in your site{/tr}.
		<br/><br/>
		{tr}Once you know what they do, you can also apply them in your production site, in order to have working templates of the underlying features, that you can further adapt to your site later on{/tr}.">
        <img src="img/icons/help.png" alt="" width="16" height="16" class="icon" />
    </div>
{tr}These configuration profiles <strong>require extra software</strong> to be installed in your server to function as expected{/tr}.
{tr}See details in the instructions page shown in your site once each profile is applied{/tr}.
{/remarksbox}
<div class="adminWizardContent">
<fieldset>
	<legend>{tr}Profiles:{/tr}</legend>
	<table style="width:100%">
	<tr>
        <td style="width:48%">
            <b>{tr}CartoGraf{/tr}</b> (<a href="tiki-admin.php?profile=CartoGraf&show_details_for=CartoGraf&categories%5B%5D={$tikiMajorVersion}.x&repository=http%3a%2f%2fprofiles.tiki.org%2fprofiles&page=profiles&preloadlist=y&list=List#step2" target="_blank">{tr}apply profile now{/tr}</a>)
            <br>
            {tr}CartoGraf is an interactive web-based mapping application to enhance learning in history and geography classes in high schools.{/tr}
            {tr}CartoGraf is a great example of how to use profiles in a general purpose app (Tiki) to make a very specific application.{/tr}
            <br/><a href="https://tv.tiki.org/CartoGraf"  target="tikihelp" class="tikihelp" title="{tr}CartoGraf{/tr}:
            {tr}This profile is using Tiki as Framework, with these details:{/tr}
            <ul>
                <li>{tr}It is mainly based on Maps, Drawings, PluginAppFrame and Trackers{/tr}</li>
                <li>{tr}It uses its own Cartograf style (downloaded aside){/tr}</li>
                <li>{tr}It allows custom markers for maps (placed in a file gallery){/tr}</li>
                <li>{tr}It is used in production at http://cartograf.recitus.qc.ca (in French){/tr}</li>
            </ul>
            {tr}Click to read more{/tr}">
                <img src="img/icons/help.png" alt="" width="16" height="16" class="icon" />
            </a>
            <div style="display:block; margin-left:auto; margin-right:auto; width:202px;">
                <a href="http://tiki.org/display545" class="internal" rel="box" title="{tr}Click to expand{/tr}">
                    <img src="img/profiles/profile_thumb_cartograf.png"  width="200" style="display:block; margin-left:auto; margin-right:auto;border:1px solid darkgray;" alt="Click to expand" class="regImage pluginImg" title="{tr}Click to expand{/tr}" />
                </a>
                <div class="mini" style="width:100px;">
                    <div class="thumbcaption text-center">{tr}Click to expand{/tr}</div>
                </div>
            </div>
            <br/>
       </td>
        <td style="width:4%">
            &nbsp;
        </td>
        <td style="width:48%">
            <b>{tr}R demo{/tr}</b> (<a href="tiki-admin.php?profile=R_demo&show_details_for=R_demo&categories%5B%5D={$tikiMajorVersion}.x&repository=http%3a%2f%2fprofiles.tiki.org%2fprofiles&page=profiles&preloadlist=y&list=List#step2" target="_blank">{tr}apply profile now{/tr}</a>)
            <br/>
            {tr}This profile demonstrates common uses of R language for statistics to produce advanced and/or interactive graphs and reports in web 2.0 pages.{/tr}
            <br/><a href="https://doc.tiki.org/PluginR"  target="tikihelp" class="tikihelp" title="{tr}R demo{/tr}:
            {tr}It uses the R Project for Statistical Computing software & PluginR in Tiki (both of which are not bundled within a default Tiki installation and they need to be installed in the same server as Tiki).{/tr}
            <br/><br/>
            {tr}The profile creates many demo pages, which comprise:{/tr}
	        <ul>
	            <li>{tr}Simple R syntax in wiki pages to produce interactive charts{/tr}</li>
                <li>{tr}Advanced usage to create full GUI for an R application{/tr}</li>
                <li>{tr}Many other examples of nice charts and reports that you can produce with this system{/tr}</li>
            </ul>
            {tr}Click to read more{/tr}">
            <img src="img/icons/help.png" alt="" width="16" height="16" class="icon" />
            </a>
            <a href="http://r.tiki.org" target="tikihelp" class="tikihelp" title="{tr}r.tiki.org site{/tr}:
        	    <em>{tr}See also{/tr} {tr}r.tiki.org live site{/tr}</em>
        	    <br/><br/>
                {tr}Click to read more{/tr}">
                <img src="img/icons/help.png" alt="" width="16" height="16" class="icon" />
            </a>
            <div style="display:block; margin-left:auto; margin-right:auto; width:202px;">
                <a href="http://r.tiki.org/tiki-download_file.php?fileId=23&display&max=800" class="internal" rel="box" title="{tr}Click to expand{/tr}">
                    <img src="img/profiles/profile_thumb_r_demo.png"  width="200" style="display:block; margin-left:auto; margin-right:auto;border:1px solid darkgray;" alt="Click to expand" class="regImage pluginImg" title="{tr}Click to expand{/tr}" />
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
            <b>{tr}R Heatmaps{/tr}</b> (<a href="tiki-admin.php?profile=R_Heatmaps&show_details_for=R_Heatmaps&categories%5B%5D={$tikiMajorVersion}.x&repository=http%3a%2f%2fprofiles.tiki.org%2fprofiles&page=profiles&preloadlist=y&list=List#step2" target="_blank">{tr}apply profile now{/tr}</a>)
            <br/>
            {tr}This profile adds a web interface for an R package called EasyHeatMap (link to package provided in the instructions page once the profile is applied).{/tr}
            {tr}This R package allows the creation and edition of HeatMap graphics (as used in Bioinformatics).{/tr}
            <br/><a href="http://ueb.vhir.org/tools/Heatmaps"  target="tikihelp" class="tikihelp" title="{tr}R Heatmaps{/tr}:
            {tr}This profile creates:{/tr}
	        <ul>
	            <li>{tr}A single wiki page where the whole heatmap generation can be run, to allow debugging your server installation of the required system and R packages if anything fails for you{/tr}</li>
                <li>{tr}A few wiki pages to list, view and edit analysis in R to produce HeatMaps for differential expression of gene sets{/tr}</li>
                <li>{tr}Example input files and default values provided as a happy path to produce your first Heatmaps{/tr}</li>
            </ul>
        	{tr}Click to read more{/tr}">
                <img src="img/icons/help.png" alt="" width="16" height="16" class="icon" />
            </a>
            <a href="https://doc.tiki.org/PluginR" target="tikihelp" class="tikihelp" title="{tr}Plugin R{/tr}:
        	    <em>{tr}See also{/tr} {tr}Plugin R in doc.tiki.org{/tr}</em>
        	    <br/><br/>
                {tr}Click to read more{/tr}">
                <img src="img/icons/help.png" alt="" width="16" height="16" class="icon" />
            </a>
            <div style="display:block; margin-left:auto; margin-right:auto; width:202px;">
                <a href="http://tiki.org/display546" class="internal" rel="box" title="{tr}Click to expand{/tr}">
                    <img src="img/profiles/profile_thumb_r_heatmaps.png"  width="200" style="display:block; margin-left:auto; margin-right:auto;border:1px solid darkgray;" alt="Click to expand" class="regImage pluginImg" title="{tr}Click to expand{/tr}" />
                </a>
                <div class="mini" style="width:100px;">
                    <div class="thumbcaption text-center">{tr}Click to expand{/tr}</div>
                </div>
            </div>
            <br/>
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

