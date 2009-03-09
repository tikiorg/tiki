
function URLDecode(encoded)
{
   var HEXCHARS = "0123456789ABCDEFabcdef"; 
   var plaintext = "";
   var i = 0;
   while (i < encoded.length) {
       var ch = encoded.charAt(i);
	   if (ch == "+") {
	       plaintext += " ";
		   i++;
	   } else if (ch == "%") {
			if (i < (encoded.length-2) 
					&& HEXCHARS.indexOf(encoded.charAt(i+1)) != -1 
					&& HEXCHARS.indexOf(encoded.charAt(i+2)) != -1 ) {
				plaintext += unescape( encoded.substr(i,3) );
				i += 3;
			} else {
				alert( 'Bad escape combination near ...' + encoded.substr(i) );
				plaintext += "%[ERROR]";
				i++;
			}
		} else {
		   plaintext += ch;
		   i++;
		}
	} // while
   return plaintext;
};


function getQueryVariable(variable) {
  var query = window.location.search.substring(1);
  var vars = query.split("&");
  for (var i=0;i<vars.length;i++) {
    var pair = vars[i].split("=");
    if (pair[0] == variable) {
      return pair[1];
    }
  } 
  return '';
}

function updateSearch() {
  document.getElementById('search').value=URLDecode(getQueryVariable('search'));
}

// The search function takes a query, parses the query and returns
// a list of URLs that match the query ordered by URL name.
function query() {
  // This assumes theres a visible dataSources vbb available, not passed as
  // an argument for performance considerations (avoid copying the vbb)

}




function trim(s) {
	 s=s.replace(/^[\s]+/g,"");
     s=s.replace(/[\s]+$/g,"");
     return s;
}

// Binary search of a single word in the list of keywords
function single_query(single,min,max) {
  // Search this single word in the keywords array
  // uses a bnary search
  var mid = min + Math.floor( (max-min) / 2);
  if(single == parent.searchdata.keywords[mid][0]) {
    // The list of documents where this single query appears
  	return parent.searchdata.keywords[mid][1];
  } else {
    if(min==max) return -1;
    if(single > parent.searchdata.keywords[mid][0]) {
      return single_query(single,mid+1,max);
    } else {
      return single_query(single,min,mid-1);
    }
  }
}



function run_query(test) {
	var res = new Array();
    var aux = new Array();
    test=test.toLowerCase();
    var i;
    var j;
	var and_array = test.split('+');
	if(and_array.length>1) {
		for(i=0;i<and_array.length;i++) {
		  aux = run_query(trim(and_array[i]));
		  if(res.length>0) {
		    res = intersect_sorted(res,aux);
		  } else {
		    res = aux;
		  }
		}
		return res;
	} else {
		var or_array = test.split(' ');
		if(or_array.length>1) {
		  for(i=0;i<or_array.length;i++) {
		    aux = run_query(trim(or_array[i]));
		    res = union_sorted(res,aux);
		  }
		  return res;
		} else {
		  aux = single_query(test,0,parent.searchdata.keywords.length);
		  for(i=0;i<aux.length;i++) {
		    res[res.length] = aux[i];
   	      }
		  res = res.sort();
		  return res;
		}
	}
}


function intersect_sorted(a1,a2) {
  var i = 0; var j =0;
  var nr = new Array();
  while(i<a1.length && j<a2.length) {
    if(a1[i]<a2[j]) {
      i++;
    } else {
      if(a1[i]>a2[j]) {
        j++;
      } else {
        nr[nr.length]=a1[i];
        i++; j++;
      }
    }
  }
  return nr; 
}

function union_sorted(a1,a2) {
  var i = 0; var j =0;
  if(a1.length==0) return a2;
  if(a2.length==0) return a1;
  var nr = new Array();
  while(i<a1.length || j<a2.length) {
    if((i<=a1.length && j<=a2.length && a1[i]<a2[j]) || (j>=a2.length)) {
      nr[nr.length]=a1[i];
      i++;
    } else {
      if((i<=a1.length && j<=a2.length && a1[i]>a2[j])||(i>=a1.length)) {
        nr[nr.length]=a2[j];

        j++;
      } else {
        nr[nr.length]=a1[i];
        i++; j++;
      }
    }
  }
  return nr; 
}

function doQuery() {
  parent.docs = new Array();
  parent.content.window.location.reload();
  query = parent.toolbar.document.getElementById('search').value;
   //Now perform a single query
  var results = run_query(query,0,parent.searchdata.keywords.length);
  //alert(results);
  for(i=0;i<results.length;i++) {
    parts = results[i].split('|');
    parent.docs[parent.docs.length]=parts[1]+'|'+parts[0];
  }
  parent.docs.sort();
  parent.title='pepe';
  parent.menu.location.href='results.html';
}



