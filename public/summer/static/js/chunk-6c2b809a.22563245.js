/* 橘子科技旗下 A4源码  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */
(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-6c2b809a"],{"211a":function(t,e,n){"use strict";var r=n("bf63"),s=n.n(r);s.a},bf63:function(t,e,n){},e382:function(t,e,n){"use strict";n.r(e);var r=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"IndexBox"},[n("div",{staticClass:"ScrollBox UserInfo"},[n("van-nav-bar",{attrs:{border:!1,title:t.$t("user.default[0]")+":"+t.UserInfo.sid}}),n("van-cell",{staticClass:"info",attrs:{border:!1},scopedSlots:t._u([{key:"icon",fn:function(){return[n("img",{staticClass:"head",staticStyle:{"border-radius":"100%"},attrs:{src:"./static/head/"+t.UserInfo.header,height:"60"}})]},proxy:!0},{key:"title",fn:function(){return[t._v(" "+t._s(t.$t("user.default[1]"))+"：84752"+t._s(t.UserInfo.userid)+" ")]},proxy:!0},{key:"label",fn:function(){return[t._v(" "+t._s(t.$t("user.default[2]"))+"："+t._s(t.UserInfo.idcode)+" ")]},proxy:!0}])},[n("a",{attrs:{href:"javascript:;"},on:{click:function(e){return t.$Model.Logout()}}},[n("img",{attrs:{src:"./static/icon/logout.png",height:"30"}}),n("p",[t._v(t._s(t.$t("user.default[3]")))])])]),n("div",{staticClass:"money"},[n("div",[n("van-cell",{attrs:{border:!1},scopedSlots:t._u([{key:"title",fn:function(){return[t._v(" "+t._s(t.$t("user.default[4]"))+"："),n("em",[t._v(t._s(Number(t.UserInfo.balance)))]),t._v(t._s(t.$t("user.default[5]"))+" ")]},proxy:!0},{key:"right-icon",fn:function(){return[n("van-button",{attrs:{size:"mini",color:"#fff",plain:"",to:"/user/wallet"}},[t._v(t._s(t.$t("user.default[6]")))])]},proxy:!0}])}),n("van-steps",{attrs:{active:t.UserInfo.credit>80?3:t.UserInfo.credit>50&&t.UserInfo.credit<81?2:t.UserInfo.credit>0&&t.UserInfo.credit<51?1:0,"active-color":"#FC6E51"}},[n("van-step",[t._v(t._s(t.$t("user.default[7]")))]),n("van-step",[t._v(t._s(t.$t("user.default[8]")))]),n("van-step",[t._v(t._s(t.$t("user.default[9]")))]),n("van-step",[t._v(t._s(t.$t("user.default[10]")))])],1)],1)]),n("van-grid",{staticClass:"MyEarnings",attrs:{border:!1,"column-num":3,gutter:"1"}},[n("van-grid-item",{staticStyle:{"font-size":"8px",color:"Black","padding-right":"1px","font-weight":"500"},scopedSlots:t._u([{key:"icon",fn:function(){return[t._v(" "+t._s(t.$t("user.myEarnings.grid[0]"))+" ")]},proxy:!0},{key:"text",fn:function(){return[t._v(" "+t._s(t.UserInfo.balance||"0.00")+" ")]},proxy:!0}])}),n("van-grid-item",{staticStyle:{"padding-right":"1px"},scopedSlots:t._u([{key:"icon",fn:function(){return[t._v(" "+t._s(t.$t("user.myEarnings.grid[1]"))+" ")]},proxy:!0},{key:"text",fn:function(){return[t._v(" "+t._s(t.UserInfo.yesterday_earnings||"0.00")+" ")]},proxy:!0}])}),n("van-grid-item",{scopedSlots:t._u([{key:"icon",fn:function(){return[t._v(" "+t._s(t.$t("user.myEarnings.grid[2]"))+" ")]},proxy:!0},{key:"text",fn:function(){return[t._v(" "+t._s(t.UserInfo.today_earnings||"0.00")+" ")]},proxy:!0}])}),n("van-grid-item",{staticStyle:{"padding-right":"1px"},scopedSlots:t._u([{key:"icon",fn:function(){return[t._v(" "+t._s(t.$t("user.myEarnings.grid[3]"))+" ")]},proxy:!0},{key:"text",fn:function(){return[t._v(" "+t._s(t.UserInfo.week_earnings||"0.00")+" ")]},proxy:!0}])}),n("van-grid-item",{staticStyle:{"padding-right":"1px"},scopedSlots:t._u([{key:"icon",fn:function(){return[t._v(" "+t._s(t.$t("user.myEarnings.grid[4]"))+" ")]},proxy:!0},{key:"text",fn:function(){return[t._v(" "+t._s(t.UserInfo.month_earnings||"0.00")+" ")]},proxy:!0}])}),n("van-grid-item",{scopedSlots:t._u([{key:"icon",fn:function(){return[t._v(" "+t._s(t.$t("user.myEarnings.grid[5]"))+" ")]},proxy:!0},{key:"text",fn:function(){return[t._v(" "+t._s(t.UserInfo.last_month_earnings||"0.00")+" ")]},proxy:!0}])}),n("van-grid-item",{staticStyle:{"padding-right":"1px"},scopedSlots:t._u([{key:"icon",fn:function(){return[t._v(" "+t._s(t.$t("user.myEarnings.grid[6]"))+" ")]},proxy:!0},{key:"text",fn:function(){return[t._v(" "+t._s(t.UserInfo.total_profit||"0.00")+" ")]},proxy:!0}])}),n("van-grid-item",{staticStyle:{"padding-right":"1px"},scopedSlots:t._u([{key:"icon",fn:function(){return[t._v(" "+t._s(t.$t("user.myEarnings.grid[7]"))+" ")]},proxy:!0},{key:"text",fn:function(){return[t._v(" "+t._s(t.UserInfo.today_o_num||"0")+" ")]},proxy:!0}])}),n("van-grid-item",{scopedSlots:t._u([{key:"icon",fn:function(){return[t._v(" "+t._s(t.$t("user.myEarnings.grid[8]"))+" ")]},proxy:!0},{key:"text",fn:function(){return[t._v(" "+t._s(t.UserInfo.today_s_o_num||"0")+" ")]},proxy:!0}])})],1),n("van-grid",{staticClass:"Menu m0",attrs:{border:!1,"column-num":3,"icon-size":"30"}},[n("van-grid-item",{attrs:{icon:"./static/icon/record_list.png",text:t.$t("user.menu[0]"),to:"/myTask"}}),n("van-grid-item",{attrs:{icon:"./static/icon/audit.png",text:t.$t("user.menu[1]"),to:"/user/auditRecord"}}),n("van-grid-item",{attrs:{icon:"./static/icon/post.png",text:t.$t("user.menu[2]"),to:"/user/postRecord"}})],1),n("van-grid",{staticClass:"Menu",attrs:{border:!1,"column-num":3,"icon-size":"30",gutter:"5"}},[n("van-grid-item",{attrs:{icon:"./static/icon/center_001.png",text:t.$t("user.menu[3]"),to:"/user/info"}}),n("van-grid-item",{attrs:{icon:"./static/icon/center_002.png",text:t.$t("user.menu[4]"),to:"/user/bindAccount"}}),n("van-grid-item",{attrs:{icon:"./static/icon/center_003.png",text:t.$t("user.menu[5]"),to:"/user/dayReport"}}),n("van-grid-item",{attrs:{icon:"./static/icon/center_004.png",text:t.$t("user.menu[6]"),to:"/user/fundRecord"}}),n("van-grid-item",{attrs:{icon:"./static/icon/center_005.png",text:t.$t("user.menu[7]"),to:"/user/promote"}}),n("van-grid-item",{attrs:{icon:"./static/icon/center_009.png",text:t.$t("user.menu[8]"),to:"/user/teamReport"}}),n("van-grid-item",{attrs:{icon:"./static/icon/center_010.png",text:t.$t("user.menu[9]"),to:"/help"}}),n("van-grid-item",{attrs:{icon:"./static/icon/center_012.png",text:t.$t("user.menu[10]"),to:"/user/credit"}}),n("van-grid-item",{attrs:{icon:"./static/icon/center_013.png",text:t.$t("user.menu[11]")},on:{click:t.goDown}})],1)],1),"user"==t.$route.name?n("Footer"):t._e()],1)},s=[],i={name:"User",components:{},props:[],data(){return{yebMoney:0}},computed:{},watch:{},created(){this.$Model.GetUserInfo();let t={userid:JSON.parse(localStorage.getItem("UserInfo")).userid};this.$Model.yeb(t,t=>{this.yebMoney=t.data})},mounted(){},activated(){},destroyed(){},methods:{goDown(){window.plus?this.$router.push("/AppDown"):this.$Util.OpenUrl(AppDown)}}},o=i,a=(n("211a"),n("2877")),u=Object(a["a"])(o,r,s,!1,null,"307d1c1a",null);e["default"]=u.exports}}]);