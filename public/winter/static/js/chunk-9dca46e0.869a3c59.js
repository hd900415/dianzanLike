/* 橘子科技旗下 A4源码  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */
(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-9dca46e0"],{3796:function(t,e,a){"use strict";var s=a("d1a8"),i=a.n(s);i.a},"4a2a":function(t,e,a){"use strict";a.r(e);var s=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",{staticClass:"PageBox"},[a("div",{staticClass:"ScrollBox"},[a("van-tabs",{attrs:{color:"#4087f1","title-active-color":"#4087f1",background:"#151d31","title-inactive-color":"#bbb","line-width":"60",border:!1},model:{value:t.tabsActive,callback:function(e){t.tabsActive=e},expression:"tabsActive"}},t._l(t.tabsList,(function(e,s){return a("van-tab",{key:s,attrs:{title:e.group_name}},[a("van-form",{staticClass:"mt15",on:{submit:function(e){return t.onSubmit(s)}}},[1==e.bind_type?a("van-field",{attrs:{label:t.$t("bindAccount.label[2]"),placeholder:t.$t("bindAccount.placeholder",{account:e.group_name}),clearable:"",readonly:!!t.UserInfo[e.bind_field]},model:{value:t.accountArr[s],callback:function(e){t.$set(t.accountArr,s,e)},expression:"accountArr[index]"}}):a("van-field",{attrs:{label:t.$t("bindAccount.label[1]")},scopedSlots:t._u([{key:"input",fn:function(){return[a("van-uploader",{attrs:{"after-read":t.afterRead,"max-count":1,deletable:!t.UserInfo[e.bind_field]},model:{value:t.accountArr[s],callback:function(e){t.$set(t.accountArr,s,e)},expression:"accountArr[index]"}})]},proxy:!0}],null,!0)}),a("div",{staticStyle:{margin:"25px 16px"}},[a("van-button",{staticStyle:{"font-size":"16px"},attrs:{round:"",block:"",type:"danger","native-type":"submit",disabled:!!t.UserInfo[e.bind_field]}},[t._v(" "+t._s(t.$t("bindAccount.default[2]"))+" ")])],1)],1)],1)})),1)],1)])},i=[],n={name:"BindAccount",components:{},props:[],data(){return{tabsActive:0,weixinAcc:"",postData:{},douyinImg:[],kuaishouImg:[],accountArr:[],tabsList:[]}},computed:{},watch:{},created(){this.$Model.GetUserInfo(),this.$parent.navBarTitle=this.$t("bindAccount.default[0]"),this.tabsList=this.InitData.taskclasslist.filter(t=>1==t.bind_status&&1==t.state),this.accountArr=this.tabsList.flatMap(t=>2==t.bind_type?[this.UserInfo[t.bind_field]?[{url:this.InitData.setting.up_url+this.UserInfo[t.bind_field]}]:[]]:this.UserInfo[t.bind_field]||"")},mounted(){},activated(){},destroyed(){},methods:{onSubmit(t){this.postData={};const e=this.tabsList[t].bind_field;2==this.tabsList[t].bind_type?this.postData[e]=this.accountArr[t][0].url:this.postData[e]=this.accountArr[t],this.$Model.SetUserInfo(this.postData)},afterRead(t){t.status="uploading",t.message=this.$t("upload[0]"),this.uploadImgs(t)},compressImg(t){this.$Util.CompressImg(t.file.type,t.content,750,e=>{let a=new FormData;a.append("token",localStorage["Token"]),a.append("type",3),a.append("image",e,t.file.name),this.$Model.UploadImg(a,e=>{1==e.code?(t.message=this.$t("upload[2]"),t.status="success",t.url=e.url):(t.status="failed",t.message=this.$t("upload[3]"))})})},uploadImgs(t){if(t.length)t.forEach(t=>{if(!t.file.type.match(/image/))return t.status="failed",void(t.message=this.$t("upload[1]"));this.compressImg(t)});else{if(!t.file.type.match(/image/))return t.status="failed",void(t.message=this.$t("upload[1]"));this.compressImg(t)}}}},o=n,c=(a("3796"),a("2877")),l=Object(c["a"])(o,s,i,!1,null,"45135cac",null);e["default"]=l.exports},d1a8:function(t,e,a){}}]);