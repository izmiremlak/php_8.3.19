function AjaxFormS(FORMID,SONUCID){

$("#"+SONUCID).fadeOut(400);

var urlal		= $("#"+FORMID).attr("action");

$.ajax({ // ajax iþlemi baþlar

type:'POST', // veri gönderme tipimiz. get olabilirdi json olabilirdi. ama biz post kullanýyoruz

url:urlal, // post edilecek adres

data:$('#'+FORMID).serialize(), //post edilecek veriler

success:function(cevap){// iþlem baþarýlýysa

$("#"+SONUCID).html(cevap); //sonuc id'sine ajaxPost.php den dönen verileri basýyoruz. 

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

