{if $prefs.feature_help eq "y"}
  <div class="rbox" style="margin-top: 10px;">
    <div class="rbox-title" style="background-color: #eeee99; font-weight : bold; display : inline; padding : 0 10px;">{tr}Tip{/tr}</div>  
    <div class="rbox-data" style="padding: 2px 10px; background-color: #eeee99;">{tr}Freetags rock!{/tr}</div>
  </div>
  <br />
{/if}

  <div id ="Browsing" class="cbox">
    <div class="cbox-title">
      {tr}Freetag Browsing{/tr}
    </div>
    <div class="cbox-data">
    <form action="tiki-admin.php?page=freetags" method="post">
      <table class="admin">
        <tr>
          <td colspan="2" class="heading">{tr}General{/tr}</td>
        </tr>
        <tr>
          <td class="form">{tr}Show Tag Cloud{/tr}: </td>
          <td><input type="checkbox" name="freetags_browse_show_cloud" {if $prefs.freetags_browse_show_cloud eq 'y'}checked="checked"{/if} /></td>
        </tr>
        <tr>
          <td class="form">{tr}Random tag cloud colors (separated by comma){/tr}: </td>
          <td><input type="text" name="freetags_cloud_colors" value="{foreach from=$prefs.freetags_cloud_colors item=color naem=colors}{$color}{if !$smarty.foreach.colors.last},{/if}{/foreach}" /></td>
        </tr>
{*
        <tr>
          <td class="form">{tr}Tagging remembers tagger{/tr}: </td>
          <td><input type="checkbox" name="freetags_taggers"{if $prefs.freetags_taggers eq 'y'} checked="checked"{/if} /></td>
        <tr>
          <td class="form">{tr}Color of tags used by user{/tr}: </td>
          <td><input type="text" name="freetags_user_color" value="{$prefs.freetags_user_color}" /></td>
        <tr>
*}
          <td class="form">{tr}Preload freetag random search when arriving on <a href="tiki-browse_freetags.php">freetag search grid</a>{/tr}: </td>
          <td><input type="checkbox" name="freetags_preload_random search" {if $prefs.freetags_preload_random_search eq 'y'}checked="checked"{/if} /></td>
        </tr>
        <tr>
          <td class="form">{tr}Number of Tags to show in Cloud{/tr}: </td>
          <td><input type="text" name="freetags_browse_amount_tags_in_cloud" value="{$prefs.freetags_browse_amount_tags_in_cloud|escape}" size="3" /></td>
        </tr>
        <tr>
          <td class="form">{tr}Number of Tags to show in Tag Suggestions{/tr}: </td>
          <td><input type="text" name="freetags_browse_amount_tags_suggestion" value="{$prefs.freetags_browse_amount_tags_suggestion|escape}" size="3" /></td>
        </tr>
        <tr>
          <td class="form">{tr}Valid characters pattern{/tr}: </td>
          <td>
            <input type="text" id="freetags_normalized_valid_chars" name="freetags_normalized_valid_chars" value="{$prefs.freetags_normalized_valid_chars}" /><br />
			<a href='#Browsing' onclick="document.getElementById('freetags_normalized_valid_chars').value='a-zA-Z0-9';">{tr}Only accept alphanumeric ASCII freetags (no accents or special chars){/tr}</a><br />
			<a href='#Browsing' onclick="document.getElementById('freetags_normalized_valid_chars').value='';">{tr}Accept all{/tr}</a>
          </td>
        </tr>
        <tr>
          <td class="form">{tr}Only accept lowercase freetags{/tr}: </td>
          <td><input type="checkbox" name="freetags_lowercase_only" {if $prefs.freetags_lowercase_only eq 'y'}checked="checked"{/if} /></td>
        </tr>
        <tr>
          <td colspan="2" class="button"><input type="submit" name="freetagsfeatures" value="{tr}Change preferences{/tr}" /></td>
        </tr>
      </table>
    </form>
    </div>
  </div>
    
  <div class="cbox">
    <div class="cbox-title">
      {tr}Tag Management{/tr}
    </div>
    <div class="cbox-data">    
      <form action="tiki-admin.php?page=freetags" method="post">
        <table class="admin">               
          <tr>
            <td>{tr}Cleanup unused tags{/tr}:</td>
            <td>
              <input type="submit" value="{tr}cleanup{/tr}" name="cleanup" />     
            </td>
          </tr>
          
          <tr>
            <td colspan="2" class="heading">{tr}More Like This/Get Similar Module{/tr}</td>
          </tr>
        
          <tr>    
	    <td>{tr}More Like This algorithm{/tr}</td>
	    <td>
	      <select name="morelikethis_algorithm">			
	        <option value="basic"{if $prefs.morelikethis_algorithm eq 'basic' or ! $prefs.morelikethis_algorithm} selected="selected"{/if}>{tr}basic{/tr}</option>
	        <option value="weighted"{if $prefs.morelikethis_algorithm eq 'weighted'} selected="selected"{/if}>{tr}weighted{/tr}</option>
	      </select>			
	    </td>
	  </tr>
	
          <tr>
	    <td>{tr}Basic algorithm - Minimum amount of tags in common{/tr}</td>
	    <td>
	      <select name="morelikethis_basic_mincommon">
	        <option value="1"{if $prefs.morelikethis_basic_mincommon eq '1'} selected="selected"{/if}>{tr}1{/tr}</option>
	        <option value="2"{if $prefs.morelikethis_basic_mincommon eq '2' or ! $prefs.morelikethis_basic_mincommon} selected="selected"{/if}>{tr}2{/tr}</option>
	        <option value="3"{if $prefs.morelikethis_basic_mincommon eq '3'} selected="selected"{/if}>{tr}3{/tr}</option>
	        <option value="4"{if $prefs.morelikethis_basic_mincommon eq '4'} selected="selected"{/if}>{tr}4{/tr}</option>
	        <option value="5"{if $prefs.morelikethis_basic_mincommon eq '5'} selected="selected"{/if}>{tr}5{/tr}</option>
	        <option value="6"{if $prefs.morelikethis_basic_mincommon eq '6'} selected="selected"{/if}>{tr}6{/tr}</option>
	        <option value="7"{if $prefs.morelikethis_basic_mincommon eq '7'} selected="selected"{/if}>{tr}7{/tr}</option>
	        <option value="8"{if $prefs.morelikethis_basic_mincommon eq '8'} selected="selected"{/if}>{tr}8{/tr}</option>
	        <option value="9"{if $prefs.morelikethis_basic_mincommon eq '9'} selected="selected"{/if}>{tr}9{/tr}</option>
	      </select>			
	    </td>		
	  </tr>
	
          <tr>
	    <td colspan="2" class="button"><input type="submit" name="morelikethisoptions" value="{tr}Change preferences{/tr}"/></td>
	  </tr>
        </table>    
      </form>
  </div>
</div>

{if $prefs.feature_morcego eq "y"}
  <div class="cbox">
    <div class="cbox-title">
    {tr}Freetag 3D Browser Configuration{/tr}
    </div>
    <div class="cbox-data">
    <form action="tiki-admin.php?page=freetags" method="post">
    <table class="admin">
      <tr>
        <td colspan="2" class="heading">{tr}General{/tr}</td>
      </tr>
      <tr>
        <td class="form">{tr}Enable freetags 3D browser{/tr}:</td>
        <td><input type="checkbox" name="freetags_feature_3d" {if $prefs.freetags_feature_3d eq 'y'}checked="checked"{/if}/></td>
      </tr>
{*      <tr>
        <td class="form">{tr}Load page on navigation{/tr}: </td>
	<td><input type="checkbox" name="freetags_3d_autoload" value="true" {if $prefs.freetags_3d_missing_page_color eq 'true'}checked="checked"{/if} /></td>
      </tr> *}
      <tr>
        <td class="form">{tr}Browser width{/tr}: </td>
	<td><input type="text" name="freetags_3d_width" value="{$prefs.freetags_3d_width|escape}" size="3" /></td>
      </tr>
      <tr>
        <td class="form">{tr}Browser height{/tr}: </td>
	<td><input type="text" name="freetags_3d_height" value="{$prefs.freetags_3d_height|escape}" size="3" /></td>
      </tr>
      <tr>
        <td colspan="2" class="heading">{tr}Graph appearance{/tr}</td>
      </tr>
      <tr>
        <td class="form">{tr}Navigation depth{/tr}: </td>
	<td><input type="text" name="freetags_3d_navigation_depth" value="{$prefs.freetags_3d_navigation_depth|escape}" size="2" /></td>
      </tr>
      <tr>
        <td class="form">{tr}Node size{/tr}: </td>
	<td><input type="text" name="freetags_3d_node_size" value="{$prefs.freetags_3d_node_size|default:"30"}" size="2" /></td>
      </tr>
      <tr>
        <td class="form">{tr}Text size{/tr}: </td>
	<td><input type="text" name="freetags_3d_text_size" value="{$prefs.freetags_3d_text_size|default:"40"}" size="3" /></td>
      </tr>
      <tr>
        <td class="form">{tr}Spring (connection) size{/tr}: </td>
	<td><input type="text" name="freetags_3d_spring_size" value="{$prefs.freetags_3d_spring_size|default:"100"}" size="3" /></td>
      </tr>
      <tr>
        <td class="form">{tr}Node color{/tr}: </td>
	<td><input type="text" name="freetags_3d_existing_page_color" value="{$prefs.freetags_3d_existing_page_color|escape}" size="7" /></td>
      </tr>
      <tr>
{*        <td class="form">{tr}Missing page node color{/tr}: </td>
	<td><input type="text" name="freetags_3d_missing_page_color" value="{$prefs.freetags_3d_missing_page_color|escape}" size="7" /></td>
      </tr> *}
      <tr>
        <td colspan="2" class="heading">{tr}Camera settings{/tr}</td>
     </tr>
      <tr>
        <td class="form">{tr}Camera distance adjusted relative to nearest node{/tr}: </td>
	<td><input type="checkbox" name="freetags_3d_adjust_camera" {if $prefs.freetags_3d_adjust_camera eq 'true'}checked="checked"{/if} /></td>
      </tr>
      <tr>
        <td class="form">{tr}Camera distance{/tr}: </td>
	<td><input type="text" name="freetags_3d_camera_distance" value="{$prefs.freetags_3d_camera_distance|default:"200"}" size="3" /></td>
      </tr>
      <tr>
        <td class="form">{tr}Field of view{/tr}: </td>
	<td><input type="text" name="freetags_3d_fov" value="{$prefs.freetags_3d_fov|default:"250"}" size="3" /></td>
      </tr>
      <tr>
        <td class="form">{tr}Feed animation interval (milisecs){/tr}: </td>
	<td><input type="text" name="freetags_3d_feed_animation_interval" value="{$prefs.freetags_3d_feed_animation_interval|escape}" size="4" /></td>
      </tr>
      {* new fields *}
      <tr>
        <td colspan="2" class="heading">{tr}Physics engine{/tr}</td>
     </tr>
      <tr>
        <td class="form">{tr}Friction constant{/tr}: </td>
	<td><input type="text" name="freetags_3d_friction_constant" value="{$prefs.freetags_3d_friction_constant|default:"0.4f"}" size="7" /></td>
      </tr>
      <tr>
        <td class="form">{tr}Elastic constant{/tr}: </td>
	<td><input type="text" name="freetags_3d_elastic_constant" value="{$prefs.freetags_3d_elastic_constant|default:"0.5f"}" size="7" /></td>
      </tr>
      <tr>
        <td class="form">{tr}Eletrostatic constant{/tr}: </td>
	<td><input type="text" name="freetags_3d_eletrostatic_constant" value="{$prefs.freetags_3d_eletrostatic_constant|default:"1000f"}" size="7" /></td>
      </tr>
      <tr>
        <td class="form">{tr}Node mass{/tr}: </td>
	<td><input type="text" name="freetags_3d_node_mass" value="{$prefs.freetags_3d_node_mass|default:"5"}" size="7" /></td>
      </tr>
      <tr>
        <td class="form">{tr}Node charge{/tr}: </td>
	<td><input type="text" name="freetags_3d_node_charge" value="{$prefs.freetags_3d_node_charge|default:"1"}" size="7" /></td>
      </tr>

      <tr>
        <td colspan="2" class="button"><input type="submit" name="freetagsset3d" value="{tr}Change configuration{/tr}" /></td>
      </tr>    
    </table>
    </form>
    </div>
  </div>
{/if}
