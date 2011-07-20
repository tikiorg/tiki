$.fn.extend({
	cartProductClassMissingForm: function(settings){
		settings = $.extend({
			informationForm: ''
		}, settings);
		
		$(this).each(function() {
			var formParent = $(this).submit(function(){
				if (formParent.attr('satisfied')) return true;
				formParent.addClass('hasMissingForm');
				
				var formDialog = window.formDialog = $('<div id="formDialog" />').load('tiki-index_raw.php?page=' + escape(settings.informationForm), function(){
					formDialog.dialog({
						title: settings.informationForm,
						modal: true,
						height: $(window).height() * 0.8,
						width: $(window).width() * 0.8
					});
					
					var loading = $('<div><span>Loading...</span><img src=\"img/loading.gif\" /></div>').hide().appendTo(formDialog);
					
					var forms = formDialog.find('form');
					forms.each(function(){
						var form = $(this).submit(function(){
						
							var satisfied = true;
							$('.mandatory_field').each(function(){
								var field = $(this).children().first();
								if (!field.val()) {
									$(this).addClass('ui-state-error');
									satisfied = false;
								}
							});
							if (!satisfied) 
								return false;
							
							$.post(form.attr('action'), form.serialize(), function(){
								form
									.slideUp(function(){
										formDialog.animate({
											scrollTop: form.next().offset().top
										});
									})
									.attr('satisfied', true);
								
								satisfied = true;
								
								forms.each(function(){
									if (!$(this).attr('satisfied')) {
										satisfied = false;
									}
								});
								
								if (satisfied) {
									loading.show()
										.prevAll()
										.hide();
									
									formParent
										.attr('satisfied', true)
										.submit();
								}
							});
							
							return false;
						});
					});
				});
				return false;
			});
		});
	},
	cartAjaxAdd: function() {
		if (window.cartAjaxAdd) return this; //prevent multi calls
		window.cartAjaxAdd = true;
		
		$(this).each(function() {
			var form = $(this);
			form.submit(function() {
				var params = form.serialize();
				
				if (form.hasClass('hasMissingForm') && !form.attr('satisfied')) return false;
				
				$('div.box-cart').load('tiki-ajax_services.php?listonly=module&q=cart&' + params + ' div.box-cart > *', function(o){
					if (window.formDialog) {
						window.formDialog.dialog('destroy');
						window.formDialog = null;
					}
				});
				
				return false;
			});
		});
	}
});