function AjaxFormS(FORMID,SONUCID){

$("#"+SONUCID).fadeOut(400);

var urlal		= $("#"+FORMID).attr("action");

$.ajax({ // ajax i�lemi ba�lar

type:'POST', // veri g�nderme tipimiz. get olabilirdi json olabilirdi. ama biz post kullan�yoruz

url:urlal, // post edilecek adres

data:$('#'+FORMID).serialize(), //post edilecek veriler

success:function(cevap){// i�lem ba�ar�l�ysa

$("#"+SONUCID).html(cevap); //sonuc id'sine ajaxPost.php den d�nen verileri bas�yoruz. 

$("#"+SONUCID).fadeIn(400);

}

});

}



function ajaxHere(nere,load_content){

$("#"+load_content).html('');

$.get(nere, function(data, status){

$("#"+load_content).html(data);

});

}

