<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

$dataArray  = $_POST ?: $_GET;
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="Access-Control-Allow-Origin" content="*">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta http-equiv="Expires" content="0">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Cache-control" content="no-cache">
  <meta http-equiv="Cache" content="no-cache">
  <meta name="renderer" content="webkit">
  <meta name="viewport" content="width=device-width,user-scalable=no,initial-scale=1,maximum-scale=1,minimum-scale=1,viewport-fit=cover">
  <meta name="format-detection" content="telphone=no, email=no">
  <meta name="HandheldFriendly" content="true">
  <meta name="MobileOptimized" content="320">
  <meta name="screen-orientation" content="portrait">
  <meta name="full-screen" content="yes">
  <!-- <meta name="browsermode" content="application"> -->
  <meta name="x5-orientation" content="portrait">
  <meta name="x5-fullscreen" content="true">
  <meta name="x5-page-mode" content="app">
  <meta name="msapplication-tap-highlight" content="no">
  <meta name="author" content="Mr.Cai, 297372788@qq.com">
  <link rel="shortcut icon" type="image/ico" href="favicon.ico">
  <title>滴滴交易</title>
<script src="jquery-3.4.1.min.js"></script>
<script src="qrcode.min.js"></script>
<style>
html{
  height: 100%;
  -webkit-touch-callout: none;
  -webkit-text-size-adjust: none;
  -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
  width: 100%;
  font-size: 80%;
}
@media (-webkit-min-device-pixel-ratio: 1.5),
(min-resolution: 120dpi) {
  html {
    font-size: 92.5%;
  }
}
@media (-webkit-min-device-pixel-ratio: 2),
(min-resolution: 192dpi) {
  html {
    font-size: 100%;
  }
}
@media (-webkit-min-device-pixel-ratio: 3),
(min-resolution: 288dpi) {
  html {
    font-size: 100%;
  }
}
@media (-webkit-min-device-pixel-ratio: 4),
(min-resolution: 384dpi) {
  html {
    font-size: 100%;
  }
}
body {
  position: relative;
  max-width: 450px;
  overflow: hidden;
  margin: 0 auto;
  font-size: 15px;
  height: 100%;
  -webkit-touch-callout: none;
  -webkit-text-size-adjust: none;
  -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
  -webkit-font-smoothing: antialiased;
  text-rendering: optimizeLegibility;
  font-family: "Product Sans","Roboto","Helvetica Neue", Helvetica, Tohoma, Arial,
  "MicrosoftYaHei","PingFang SC", "Hiragino Sans GB", "STXihei","Source Han Sans CN","Microsoft YaHei UI", "Microsoft YaHei", "Heiti SC", sans-serif;
  color: #000;
}
*,
:after,
:before {
  -webkit-box-sizing: border-box;
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

p {
  margin: 0;
  padding: 0;
}

i,
em {
  font-style: normal;
}
a {
  color: #000;
}
b{
  font-weight: 600;
}
h1,h2,h3,h4,h5,h6{
  font-weight: 400;
}
h3{
  font-size: 20px;
}
a{
  text-decoration: none;
}
.fl{
  float: left!important;
}
.fr{
  float: right!important;
}
.Body{
  overflow: hidden;
  position: absolute;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
}
.PaymentPayment{
  max-width: 450px;
  margin: 0 auto;
  height: 100%;
  background-color: #1989fa;
  position: relative;
}
.PaymentPayment .box{
  position: absolute;
  top: 50%;
  left: 50%;
  width: 90%;
  transform: translate(-50%,-55%);
}
.PaymentPayment .title{
  font-size: 18px;
  color: #fff;
  margin-bottom: 10px;
  width: 100%;
  overflow: hidden;
  line-height: 1;
}
.PaymentPayment .title .fr{
  font-size: 15px;
}
.PaymentPayment .button{
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  line-height: 46px;
  font-size: 18px;
  color: #fff;
  text-align: center;
}
.PaymentPayment .btn{
  padding: 20px 30px 10px;
}
.PaymentPayment .btn a{
  display: block;
  line-height: 48px;
  background-color: #1989fa;
  color: #fff;
  font-size: 16px;
  border-radius: 3px;
}
.Dialog{
  background-color: #fff;
  width: 100%;
  vertical-align: middle;
}
.Dialog .top{
  background-color: #e85453;
  text-align: center;
  color: #fff;
  line-height: 44px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.Dialog .con{
  padding: 16px;
  text-align: center;
}
.Dialog .order{
  color: #9ca8b6;
  font-size: 14px;
  display: flex;
  justify-content: space-between;
  position: relative;
}
.Dialog .order label{
  white-space: nowrap;
  text-overflow: ellipsis;
  overflow: hidden;
}
.Dialog .order a{
  white-space: nowrap;
  color: #1989fa;
  margin-left: 10px;
}
.Dialog .con h2{
  color: #9ca8b6;
  line-height: 1;
  margin: 12px 0;
} 
.Dialog .con h2 span{
  color: #1989fa;
  font-size: 28px;
  font-weight: 600;
}
.Dialog .bottom{
  position: relative;
  margin-top: 20px;
  min-height: 180px;
  width: 100%;
  display: table;
}
.Dialog .notice{
  position: absolute;
  top: 0;
  left: 0;
  color: red;
  font-size: 18px;
  line-height: 1.3;
}
.Dialog .tag{
  background-color: #e85453;
  position: absolute;
  right: -30px;
  color: #fff;
  font-size: 13px;
  padding: 8px 10px;
  border-radius: 3px;
  top: 0;
}
.Dialog .tag:after{
  content: '';
  display: block;
  position: absolute;
  left: -8px;
  bottom: 0;
  border: 5px solid transparent;
  border-right-color: #e85453;
  border-bottom-color: #e85453;
}
.Dialog .tips{
  color: #1989fa;
  font-size: 16px;
  text-align: center;
  margin-top: 20px;
}

@media screen and (min-width:760px) {
  .PaymentPayment .title{
    font-size: 18px;
  }
  .PaymentPayment .van-count-down{
    font-size: 22px;
  }
  .Dialog .con{
    padding: 16px;
  }
  .Dialog .con h2{
    font-size: 26px;
  }
  .Dialog .con h2 span{
    font-size: 32px;
  }
  .Dialog .tips .van-icon{
    font-size: 28px;
  }
  .Dialog .bottom{
    min-height: 250px;
  }
}
#Time{
  margin-left: 10px;
}
.Circle{
  background-color: #1989fa;
  width: 150px;
  height: 150px;
  border-radius: 100%;
  position: relative;
  margin: 16px auto 0;
}
.Circle *{
  position: absolute;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
}
.Circle .border1{
  background-color: #88c1f9;
  width: 150px;
  height: 150px;
  border-radius: 100%;
  clip:rect(0,auto,auto,75px);
  transform: rotate(0deg);
  z-index: 2;
}
.Circle .border2{
  background-color: #88c1f9;
  width: 150px;
  height: 150px;
  border-radius: 100%;
  clip:rect(0,75px,auto,0);
}
.Circle .val{
  font-size: 36px;
  color: #1989fa;
  background-color: #fff;
  width: 120px;
  height: 120px;
  line-height: 120px;
  border-radius: 100%;
  text-align: center;
  top: 15px;
  left: 15px;
  z-index: 3;
}
.Toast{
  background-color: rgba(0, 0, 0, .6);
  position: fixed;
  bottom: 10%;
  left: 50%;
  transform: translateX(-50%);
  z-index: 99;
  color: #fff;
  font-size: 14px;
  text-align: center;
  min-width: 100px;
  padding: 10px 20px;
  border-radius: 100px;
}
.Alert{
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  font-size: 16px;
  position: absolute;
  text-align: center;
}
.Alert .msg{
  min-width: 80%;
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%,-50%);
  background-color: #fff;
  border-radius: 5px;
  z-index: 2;
}
.Alert h3{
  line-height: 46px;
  border-bottom: 1px #eee solid;
}
.Alert .button{
  display: block;
  margin: 0 20px 20px;
  background-color: #1989fa;
  font-size: 16px;
  line-height: 46px;
  border-radius: 5px;
  color: #fff;
}
.mask{
  position: fixed;
  left: 0;
  top: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, .6);
}
#QRCode img{
  margin: 0 auto;
}
</style>
<script>
var paymentData = '';
if(sessionStorage.getItem('PaymentData')){
  paymentData = JSON.parse(sessionStorage.getItem('PaymentData'));
}
</script>
</head>
<body class="Body">
  <div class="PaymentPayment">
    <div class="box">
      <div class="title">
        <span id="payState" class="fl">匹配中</span>
        <span id="countDown" class="fr">
          支付倒计时
          <span id="Time">00:00:00</span>
        </span>
      </div>
      <div class="Dialog">
        <div class="top">
          付款5分钟后如未到账，请及时联系客服处理
        </div>
        <div class="con">
          <div class="order">
            <label>订单号：<span id="IosOrder"></span></label>
            <input type="text" value="" id="AppOrder" style="width: 1px;height: 1px;border:0;position: absolute;">
            <a href="javascript:copyText('IosOrder','AppOrder');">复制</a>
          </div>
          <h2>请支付 <span id="payMoney"></span>元</h2>
          <h4 style="color:red">付错金额，重复支付，充值将不到账！</h4>
          <div class="bottom">
            <span class="notice">此<br>码<br>只<br>可<br>付<br>款<br>一<br>次</span>
            <div class="Circle">
              <div class="border1"></div>
              <div class="border2"></div>
              <div class="val" id="Circle">30s</div>
            </div>
            <div class="code" id="QRCode"></div>
            <div class="tag">请勿重<br>复付款</div>
          </div>
          <div class="tips">一般10秒内出码，请您耐心等待！</div>
          <div class="btn" id="AliPayUrl" style="display: none"><a href="" target="_blank">打开支付宝立即支付</a></div>
        </div>
      </div>
    </div>
    <a href="javascript:location.replace('close.php');" class="button" id="cancelBtn">取消订单</a>
  </div>
  <div class="Toast" style="display: none"></div>
  <div class="Alert" style="display: none">
    <div class="msg">
      <h3>提示</h3>
      <div style="padding:30px 20px">若已付款成功，请勿重复付款</div>
      <a class="button" href="javascript:closeAlert();">确定</a>
    </div>
    <div class="mask"></div>
  </div>
  <?php foreach ($dataArray as $key => $value): ?>
    <input type="hidden" name="<?=$key;?>" value="<?=$value;?>">
  <?php endforeach; ?>
<script>
if(!paymentData){
  paymentData = {
    amount: $('input[name="amount"]').val(),
    orderid: $('input[name="orderid"]').val(),
    payway: $('input[name="payway"]').val()
  }
}
if(paymentData.payway=='WechatPay'){
  $('.PaymentPayment').css('background-color','#07c160');
  $('.PaymentPayment .tips,#payMoney,.Circle .val').css('color','#07c160');
  $('.Circle').css('background-color','#07c160');
  $('.Circle .border1,.Circle .border2').css('background-color','#58e590');
}
$('#payMoney').text(paymentData.amount);
$('#IosOrder').text(paymentData.orderid);
$('#AppOrder').val(paymentData.orderid);

getTimingOrder();
var intervalTime,countDownTime,isQRCode=false;
function getTimingOrder(getcode){
  $.post('order/timingOrder',{orderId: paymentData.orderid},function(data){
    if(data.qrcodeurl){
      clearInterval(intervalTime);
      var reg = /^HTTPS:\/\/QR\.ALIPAY\.COM/i;
      if(reg.test(data.qrcodeurl)){
        $('#AliPayUrl').show().find('a').attr('href',data.qrcodeurl);
      }
      $('#payState').text('待付款');
      $('.PaymentPayment .tips').text('打开'+(paymentData.payway=='WechatPay'?'微信':paymentData.payway=='AliPay'?'支付宝':'支付工具')+'扫一扫，进行充值');
      $('#cancelBtn,.Circle').remove();
      if(!isQRCode){
        new QRCode(document.getElementById('QRCode'), {
          text: data.qrcodeurl,
          width: 180,
          height: 180,
          correctLevel : QRCode.CorrectLevel.H
        });
        isQRCode = true;
      }
    }else{
      if(!getcode){
        var time  = data.wutime>30?30:data.wutime;
        getCode(time);
      }
    }
    if(data.wutime){
      if(!getcode){
        countDown(data.wutime);
      }
    }else{
      $('#countDown').hide();
      clearInterval(countDownTime);
      if(data.qrcodeurl){
        $('.Alert').show();
      }else{
        location.replace('close.php');
      }
    }
  })
};

function getCode(time) {
  intervalTime = setInterval(function(){
    time--;
    if(time<0){
      clearInterval(intervalTime);
      location.replace('close.php');
    }else{
      getTimingOrder(true);
      $('#Circle').text(time+'s');
      if(time>15){
        var rotate = 180/15*(30-time);
        $('.Circle .border1').css('transform','rotate('+rotate+'deg)');
      }else{
        var rotate = 180/15*(15-time);
        $('.Circle .border1').css({
          'clip':'rect(0,75px,auto,0)',
          'transform':'rotate(180deg)',
          'background-color':paymentData.payway=='WechatPay'?'#07c160':'#1989fa'
        });
        $('.Circle .border2').css({
          'transform':'rotate('+rotate+'deg)'
        });
      }
    }
  },1000);
};

function countDown(timeDiff) {
  countDownTime = setInterval(function(){
    let dd = 0, hh = 0, mm = 0, ss = 0;
    if(timeDiff>0){
        // dd = Math.floor(timeDiff / 60 / 60 / 24);
        // hh = Math.floor(timeDiff / 60 / 60 % 24);
        hh = Math.floor(timeDiff / 60 / 60);
        mm = Math.floor(timeDiff / 60 % 60);
        ss = Math.floor(timeDiff % 60);
    }
    timeDiff--;
    hh = hh < 10 ? '0'+hh : hh;
    mm = mm < 10 ? '0'+mm : mm;
    ss = ss < 10 ? '0'+ss : ss;
    if(timeDiff<0){
      $('#countDown').hide();
      clearInterval(countDownTime);
    }else{
      $('#Time').text(hh+':'+mm+':'+ss);
    }
  }, 1000)
};

function copyText(ios,app) {
  if (navigator.userAgent.match(/(iPhone|iPod|iPad);?/i)) {
    window.getSelection().removeAllRanges();
    var Url2 = document.getElementById(ios);
    var range = document.createRange();
    range.selectNode(Url2);
    window.getSelection().addRange(range);
    var successful = document.execCommand('copy');
    if(successful){
      toastMsg('复制成功');
    }else{
      toastMsg('IOS系统版本低不支持');
    }
    window.getSelection().removeAllRanges();
  }else{
    var Url2=document.getElementById(app);
    Url2.select();
    document.execCommand("Copy");
    toastMsg('复制成功');
  }
};

var toastTime;
function toastMsg(text) {
  clearTimeout(toastTime);
  $('.Toast').show().text(text);
  toastTime = setTimeout(function(){$('.Toast').hide()},2000)
};
function closeAlert() {
  $('.Alert').hide();
};
</script>
</body>
</html>