/**
 * Frequently Asked Questions JS
 * 
 * @author Angel Kostadino
 * @copyright Anowave
 * @support For Magento < 2.3, replace "tinymce4" with "tinymce". Magento 2.3 does not support TinyMCE natively.
 */

require(['ko','jquery', 'reloadGrid', 'tinymce4','Magento_Ui/js/modal/modal','mage/translate'], function (ko, $, reloadGrid, tinyMCE, modal) 
{
	window.P = (function($)
	{
		return {
			save: function(element)
			{
				var endpoint = $('[name=faq_endpoint').val(), wrapper = $(element).parents('.admin__fieldset-wrapper-content:first');
				
				var data = 
				{
					faq_product_id: $('[name=faq_product_id]').val(),
					faq_store_id:	$('[name=faq_store_id]').val(),
					faq: 		 	$('[name=faq_question]').val(),
					faq_content: 	$('[name=faq_answer]').val(),
					faq_enable:		$('[name=faq_enable]').prop('checked')
				};
				
				$('body').loader('show');
				
				$.post(endpoint, data, function(response)
				{
					$('body').loader('hide');
					
					if (response.success)
					{
						reloadGrid.reloadUIComponent("faq_listing.faq_listing_data_source");
						
						$('html, body').animate(
						{
							scrollTop: wrapper.offset().top
							
						}, 500);
						
						/**
						 * Reset question
						 */
						$('[name=faq_question]').val('');
						
						/**
						 * Reset answer
						 */
						for(i = 0; i < tinymce.editors.length; i++)
						{
							tinyMCE.editors[i].setContent('');
						    
						    $("[name='" + tinyMCE.editors[i].targetElm.name + "']").val('');
						}
					}
					
				},'json');
				
				return false;
			},
			edit: function()
			{
				return false;
			},
			enrich: function(context)
			{
				return true;
			},
			modal: function(context)
			{
				var textarea = $(context).parent().prev(), content = textarea.clone().css({ width:'100%'}), modal = $('<div/>').html(content), editor = null;
				
				var viewModel = ko.dataFor(textarea.get(0));

		        modal.modal(
		        {
		            title: $.mage.__('Edit FAQ Content'),
		            innerScroll: true,
		            modalClass: '_image-box',
		            buttons: 
		            [
		            	{
			                text: $.mage.__('Update'),
			                attr: 
			                {
			                    'data-action': 'save'
			                },
			                'class': 'action-primary',
			                click: function () 
			                {
			                	viewModel.value(tinyMCE.get('faq_content_edit').getContent());
			 
			                	modal.modal('closeModal');
			                }
			            }
		            ],
		            opened: function()
		            {
		            	content.attr('id','faq_content_edit');
		            	
		            	editor = tinyMCE.init(
        				{
        	                mode: "exact",
        	                theme: "modern",
        	                elements: content.attr('id'),
        	                plugins: "table,save,media,searchreplace,contextmenu,paste,directionality,noneditable,visualchars,nonbreaking",
        	                theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect|cut,copy,paste,pastetext|bullist,numlist|undo,redo|link,unlink,anchor,image,cleanup,code|forecolor,backcolor|removeformat,media",
        	                theme_advanced_buttons2 : "",
        	                theme_advanced_buttons3 : "",
        	                theme_advanced_buttons4 : "",
        	                theme_advanced_toolbar_location: "top",
        	                theme_advanced_toolbar_align: "left",
        	                theme_advanced_path_location: "bottom",
        	                extended_valid_elements: "a[name|href|target|title|onclick],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",
        	                theme_advanced_resize_horizontal: 'false',
        	                theme_advanced_resizing: 'true',
        	                apply_source_formatting: 'true',
        	                convert_urls: 'false',
        	                force_br_newlines: 'true',
        	                doctype: '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',
        	                convert_urls : false,
        	                file_browser_callback:function (fieldName, url, objectType, w) 
        	                {
        	                    varienGlobalEvents.fireEvent('open_browser_callback', 
        	                    {
        	                        win: w,
        	                        type: objectType,
        	                        field: fieldName
        	                    });
        	                }
        	            });
		            },
		            closed: function()
		            {
		            	modal.remove();
	                	content.remove();
		            }
		        }).trigger('openModal');
			},
			apply: function()
			{
				return this;
			}
		}
	})(jQuery).apply();
});