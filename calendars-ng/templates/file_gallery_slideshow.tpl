{* $Id$ *}

<html>
	<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="lib/mootools/mootools_packed.js"></script>
<script type="text/javascript" src="lib/slideshow/slideshow.rc1.packed.js"></script>
{literal}
<script type="text/javascript">		
<!--//--><![CDATA[//><!--
	window.onload = function() { new editor(); }
//--><!]]>
</script>
<style type="text/css">
		div.slideshow {
			margin: 18px auto;
		}
		div.slideshow ul {
			background: #FFF;
			bottom: 0;
			position: absolute;
			overflow: hidden;
			padding: 4px 8px;
			right: 0;
			z-index: 1000;
		}
		div.slideshow ul a {
			background: url(lib/slideshow/icons/bullet_blue.png) no-repeat;
			cursor: pointer;
			display: block;
			float: left;
			height: 16px;
			margin: 0 4px 0 0;
			overflow: hidden;
			width: 16px;
			opacity: 0.5;
		}
		div.slideshow ul a.prev {
			background-image: url(lib/slideshow/icons/control_rewind_blue.png);
			width: 16px;
		}
		div.slideshow ul a.next {
			background-image: url(lib/slideshow/icons/control_fastforward_blue.png);
			margin-right: 0;
			width: 16px;
		}
		div.slideshow ul a.active,
		div.slideshow ul a.prev:hover,
		div.slideshow ul a.next:hover {
			opacity: 1.0;
		}
		div.slideshow ul li {
			float: left;
			list-style: none;
		}
{/literal}
</style>

</head>
<body>


 <div id="my_slideshow" class="slideshow">
   <img src="{if $tikiroot neq ""}{$tikiroot}{else}/{/if}tiki-download_file.php?fileId={$firstId}" alt="A picture" />
 </div>
{*
<p>
{tr}Size{/tr}
<span><input name="width" type="text" size="4" value="400" /></span>
<span><input name="height" type="text" size="4" value="300" /></span>
</p>
<p>
{tr}Navigation{/tr}:<select name="navigation">
    <option value="arrows">{tr}thumbnails{/tr}</option>
    <option value="arrows">{tr}arrows{/tr}</option>
    <option value="arrows+">{tr}arrows{/tr}+</option>
</select>
{tr}Resize{/tr}:<select name="resize">
    <option value="true">{tr}true{/tr}</option>
    <option value="false">{tr}false{/tr}</option>
</select>
{tr}Type{/tr}:<select name="type">
  <option value="fade">{tr}fade{/tr}</option>
  <option value="pan">{tr}pan{/tr}</option>
  <option value="zoom">{tr}zoom{/tr}</option>
  <option value="combo">{tr}combo{/tr}</option>
  <option value="push">{tr}push{/tr}</option>
  <option value="wipe">{tr}wipe{/tr}</option>
</select>
</p>
<p>
{tr}Transition{/tr}:<select name="transition">
  <option value="Fx.Transitions.linear">linear</option>
  <option value="Fx.Transitions.quadIn">quadIn</option>
  <option value="Fx.Transitions.quadOut">quadOut</option>
  
  <option value="Fx.Transitions.quadInOut">quadInOut</option>
  <option value="Fx.Transitions.cubicIn">cubicIn</option>
  <option value="Fx.Transitions.cubicOut">cubicOut</option>
  <option value="Fx.Transitions.cubicInOut">cubicInOut</option>
  <option value="Fx.Transitions.quartIn">quartIn</option>
  <option value="Fx.Transitions.quartOut">quartOut</option>
  
  <option value="Fx.Transitions.quartInOut">quartInOut</option>
  <option value="Fx.Transitions.quintIn">quintIn</option>
  <option value="Fx.Transitions.quintOut">quintOut</option>
  <option value="Fx.Transitions.quintInOut">quintInOut</option>
  <option value="Fx.Transitions.sineIn">sineIn</option>
  <option value="Fx.Transitions.sineOut">sineOut</option>
  
  <option value="Fx.Transitions.sineInOut">sineInOut</option>
  <option value="Fx.Transitions.expoIn">expoIn</option>
  <option value="Fx.Transitions.expoOut">expoOut</option>
  <option value="Fx.Transitions.expoInOut">expoInOut</option>
  <option value="Fx.Transitions.circIn">circIn</option>
  <option value="Fx.Transitions.circOut">circOut</option>
  
  <option value="Fx.Transitions.circInOut">circInOut</option>
  <option value="Fx.Transitions.elasticIn">elasticIn</option>
  <option value="Fx.Transitions.elasticOut">elasticOut</option>
  <option value="Fx.Transitions.elasticInOut">elasticInOut</option>
  <option value="Fx.Transitions.backIn">backIn</option
  ><option value="Fx.Transitions.backOut">backOut</option>
  <option value="Fx.Transitions.backInOut">backInOut</option>
  
  <option value="Fx.Transitions.bounceIn">bounceIn</option>
  <option value="Fx.Transitions.bounceOut" selected="selected">bounceOut</option>
  <option value="Fx.Transitions.bounceInOut">bounceInOut</option>
</select>
{tr}Duration{/tr}:
<span><input type="text" value="[4000, 4000]" name="duration" /></span>
</p>
*}

<script type="text/javascript">
<!--//--><![CDATA[//><!--
   myShow = new Slideshow('my_slideshow', 
   {ldelim} thumbnailre: [ new RegExp("display") ,"display\&max=16" ], 
   type: 'push', 
   navigation: 'thumbnails', 
   transition: Fx.Transitions.backInOut, 
   duration: [4000, 4000], 
   hu: '{if $tikiroot neq ""}{$tikiroot}{else}/{/if}tiki-download_file.php?', 
   images: [{foreach from=$filesid item=id name=images}'display&fileId={$id}'{if !$smarty.foreach.images.last},{/if}{/foreach}],
   height:300, 
   width:400, 
   resize:true, 
   captions: [{foreach from=$file item=f name=files}'{if $gal_info.show_name eq 'a' || $gal_info.show_name eq 'n'}{if $f.name ne ''}{$f.name|escape:"utf-8"}-{/if}{/if}{if $gal_info.show_name eq 'a' || $gal_info.show_name eq 'f'}{$f.filename|escape:"utf-8"}{/if}'{if !$smarty.foreach.files.last},{/if}{/foreach}]
   {rdelim});
//--><!]]>
</script>

	</body>
</html>

