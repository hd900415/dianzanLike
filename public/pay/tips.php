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
.Body{
  overflow: hidden;
  position: absolute;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
}
.PaymentTips {
  max-width: 450px;
  margin: 0 auto;
  height: 100%;
  background-color: #f5f2f5;
  position: relative;
}
.PaymentTips .con{
  position: absolute;
  left: 50%;
  top: 50%;
  transform: translate(-50%, -50%);
  background-color: #fff;
  z-index: 2;
  width: 95%;
  padding: 30px 20px;
  border-radius: 5px;
  font-size: 18px;
  text-align: justify;
}
.PaymentTips .con h2{
  text-align: center;
  font-size: 24px;
}
.PaymentTips .con h3{
  color: #9ca8b6;
  margin: 20px 0;
  font-size: 18px;
}
.PaymentTips .con h3 span{
  font-size: 22px;
  color: #1989fa;
}
.PaymentTips .notice p{
  margin-top: 8px;
}
.PaymentTips .time{
  margin: 30px 0;
  line-height: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #9ca8b6;
  font-size: 16px;
}
.PaymentTips .time span{
  color: red;
  margin-left: 10px;
}
.PaymentTips button{
  background-color: #1989fa;
  color: #fff;
  font-size: 18px;
  height: 46px;
  border: 0;
  width: 100%;
  border-radius: 5px;
}
.PaymentTips .mask{
  background-color: rgba(0, 0, 0, .5);
  position: fixed;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
}
@media screen and (min-width:760px) {
  .PaymentTips .con {
    font-size: 22px;
  }
  .PaymentTips .con h2{
    font-size: 26px;
  }
  .PaymentTips .con h3{
    font-size: 22px;
  }
  .PaymentTips .con h3 span{
    font-size: 28px;
  }
  .PaymentTips .time{
    font-size: 20px;
  }
  .PaymentTips button{
    font-size: 22px;
    height: 54px;
  }
}
.Loading{
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.01);
  z-index: 999;
}
.Loading .msg{
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%,-50%);
  background-color: rgba(0, 0, 0, .8);
  color: #fff;
  text-align: center;
  padding: 20px 30px;
  line-height: 1;
  border-radius: 5px;
}
.Loading .msg p{
  margin-top: 10px;
}
</style>
<script>
var failOrder = sessionStorage.getItem('FailOrder')?Number(sessionStorage.getItem('FailOrder')):0;
var countDown = sessionStorage.getItem('CountDown')?Number(sessionStorage.getItem('CountDown')):1;
if(failOrder||!countDown){
  location.replace('close.php');
}
</script>
</head>
<body class="Body">
  <div class="PaymentTips">
    <div class="con">
      <h2>重要提示</h2>
      <h3>请足额支付 <span id="mColor" style="<?php if($dataArray['payWay']=='WechatPay'){?>color: #07c160<?php }?>"><?php echo $dataArray['amount']; ?>元</span></h3>
      <div class="notice">
        <p>1、付错金额，充值将不到账</p>
        <p>2、付款5分钟后未到帐，请及时联系客服</p>
        <p>3、充值到账后请及时删除收款码，重复使用收款码将不会到账</p>
        <p>4、转账时若提示24小时到账，请取消订单不要付款，重新下单</p>
      </div>
      <div class="time">
        充值倒计时
        <span id="Time">00:00:00</span>
      </div>
         <form id="Form" action="order/<?php if($_SERVER['REQUEST_METHOD'] == 'GET'){?>getCreateOrder<?php }else{?>postCreateOrder<?php }?>" method="post">
          <?php foreach ($dataArray as $key => $value): ?>
          <input type="hidden" name="<?=$key;?>" value="<?=$value;?>">
          <?php endforeach; ?>
        <button type="submit" id="submitBtn" style="<?php if($dataArray['payWay']=='WechatPay'){?>background-color: #07c160<?php }?>">确认</button>
      </form>
    </div>
    <div class="mask"></div>
  </div>
<script>
var timeDiff = sessionStorage.getItem('CountDown')?Number(sessionStorage.getItem('CountDown')):300;
var intervalTime = setInterval(function(){
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
    clearInterval(intervalTime);
    sessionStorage.setItem('CountDown',0);
    location.replace('close.php');
  }else{
    sessionStorage.setItem('CountDown',timeDiff);
    $('#Time').text(hh+':'+mm+':'+ss);
  }
}, 1000);
$('#Form').submit(function(event) {
  event.preventDefault();
  var jsonData = {};
  var formData = new FormData(event.target);
  formData.forEach((value, key) => jsonData[key] = value);
  var paymentData = {
    payway: jsonData.payWay,
    amount: jsonData.amount,
    orderid: jsonData.id
  }
  sessionStorage.setItem('PaymentData',JSON.stringify(paymentData));
  location.replace('paycode.php');
});
</script>
</body>
</html>