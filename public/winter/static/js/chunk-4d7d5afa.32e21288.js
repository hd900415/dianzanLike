/* 橘子科技旗下 A4源码  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */
(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-4d7d5afa"],{"9ab4":function(t,s,a){},c430:function(t,s,a){"use strict";a.r(s);var e=function(){var t=this,s=t.$createElement,a=t._self._c||s;return a("div",{staticClass:"Site IndexBox"},[a("div",{staticClass:"ScrollBox"},[a("van-swipe",{staticStyle:{height:"10rem"},attrs:{autoplay:3e3,"indicator-color":"#888"}},t._l(t.InitData.bannerList,(function(t,s){return a("van-swipe-item",{key:s},[a("img",{attrs:{src:t,width:"100%"}})])})),1),a("van-tabs",{attrs:{ellipsis:!1,border:!1,color:"#4087f1","title-active-color":"#ffffff",background:"#8CC152","title-inactive-color":"#fff","line-width":"60"},on:{change:t.changeTabs},model:{value:t.tabsIndex,callback:function(s){t.tabsIndex=s},expression:"tabsIndex"}},t._l(t.InitData.taskclasslist.filter((function(t){return 1==t.state})),(function(s){return a("van-tab",{key:s.group_id,staticStyle:{padding:"0 12px"},attrs:{title:s.group_name}},t._l(t.InitData.UserGradeList,(function(s){return a("van-cell",{key:s.grade,on:{click:function(a){return t.openTaskList(s.grade,s.name)}},scopedSlots:t._u([{key:"title",fn:function(){return[a("b",[t._v(t._s(s.name))]),a("div",{domProps:{innerHTML:t._s(t.$t("vip.list.label",{number:s.number}))}})]},proxy:!0}],null,!0)},[t.UserInfo.vip_level==s.grade?a("span",{staticClass:"tag"},[t._v(t._s(t.$t("task.index[0]")))]):t._e()])})),1)})),1)],1),a("Footer")],1)},i=[],n={name:"Task",components:{},props:["tabsActive"],data(){return{taskType:"",tabsIndex:0,isStore:!1}},computed:{},watch:{},created(){this.tabsIndex=this.tabsActive?Number(this.tabsActive):0,this.InitData.taskclasslist.length&&(this.taskType=this.InitData.taskclasslist.filter(t=>1==t.state)[0].group_id,this.tabsActive&&(this.taskType=this.InitData.taskclasslist.filter(t=>1==t.state)[this.tabsActive].group_id))},mounted(){},activated(){},destroyed(){},methods:{changeTabs(t){this.taskType=this.InitData.taskclasslist.filter(t=>1==t.state)[t].group_id},openTaskList(t,s){if(this.UserInfo.vip_level<t){const t=this.$t("task.index[1]",{currVip:this.UserInfo.useridentity,vip:s});this.$Dialog.Confirm(t,()=>{this.$router.push("/vip")},this.$t("task.index[2]"))}else this.taskType?this.$router.push(`/taskList/${this.taskType}/${t}`):this.$Dialog.Toast(this.$t("task.index[3]"))}}},r=n,o=(a("f2a0"),a("2877")),l=Object(o["a"])(r,e,i,!1,null,"3534f3a2",null);s["default"]=l.exports},f2a0:function(t,s,a){"use strict";var e=a("9ab4"),i=a.n(e);i.a}}]);