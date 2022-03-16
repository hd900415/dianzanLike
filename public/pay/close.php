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
  max-width: 750px;
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
.PaymentClose{
  width: 100%;
  height: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  justify-content: center;
  font-size: 18px;
}
.PaymentClose h2{
  margin: 30px 0;
  font-size: 26px;
}
@media screen and (min-width:760px) {
  .PaymentClose{
    font-size: 22px;
  }
  .PaymentClose h2{
    font-size: 30px;
  }
}
</style>
<script>
sessionStorage.clear()
</script>
</head>
<body class="Body">
  <div class="PaymentClose">
    <img src="cancel-order.png" width="70">
    <h2>充值已取消</h2>
    <p>请关闭此页面，重新下单</p>
  </div>
</body>
</html>