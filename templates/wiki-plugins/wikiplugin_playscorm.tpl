{button href="#" _onclick="return false;" _text="{tr}Click to view interactive SCORM learning object{/tr}" _id="scormpreview`$id`"}
{jq}
	$('#scormpreview{{$id}}').colorbox({
		href: "{{$iframeurl}}",
		iframe: true,
		width: {{$iframewidth}},
		height: {{$iframeheight}},
		scrolling: {{$iframescrolling}}
	});
{/jq}
