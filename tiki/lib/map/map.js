// functions to be used in the TikiMaps feature

function selectimgzoom(x)
{
	var arrimgzoom = new Array(8)

	arrimgzoom[0]=document.frmmap.imgzoom0
	arrimgzoom[1]=document.frmmap.imgzoom1
	arrimgzoom[2]=document.frmmap.imgzoom2
	arrimgzoom[3]=document.frmmap.imgzoom3
	arrimgzoom[4]=document.frmmap.imgzoom4
	arrimgzoom[5]=document.frmmap.imgzoom5
	arrimgzoom[6]=document.frmmap.imgzoom6
	arrimgzoom[7]=document.frmmap.imgzoom7
	

	for(var i=0;i<=7;i++)
	{
	  arrimgzoom[i].border=0
	  if (i==x)
	  {
	    arrimgzoom[i].border=1
	  }
	}
}

function zoomin(x){
	document.frmmap.zoom.options[x].selected=true
	selectimgzoom(x)
}

function cbzoomchange() {
	var selected
	selected=document.frmmap.zoom.selectedIndex
	selectimgzoom(selected)

}
