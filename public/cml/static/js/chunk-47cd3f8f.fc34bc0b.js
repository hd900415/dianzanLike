/* 橘子科技旗下 A4源码  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */
(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-47cd3f8f"],{"32bb":function(t,i,a){"use strict";a.r(i);var e=function(){var t=this,i=t.$createElement,a=t._self._c||i;return a("div",{staticClass:"Main",staticStyle:{"background-color":"#ededed"}},[a("van-nav-bar",{attrs:{fixed:"",border:!1,title:t.$route.meta.title,"left-arrow":""},on:{"click-left":function(i){return t.$router.go(-1)}}}),a("div",{staticClass:"PageBox",staticStyle:{"padding-bottom":"44px"}},[t.infoData?a("div",{staticClass:"ScrollBox"},[a("div",{staticClass:"Details"},[a("dl",[a("dt",[a("label",[t._v(t._s(t.infoData.title))]),a("i",[t._v("+"+t._s(t.infoData.reward_price)+"元")])]),a("dd",{staticStyle:{"padding-top":"0","justify-content":"space-between"}},[a("em",[t._v(t._s(t.infoData.y_surplus_number)+"人已赚到")]),a("em",[t._v("剩余"+t._s(t.infoData.surplus_number)+"个名额")]),a("em",[t._v("48小时内审核")])]),a("dd",{staticStyle:{"border-top":"1px #eee solid"}},[a("label",[t._v("任务描述：")]),a("span",[t._v(t._s(t.infoData.content))])])]),a("dl",[a("dt",{staticStyle:{"justify-content":"flex-start"}},[a("label",[a("img",{staticStyle:{"border-radius":"100%","vertical-align":"middle","margin-right":"10px"},attrs:{src:"./static/head/"+t.infoData.f_header,height:"40"}})]),a("span",[a("p",[t._v("需求方")]),t._v(" "+t._s(t.infoData.f_username)+" ")])]),a("dd",{staticStyle:{"border-top":"1px #eee solid"}},[a("label",[t._v("审核标准：")]),a("div",{staticStyle:{flex:"auto"}},[a("van-checkbox-group",{model:{value:t.conditionArr,callback:function(i){t.conditionArr=i},expression:"conditionArr"}},t._l(t.InitData.authenticationList,(function(i,e){return a("van-checkbox",{key:e,attrs:{name:i,disabled:""}},[t._v(t._s(i))])})),1)],1)]),a("dd",[a("input",{staticClass:"link",attrs:{id:"AppLink",type:"text",readonly:""},domProps:{value:t.infoData.link_info}}),a("span",{staticStyle:{position:"absolute",opacity:"0"},attrs:{id:"IosLink"}},[t._v(t._s(t.infoData.link_info))]),a("div",[a("van-button",{attrs:{block:"",size:"mini",plain:"",type:"info",round:""},on:{click:function(i){return t.$Util.CopyText("IosLink","AppLink")}}},[t._v("复制")]),a("van-button",{staticStyle:{margin:"5px 0 0"},attrs:{block:"",size:"mini",plain:"",type:"danger",round:""},on:{click:function(i){return t.$Util.OpenUrl(t.infoData.link_info)}}},[t._v("跳转")])],1)])])]),a("van-tabs",{attrs:{border:!1,color:"#dd6161","title-active-color":"#dd6161"}},[a("van-tab",{attrs:{title:"任务步骤"}},t._l(t.infoData.task_step,(function(i,e){return a("dl",{key:e},[a("dt",[a("label",[t._v("第"+t._s(e+1)+"步：")]),a("span",[t._v(t._s(i.describe))])]),a("dd",[a("van-image",{attrs:{fit:"cover",src:""+t.ApiUrl+i.img},on:{click:function(a){return t.$ImagePreview([""+t.ApiUrl+i.img])}}})],1)])})),0),a("van-tab",{attrs:{title:"审核样例"}},[t.infoData.examine_demo?a("div",{staticStyle:{"text-align":"center"}},t._l(t.infoData.examine_demo,(function(i,e){return a("van-image",{key:e,attrs:{fit:"cover",src:""+t.ApiUrl+i},on:{click:function(a){return t.$ImagePreview([""+t.ApiUrl+i])}}})})),1):a("van-empty",{attrs:{image:"error",description:"无审核样例"}})],1)],1)],1):t._e(),t.isLoad?a("van-loading",{staticClass:"DataLoad",attrs:{size:"60px",vertical:""}},[t._v("数据加载中...")]):t._e()],1),a("div",{staticClass:"Button",staticStyle:{position:"fixed",bottom:"0",width:"100%"}},[t.isLogin?a("van-button",{staticStyle:{"font-size":"16px"},attrs:{block:"",type:"danger",loading:t.isSubmit,"loading-text":"正在提交...",disabled:0!=t.infoData.is_l},on:{click:t.onSubmit}},[t._v("领取任务")]):a("van-button",{staticStyle:{"font-size":"16px"},attrs:{block:"",type:"danger",to:"/login"}},[t._v("登 录")])],1)],1)},n=[],o={name:"Show",components:{},props:["taskId"],data(){return{isLoad:!0,infoData:"",conditionArr:[],isLogin:!!localStorage["Token"],isSubmit:!1}},computed:{},watch:{},created(){this.getTaskinfo()},mounted(){},activated(){},destroyed(){},methods:{getTaskinfo(){this.$Model.GetTaskinfo(this.taskId,t=>{this.isLoad=!1,1==t.code&&(this.infoData=t.info,this.conditionArr=t.info.finish_condition||[])})},onSubmit(){this.isSubmit=!0,this.$Model.ReceiveTask(this.taskId,t=>{this.isSubmit=!1,1==t.code&&this.getTaskinfo()})}}},s=o,r=(a("ac01"),a("2877")),l=Object(r["a"])(s,e,n,!1,null,"3832f8cc",null);i["default"]=l.exports},"8dea":function(t,i,a){},ac01:function(t,i,a){"use strict";var e=a("8dea"),n=a.n(e);n.a}}]);