<?php include "../../functions.php";
header("Content-Type:text/css; Charset=utf8");
?>@charset "utf-8";
body{font-family:'Open Sans',sans-serif;font-size:15px;line-height:normal;color:#333;margin:0;padding:0;overflow-x:hidden}
#wrapper{width:1050px;margin-left:auto;margin-right:auto}
a,img,input,select,textarea{-webkit-transition:all .3s ease-out;-moz-transition:all .3s ease-out;-ms-transition:all .3s ease-out;-o-transition:all .3s ease-out;transition:all .3s ease-out}
a{color:#333;text-decoration:none}
input,select,textarea{font-family:'Open Sans',sans-serif;outline:none;width:96%;padding:7px;font-size:15px;color:#333;border:1px solid #ccc}
select {-moz-appearance: none;text-indent: 0.01px;text-overflow: '';}
table tr td{border-bottom-width:1px;border-bottom-style:dotted;border-bottom-color:#CCC;padding:5px}
.notice{color:orange;font-weight:700}
.error{color:red;}
.complete{color:green;font-weight:700}
.clear{clear:both}
h1,h2,h3,h4,h5,h6{padding:0;margin:0;font-weight:400;font-family: 'Titillium Web', sans-serif;}
h1{font-size:32px}
h2{font-size:28px}
h3{font-size:26px}
h4{font-size:20px}
h5{font-size:18px}
.yuzde10{width:9%;display:inline-block}
.yuzde20{width:19%;display:inline-block}
.yuzde30{width:29%;display:inline-block}
.yuzde40{width:39%;display:inline-block}
.yuzde50inpt{width:49%;display:inline-block}
.yuzde50{width:49%;display:inline-block}
.yuzde60{width:59%;display:inline-block}
.yuzde70{width:69%;display:inline-block}
.yuzde80{width:79%;display:inline-block}
.yuzde90{width:89%;display:inline-block}
.yuzde25{width:25%;display:inline-block}
.yuzde75{width:74%;display:inline-block}
button{font-family:'Open Sans',sans-serif;-webkit-transition:all .3s ease-out;-moz-transition:all .3s ease-out;-ms-transition:all .3s ease-out;-o-transition:all .3s ease-out;transition:all .3s ease-out}
.header{position:absolute;top:0;z-index:98;width:100%; background-image: url(../images/headbg.png);    background-repeat: repeat-x;    background-position: left top;min-height:169px;}
.menu{width:100%;background:#fff;height:90px;box-shadow: 0px 0px 10px #b1b1b1;}
.menu ul{padding:0;margin:0;float:right}
.menu li{float:left;position:relative;list-style-type:none}
.menu li a{float:right;color:<?=$gayarlar->renk2;?>;padding-left:40px;padding-right:30px;text-decoration:none;line-height:87px;font-weight:600;font-size:18px;font-family:Titillium Web;border-bottom-width:3px;border-bottom-style:solid;border-bottom-color:#fff}
.menu li:hover a{background:#f1f1f1;}
.menu ul li ul li:hover a{background:#e2e2e2;border-bottom-width:1px;border-bottom-style:solid;border-bottom-color:#EFEFEF}
.menu li a:hover{background:#F3F3F3;border-bottom-width:3px;border-bottom-style:solid;border-bottom-color:<?=$gayarlar->renk2;?>}
.menu ul li ul{width:200px;float:left;position:absolute;top:87px;left:0;z-index:1;display:none;margin:0;padding:0}
.menu ul li ul li{float:none;margin:0;padding:0}
.menu ul li ul li a{background:#fff;font-size:15px;float:none;text-align:left;display:block;line-height:30px;margin:0;border-bottom-width:1px;border-bottom-style:solid;border-bottom-color:#F3F3F3;padding:0 0 0 10px}
.menu li:hover > ul{display:block}
.menu ul li ul li ul{width:200px;height:auto;float:left;position:absolute;top:0;left:200px;z-index:1;display:none}
.menu ul li ul li ul li a:hover{background:#fff;border-bottom-width:1px;border-bottom-style:solid;border-bottom-color:#F3F3F3}
.menuAc{padding:5px;color:#fff;font-weight:700;cursor:pointer;display:none;font-size:24px;width:100%;float:left}
.sayfalama{width:100%;text-align:center;margin:17px auto}
.sayfalama span a{text-decoration:none;color:#666;-webkit-border-radius:3px;-moz-border-radius:3px;border-radius:3px;font-size:13px;-webkit-transition:all .3s ease-out;-moz-transition:all .3s ease-out;-ms-transition:all .3s ease-out;-o-transition:all .3s ease-out;transition:all .3s ease-out;border:1px solid #CCC;text-shadow:.06em .06em #fff;padding:2px 8px}
.sayfalama span a:hover{background-color:#ccc}
.sayfalama .sayfalama-active a{background-color:#ccc}
.iletisimtable{float:left;width:49%}
.iletisiminfo{text-align:center;margin-top:50px;margin-bottom:15px}
.iletisimtable h4{color:#a93030}
.gonderbtn{color:#202930;border:1px solid #1F282F;float:left;-webkit-border-radius:2px;-moz-border-radius:2px;border-radius:2px;text-align:center;padding:7px 25px}
.gonderbtn:hover{color:#FFF;text-decoration:none;background-color:#202930}
.iletisimbilgileri iframe{box-shadow:0 0 15px #ccc}
.clearmob{display:none}
#largeredbtn{font-family:"Titillium Web",sans-serif;font-weight:700;font-size:16px;line-height:60px;color:#fff;text-decoration:none;text-shadow:0 0 3px #000;border:1px solid #FFF;-webkit-border-radius:50px;-moz-border-radius:50px;border-radius:50px;padding:15px 70px}
#largeredbtn:hover{background-color:#fff;color:#000}
.headsosyal a{float:left;font-size:18px;line-height:50px;color:#FFF;padding-right:15px;padding-left:15px}
.languages{color:white; float:left;margin-left:30px;width:165px;margin-top:22px}
.languages select{background:transparent;padding-right:25px;padding-left:25px;font-family:Titillium Web;color:#FFF;border:none;outline:none;height:50px;font-size:14px;cursor:pointer}
.languages option{background:#999}
.languages a {color:white;}
.headinfo{float:right;color:#FFF;margin-top:7px;margin-right:30px;}
.headinfo h3{font-size:18px;line-height:50px;float:right;margin-left:30px}
.logo{float:left}
.headinfo a {color:white;}
#ustline{border-top-width:3px;border-top-style:solid;border-top-color:<?=$gayarlar->renk2;?>}
#menuustok{position:absolute;top:-10px;left:50px;color:<?=$gayarlar->renk2;?>}
#ilanverbtn{color:#FFF;float:right;border:none;margin-left:12px;margin-top:15px;margin-bottom:15px;border:1px solid white;}
#ilanverbtn:hover{color:<?=$gayarlar->renk1;?>;background-color:#fff}
.headsosyal{margin-top:10px;float:left}
.headsosyal a:hover{margin-top:-5px}
.sidebar{float:left;width:230px;margin-top:15px;min-height:300px}
.altbaslik{margin-bottom:15px;padding-bottom:15px;border-bottom-width:1px;border-bottom-style:solid;border-bottom-color:#EEE}
.gelismisara input{border:1px solid #FFF;font-family:"Open Sans";color:#333;font-size:16px;padding:8px;width:93%;background:#F2F2F2;margin-bottom:3px;padding-top:11px;padding-bottom:11px;}
.gelismisara ::-webkit-input-placeholder{color:#333}
.gelismisara :-moz-placeholder{color:#333}
.gelismisara ::-moz-placeholder{color:#333}
.gelismisara :-ms-input-placeholder{color:#333}
#yariminpt {
    width: 48%;
    /* float: left; */
    display: inline-block;
}
.gelismisara .gonderbtn{width:78%;color:<?=$gayarlar->renk2;?>;border:2px solid <?=$gayarlar->renk2;?>;padding-top:10px;padding-bottom:10px;font-size:15px;    font-weight: 600;} 
.gelismisara .gonderbtn:hover{color:#fff;background:<?=$gayarlar->renk2;?>}
.gelismisara .checkbox-custom + .checkbox-custom-label:before, .radio-custom + .radio-custom-label:before {
    content: '';
    background: #fff;
    display: inline-block;
    line-height: 15px;
    vertical-align: middle;
    width: 15px;    border: 2px solid #ccc;
    font-size: 13px;
    height: 15px;
    padding: 2px;
    margin-right: 10px;
    text-align: center;
}
.content{float:right;width:800px;margin-top:10px}
#sicakfirsatlar{color:<?=$gayarlar->renk2;?>}
#sicakfirsatlar a{color:<?=$gayarlar->renk2;?>}
.kareilan{margin:7px;margin-bottom:10px;float:left;width:250px;box-shadow:0 0 5px #ccc;-webkit-transition:all .3s ease-out;-moz-transition:all .3s ease-out;-ms-transition:all .3s ease-out;-o-transition:all .3s ease-out;transition:all .3s ease-out}
.kareilan:hover{box-shadow:0 0 0px #888}
.fiyatlokasyon {float:left;width:100%;padding-top:5px;padding-bottom:0px;    margin-top: -55px;    background: rgba(0, 0, 0, 0.5); color:#fff;padding-top:5px;padding-bottom:7px;-webkit-transition:all .3s ease-out;-moz-transition:all .3s ease-out;-ms-transition:all .3s ease-out;-o-transition:all .3s ease-out;transition:all .3s ease-out}
.kareilan:hover .fiyatlokasyon {opacity:0.0; filter:alpha(opacity=00);}
.kareilan img{float:left;width:100%;height: 200px;}
.fiyatlokasyon h3{font-weight:700;font-size:16px;margin-left:10px;}
.fiyatlokasyon h4{font-size:13px;margin-left:10px}
.kareilanbaslik h3{font-size:14px;margin-left:10px;font-weight:700;margin-top:10px;float:left;margin-bottom:0px;height:35px;line-height:16px;}
.ilandurum{position:relative;float:left;padding:5px 10px;background:<?=$gayarlar->renk2;?>;color:#fff;font-size:13px;opacity:.8;filter:alpha(opacity=80);margin-bottom:-30px}
#kiralik{background:green;opacity:.8;filter:alpha(opacity=80)}
.ilanbigbtn{width:100%;border:2px solid #0C0;height:90px;background-image:url(../images/ilanbigbtn-bg.jpg);background-repeat:no-repeat;background-position:left center;margin-top:20px;margin-bottom:-30px}
.ilanbigbtn h1{line-height:90px;font-weight:700;float:left;font-size:24px;margin-left:25px}
.ilanbigbtn .gonderbtn{float:right;padding:20px 40px;background-color:#0C0;border:2px solid #0C0;font-family:Titillium Web;font-weight:700;color:#fff;font-size:20px;margin:7px}
.ilanbigbtn .gonderbtn:hover{background-color:red;border:2px solid red;color:#fff}
.ilanasama{text-align:center;float:left;margin-top:20px}
.ilanasama h2{font-weight:700;font-size:23px;line-height:30px;}
.ilanasama i{font-size:90px}
.ilanvertanitim .fa.fa-long-arrow-right{float:left;margin:8%;font-size:70px;color:#ccc;margin-top:40px}
.hbveblog{float:left;width:49%;margin-top:50px;margin-bottom:40px}
.hbveblog-container{background-color:<?=$gayarlar->renk2;?>;width:100%;float:left;color:#FFF}
.foovekaciklama{float:left;width:50%;word-wrap:break-word}
.hbveblog h4{font-weight:800;color:<?=$gayarlar->renk2;?>;margin-bottom:15px;font-size:24px;float:left}
.foovekaciklama p{margin-left:10px}
.foovekaciklama p a{color:#FFF}
.hbblogbasliklar{float:right;width:50%}
.hbblogbasliklar h5{font-size:16px;font-weight:600;cursor:pointer;-webkit-transition:all .3s ease-out;-moz-transition:all .3s ease-out;-ms-transition:all .3s ease-out;-o-transition:all .3s ease-out;transition:all .3s ease-out;padding:17px 5px 17px 15px}
.hbblogbasliklar h5:hover{background:rgba(0,0,0,0.1)}
.hbblogbasliklar_active{background:rgba(0,0,0,0.1)}
.hbblogline{float:left;height:1px;width:100%;background:rgba(0,0,0,0.1)}
.hbveblog h6{float:right;margin-bottom:15px}
.hbveblog h6 a{font-size:17px;color:<?=$gayarlar->renk2;?>}
.hbveblog h6 a:hover{margin-right:5px}
#homeblog h6 a{color:#292929}
.hbveblog h4 i{margin-right:10px}
.foovekaciklama img{width:100%}
#homeblog{float:right}
#homeblog .hbveblog-container{background-color:#292929}
#homeblog h4{color:#292929}
.haberveblog{float:left;width:100%;background-image:url(../images/footer-bg.jpg);background-repeat:repeat;background-position:center center;margin-top:5px;background-size:100% auto;}
.haberveblog-overlay{float:left;width:100%}
.tumulink{color:<?=$gayarlar->renk2;?>;float:right}
.tumulink:hover{margin-right:5px}
#sehirbutonlar-container{width:100%;margin:auto}
.sehirbtn{margin:10px;display:inline-block;height:280px;width:167px;text-align:center;background:#000}
.sehirbtn img{float:left;opacity:.7;filter:alpha(opacity=70)}
/* anasayfa ÅŸehir resimleri Ã¼zerindeki yazÄ±larÄ±n ayaÄ±Ä± */
.sehiristatistk{position:absolute;margin-top:170px;color:#fff;width:167px;text-shadow:0 0 2px #000}
.sehiristatistk h1{font-weight:700;font-size:24px;}
.sehiristatistk h2{font-size:20px;}
.sehiristatistk h3{font-size:18px}
.footinfo{background-color:<?=$gayarlar->renk2;?>;float:left;width:100%;margin-top:15px;text-align:center;color:#fff}
.footinfo h1{line-height:90px;font-size:22px}
.footseolinks{background-color:#454545;float:left;width:100%;padding-top:10px;padding-bottom:10px}
.footseolinks a{float:left;width:20%;margin-top:2px;margin-bottom:2px;color:#ccc;font-size:13px;text-shadow:.06em .06em #000}
.footseolinks a:hover{color:#666;text-shadow:.06em .06em #000}
.footer{background-repeat:repeat;width:100%;padding-top:15px;padding-bottom:15px;color:#FFF;height:auto;float:left;background-position:center center;background-color:#333}
#uyegirispage .footer {display:none;}
.footblok{float:left;width:33%;margin:15px}
.footblok p{font-size:13px}
.footblok h4{padding-top:5px;padding-bottom:5px}
.footblok h3{margin-bottom:25px;font-size:20px;font-weight:700}
#footlinks a{    font-family: 'Titillium Web', sans-serif;font-size:15px;color:#FFF;float:left;width:100%;padding:8px 5px}
#footlinks a:hover{padding-left:10px;color:<?=$gayarlar->renk2;?>}
.footblok h4 span{width:80px;float:left;font-size:16px;}
.footblok h4 {font-size:16px;}
.footblok h5 {font-size:16px;}
.footblok h5 span{width:80px;float:left;margin-bottom:40px;}
#footlinks{width:26%}
#footebulten{width:30%}
#footebulten .btn{float:left;width:59%;-webkit-border-radius:0;-moz-border-radius:0;border-radius:0;font-family:Titillium Web;font-size:18px}
.altfooter{background-color:#292929;width:100%;height:80px;float:left}
.altfooter h5{font-size:14px;color:#fff;float:left;line-height:80px;opacity:.4;filter:alpha(opacity=40)}
.altfooter .headsosyal{float:right}
.altfooter .headsosyal a{line-height:80px}
#footebulten input{background-color:#333;float:left;width:80%;padding:15px;margin-bottom:5px;font-family:Titillium Web;font-size:15px;color:#fff;font-weight:700}
#footebulten ::-webkit-input-placeholder{color:#fff}
#footebulten :-moz-placeholder{color:#fff}
#footebulten ::-moz-placeholder{color:#fff}
#footebulten :-ms-input-placeholder{color:#fff}
.cd-top.cd-is-visible{visibility:visible;opacity:1}
.cd-top.cd-fade-out{opacity:.5}
.cd-top{display:inline-block;height:40px;width:40px;position:fixed;bottom:40px;right:10px;box-shadow:0 0 10px rgba(0,0,0,0.05);overflow:hidden;text-indent:100%;white-space:nowrap;background:rgba(41,41,41,0.8) url(../images/cd-top-arrow.png) no-repeat center 50%;visibility:hidden;opacity:0;-webkit-transition:opacity .3s 0s,visibility 0 .3s;-moz-transition:opacity .3s 0s,visibility 0 .3s;transition:opacity .3s 0s,visibility 0 .3s}
.cd-top.cd-is-visible,.cd-top.cd-fade-out,.no-touch .cd-top:hover{-webkit-transition:opacity .3s 0s,visibility 0 0;-moz-transition:opacity .3s 0s,visibility 0 0;transition:opacity .3s 0s,visibility 0 0}
.cd-top.cd-is-visible{visibility:visible;opacity:1}
.cd-top.cd-fade-out{opacity:.5}
.no-touch .cd-top:hover{background-color:<?=$gayarlar->renk2;?>;opacity:1}
.foot-overlay{width:100%;float:left;background:#000}
.btn{color:<?=$gayarlar->renk2;?>;text-align:center;border:1px solid <?=$gayarlar->renk2;?>;-webkit-border-radius:30px;-moz-border-radius:30px;border-radius:30px;font-size:16px;cursor:pointer;padding:12px 50px}
.btn:hover{color:#fff;background:<?=$gayarlar->renk2;?>}
#footebulten .btn{float:left;width:59%;-webkit-border-radius:0;-moz-border-radius:0;border-radius:0;font-family:Titillium Web;font-size:18px}
.altfooter{background-color:#292929;width:100%;height:80px;float:left}
.altfooter h5{font-size:14px;color:#fff;float:left;line-height:80px;opacity:.4;filter:alpha(opacity=40)}
.altfooter .headsosyal{float:right;margin:0}
.altfooter .headsosyal a{line-height:80px}
.moblogo{display:none}
.headerbg{float:left;height:250px;width:100%;background-image:url(../images/slide1.jpg);background-repeat:repeat;background-position:center center;color:#FFF;background-size:100% 100%;}
.headtitle{float:left;margin-top:190px;width:100%;text-shadow:0 0 2px #555;word-wrap:break-word;    z-index: 5;    position: relative;}
.headtitle h1{font-size:84px;font-weight:800;display:none;}
.sayfayolu{font-size:18px;font-family:Titillium Web}
.sayfayolu a{color:#FFF;float:left;}
.sayfayolu i {
    font-size: 10px;
    margin: 0px 10px;
    float: left;
    line-height: 28px;
}
.sayfayolu a:hover{color:<?=$gayarlar->renk2;?>}
#kfirmaprofili .headtitle {margin-top:185px;}
#kfirmaprofili .headtitle h1 {
    font-size: 24px;
    display: block;
    float: left;
    margin-right: 20px;
}
#kurumsalprofillink {
    font-size: 22px;
    font-weight: 200;
    margin-top: 2px;
}
.gelismissirala{float:right;width:160px;font-size:13px;    margin-top:-30px;}
.ilanlistesi table{font-size:14px}
.mobilanbilgi{display:none}
#bigcontent{width:100%}
.paypasbutonlar{position:relative;float:right;padding:10px;margin-top:-63px;margin-bottom:-30px;text-align:center;color:#666;background:#fff;-webkit-border-top-left-radius:3px;-webkit-border-top-right-radius:3px;-moz-border-radius-topleft:3px;-moz-border-radius-topright:3px;border-top-left-radius:3px;border-top-right-radius:3px}
.paypasbutonlar h5{font-size:15px;font-weight:600}
.paypasbutonlar a{color:#666;margin:5px;float:left;height:40px;width:40px;font-size:16px;border:1px solid #666;-webkit-border-radius:100%;-moz-border-radius:100%;border-radius:100%;text-align:center;line-height:250%}
.paypasbutonlar a:hover{color:#fff;background:#666}
#facepaylas:hover{background:#365899;border:1px solid #365899}
#twitpaylas:hover{background:#1da1f2;border:1px solid #1da1f2}
#googlepaylas:hover{background:#d73d32;border:1px solid #d73d32}
#linkedpaylas:hover{background:#008cc9;border:1px solid #008cc9}
.ilanfotolar{float:left;width:540px;margin-right:10px;}
.ilandigerfotolar img{width:116px;height:90px}
.ilanbigfoto{box-shadow:0 0 5px #666;margin-bottom:5px}
.ilanozellikler{float:left;width:250px;}
.ilanozellikler table tr td h5{font-size:13px;font-weight:600}
.ilanozellikler table tr td{font-size:13px}
.ilanozellikler table tr td h5 a:HOVER{text-decoration:underline}
.danisman{float:right;width:210px;border:1px solid #EEE;text-align:center}
.danismantitle{font-size:16px;font-weight:700;margin-top:15px;margin-bottom:15px;color:<?=$gayarlar->renk2;?>}
.danisman img{margin-bottom:10px;-webkit-border-radius: 4px;-moz-border-radius: 4px;border-radius: 4px;width:150px;height:150px;}
.danisman h4{font-size:16px}
.danisman h5{font-size:16px;margin-top:15px}
.danismaniletisim{padding:10px;border:1px solid #ecb200;color:#ecb200;margin:auto;width:80%;margin-bottom:12px;font-size:13px;-webkit-border-radius:4px;-moz-border-radius:4px;border-radius:4px}
.danismaniletisim h6{font-size:16px;font-weight:700;margin-bottom:5px}
.ilanaciklamalar h3{float:left;font-size:18px;font-weight:700;width:100%;padding-bottom:10px;margin-bottom:10px;border-bottom-width:2px;border-bottom-style:solid;border-bottom-color:<?=$gayarlar->renk2;?>}
.ilanozellik{margin:auto;width:90%}
.ilanozellik h4{float:left;width:100%;margin-bottom:10px;padding-bottom:10px;border-bottom-width:1px;border-bottom-style:solid;border-bottom-color:#CCC;margin-top:20px}
.ilanaciklamalar{margin-bottom:30px;float:left;width:100%;margin-top:30px;}
.ilanozellik span{float:left;width:183px;margin-bottom:10px;color:#ccc;font-size:14px}
#ozellikaktif{color:#000;font-weight:700}
.ilanozellik span i{color:#4CAF50;margin-right:7px}
.sidebar h2{font-size:18px;font-weight:700;margin-bottom:15px}
.sidebar h2 i{margin-right:7px}
.listeleme{float:left;padding-top:15px;padding-bottom:15px}
.listefoto{float:left;width:200px;overflow:hidden;text-align:center;height:180px;border:5px solid #F2F2F2;margin-right:10px;-webkit-transition:all .3s ease-out;-moz-transition:all .3s ease-out;-ms-transition:all .3s ease-out;-o-transition:all .3s ease-out;transition:all .3s ease-out}
.detaylink{color:<?=$gayarlar->renk2;?>} 
.detaylink:hover{margin-left:7px}
.listeicerik h3 a{color:<?=$gayarlar->renk2;?>}
.listeicerik h3 a:hover{text-decoration:underline}
.listeleme:hover .listefoto{border:5px solid <?=$gayarlar->renk2;?>}
.sidelinks a{float:left;width:100%;margin-bottom:10px;color:#666}
.sidelinks a:hover{padding-left:5px;color:#000}
.sidelinks{margin-bottom:25px;float:left}
.sidelinks i{margin-right:7px;font-size:12px}
#projeler .sehirbtn{height:290px;width:320px}
#projeler #sehirbutonlar-container{width:100%}
#projeler .sehiristatistk{width:320px;margin-top:230px}
#projefotolar{width:770px;margin-bottom:25px}
.iletisimdetay{float:left;width:49%}
.iletisimform{float:right;width:49%}
.iletisim table{font-family:Titillium Web;font-size:18px}
.iletisim table tr td{padding:10px;border-bottom-width:2px;border-bottom-style:solid;border-bottom-color:#F0F0F0}
.iletisim h3{color:#333;font-size:22px}
.iletisimform table tr td input {background-color:#F3F3F3;padding:13px;font-family:Titillium Web;font-size:18px;font-weight:500;border-style:none}
.iletisimform table tr td textarea {background-color:#F3F3F3;padding:13px;font-family:Titillium Web;font-size:18px;font-weight:500;border-style:none}
.iletisimform table tr td input[type=text] {-webkit-transition:all .3s ease-in-out;-moz-transition:all .3s ease-in-out;-ms-transition:all .3s ease-in-out;-o-transition:all .3s ease-in-out;outline:none;border:1px solid #F3F3F3}
.iletisimform table tr td input[type=text]:focus,textarea:focus{box-shadow:0 0 5px #00b7be;border:1px solid #00b7be}
.iletisimform::-webkit-input-placeholder{color:#666}
.iletisimform:-moz-placeholder{color:#666}
.iletisimform::-moz-placeholder{color:#666}
.iletisimform:-ms-input-placeholder{color:#666}
.iletisimform table tr td .btn{float:left;margin-right:10px}
.subebayibtn{width:100%;border:1px solid #00b7be;float:left;font-size:24px;color:#00b7be;font-family:Titillium Web;margin-bottom:10px;line-height:60px}
.subebayibtn i{font-size:35px;float:left;margin-left:20px;margin-right:20px;line-height:60px}
.subebayibtn:hover{color:#fff;background:#00b7be}
.subebayibtns{margin:auto;width:40%;margin-top:35px}
.lokasyonsec{text-align:center;margin-top:30px;margin-bottom:50px;}
.lokasyonsec select{width:320px;padding:15px;font-size:20px;font-family:Open Sans;font-weight:600}
.lokasyonsec h3{font-weight:600;margin-bottom:25px}
.bayisubedetay{width:70%;margin:25px auto auto}
.bayisubedetay table tr td{border-bottom-width:1px;border-bottom-style:dotted;border-bottom-color:#CCC}
.checkbox-custom,.radio-custom{opacity:0;position:absolute}
.checkbox-custom,.checkbox-custom-label,.radio-custom,.radio-custom-label{display:inline-block;vertical-align:middle;cursor:pointer}
.checkbox-custom-label,.radio-custom-label{position:relative}
.checkbox-custom + .checkbox-custom-label:before,.radio-custom + .radio-custom-label:before{content:'';background:#fff;border:1px solid <?=$gayarlar->renk2;?>;display:inline-block;line-height:20px;vertical-align:middle;width:20px;height:20px;padding:2px;margin-right:10px;text-align:center}
.checkbox-custom:checked + .checkbox-custom-label:before{content:"\f00c";font-family:'FontAwesome';background:<?=$gayarlar->renk2;?>;color:#fff}
.radio-custom + .radio-custom-label:before{border-radius:50%}
.radio-custom:checked + .radio-custom-label:before{content:"\f00c";font-family:'FontAwesome';color:<?=$gayarlar->renk2;?>}
.girisyap{float:left;width:49%}
.uyeolgirisyap{float:left;width:100%;margin-bottom:80px}
.sifreunuttulink{float:right}
.uyeolgirisyap .btn{float:right;    background: white;margin-bottom:15px;}
.uyeolgirisyap .btn:hover{float:right;    background: <?=$gayarlar->renk2;?>; color:white;}
.uyeol{float:left;width:45%;background:#fff;-webkit-border-radius:4px;-moz-border-radius:4px;border-radius:4px}
.girisyap h3{text-align:center;font-size:20px;margin-top:30px;font-weight:700}
.uyeolgirisyap .ilanvertanitim{float:left;width:100%;margin-top:25px;margin-bottom:25px}
.uyepanellinks .btn{color:#333;text-align:left;border:1px solid #333;-webkit-border-radius:2px;-moz-border-radius:2px;border-radius:2px;font-size:15px;cursor:pointer;width:157px;margin-bottom:7px;float:left;padding:12px 50px 12px 20px}
.uyepanellinks .btn:hover{color:#fff;border:1px solid #333;background:#333}
.uyepanellinks .btn i{margin-right:10px}
.uyepanellinks h5{margin-bottom:25px}
.uyepaneltitle{margin-bottom:46px;font-weight:700}
#uyeaktifbtn{color:#fff;border:1px solid #333;background:#333}
#uyepanelilantable{font-size:13px}
.uyeilankontrolbtn{color:#999;font-size:18px}
.uyeilankontrolbtn:hover{color:#000}
.uyeilankontrolbtn i{margin:5px}
.ilantarih{margin-top:5px;float:left;margin-right:10px}
#uyepanelyeniilan:hover{color:#fff;border:1px solid green;background:green}
#uyepanelyeniilan{color:green;border:1px solid green;background:#fff;font-weight:bold;}
.uyedetay .ilanozellik{width:100%}
.uyedetay .ilanozellik span{color:#666}
.uyepanel .ilanozellik span input{width:20px;float:left}
.uyepanel .ilanozellik span input:focus{box-shadow:none;border:1px solid #fff}
.boxedcontainer{max-width:1170px;margin:auto;padding:0 30px}
.tp-banner-container{width:100%;position:relative;padding:0}
.tp-banner{width:100%;position:relative}
.tp-banner-fullscreen-container{width:100%;position:relative;padding:0}
.homearama{text-align:center;width:100%;color:#fff;margin-top:280px;text-shadow:0 0 2px #000}
.homearama h3{font-size:44px;margin-bottom:10px;font-weight:700}
.homearama h3 strong{font-family:Georgia,"Times New Roman",Times,serif;font-style:italic;color:#FC0}
.homearama h4{font-size:29px;margin-bottom:40px;font-weight: 200;}
.homearama select{font-family:"Open Sans";border:none;width:16%;padding:16px;margin-right:-4px;background:#fff;font-weight:700;border-right-width:1px;border-right-style:solid;border-right-color:#e6e6e6;font-size:16px}
.homearamaselect{width:1000px;margin:auto}
.homearama select{-webkit-appearance:none;border-radius:0}
.gelismisara input{-webkit-appearance: none;
    border-radius: 0;
    border: 1px solid #FFF;
    font-family: "Open Sans";
    color: #333;
    font-size: 13px;
    padding: 8px;
    width: 100%;
     background: none;
    margin-bottom: 3px;
    border-bottom-width: 2px;
    border-bottom-color: #ccc;
    padding: 10px 0;
    border-style: none none solid;}
.gelismisara select {
    -webkit-appearance: none;
    border-radius: 0;
    border: 1px solid #FFF;
    font-family: "Open Sans";
    color: #333;
    font-size: 13px;
    width: 100%;
    /* background: #F2F2F2; */
    margin-bottom: 3px;
    border-bottom-width: 2px;
    border-bottom-color: #ccc;
    padding: 8px 0;
    border-style: none none solid;
    background: url(../images/select_arrow.png) no-repeat center right #fff;
}
.gelismisara select:focus {border-bottom-width: 2px;
    border-bottom-color: <?=$gayarlar->renk2;?>;
    padding-left:5px;}
    .gelismisara input:focus {border-bottom-width: 2px;
    border-bottom-color: <?=$gayarlar->renk2;?>;
    padding-left:5px;}
.gelismisara .checktext {font-size:13px;}
#leftradius{-webkit-border-top-left-radius:4px;-webkit-border-bottom-left-radius:4px;-moz-border-radius-topleft:4px;-moz-border-radius-bottomleft:4px;border-top-left-radius:4px;border-bottom-left-radius:4px}
.homearabtn{background:#fc0;text-shadow:0 0 2px #999;padding:17px;font-weight:700;color:#fff;-webkit-border-top-right-radius:4px;-webkit-border-bottom-right-radius:4px;-moz-border-radius-topright:4px;-moz-border-radius-bottomright:4px;border-top-right-radius:4px;border-bottom-right-radius:4px}
.homearabtn:hover{background:#e2aa00}
#ilantutar{width:70%;float:left}
#ilanpbirimi{width:22%;margin-left:2px}  
.ilandigerfotolar{margin-bottom:20px} 
.sayfadetay p{line-height:24px}
.listeicerik p{line-height:24px} 
.gmapsecenek input{float:left;width:112px;margin:5px}
#IlanOlusturForm table {font-size:14px} 
#IlanOlusturForm input{padding-top:7px;padding-bottom:7px;font-size:13px}
#IlanOlusturForm select{padding-top:7px;padding-bottom:7px;font-size:13px}
#IlanOlusturForm_output{float:left;margin-top:15px}
.alert-info {border:1px solid #009688;padding:15px;color:#009688;-webkit-border-radius:4px;-moz-border-radius:4px;border-radius:4px;margin-top:15px;margin-bottom:15px;}
.alert-info a{color:#009688;}
.alert-notice {border:1px solid #e91e63;padding:15px;color:#e91e63;-webkit-border-radius:4px;-moz-border-radius:4px;border-radius:4px}
.alert-notice a{color:#e91e63;}
.alert-error {border:1px solid #f44336;padding:15px;background:#f44336;color:white;-webkit-border-radius:4px;-moz-border-radius:4px;border-radius:4px}
.alert-error a{color:#fff;}
#uyegirispage video{position:fixed;top:50%;left:50%;min-width:100%;min-height:100%;width:auto;height:auto;z-index:-100;transform:translateX(-50%) translateY(-50%);background:url(//demosthenes.info/assets/images/polina.jpg) no-repeat;background-size:cover;transition:1s opacity;opacity:.4;filter:alpha(opacity=40)}
.stopfade{opacity:.5}
#uyegirispage{background:#000}
#uyegirispage .uyeolgirisyap input{border:1px solid #ccc}
#uyegirispage .menu{display:none}
#uyegirispage .footinfo{display:none}
#uyegirispage .footseolinks{display:none}
.uyeolgirisslogan{float:right;color:#fff;width:48%;text-align:center;margin-top:75px}
#uyegirispage .uyeolgirisyap .gonderbtn{float:none;color:#fff;border:1px solid #fff;padding:15px 45px;font-weight:700}
#uyegirispage .uyeolgirisyap .gonderbtn:hover{background:#fff;color:#000}
.uyeolgirislogo{float:left;width:100%;margin-top:30px;margin-bottom:30px;padding-bottom:20px;border-bottom-width:1px;border-bottom-style:solid;border-bottom-color:#000}
.uyeolgirislogo h1{float:right;color:#fff;margin-top:30px}
.uyeavatar img{width:130px;height:130px;-webkit-border-radius:100%;-moz-border-radius:100%;border-radius:100%}
.avatarguncelle{position:absolute;background:rgba(0,0,0,0.3);color:#fff;width:50px;height:50px;line-height:50px;text-align:center;font-size:16px;cursor:pointer;margin-left:40px;margin-top:40px;-webkit-border-radius:100%;-moz-border-radius:100%;border-radius:100%}
.avatarguncelle:hover{background:rgba(0,0,0,0.7)}
.uyeprofil{margin-top:35px;float:left;width:100%}
.uyeprofil .danisman{float:left}
.mobilonaybtn{float:none;color:#000;margin-top:10px;border:1px solid #000;padding:10px 35px;display: inline-block;}
.mobilonaybtn:hover{float:none;color:#fff;background:#000}

.uyeprofil .gelismissirala{margin-top:-13px}
.headerwhite{display:none;background-image:url(../images/headerwhite.png);background-repeat:repeat;background-position:center bottom;width:100%;height:217px;margin-top:-127px;}
#msjkisiler{width:325px}
#msjkisiler input{margin-bottom:5px;-webkit-border-radius:2px;-moz-border-radius:2px;border-radius:2px;border:none;background:#eee;padding:10px 0 10px 35px;width:86%}
#msjaraicon{position:absolute;margin-left:10px;margin-top:10px;color:gray}
.mesajkisi{float:left;width:98%;padding:10px 0 10px 10px;border-bottom-width:1px;border-bottom-style:solid;border-bottom-color:#eee;-webkit-transition:all .3s ease-out;-moz-transition:all .3s ease-out;-ms-transition:all .3s ease-out;-o-transition:all .3s ease-out;transition:all .3s ease-out}
.mesajkisi:hover{background:#eee}
#mesajkisiaktif{background:#eee}
.mesajkisi img{-webkit-border-radius:100%;-moz-border-radius:100%;border-radius:100%;width:55px;height:55px;float:left}
.mesajkisiinfo{float:right;width:80%}
.mesajkisiinfo h4{font-size:18px}
.mesajkisiinfo p{font-size:12px;margin:0;color:rgba(153,153,153,1)}
ul.tab{list-style-type:none;margin:0;padding:0;overflow:hidden;border-top-width: 1px;
	border-right-width: 1px;
	border-left-width: 1px;
	border-top-style: solid;
	border-right-style: solid;
	border-left-style: solid;
	border-top-color: #eee;
	border-right-color: #eee;
	border-left-color: #eee;background-color:#eee}
ul.tab li{float:left}
ul.tab li a{display:inline-block;color:#000;text-align:center;padding:14px 40px;text-decoration:none;transition:.3s;font-size:16px;border-right-width: 1px;
    border-right-style: solid;
    border-right-color: #f1f1f1;}
ul.tab li a:hover{background-color:#ddd}
ul.tab li a:focus,.active{background-color:#fff}
.tabcontent{display:none;padding:6px 12px;border:1px solid #eee;border-top:none}
.tabcontent h4{margin-top:10px}
.tabcontent{-webkit-animation:fadeEffect 1s;animation:fadeEffect 1s}
@-webkit-keyframes fadeEffect{from{opacity:0}to{opacity:1}}@keyframes fadeEffect{from{opacity:0}to{opacity:1}}
.showscroll{display:block;width:100%;height:400px;overflow-x:hidden;overflow-y:scroll}
::-webkit-scrollbar{width:10px}
::-webkit-scrollbar-track{background:transparent}
::-webkit-scrollbar-thumb{border-radius:10px;-webkit-box-shadow:inset 0 0 6px rgba(0,0,0,0.3);-webkit-transition:all .3s ease-out;-moz-transition:all .3s ease-out;-ms-transition:all .3s ease-out;-o-transition:all .3s ease-out;transition:all .3s ease-out}
::-webkit-scrollbar-thumb:hover{border-radius:10px;-webkit-box-shadow:inset 0 0 6px rgba(0,0,0,0.5)}
.showscroll:hover::-webkit-scrollbar-thumb{-webkit-box-shadow:inset 0 0 6px rgba(0,0,0,0.5)}
#uyemesajlari{width:67%}
.uyemsjprofili{float:left;width:100%;padding-bottom:15px;border-bottom-width:1px;border-bottom-style:solid;border-bottom-color:#eee}
.uyemsjprofili img{-webkit-border-radius:100%;-moz-border-radius:100%;border-radius:100%;float:left;margin-right:25px}
.uyemsjprofili h4{margin-bottom:15px}
.uyemsjprofili h4 span{font-size:16px}
.uyemsjprofili .gonderbtn{font-size:13px;padding:5px 15px;margin-right:7px}
.msjbaloncuk{float:LEFT;width:75%;padding:15px;margin:10px;background:#f1f1f1;font-size:14px;-webkit-border-radius:4px;-moz-border-radius:4px;border-radius:4px;word-wrap:break-word;}
.msjbaloncuk h5{float:left;width:100%;padding-top:7px;border-top-width:1px;border-top-style:solid;border-top-color:#ccc;margin-top:7px;font-size:14px}
.bnmmsjim{float:right;text-align:right;background:#dcffe1}
.uyemsjarea{float:left;width:100%;margin-top:10px;padding-top:15px;border-top-width:1px;border-top-style:solid;border-top-color:#eee}
.msjvar{background:red;text-align:center;color:#fff;width:20px;height:20px;line-height:20px;font-size:12px;position:relative;-webkit-border-radius:100%;-moz-border-radius:100%;border-radius:100%;float:left;margin-right:-20px}
.prufilurllink {   width: 100%;    float: left;    word-wrap: break-word;}
.sidebar #ContactMessagesBox {    height: 600px;}
.modalDialog{position:fixed;top:0;right:0;bottom:0;left:0;background:rgba(0,0,0,0.8);z-index:99999;opacity:0;-webkit-transition:opacity 400ms ease-in;-moz-transition:opacity 400ms ease-in;transition:opacity 400ms ease-in;pointer-events:none}
.modalDialog:target{opacity:1;pointer-events:auto}
.modalDialog > div{width:500px;position:relative;margin:10% auto;border-radius:4px;background:#fff;height:auto}
.modalDialog > div h2{font-size:20px;padding-bottom:15px;margin-bottom:15px;border-bottom-width:1px;border-bottom-style:solid;border-bottom-color:#eee}
.modalDialog > div textarea{width:97%;font-size:16px;height:150px}
.close{background:#606061;color:#FFF;line-height:25px;position:absolute;right:-12px;text-align:center;top:-10px;width:24px;text-decoration:none;font-weight:700;-webkit-border-radius:12px;-moz-border-radius:12px;border-radius:12px;-moz-box-shadow:1px 1px 3px #000;-webkit-box-shadow:1px 1px 3px #000;box-shadow:1px 1px 3px #000}
.close:hover{background:#00d9ff}
.modalDialog > div .gonderbtn{float:none}
.list_carousel{width:100%}
.list_carousel ul{margin:0;padding:0;list-style:none;display:block}
.list_carousel #foo2 li{width:800px;height:auto;display:block;float:left}
.list_carousel.responsive{width:auto;margin-left:0}
.clearfix{float:none;clear:both}
.pager{float:right}
.pager a{color:transparent;background:#eee;width:20px;height:20px;float:left;-webkit-border-radius:100%;-moz-border-radius:100%;border-radius:100%;margin-left:5px}
.pager a.selected{background:#ccc}
.list_carousel #foo3 .kareilan{width:100%;margin:0}
/* Vitrin Ä°lanlarÄ± resim geniÅŸliÄŸi */
.list_carousel #foo3 li{width:245px;height:auto;display:block;float:left;margin:7px}
.list_carousel #foo4 .kareilan{width:100%;margin:0}
/* Ã–neÃ§Ä±kanÄ°lanlarda GÃ¶ster resim geniÅŸliÄŸi */
.list_carousel #foo4 li{width:245px;height:auto;display:block;float:left;margin:7px}
/* SÄ±cak FÄ±rsatlar resim geniÅŸliÄŸi */
.list_carousel #foo5{width:100%;margin:0}
.list_carousel #foo5 li{width:150px;height:auto;display:block;float:left;text-align:center;margin:7px}
/* DanÄ±ÅŸman resim ve yazÄ± ve gÃ¶lgeleme */
.list_carousel #foo55{width:100%;margin:0}
.list_carousel #foo55 li{width:150px;height:auto;display:block;float:left;text-align:center;margin:7px}
.danismanfotoana{width:150px;box-shadow:0 0 0 #fff,inset 0 0 20px 10px #666;height:150px;-webkit-border-radius:6px;-moz-border-radius:6px;border-radius:6px;background-size:100% 100%;-webkit-filter:grayscale(10%);-moz-filter:grayscale(10%);filter:grayscale(10%);transition:all .5s ease}
.anadanisman:hover .danismanfotoana{box-shadow:0 0 0 #fff,inset 0 0 0 0 #fff;-webkit-filter:grayscale(0%);-moz-filter:grayscale(0%);filter:grayscale(0%)}
.danismanbilgisi{margin-top:10px}
.danismanbilgisi h4{font-size:16px;font-weight:700;color:#be2527}
.danismanbilgisi h5{font-size:15px}

#EngelButon{color:red;border:1px solid red}
#EngelButon:hover{background:red;color:#fff}
.favyaz{float:right;font-size:13px;margin-right:10px;margin-top:5px;line-height:20px}
.favyaz a{color:#999}
.favyaz a:hover{color:#333}
#favyazaktif{color:#e91e63}
.desktopclear{clear:both}
#uyepaketlink{border:1px solid #FF9800;color:#FF9800;font-weight:700}
#uyepaketlink:hover{color:#fff;background:#FF9800}
#galeri_video_ekle{float:left;width:100%}
#galeri_video_ekle input{width:250px;margin-top:60px;margin-bottom:60px}
    
    .yuklebar {    width: 70%;
    border: 1px solid <?=$gayarlar->renk2;?>;
    height: 30px;
    margin: auto;
    margin-top: 60px;
 }
    .yuklebarasama {width:1%;background:<?=$gayarlar->renk2;?>;height:30px;-webkit-transition: all 0.3s ease-out;
-moz-transition: all 0.3s ease-out;
-ms-transition: all 0.3s ease-out;
-o-transition: all 0.3s ease-out;
transition: all 0.3s ease-out;}

#percent {  
    float: left;
    width: 100%;
    text-align: center;
    font-size: 16px;
    font-weight: bold;
    line-height: 30px;
    color: <?=$gayarlar->renk2;?>;}
    
    .dovizkredi {  float: left;    width: 100%;    margin-top: 35px;}
.dovizkredi table { width:100%;}
.dovizkurlari {float:left;width:46%}
.kredihesaplama {float:right;width:46%}
.dovizkredi h4 {font-size:18px;color:<?=$gayarlar->renk2;?>}
.ad728home{width:728px;margin:auto;margin-top:20px;margin-bottom:2px}
.ad336x280{float:right;margin:10px}
.emlaktalepformu{width:65%;margin:auto;margin-bottom:35px}
.uyepaket{float:left;width:22.7%;text-align:center;box-shadow:0 0 5px #ccc;margin:10px;background:#fff;background:-moz-linear-gradient(top,#fff 0%,#ededed 100%);background:-webkit-linear-gradient(top,#fff 0%,#ededed 100%);background:linear-gradient(to bottom,#fff 0%,#ededed 100%);filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffff',endColorstr='#ededed',GradientType=0);border:2px solid #ccc}
.uyepaket h1 {    font-weight: bold;    font-size: 22px;    color: <?=$gayarlar->renk2;?>;    margin-bottom: 15px;    padding-bottom: 15px;    border-bottom-width: 1px;    border-bottom-style: solid;    border-bottom-color: #EEE;}
.uyepaket span{float:left;font-size:14px;text-align:left;width:100%;padding:10px 0;border-bottom-width:1px;border-bottom-style:dotted;border-bottom-color:#EEE}
.uyepaket select{margin-top:12px;margin-bottom:12px;font-weight:700;text-align-last:center}
.uyepaket .btn{display:inline-block}
[data-tooltip],.tooltip{position:relative;cursor:pointer}
.tooltip-bottom{color:#999}
.tooltip-bottom:hover{color:#000}
[data-tooltip]:before,[data-tooltip]:after,.tooltip:before,.tooltip:after{position:absolute;visibility:hidden;-ms-filter:progid:DXImageTransform.Microsoft.Alpha(Opacity=0);filter:progid:DXImageTransform.Microsoft.Alpha(Opacity=0);opacity:0;-webkit-transition:opacity .2s ease-in-out,visibility .2s ease-in-out,-webkit-transform .2s cubic-bezier(0.71,1.7,0.77,1.24);-moz-transition:opacity .2s ease-in-out,visibility .2s ease-in-out,-moz-transform .2s cubic-bezier(0.71,1.7,0.77,1.24);transition:opacity .2s ease-in-out,visibility .2s ease-in-out,transform .2s cubic-bezier(0.71,1.7,0.77,1.24);-webkit-transform:translate3d(0,0,0);-moz-transform:translate3d(0,0,0);transform:translate3d(0,0,0);pointer-events:none}
[data-tooltip]:hover:before,[data-tooltip]:hover:after,[data-tooltip]:focus:before,[data-tooltip]:focus:after,.tooltip:hover:before,.tooltip:hover:after,.tooltip:focus:before,.tooltip:focus:after{visibility:visible;-ms-filter:progid:DXImageTransform.Microsoft.Alpha(Opacity=100);filter:progid:DXImageTransform.Microsoft.Alpha(Opacity=100);opacity:1}
.tooltip:before,[data-tooltip]:before{z-index:1001;border:6px solid transparent;background:transparent;content:""}
.tooltip:after,[data-tooltip]:after{z-index:1000;padding:8px;width:160px;background-color:#000;background-color:hsla(0,0%,20%,0.9);color:#fff;content:attr(data-tooltip);font-size:12px;line-height:1.2}
[data-tooltip]:before,[data-tooltip]:after,.tooltip:before,.tooltip:after,.tooltip-top:before,.tooltip-top:after{bottom:100%;left:50%}
[data-tooltip]:before,.tooltip:before,.tooltip-top:before{margin-left:-6px;margin-bottom:-12px;border-top-color:#000;border-top-color:hsla(0,0%,20%,0.9)}
[data-tooltip]:after,.tooltip:after,.tooltip-top:after{margin-left:-80px}
[data-tooltip]:hover:before,[data-tooltip]:hover:after,[data-tooltip]:focus:before,[data-tooltip]:focus:after,.tooltip:hover:before,.tooltip:hover:after,.tooltip:focus:before,.tooltip:focus:after,.tooltip-top:hover:before,.tooltip-top:hover:after,.tooltip-top:focus:before,.tooltip-top:focus:after{-webkit-transform:translateY(-12px);-moz-transform:translateY(-12px);transform:translateY(-12px)}
.tooltip-left:before,.tooltip-left:after{right:100%;bottom:50%;left:auto}
.tooltip-left:before{margin-left:0;margin-right:-12px;margin-bottom:0;border-top-color:transparent;border-left-color:#000;border-left-color:hsla(0,0%,20%,0.9)}
.tooltip-left:hover:before,.tooltip-left:hover:after,.tooltip-left:focus:before,.tooltip-left:focus:after{-webkit-transform:translateX(-12px);-moz-transform:translateX(-12px);transform:translateX(-12px)}
.tooltip-bottom:before,.tooltip-bottom:after{top:100%;bottom:auto;left:50%}
.tooltip-bottom:before{margin-top:-12px;margin-bottom:0;border-top-color:transparent;border-bottom-color:#000;border-bottom-color:hsla(0,0%,20%,0.9)}
.tooltip-bottom:hover:before,.tooltip-bottom:hover:after,.tooltip-bottom:focus:before,.tooltip-bottom:focus:after{-webkit-transform:translateY(12px);-moz-transform:translateY(12px);transform:translateY(12px)}
.tooltip-right:before,.tooltip-right:after{bottom:50%;left:100%}
.tooltip-right:before{margin-bottom:0;margin-left:-12px;border-top-color:transparent;border-right-color:#000;border-right-color:hsla(0,0%,20%,0.9)}
.tooltip-right:hover:before,.tooltip-right:hover:after,.tooltip-right:focus:before,.tooltip-right:focus:after{-webkit-transform:translateX(12px);-moz-transform:translateX(12px);transform:translateX(12px)}
.tooltip-left:before,.tooltip-right:before{top:3px}
.tooltip-left:after,.tooltip-right:after{margin-left:0;margin-bottom:-16px}
.uyeliktaksit{margin-top:25px;margin-bottom:25px}
.ilanasamalar{float:left;width:100%;margin-bottom:30px;padding-bottom:20px;border-bottom-width:1px;border-bottom-style:solid;border-bottom-color:#CCC}
.ilanasamax{text-align:center;color:#777;width:20%;float:left}
.ilanasamax h3{font-family:"open sans";width:80px;background:#eee;text-align:center;height:80px;margin-bottom:7px;-webkit-border-radius:100%;-moz-border-radius:100%;border-radius:100%;line-height:80px;font-size:34px;font-weight:700;color:#777;text-shadow: 0.03em 0.03em #fff;}
.asamaline{width:100%;background:#eee;height:5px;margin-top:60px;float:left;margin-bottom:-45px}
#asamaaktif h3 {background:<?=$gayarlar->renk2;?>;color:white;text-shadow:none;}
#asamaaktif  {color:<?=$gayarlar->renk2;?>; font-weight:bolder;}
.doping5 {background:#e2fdc4;font-weight:bold;}
#dopingler table tr td {font-size:14px;padding:10px;}
#dopingler table tr td input {box-shadow:none;width:auto;}
#OdemeYontemiForm table {font-size: 13px;
    line-height: 22px;}
    .uyeolgirisslogan h4 {font-weight:200;}

.kurumsalbtns {background:#eee;width:100%;height:60px;float:left;}
.kurumsalbtns a {
    line-height: 60px;
    padding: 0px 30px;
    float: left;
    font-size: 18px;
}
.kurumsalbtns a:hover {background:#ccc;}
#kurumsalbtnaktif {background:white;}
.firmaprofililetisim {float:left;width:49%; border:2px solid #eee;color:#777;min-height:300px;}
.pfirmalogo {float:left;text-align:center;width:45%;}
.pfirmalogo img {-webkit-border-radius: 6px;-moz-border-radius: 6px;border-radius: 6px;}
.firmaprofililetisim i{    margin-bottom: 15px;width: 28px;    margin-right: 5px;
    float: left;text-align:center;
    margin-top: 3px;
    font-size: 16px;
    color: #d6d6d6;}
.firmaprofililetisim span {float:left;margin-bottom:10px;width:100%;}
.fprofilinfos {float:right;width:52%;}
.fprofilinfos h5 {
    font-size: 15px;
    line-height: 22px;
    margin-bottom: 25px;
}
.fprofilinfos h4 {font-size:18px;    margin-bottom: 7px;}
.fprofilmap {
    float: right;
    width: 49%;
    border: 2px solid #eee;
}
  #accordion {
    margin-top: 40px;
    font-family: Open Sans, sans-serif;
}
  #accordion h3 {font-size:16px;font-weight:bold;-webkit-transition: all 0.3s ease-out;
-moz-transition: all 0.3s ease-out;
-ms-transition: all 0.3s ease-out;
-o-transition: all 0.3s ease-out;
transition: all 0.3s ease-out;}
  #accordion div p {font-family: Open Sans, Sans Serif;}
  .ui-state-active {    background: #be2527;}
  #accordion table {font-size:14px;}
  #accordion table tr td {padding:10px;}
#faiz {width:101%;}
.kurumsalbtns .gonderbtn {
float: right;
    padding: 10px 20px;
    font-size: 16px;
    line-height: normal;
    margin-top: 8px;
    }
.kurumsalbtns .gonderbtn:hover {
background:#1F282F;color:white;
    }
.profilgsm {font-size:18px;color:white;background:#4CAF50;padding-top:8px;padding-bottom:8px;    text-shadow: 0.08em 0.08em #327835;}
#uyelik_bilgileri table {font-size:14px;}
#uyelik_bilgileri table input {font-size:13px;}
#uyelik_bilgileri table textarea {font-size:13px;}
#uyelik_bilgileri table select {font-size:13px;}
#MesajlasmaContent textarea {width:98%;}
.uyepaneltitle .gonderbtn {float:right; font-size:14px;}
#DanismanEkleForm table {font-size:14px;}
#DanismanEkleForm table input {font-size:13px;}
#DanismanEkleForm table textarea {font-size:13px;}
#DanismanEkleForm table select {font-size:13px;}
#DanismanEkleForm table textarea {font-size:13px;}
.profilfirmahakkinda {line-height:23px;color:#555;}
.sehirbutonlar  {
    display:inline-block;
    text-align:center;
    width: 100%;
    margin-top: 20px;
}
.tablolistx {float:right;margin-left:20px;    margin-top: -26px;}
.tablolistx a {color:#ccc;    float: right;    margin-left: 10px;       margin-top: 7px; }
.tablolistx a:hover {color:#333;}
#tablolistx-aktif {color:#333;}
.doping5_grid .kareilanbaslik h3 {color:green;}
.doping5_grid {    box-shadow: 0 0 5px green;}
.titlekisalt {    font-size: 16px;}
.ilanfototasi {  position: absolute;
    margin-left: 90px;
    color: white;
    font-size: 22px;
    opacity: 0.8;
    filter: alpha(opacity=80);cursor:-webkit-grabbing;}
 
 .ilanfototasi:hover { 
    opacity: 1.8;
    filter: alpha(opacity=10);}
    
.ilanvertanitim  {
    margin-bottom: 25px;
    float: left;
    width: 100%;
}

.profilfirmahakkinda img {webkit-border-radius: 6px;
    -moz-border-radius: 6px;
    border-radius: 6px;}

#kurumsalprofillink:hover {color:#ccc;}

#prevnextbtn{padding:5px;padding-left:10px;padding-right:10px;border:1px solid <?=$gayarlar->renk2;?>;color:<?=$gayarlar->renk2;?>;-webkit-transition:all .3s ease-out;-moz-transition:all .3s ease-out;-ms-transition:all .3s ease-out;-o-transition:all .3s ease-out;transition:all .3s ease-out}
#prevnextbtn:hover{color:#fff;background:<?=$gayarlar->renk2;?>}
.prevnextbtn{padding:5px;padding-left:10px;padding-right:10px;border:1px solid #00b7be;color:#00b7be;-webkit-transition:all .3s ease-out;-moz-transition:all .3s ease-out;-ms-transition:all .3s ease-out;-o-transition:all .3s ease-out;transition:all .3s ease-out}
.prevnextbtn:hover{color:#fff;background:<?=$gayarlar->renk2;?>}
.nextprevbtns{text-align:center;float:right;width:65px}

/* mobil responsive start */
@media only screen and (min-width: 320px) and (max-width: 769px) {
#yariminpt {width:44%;}
.kurumsalbtns .gonderbtn {
    padding: 10px 0px;
    display: inline-block;
    width: 100%;
    margin-bottom: 10px;
}
.headerwhite{   display:none;}
.uyeprofil .paypasbutonlar{float:none;margin:auto;width:210px}
#ilan_video video{width:100%;height:auto}
#msjkisiler{width:100%}
#uyemesajlari{width:100%}
.uyemsjprofili .gonderbtn{margin:2px}
.uyemsjarea textarea{width:92%}
ul.tab li{float:none}
ul.tab li a{text-align:center;padding:14px 0;width:100%;border-bottom-width:1px;border-bottom-style:solid;border-bottom-color:#EEE}
.uyeolgirislogo h1{float:none;color:#fff;margin-top:15px;font-size:28px}
.uyeolgirisslogan{float:left;color:#fff;width:100%;text-align:center;margin-top:0;margin-bottom:30px}
.headinfo a{color:#000}
#uyegirispage .logo{display:block}
.uyeolgirislogo{text-align:center}
#ilantutar{width:45%;float:left}
#ilanpbirimi{width:39%;margin-left:2px}
.headerbg {
        margin-top: 25px;
        background-size: 170% 100%;
    height: 120px;
}
.iletisimdetay{width:100%}
.iletisimform{float:left;width:97%}
.subebayibtns{width:172px}
#projefotolar{width:100%}
.mobilanbilgi{display:block;float:left;margin-right:10px}
.headtitle{margin-top:20px}
#mobtd{display:none}
.clearmob{display:block;clear:both}
.moblogo{display:block;width:100%;text-align:center;margin-top:30px;margin-bottom:50px}
.logo{display:none}
#wrapper{width:97%}
.headsosyal a{color:#333}
#slider{margin-top:20px;float:left;width:100%}
.headsosyal{display:none}
.languages{float:left;margin:auto;width:100%;color:#000;text-align:center}
.headinfo{color:#333;width:100%;float:left;text-align:center;margin-top:20px}
.headinfo h3{font-size:18px;line-height:0;float:none;margin-left:0}
.languages select{background:transparent;color:#333}
.languages a{color:#000}
#ilanverbtn{color:#000;float:right;border:none;border:1px solid #000;margin:15px 5px 15px 12px}
.header{position:relative;width:100%;background:none;}
.menuAc{display:block;width:100%;background:none;text-align:center;padding:0;margin-top:-35px;margin-bottom:0;background:none;color:<?=$gayarlar->renk1;?>}
.menu ul{display:none;position:relative;float:left;width:100%}
.menu ul li{float:none}
.menu{float:none;margin:0;height:auto}
.menu li{float:left;width:100%}
.menu li a{float:left;width:100%;font-size:20px;background:#fff;line-height:50px;border-bottom-width:1px;border-bottom-style:solid;border-bottom-color:#e0e0e0}
.menu ul li ul{width:100%;float:left;position:relative;top:0;left:0;z-index:1;display:none;margin:0;padding:0;font-size:16px}
.menu li:hover a{background:#fff;border:none}
.menu ul li ul li a{width:100%;font-size:16px;padding-left:35px}
.content{width:98%;float:none;margin:auto}
.sidebar{width:98%;float:none;margin:auto;margin-top:40px}
.altbaslik{margin-top:15px;float:left;width:100%}
.altbaslik h4 {font-size:18px;}
.gelismisara .gonderbtn{margin-left:10px}
.ilanbigbtn{width:93%;height:auto;background-image:url(../images/ilanbigbtn-bg.jpg);background-repeat:repeat;background-position:left center;margin:auto;margin-top:30px;text-align:center;padding-bottom:40px}
.ilanbigbtn h1{line-height:35px;font-weight:700;float:left;font-size:24px;margin-left:25px;margin-top:25px;margin-bottom:45px}
.ilanbigbtn .gonderbtn{float:none;font-size:20px;margin-right:0;margin-top:11px}
.ilanasama{float:none;width:80%;margin:auto;padding-top:35px;padding-bottom:15px}
.ilanvertanitim .fa.fa-long-arrow-right{display:none}
.hbveblog{width:100%}
.foovekaciklama{width:100%}
.foovekaciklama img{width:100%;height:auto}
.hbblogbasliklar{width:100%}
.sehirbtn{float:none;margin:auto;margin-bottom:25px}
.footinfo h1{line-height:35px;padding:10px}
.footseolinks a{width:40%;margin-left:15px;margin-bottom:6px;}
.footblok{width:93%;margin-left:5%}
#footlinks{width:93%}
#footebulten{width:93%}
.altfooter{height:auto;text-align:center}
.altfooter .headsosyal{float:none;text-align:center;margin-bottom:40px}
.altfooter .headsosyal a{line-height:normal;float:none}
.altfooter h5{float:none;line-height:normal;width:90%;margin:20px auto}
.ilanfotolar{width:100%}
.ilanbigfoto{width:100%;height:auto}
.ilanozellikler{width:100%;margin-left:0}
.danisman{float:right;width:100%;margin-top:10px;margin-bottom:20px}
.danisman span{width:87%}
.ilanozellik span{width:163px}
.girisyap{float:left;width:100%;margin-top:15px}
.uyeol{width:100%;margin-top:20px}
.girisyap h3{display:none}
.uyedetay{float:left;width:100%;margin-top:20px}
.uyepanellinks{float:left;margin-top:25px;width:100%}
.uyepanellinks .btn{width:80%}
.homearama{text-align:center;width:100%;color:#fff;margin-top:25px;float:left;background:#333;padding-top:10px;padding-bottom:15px}
.homearamaselect{width:100%;margin:auto}
.homearama select {margin-bottom: 1px;width:100%;margin-right:0;background:#fff;font-weight:700;border:none}
#leftradius{-webkit-border-top-left-radius:0;-webkit-border-bottom-left-radius:0;-moz-border-radius-topleft:0;-moz-border-radius-bottomleft:0;border-top-left-radius:0;border-bottom-left-radius:0}
.homearabtn {background:#fc0;text-shadow:0 0 2px #999;padding:18px;font-weight:700;color:#fff;-webkit-border-top-right-radius:0;-webkit-border-bottom-right-radius:0;-moz-border-radius-topright:0;-moz-border-radius-bottomright:0;border-top-right-radius:0;border-bottom-right-radius:0;float:left;width:100%;padding:0;padding-top:15px;padding-bottom:15px;font-size:18px}
.homearama h4 {font-size:26px}

.kareilan{margin:auto;width:100%;margin-bottom:25px}
.prufilurllink {   width: 200px;  }
.sidebar #ContactMessagesBox {    height: 300px;}
.modalDialog > div {    width: 95%;}
.modalDialog > div .gonderbtn {  
display: inline-block;
    width: 100%;
    margin-bottom: 10px;
    margin-top: 10px;
    padding: 12px 0px;
    }
.close{right:-3px;top:-3px}
.list_carousel{width:320px;margin:auto}
.list_carousel #foo2 li{width:320px}
.list_carousel #foo2 li .kareilan{width:320px}
.list_carousel #foo3 li{width:320px;height:auto;display:block;float:left;margin:7px}
.list_carousel #foo4 li{width:300px;height:auto;display:block;float:left;margin:7px}
#default_acilis{display:none}
.favyaz{font-size:16px;margin-top:10px;margin-bottom:10px}
.favyaz a{margin-left:30px}
.desktopclear{display:none}
#galeri_video_ekle .btn{margin-bottom:15px}
#anadanismanlar{width:195px;margin:auto}
.dovizkurlari{width:100%}
.kredihesaplama{width:100%;margin-top:25px}
.kareilan img{height:250px}
.ad728home{width:100%}
.ad728home img{width:100%}
.emlaktalepformu{width:100%}
.uyeolgirisyap .btn{margin-bottom:15px}
.ilanasamax{width:20%}
.uyeolgirisyap{margin-bottom:15px}
.uyepaket{float:none;width:92%;margin:auto;margin-bottom:25px}
.uyeliktaksit{width:100%}
.gelismisara select {    width: 99%;}
.gelismisara input {    width: 94%;}
#kfirmaprofili {
    height: 150px;
}
#kfirmaprofili .headtitle {
    margin-top: 50px;    text-align: center;
}
.kurumsalbtns {    height: auto;    margin-bottom: 10px;}
.firmaprofililetisim {    width: 100%;}
 .fprofilinfos {    width: 56%;}
 .pfirmalogo {    width: 40%;}
 .profilfirmahakkinda img {width:30%;}
 .fprofilmap {    width: 100%;    margin-top: 15px;}
 .headtitle h1 {
    font-size: 22px;

}
#kurumsalprofillink {
    font-size: 20px;
    float: none;
}
.sayfayolu {
    font-size: 15px;
}
#kfirmaprofili .headtitle h1 {
    float: none;
    margin-right: 0px;
}
.gelismissirala {
    width: 100%;
    margin-top: -3px;
}
#sicakfirsatlar {
    font-size: 16px;    margin-bottom: 10px;
}
.tablolistx {    margin-top: -44px;}
.paypasbutonlar {
    margin-top: -35px;padding: 2px;}
    .paypasbutonlar h5 {
    display: none;
}
.paypasbutonlar a {
   
    height: 30px;
    width: 30px;
    font-size: 14px;
       margin: 3px;
    line-height: 30px;
}
 
} /* mobil responsive end */


/* Loading Spinner */
.gelismisara .gonderbtn .spinner > div { background-color:#0C0;}
.gelismisara:hover .gonderbtn .spinner > div { background-color:white;}
.mobilonaybtn .spinner > div { background-color:white;}
 .gonderbtn .spinner > div { background-color:#1F282F;}
 .gonderbtn:hover .spinner > div { background-color:white;}
.spinner {width:100%;height:100%;text-align:center;}
.btn  .spinner > div { background-color: <?=$gayarlar->renk2;?>;}
.btn:hover  .spinner > div { background-color: white;}
.spinner > div {width: 18px; height: 18px; background-color: <?=$gayarlar->renk2;?>; border-radius: 100%;display: inline-block; -webkit-animation: sk-bouncedelay 1.4s infinite ease-in-out both; animation: sk-bouncedelay 1.4s infinite ease-in-out both;}
.spinner .bounce1 {-webkit-animation-delay: -0.32s; animation-delay: -0.32s;}
.spinner .bounce2 {-webkit-animation-delay: -0.16s;animation-delay: -0.16s;}
@-webkit-keyframes sk-bouncedelay { 0%, 80%, 100% { -webkit-transform: scale(0) } 40% { -webkit-transform: scale(1.0) } }
@keyframes sk-bouncedelay { 0%, 80%, 100% { -webkit-transform: scale(0); transform: scale(0); } 40% { -webkit-transform: scale(1.0); transform: scale(1.0);}}
#mobileMenu.active {
    display: block;
}
@media (max-width: 768px) {
    #mobileMenu {
        display: none;
    }
}