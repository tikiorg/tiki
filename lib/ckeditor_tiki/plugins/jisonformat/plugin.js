CKEDITOR.plugins.add( 'jisonformat', {
	init: function( editor ) {
		CKEDITOR.config.format_tags = 'p;h1;h2;h3;h4;h5;h6;pre;address;div';
		CKEDITOR.config.format_p = { element: 'p' };
		CKEDITOR.config.format_div = { element: 'div' };
		CKEDITOR.config.format_address = { element: 'address' };

		CKEDITOR.config.format_pre = {
			element: 'pre',
			attributes:{
				'data-t':jisonSyntax.preFormattedText
			}
		};
		CKEDITOR.config.format_h1 = {
			element: 'h1',
			attributes:{
				'data-t':jisonSyntax.header
			}
		};
		CKEDITOR.config.format_h1 = {
			element: 'h2',
			attributes:{
				'data-t':jisonSyntax.header
			}
		};
		CKEDITOR.config.format_h1 = {
			element: 'h3',
			attributes:{
				'data-t':jisonSyntax.header
			}
		};
		CKEDITOR.config.format_h1 = {
			element: 'h4',
			attributes:{
				'data-t':jisonSyntax.header
			}
		};
		CKEDITOR.config.format_h1 = {
			element: 'h5',
			attributes:{
				'data-t':jisonSyntax.header
			}
		};
		CKEDITOR.config.format_h1 = {
			element: 'h6',
			attributes:{
				'data-t':jisonSyntax.header
			}
		};
	}
});