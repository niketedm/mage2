require([
    'jquery',
    'mage/translate',
    'jquery/validate'],
    function($){
        $.validator.addMethod(
            'validate-question-enabled', function (v) {

                var questionSnippet = $("#powerreviews_reviewdisplay_on_off_sections_product_page_question_snippet").val();
                var questionDisplay = $("#powerreviews_reviewdisplay_on_off_sections_product_page_question_display").val();

                if (questionSnippet == "0" && questionDisplay == "0") {
                    return true;
                }

                qaEnabled = false;

                function validateQuestions(){
                    var merchantGroupId = parseInt($("#powerreviews_reviewdisplay_general_merchant_group_id").val());

                    return $.ajax({
                        async: false,
                        showLoader: true,
                        async: false,
                        url: 'https://portal.powerreviews.com/api/v1/merchant_groups/merchant_group_id/' + merchantGroupId + "/",
                        type: "GET",
                        dataType: 'json'
                    });
                }

                validateQuestions().complete(function(data){
                    if (data.responseJSON.questions_and_answers == true) {
                        qaEnabled = true;
                    }
                });

                return qaEnabled;

            }, $.mage.__("The Q&A solution isn't enabled for your account. To enable Q&A, contact magento@powerreviews.com."));
    }
);
