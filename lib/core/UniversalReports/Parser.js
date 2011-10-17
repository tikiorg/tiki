$.fn.universalReportsParser = function(o) {
	var result = {};
	
	var vals = $(this).serializeArray();
	
	$(vals).each(function() {
		var val = [];
		
		if (!result[this.name]) {
			result[this.name] = this.value;
		} else if ($.isArray(result[this.name])) {
			result[this.name].push(this.value);
		} else {
			result[this.name] = [result[this.name]];
			result[this.name].push(this.value); 
		}
	});
	
	return result;
};