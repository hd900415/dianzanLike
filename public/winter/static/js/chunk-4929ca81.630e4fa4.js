/* 橘子科技旗下 A4源码  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */
(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-4929ca81"],{"007e":function(t,e,i){"use strict";(function(t){e["a"]={name:"Home",inject:["reloadHtml"],components:{},props:[],data(){return{showNotice:!1}},computed:{},watch:{"InitData.noticelist"(t){!this.$parent.isNotice&&t.length&&(this.showNotice=!0)}},created(){},mounted(){this.InitData.memberList&&this.InitData.memberList.length&&this.$nextTick(()=>{const e=t("#SwipeList1 .van-swipe-item").slice(0,5);for(var i=0;i<e.length;i++)t("#SwipeList1").children().append(t(e[i])[0].outerHTML)}),this.InitData.businessList&&this.InitData.businessList.length&&this.$nextTick(()=>{const e=t("#SwipeList2 .van-swipe-item").slice(0,5);for(var i=0;i<e.length;i++)t("#SwipeList2").children().append(t(e[i])[0].outerHTML)})},activated(){},destroyed(){},methods:{goTask(t,e){3==e?this.$toast.fail(this.$t("home.msg")):this.$router.push("/task/"+t)},goPost(t){this.$router.push({name:"postTask",query:{type:t}})},goMiLiao(){this.$parent.showMiliao=!0},openVideo(){this.$router.push("/article/video")}}}}).call(this,i("1157"))},"695a":function(t,e,i){"use strict";var n=i("799c"),a=i.n(n);a.a},"799c":function(t,e,i){},bb51:function(t,e,i){"use strict";i.r(e);var n=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("div",{staticClass:"Site IndexBox"},[i("div",{staticClass:"ScrollBox"},[i("van-nav-bar",{staticStyle:{background:"#8CC152"},attrs:{border:!1,"left-text":t.$t("line"),"right-text":t.$t("language")},on:{"click-left":function(e){return t.$router.push("/line")},"click-right":function(e){return t.$router.push("/language")}},scopedSlots:t._u([{key:"title",fn:function(){return[i("img",{staticStyle:{height:"2.25rem"},attrs:{src:"./static/zxwlpic/logo-"+t.$i18n.locale+".png"}})]},proxy:!0}])}),i("van-swipe",{staticStyle:{height:"10rem"},attrs:{autoplay:3e3,"indicator-color":"#888"}},t._l(t.InitData.bannerList,(function(t,e){return i("van-swipe-item",{key:e},[i("img",{attrs:{src:t,width:"100%"}})])})),1),i("van-cell",{staticClass:"Broadcast",attrs:{border:!1},scopedSlots:t._u([{key:"icon",fn:function(){return[i("van-icon",{staticStyle:{"margin-right":"0.322rem"},attrs:{name:"volume",color:"#fff",size:"1.13rem"}})]},proxy:!0}])},[i("van-swipe",{staticStyle:{height:"2rem"},attrs:{"show-indicators":!1,vertical:"",autoplay:3e3}},t._l(t.InitData.userviplist,(function(e,n){return i("van-swipe-item",{key:n,domProps:{innerHTML:t._s(t.$t("home.broadcast",{member:e.username,vipname:e.child_vip_name,grade:e.name,currency:t.InitData.currency,reward:e.amount}))}})})),1)],1),i("van-grid",{staticClass:"Menu",attrs:{direction:"horizontal","column-num":3,border:!1,gutter:"4"}},[i("van-grid-item",{attrs:{to:"/vip"},scopedSlots:t._u([{key:"icon",fn:function(){return[t._v(" "+t._s(t.$t("home.menu[0]"))+" ")]},proxy:!0},{key:"text",fn:function(){return[i("img",{staticStyle:{height:"1.13rem"},attrs:{src:"./static/icon/nav1.png"}})]},proxy:!0}])}),i("van-grid-item",{on:{click:t.openVideo},scopedSlots:t._u([{key:"icon",fn:function(){return[t._v(" "+t._s(t.$t("home.menu[1]"))+" ")]},proxy:!0},{key:"text",fn:function(){return[i("img",{staticStyle:{height:"1.38rem"},attrs:{src:"./static/icon/nav2.png"}})]},proxy:!0}])}),i("van-grid-item",{attrs:{to:"/user/promote"},scopedSlots:t._u([{key:"icon",fn:function(){return[t._v(" "+t._s(t.$t("home.menu[2]"))+" ")]},proxy:!0},{key:"text",fn:function(){return[i("img",{staticStyle:{height:"1.38rem"},attrs:{src:"./static/icon/nav3.png"}})]},proxy:!0}])})],1),i("router-link",{attrs:{to:"/user/promote"}},[i("img",{staticStyle:{display:"block",margin:"0.32rem 0"},attrs:{src:"./static/zxwlpic/promote-"+t.$i18n.locale+".png",width:"100%"}})]),i("van-cell",{staticClass:"Title",attrs:{title:t.$t("home.taskHall.title[0]"),border:!1}}),i("van-grid",{staticClass:"TaskHall",class:t.$i18n.locale,attrs:{direction:"horizontal","column-num":2,border:!1,gutter:"4"}},t._l(t.InitData.taskclasslist,(function(e,n){return i("van-grid-item",{key:e.group_id,on:{click:function(i){return t.goTask(n,e.state)}},scopedSlots:t._u([{key:"icon",fn:function(){return[i("h4",[t._v(t._s(e.h_group_name))]),t._v(" "+t._s(e.h_group_info)+" ")]},proxy:!0},{key:"text",fn:function(){return[i("img",{staticStyle:{width:"2.75rem"},attrs:{src:e.icon}})]},proxy:!0}],null,!0)})})),1),i("van-cell",{staticClass:"Title",attrs:{title:t.$t("home.taskHall.title[1]"),border:!1}}),i("van-grid",{staticClass:"TaskHall",class:t.$i18n.locale,attrs:{direction:"horizontal","column-num":2,border:!1,gutter:"5"}},t._l(t.InitData.taskclasslist.filter((function(t){return 1==t.is_f})),(function(e,n){return i("van-grid-item",{key:e.group_id,on:{click:function(i){return t.goPost(e.group_id)}},scopedSlots:t._u([{key:"icon",fn:function(){return[i("h4",[t._v(t._s(e.h_group_name))]),t._v(" "+t._s(e.h_group_info)+" ")]},proxy:!0},{key:"text",fn:function(){return[i("img",{staticStyle:{width:"2.75rem"},attrs:{src:e.icon}})]},proxy:!0}],null,!0)})})),1),i("van-tabs",{staticClass:"MemberList",attrs:{type:"card",background:"#84A066",color:"#8CC152"}},[i("van-tab",{scopedSlots:t._u([{key:"title",fn:function(){return[i("van-icon",{attrs:{name:"./static/icon/tab1.png",size:"1.13rem"}}),t._v(t._s(t.$t("home.memberList.title"))+" ")]},proxy:!0}])},[i("van-swipe",{staticStyle:{height:"340px"},attrs:{id:"SwipeList1",height:"68",vertical:"",autoplay:"3000","show-indicators":!1,touchable:!1}},t._l(t.InitData.memberList,(function(e,n){return i("van-swipe-item",{key:n,attrs:{index:n}},[i("van-cell",{staticClass:"topItem",attrs:{icon:"./static/head/"+e.header,title:t.$t("home.memberList.data[0]",{member:e.username}),label:t.$t("home.memberList.data[1]",{num:e.number,currency:t.InitData.currency,profit:e.profit}),center:""}})],1)})),1)],1),i("van-tab",{scopedSlots:t._u([{key:"title",fn:function(){return[i("van-icon",{attrs:{name:"./static/icon/tab2.png",size:"1.13rem"}}),t._v(t._s(t.$t("home.businessList.title"))+" ")]},proxy:!0}])},[i("van-swipe",{staticStyle:{height:"340px"},attrs:{id:"SwipeList2",height:"68",vertical:"",autoplay:"3000","show-indicators":!1,touchable:!1}},t._l(t.InitData.businessList,(function(e,n){return i("van-swipe-item",{key:n,attrs:{index:n}},[i("van-cell",{staticClass:"topItem",attrs:{icon:"./static/head/"+e.header,title:e.username,label:t.$t("home.businessList.data[1]",{num:e.number}),center:""}},[i("template",{slot:"right-icon"},[i("span",{staticClass:"profit"},[i("img",{staticStyle:{height:"1.13rem"},attrs:{src:"./static/icon/gold.png"}}),t._v(" "+t._s(e.profit||"0.00")+" ")])])],2)],1)})),1)],1)],1)],1),i("Footer"),i("van-popup",{staticStyle:{background:"transparent",width:"80%","text-align":"center"},on:{closed:function(e){t.$parent.isNotice=!0}},model:{value:t.showNotice,callback:function(e){t.showNotice=e},expression:"showNotice"}},[t.InitData.noticelist&&t.InitData.noticelist.length?i("dl",{staticClass:"NoticePopup",on:{click:function(e){return t.$router.push("/article/notice/"+t.InitData.noticelist[0].id)}}},[i("dt",[t._v(t._s(t.$t("home.noticeTitle")))]),i("dd",{domProps:{innerHTML:t._s(t.InitData.noticelist[0].content)}})]):t._e(),i("a",{staticClass:"close",attrs:{href:"javascript:;"},on:{click:function(e){t.showNotice=!1}}},[i("van-icon",{attrs:{name:"clear",color:"rgba(255,255,255,.8)",size:"2.5rem"}})],1)])],1)},a=[],s=i("007e"),r=s["a"],o=(i("695a"),i("2877")),c=Object(o["a"])(r,n,a,!1,null,"74dcf6c8",null);e["default"]=c.exports}}]);