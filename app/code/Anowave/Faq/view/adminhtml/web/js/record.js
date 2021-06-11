define(function () 
{
	'use strict';
	
	return function (target) 
	{
		return target.extend(
		{
			defaults: 
			{
	            templates: 
	            {
	                fields: 
	                {
	                	wysiwyg: 
	                    {
	                        component: 'Anowave_Faq/js/form/element/wysiwyg',
	                        template: 'Anowave_Faq/wysiwyg',
	                        wysiwyg: true,
							wysiwygConfigData: 
							{
								add_variables:false,
								add_widgets:false,
								add_directives:true,
								use_container:true,
								container_class:'hor-scroll'
							}
	                    },
	                    textarea: 
	                    {
	                        component: 'Magento_Ui/js/form/element/textarea',
	                        template: 'ui/form/element/textarea'
	                    }
	                }
	            }
	        }
		});
	};
});