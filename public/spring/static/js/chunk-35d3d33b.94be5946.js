/* 橘子科技旗下 A4源码  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */
(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-35d3d33b"],{1136:function(t,s,e){},c430:function(t,s,e){"use strict";e.r(s);var i=function(){var t=this,s=t.$createElement,e=t._self._c||s;return e("div",{staticClass:"Site IndexBox"},[e("div",{staticClass:"ScrollBox"},[e("van-swipe",{staticStyle:{height:"10rem"},attrs:{autoplay:3e3,"indicator-color":"#888"}},t._l(t.InitData.bannerList,(function(t,s){return e("van-swipe-item",{key:s},[e("img",{attrs:{src:t,width:"100%"}})])})),1),e("van-tabs",{attrs:{ellipsis:!1,border:!1,color:"#4087f1","title-active-color":"#ffffff",background:"#7669fd","title-inactive-color":"#bbb","line-width":"60"},on:{change:t.changeTabs},model:{value:t.tabsIndex,callback:function(s){t.tabsIndex=s},expression:"tabsIndex"}},t._l(t.InitData.taskclasslist.filter((function(t){return 1==t.state})),(function(s){return e("van-tab",{key:s.group_id,staticStyle:{padding:"0 12px"},attrs:{title:s.group_name}},t._l(t.InitData.UserGradeList,(function(s){return e("van-cell",{key:s.grade,on:{click:function(e){return t.openTaskList(s.grade,s.name)}},scopedSlots:t._u([{key:"title",fn:function(){return[e("b",[t._v(t._s(s.name))]),e("div",{domProps:{innerHTML:t._s(t.$t("vip.list.label",{number:s.number}))}})]},proxy:!0}],null,!0)},[t.UserInfo.vip_level==s.grade?e("span",{staticClass:"tag"},[t._v(t._s(t.$t("task.index[0]")))]):t._e()])})),1)})),1)],1),e("Footer")],1)},a=[],n={name:"Task",components:{},props:["tabsActive"],data(){return{taskType:"",tabsIndex:0,isStore:!1}},computed:{},watch:{},created(){this.tabsIndex=this.tabsActive?Number(this.tabsActive):0,this.InitData.taskclasslist.length&&(this.taskType=this.InitData.taskclasslist.filter(t=>1==t.state)[0].group_id,this.tabsActive&&(this.taskType=this.InitData.taskclasslist.filter(t=>1==t.state)[this.tabsActive].group_id))},mounted(){},activated(){},destroyed(){},methods:{changeTabs(t){this.taskType=this.InitData.taskclasslist.filter(t=>1==t.state)[t].group_id},openTaskList(t,s){if(this.UserInfo.vip_level<t){const t=this.$t("task.index[1]",{currVip:this.UserInfo.useridentity,vip:s});this.$Dialog.Confirm(t,()=>{this.$router.push("/vip")},this.$t("task.index[2]"))}else this.taskType?this.$router.push(`/taskList/${this.taskType}/${t}`):this.$Dialog.Toast(this.$t("task.index[3]"))}}},r=n,o=(e("f71c"),e("2877")),c=Object(o["a"])(r,i,a,!1,null,"7269cb55",null);s["default"]=c.exports},f71c:function(t,s,e){"use strict";var i=e("1136"),a=e.n(i);a.a}}]);