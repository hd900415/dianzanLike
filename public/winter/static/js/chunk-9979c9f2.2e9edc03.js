/* 橘子科技旗下 A4源码  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */
(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-9979c9f2"],{3077:function(t,s,i){"use strict";var a=i("671d"),e=i.n(a);e.a},"671d":function(t,s,i){},"93dc":function(t,s,i){"use strict";i.r(s);var a=function(){var t=this,s=t.$createElement,i=t._self._c||s;return i("div",{staticClass:"Site PageBox"},[i("van-nav-bar",{attrs:{fixed:"",border:!1,title:t.$t("task.list[0]"),"left-arrow":""},on:{"click-left":function(s){return t.$router.go(-1)}}}),i("div",{staticClass:"ScrollBox"},[i("van-pull-refresh",{on:{refresh:t.onRefresh},model:{value:t.isRefresh,callback:function(s){t.isRefresh=s},expression:"isRefresh"}},[i("van-list",{class:{Empty:!t.listData.length},attrs:{finished:t.isFinished,"finished-text":t.listData.length?t.$t("vanPull[0]"):t.$t("vanPull[1]")},on:{load:t.onLoad},model:{value:t.isLoad,callback:function(s){t.isLoad=s},expression:"isLoad"}},t._l(t.listData,(function(s){return i("van-cell",{key:s.task_id,staticClass:"TaskItem",attrs:{border:!1,to:"/taskShow/"+s.task_id},scopedSlots:t._u([{key:"title",fn:function(){return[i("div",[i("span",[t._v(t._s(t.$t("task.list[1]"))+":"+t._s(s.username))]),i("i",[t._v(t._s(s.status_dec))])]),i("div",[i("span",[t._v(t._s(t.$t("task.list[2]"))+":"),i("b",[t._v(t._s(s.surplus_number))])]),i("span",[t._v(" "+t._s(t.InitData.currency)+" "),i("em",[t._v(t._s(Number(s.reward_price)))])])]),i("div",[i("span",[t._v(t._s(t.$t("task.list[3]"))+":"+t._s(s.group_info))]),i("span",[i("van-button",{attrs:{type:"info",size:"mini",disabled:0!=s.is_l},on:{click:function(i){return i.stopPropagation(),t.receiveTask(s.task_id,s)}}},[t._v(t._s(t.$t("task.list[4]")))])],1)])]},proxy:!0}],null,!0)},[i("div",{staticClass:"icon",attrs:{slot:"icon"},slot:"icon"},[i("h4",[t._v(t._s(s.group_name))]),i("a",{attrs:{href:"javascript:;"}},[i("img",{attrs:{src:s.icon}})]),i("van-tag",{attrs:{type:"primary"}},[t._v(t._s(s.vip_dec))])],1)])})),1)],1)],1)],1)},e=[],n={name:"TaskList",components:{},props:["taskType","taskGrade"],data(){return{listData:"",isLoad:!1,isFinished:!1,isRefresh:!1,pageNo:1}},computed:{},watch:{},created(){this.getListData("init")},mounted(){},activated(){},destroyed(){},methods:{onLoad(){this.getListData("load")},getListData(t){this.isLoad=!0,this.isRefresh=!1,"load"==t?this.pageNo+=1:(this.pageNo=1,this.isFinished=!1),this.$Model.GetTaskList({group_id:this.taskType,task_level:this.taskGrade,page_no:this.pageNo,is_u:0},s=>{this.$nextTick(()=>{this.isLoad=!1}),1==s.code?("load"==t?1==this.pageNo?this.listData=s.info:this.listData=this.listData.concat(s.info):this.listData=s.info,this.pageNo==s.data_total_page?this.isFinished=!0:this.isFinished=!1):(this.listData="",this.isFinished=!0)})},onRefresh(){this.getListData("init")},receiveTask(t,s){localStorage["Token"]?this.$Model.ReceiveTask(t,t=>{1==t.code&&(s.is_l=1,this.pageNo=0)}):this.$router.push("/login")}}},o=n,r=(i("3077"),i("2877")),l=Object(r["a"])(o,a,e,!1,null,"fc9a9b12",null);s["default"]=l.exports}}]);