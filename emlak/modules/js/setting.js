jQuery(document).ready(function(){jQuery(".tp-banner").show().revolution({dottedOverlay:"none",delay:9e3,startwidth:1170,startheight:520,hideThumbs:200,thumbWidth:100,thumbHeight:50,thumbAmount:5,navigationType:"none",navigationArrows:"solo",navigationStyle:"preview1",touchenabled:"on",onHoverStop:"off",swipe_velocity:.7,swipe_min_touches:1,swipe_max_touches:1,drag_block_vertical:!1,keyboardNavigation:"on",navigationHAlign:"center",navigationVAlign:"bottom",navigationHOffset:0,navigationVOffset:20,soloArrowLeftHalign:"left",soloArrowLeftValign:"center",soloArrowLeftHOffset:20,soloArrowLeftVOffset:0,soloArrowRightHalign:"right",soloArrowRightValign:"center",soloArrowRightHOffset:20,soloArrowRightVOffset:0,shadow:0,fullWidth:"on",fullScreen:"off",spinner:"spinner0",stopLoop:"off",stopAfterLoops:-1,stopAtSlide:-1,shuffle:"off",autoHeight:"off",forceFullWidth:"off",hideThumbsOnMobile:"off",hideNavDelayOnMobile:1500,hideBulletsOnMobile:"off",hideArrowsOnMobile:"off",hideThumbsUnderResolution:0,hideSliderAtLimit:0,hideCaptionAtLimit:0,hideAllCaptionAtLilmit:0,startWithSlide:0})}),$(function(){var a=0;$(".menuAc").click(function(){0==a?($(this).text("\u2261 MENU"),a++):($(this).text("\u2261 MENU"),a=0),$(this).next("ul").slideToggle(250)})}),jQuery(document).ready(function(){jQuery(".fadein").addClass("hidden").viewportChecker({classToAdd:"visible animated fadeIn",offset:100})}),jQuery(document).ready(function(){jQuery(".fadeup").addClass("hidden").viewportChecker({classToAdd:"visible animated fadeInUp",offset:100})}),jQuery(document).ready(function(){jQuery(".fadedown").addClass("hidden").viewportChecker({classToAdd:"visible animated fadeInDown",offset:100})}),jQuery(document).ready(function(){jQuery(".fadeleft").addClass("hidden").viewportChecker({classToAdd:"visible animated slideInLeft",offset:100})}),jQuery(document).ready(function(){jQuery(".faderight").addClass("hidden").viewportChecker({classToAdd:"visible animated slideInRight",offset:100})}),jQuery(document).ready(function(){jQuery(".zoom").addClass("hidden").viewportChecker({classToAdd:"visible animated zoomIn",offset:100})}),jQuery(document).ready(function(){jQuery(".flipx").addClass("hidden").viewportChecker({classToAdd:"visible animated flipInY",offset:100})}),jQuery(document).ready(function(a){var b=300,c=1200,d=700,e=a(".cd-top");a(window).scroll(function(){a(this).scrollTop()>b?e.addClass("cd-is-visible"):e.removeClass("cd-is-visible cd-fade-out"),a(this).scrollTop()>c&&e.addClass("cd-fade-out")}),e.on("click",function(b){b.preventDefault(),a("body,html").animate({scrollTop:0},d)})}),$(document).ready(function(){$("area[rel^='prettyPhoto']").prettyPhoto(),$(".gallery:first a[rel^='prettyPhoto']").prettyPhoto({animation_speed:"fast",slideshow:3e3,social_tools:!1,autoplay_slideshow:!1}),$(".gallery:gt(0) a[rel^='prettyPhoto']").prettyPhoto({animation_speed:"fast",slideshow:1e4,hideflash:!0}),$("#custom_content a[rel^='prettyPhoto']:first").prettyPhoto({custom_markup:'<div id="map_canvas" style="width:260px; height:265px"></div>',changepicturecallback:function(){initialize()}}),$("#custom_content a[rel^='prettyPhoto']:last").prettyPhoto({custom_markup:'<div id="bsap_1259344" class="bsarocks bsap_d49a0984d0f377271ccbf01a33f2b6d6"></div><div id="bsap_1237859" class="bsarocks bsap_d49a0984d0f377271ccbf01a33f2b6d6" style="height:260px"></div><div id="bsap_1251710" class="bsarocks bsap_d49a0984d0f377271ccbf01a33f2b6d6"></div>',changepicturecallback:function(){_bsap.exec()}})});

$(document).ready(function(){
var selectt = '#haber_ve_duyurular';

$(selectt+" .hbblogbasliklar h5:first-child").addClass("hbblogbasliklar_active");
$(selectt+" .icerikler:first-child").show();

$(selectt+" .hbblogbasliklar h5").click(function(){
var indexid	= $(this).attr("data-index");
$(selectt+" .hbblogbasliklar h5").removeClass("hbblogbasliklar_active");
$(this).addClass("hbblogbasliklar_active");
$(selectt+" .icerikler").hide();
$(selectt+" .icerikler:eq("+indexid+")").show();
});


var selectt2 = '#homeblog';

$(selectt2+" .hbblogbasliklar h5:first-child").addClass("hbblogbasliklar_active");
$(selectt2+" .icerikler:first-child").show();
$(selectt2+" .hbblogbasliklar h5").click(function(){
var indexid	= $(this).attr("data-index");
$(selectt2+" .hbblogbasliklar h5").removeClass("hbblogbasliklar_active");
$(this).addClass("hbblogbasliklar_active");
$(selectt2+" .icerikler").hide();
$(selectt2+" .icerikler:eq("+indexid+")").show();
});
});