define([
    "jquery",
    'mage/translate',
    'Magento_Ui/js/modal/alert',
    "mage/calendar",
    'mage/validation'
], function ($, $t, alert) {
    'use strict';
    $.widget('mage.spininfowidget', {
        options: {
        },
        _create: function () {
            var self = this;
            var spinInfoForm = $('#spininfoformedit');
            spinInfoForm.mage('validation', {}).find('input:text').attr('autocomplete', 'off');
            $('body').on('click', '#update-spininfo', function(e) {
                if (!$('#update-spininfo').hasClass('spin-save-ajax')) {
                    e.preventDefault();
                    if($('#spininfoformedit').validation('isValid')) {
                        $('#update-spininfo').attr('disabled', 'disabled');
                        $('#spininfoformedit').submit();
                    }
                }
            });
            $('#start_date').datetimepicker({
                    dateFormat : "yy-mm-dd",
                    timeFormat : "H:m:s",
                    changeMonth: true,
                    changeYear :true,
                    numberOfMonths: 1,
                    showsTime: true,
                    minDate: '1970-01-01'
                }
            );
            $('#end_date').datetimepicker({
                    dateFormat : "yy-mm-dd",
                    timeFormat : "H:m:s",
                    changeMonth: true,
                    changeYear :true,
                    numberOfMonths: 1,
                    showsTime: true,
                    minDate: '1970-01-01'
                }
            );
            function manageDateView () {
                if (parseInt($('#scheduled').val())) {
                    $('#start_date').addClass('required-entry');
                    $('#end_date').addClass('required-entry');
                    $('#start_date').closest('.admin__field').show();
                    $('#end_date').closest('.admin__field').show();
                } else {
                    $('#start_date').removeClass('required-entry');
                    $('#end_date').removeClass('required-entry');
                    $('#start_date').closest('.admin__field').hide();
                    $('#end_date').closest('.admin__field').hide();
                }
            }
            manageDateView ();
            $('#scheduled').on('change', function() {
                manageDateView ();
            });

            $('form.spin-form').find("input, textarea, select").on("change", function() {
                $(this).closest('form.spin-form').find('.spin-save .admin__page-nav-item-message').removeClass('_changed');
            });
            //image
            $('.spin-files-button').on('click', function() {
                $("#"+$(this).data('triggerid')).trigger('click');
            });

            function updateImagePreview(input, src) {
                $('.'+$(input).data('spin-bind-image')).attr('src',src);
            }

            function uploadImage(input)
            {
                var fileExtension = ['jpeg', 'jpg', 'png', 'gif'];
                if ($.inArray($(input).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                    alert({
                        content: $t("Only '.jpeg','.jpg', '.png', '.gif' formats are allowed.")
                    });
                } else {
                    if (input.files && input.files[0]) {
                        var linkUrl = $(input).data('upload-url');
                        var file = $(input)[0].files[0];
                        var data = new FormData();
                        data.append('image', file);
                        if (file) {
                            $.ajax({
                                type: "POST",
                                url: linkUrl+"?form_key="+window.FORM_KEY,
                                enctype: 'multipart/form-data',
                                mimeType:"multipart/form-data",
                                data: data,
                                contentType: false,
                                cache: false,
                                processData:false,
                                beforeSend: function () {
                                    $('body').trigger('processStart');
                                },
                                success: function (response) {
                                    response = JSON.parse(response);
                                    $(input).closest('.admin__field').find('img.spin-image-preview').attr('src', response.url);
                                    $('#'+$(input).data('upload-id')).val(response.file);
                                    updateImagePreview(input, response.url);
                                    $('body').trigger('processStop');
                                },
                                error: function (response) {
                                    $('body').trigger('processStop');
                                }
                            });
                        }
                    }
                }
            }
    
            $("input.spin-files").change(function () {
                uploadImage(this);
            });
    
            $(".spin-image-delete").on("click", function (e) {
                e.preventDefault();
                $('#'+$(this).closest('.admin__field').find('input[type=file]').data('upload-id')).val('');
                $(this).closest('.admin__field').find('img.spin-image-preview').attr('src','');
                updateImagePreview($(this).closest('.admin__field').find('input[type=file]'),'')
            });

            //binding
            $('.spin-bind-text').on('change keypress keydown keyup blur focus', function () {  
                var elemId = $(this).data('spin-bind');
                $('.'+elemId).text($(this).val());
            });
            $('.spin-bind-style').on('change keypress blur focus', function () {  
                var elemId = $(this).data('spin-bind');
                $('.'+elemId).css($(this).data('spin-bind-prop'), $(this).val());
                $('.'+elemId+' h1,'+'.'+elemId+' p,'+'.'+elemId+' span,'+'.'+elemId+' label').css($(this).data('spin-bind-prop'), $(this).val());
            });

            // form save ajax 
            $('.spin-save-ajax').on('click', function(e) {
                e.preventDefault();
                var self = this;
                var formId = $(self).data('spin-bind-form');
                var form = $('#'+formId);
                if($(form).validation('isValid')) {
                    $.ajax({
                        type: "POST",
                        url: $(form).attr('action'),
                        data: $(form).serialize(),
                        dataType: "json",
                        beforeSend: function () {
                            $('body').trigger('processStart');
                        },
                        success: function(response)
                        {
                            if (parseInt(response.success)) {
                                $(self).find('.admin__page-nav-item-message').addClass('_changed');
                                alert({
                                    title: $t('Success'),
                                    content: $t(response.message)
                                });
                                if (response.data !== undefined && response.data) {
                                    $(form).trigger("spinAjaxComplete", [response.data]);
                                }
                            } else {
                                alert({
                                    title: $t('Error'),
                                    content: $t(response.message)
                                });
                            }
                            $('body').trigger('processStop');
                        },
                        error: function (response) {
                            $('body').trigger('processStop');
                        }
                    });
                }
            });
        }
    });
    return $.mage.spininfowidget;
});
