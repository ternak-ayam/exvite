
(function ($) {
    "use strict";



  
  
    /*==================================================================
    [ Validate ]*/
    var input = $('.validate-input .input100');

    $('.validate-form').on('submit',function(){
        var check = true;

        for(var i=0; i<input.length; i++) {
            if(validate(input[i]) == false){
                showValidate(input[i]);
                check=false;
            }
        }

        return check;
    });


    $('.validate-form .input100').each(function(){
        $(this).focus(function(){
           hideValidate(this);
        });
    });

    function validate (input) {
        if($(input).attr('type') == 'email' || $(input).attr('name') == 'email') {
            if($(input).val().trim().match(/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{1,5}|[0-9]{1,3})(\]?)$/) == null) {
                return false;
            }
        }
        else {
            if($(input).val().trim() == ''){
                return false;
            }
        }
    }

    function showValidate(input) {
        var thisAlert = $(input).parent();

        $(thisAlert).addClass('alert-validate');
    }

    function hideValidate(input) {
        var thisAlert = $(input).parent();

        $(thisAlert).removeClass('alert-validate');
    }
    

})(jQuery);

(function($){"use strict";$(".carousel-inner .item:first-child").addClass("active");$(".mainmenu-area #primary_menu li a").on("click",function(){$(".navbar-collapse").removeClass("in");});$.scrollUp({scrollText:'<i class="lnr lnr-arrow-up"></i>',easingType:'linear',scrollSpeed:900,animation:'fade'});$('.gallery-slide').owlCarousel({loop:true,margin:0,responsiveClass:true,nav:false,autoplay:true,autoplayTimeout:4000,smartSpeed:1000,navText:['<i class="lnr lnr-chevron-left"></i>','<i class="lnr lnr-chevron-right"></i>'],responsive:{0:{items:1,},600:{items:2},1280:{items:3},1500:{items:4}}});$('.team-slide').owlCarousel({loop:true,margin:0,responsiveClass:true,nav:true,autoplay:true,autoplayTimeout:4000,smartSpeed:1000,navText:['<i class="lnr lnr-chevron-left"></i>','<i class="lnr lnr-chevron-right"></i>'],responsive:{0:{items:1,},600:{items:2},1000:{items:3}}});$(".toggole-boxs").accordion();$('#mc-form').ajaxChimp({url:'https://quomodosoft.us14.list-manage.com/subscribe/post?u=b2a3f199e321346f8785d48fb&amp;id=d0323b0697',callback:function(resp){if(resp.result==='success'){$('.subscrie-form, .join-button').fadeOut();$('body').css('overflow-y','scroll');}}});$('.mainmenu-area a[href*="#"]').not('[href="#"]').not('[href="#0"]').click(function(event){if(location.pathname.replace(/^\//,'')==this.pathname.replace(/^\//,'')&&location.hostname==this.hostname){var target=$(this.hash);target=target.length?target:$('[name='+this.hash.slice(1)+']');if(target.length){event.preventDefault();$('html, body').animate({scrollTop:target.offset().top},1000,function(){var $target=$(target);$target.focus();if($target.is(":focus")){return false;}else{$target.attr('tabindex','-1');$target.focus();};});}}});var magnifPopup=function(){$('.popup').magnificPopup({type:'iframe',removalDelay:300,mainClass:'mfp-with-zoom',gallery:{enabled:true},zoom:{enabled:true,duration:300,easing:'ease-in-out',opener:function(openerElement){return openerElement.is('img')?openerElement:openerElement.find('img');}}});};magnifPopup();$(window).on("load",function(){$('.preloader').fadeOut(500);new WOW().init({mobile:false,});});})(jQuery);

/* particlesJS.load(@dom-id, @path-json, @callback (optional)); */
particlesJS.load('particles-js', 'particles.js/particlesjs.json', function() {
    console.log('callback - particles.js config loaded');
});
/*
window.onscroll = function() {scrollFunction()};

function scrollFunction() {
  if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
      document.getElementById("navbar").style.backgroundColor = "rgb(18 40 88)";
  } else {
      document.getElementById("navbar").style.backgroundColor = "rgb(18 40 88)";
  }
}
*/