require(["jquery", "domReady!"], function ($) {
  $(document).ready(function () {
    $(".loading-mask").hide();
    $(".discount-percent").hide();
    $(".fieldset:first").removeAttr("tabindex");
    //remove QA accordion glitch
    $(".pr-qa-display-headline h1").remove();
    
    $('.loader').attr('aria-live', 'assertive');
    $('.admin__data-grid-loading-mask').attr('aria-live', 'assertive');
  });

  //floating message
  $(document).scroll(function () {
    if ($(window).scrollTop() < 1500) {
      $(".page.messages").css("position", "sticky");
    } else if ($(window).scrollTop() > 1500) {
      $(".page.messages").css("position", "fixed");
    }
  });
  
});
