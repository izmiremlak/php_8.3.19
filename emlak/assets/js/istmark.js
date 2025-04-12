function AjaxFormS(FORMID,SONUCID){

var abcde = "AjaxFormS(\\'"+FORMID+"\\',\\'"+SONUCID+"\\');";

var stbutton = $("a[onclick='"+abcde+"'],button[onclick='"+abcde+"']","#"+FORMID);
var stonc 	 = stbutton.attr("onclick");
var stinn  	 = stbutton.html();
stbutton.removeAttr("onclick");
stbutton.html('<div class="spinner"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>');
$("#"+SONUCID).fadeOut(400);
$("#"+FORMID).ajaxForm({
target: '#'+SONUCID,
complete:function(){
$("#"+SONUCID).fadeIn(400);
stbutton.attr("onclick",stonc);
stbutton.html(stinn);
}
}).submit();
}

function ajaxHere(nere,load_content){
var abcde = "ajaxHere(\\'"+nere+"\\',\\'"+load_content+"\\');";
var stbutton = $("a[onclick='"+abcde+"'],button[onclick='"+abcde+"']");
var stonc 	 = stbutton.attr("onclick");
var stinn  	 = stbutton.html();
stbutton.removeAttr("onclick");
stbutton.html('<div class="spinner"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>');

$("#"+load_content).html('');
$.get(nere, function(data, status){
$("#"+load_content).html(data);
stbutton.attr("onclick",stonc);
stbutton.html(stinn);
});
}