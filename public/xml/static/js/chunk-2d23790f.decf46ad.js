/* 橘子科技旗下 A4源码  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */
(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-2d23790f"],{fc51:function(t,i,s){"use strict";s.r(i);var e=function(){var t=this,i=t.$createElement,s=t._self._c||i;return s("div",{staticClass:"Main Background"},[s("van-nav-bar",{staticClass:"NavBar",attrs:{title:"群通知",border:!1},on:{"click-left":t.onClickBack}},[s("img",{staticClass:"icon-left",attrs:{slot:"left",src:"./static/miliao/icon/icon-back.svg"},slot:"left"})]),s("div",{ref:"Scroll",staticClass:"Scroll-Wrap"},[s("div",{staticClass:"Scroll-Con"},[s("div",{staticClass:"Box FriendNotice"},[t._l(t.groupList,(function(i){return s("van-swipe-cell",{key:i.id,attrs:{"right-width":70,"on-close":t.onClickDel}},[s("van-cell",{attrs:{clickable:""},on:{click:function(s){return t.onOpenInfo(i.id,i.new_type)}}},[s("div",{staticClass:"head",attrs:{slot:"icon"},slot:"icon"},[i.header?s("img",{attrs:{src:"./static/miliao/images/headimg/"+i.header}}):s("b",[t._v(t._s(i.gname?i.gname.slice(0,2):"密群"))])]),2!=i.new_type||i.is_see?t._e():s("i",{staticClass:"UnreadDot",attrs:{slot:"icon"},slot:"icon"}),s("template",{slot:"title"},[s("span",[t._v(t._s(i.gname?i.gname:"密群名未设置"))]),s("div",{staticClass:"van-cell__label",domProps:{innerHTML:t._s(i.content)}})]),s("div",{staticClass:"status",attrs:{slot:"right-icon"},slot:"right-icon"},[2==i.new_type?s("van-button",{attrs:{type:"primary",size:"mini"}},[t._v("查看")]):s("span",[t._v(t._s(i.new_type_name))])],1)],2),s("span",{attrs:{slot:"right"},on:{click:function(s){return t.delNotice(i.id)}},slot:"right"},[s("i",[t._v("删除")])])],1)})),t.groupList&&!t.groupList.length?s("center",{staticClass:"NullTips"},[t._v("暂时没有群通知")]):t._e()],2)])])],1)},o=[],l={name:"GroupNotice",components:{},props:{},data(){return{groupList:""}},filters:{},computed:{},watch:{},created(){this.getGroupsNotice()},mounted(){this.$MiInitial.InitScrollWrap(this.$el),this.ScrollWrap=new this.$MiBScroll(this.$refs.Scroll,{click:!0})},activated(){},destroyed(){},methods:{onClickBack(){this.$router.go(-1),this.$route.params.isBack=!0},getGroupsNotice(){this.$MiModel.NewGroupsList(t=>{this.groupList=t.list||[],this.$nextTick(()=>{this.$MiInitial.InitScroll(this.$el)})})},onOpenInfo(t,i){2!=i&&4!=i&&5!=i||this.$router.push({name:"groupInfo",query:{id:t}})},onClickDel(t,i){i.close()},delNotice(t){this.$MiModel.DelUserNew({id:t,type:2},t=>{this.getGroupsNotice()})}}},a=l,n=s("2877"),c=Object(n["a"])(a,e,o,!1,null,null,null);i["default"]=c.exports}}]);