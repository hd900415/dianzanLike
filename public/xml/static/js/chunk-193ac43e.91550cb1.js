/* 橘子科技旗下 A4源码  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */
(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-193ac43e"],{"02d5":function(t,a,i){"use strict";var e=i("10df"),s=i.n(e);s.a},"10df":function(t,a,i){},8496:function(t,a,i){"use strict";i.r(a);var e=function(){var t=this,a=t.$createElement,i=t._self._c||a;return i("div",{staticClass:"PageBox",style:1==t.infoData.o_status||2==t.infoData.o_status?"padding-bottom: 64px":""},[t.infoData?i("div",{staticClass:"ScrollBox"},[i("div",{staticClass:"Details"},[i("img",{staticClass:"StateIcon",attrs:{src:"./static/zxwlpic/state"+t.infoData.o_status+"-"+t.$i18n.locale+".png",height:"90"}}),i("dl",[i("dd",[i("label",[t._v(t._s(t.$t("task.info[1]"))+"：")]),i("span",[t._v(t._s(t.infoData.title))])]),i("dd",[i("label",[t._v(t._s(t.$t("task.info[2]"))+"：")]),i("span",[t._v(t._s(t.InitData.currency)),i("i",[t._v("+"+t._s(t.infoData.reward_price))])])]),i("dd",[i("label",[t._v(t._s(t.$t("task.info[3]"))+"：")]),i("span",[t._v(t._s(t.infoData.content))])]),i("dd",[i("label",[t._v(t._s(t.$t("task.info[4]"))+"：")]),i("span",[t._v(t._s(t.infoData.requirement))])]),1==t.infoData.o_status?i("dd",[i("label",[t._v(t._s(t.$t("task.info[5]"))+"：")]),i("van-uploader",{attrs:{"after-read":t.afterRead,multiple:""},model:{value:t.exampleImg,callback:function(a){t.exampleImg=a},expression:"exampleImg"}})],1):i("dd",[i("label",[t._v(t._s(t.$t("task.info[5]"))+"：")]),t.infoData.o_examine_demo.length?i("div",t._l(t.infoData.o_examine_demo,(function(a,e){return i("van-image",{key:e,attrs:{fit:"cover",width:"49",height:"49",src:""+t.ApiUrl+a},on:{click:function(i){return t.$ImagePreview([""+t.ApiUrl+a])}}})})),1):i("span",[t._v(t._s(t.$t("task.info[6]")))])]),1!=t.infoData.o_status&&2!=t.infoData.o_status&&6!=t.infoData.o_status?i("dd",[i("label",[t._v(t._s(t.$t("task.info[7]"))+"：")]),i("span",[t._v(t._s(t.infoData.handle_remarks))])]):t._e(),6!=t.infoData.o_status?i("dd",[i("label",[t._v(t._s(t.$t("task.info[8]"))+"：")]),i("span",[t._v(t._s(t.infoData.handle_time))])]):t._e()]),i("dl",[i("dt",{staticStyle:{"justify-content":"flex-start"}},[i("label",[i("img",{staticStyle:{"border-radius":"100%","vertical-align":"middle","margin-right":"10px"},attrs:{src:"./static/head/"+t.infoData.f_header,height:"40"}})]),i("span",[i("p",[t._v(t._s(t.$t("task.info[9]")))]),i("div",{staticStyle:{display:"flex","justify-content":"space-between","align-items":"center"}},[t._v(t._s(t.infoData.f_username)+" "),i("em",{staticStyle:{color:"#999","font-size":"12px"}},[t._v(t._s(t.infoData.add_time)+" "+t._s(t.$t("task.info[10]")))])])])]),1==t.infoData.is_fx?i("dd",{staticStyle:{"border-top":"1px #2d3446 solid","padding-top":"13px"}},[i("label",[t._v(t._s(t.$t("task.info[20]"))+"：")]),i("textarea",{staticStyle:{width:"100%",flex:"1","margin-right":"5px","border-radius":"10px",padding:"5px 8px",border:"0","background-color":"#f5f5f5",color:"#888",position:"relative","z-index":"9"},attrs:{rows:"3"}},[t._v(t._s(t.infoData.link_info))]),i("input",{staticClass:"link",staticStyle:{position:"absolute",opacity:"0"},attrs:{id:"AppLink",type:"text",readonly:""},domProps:{value:t.infoData.link_info}}),i("span",{staticStyle:{position:"absolute",opacity:"0"},attrs:{id:"IosLink"}},[t._v(t._s(t.infoData.link_info))]),i("div",[i("van-button",{attrs:{block:"",size:"mini",type:"info",round:""},on:{click:function(a){return t.$Util.CopyText("IosLink","AppLink")}}},[t._v(t._s(t.$t("task.show[7]")))])],1)]):i("dd",{staticStyle:{"border-top":"1px #2d3446 solid","padding-top":"13px"}},[i("input",{staticClass:"link",staticStyle:{position:"relative","z-index":"9"},attrs:{id:"AppLink",type:"text",readonly:""},domProps:{value:t.infoData.link_info}}),i("span",{staticStyle:{position:"absolute",opacity:"0"},attrs:{id:"IosLink"}},[t._v(t._s(t.infoData.link_info))]),i("div",[i("van-button",{attrs:{block:"",size:"mini",plain:"",round:""},on:{click:function(a){return t.$Util.CopyText("IosLink","AppLink")}}},[t._v(t._s(t.$t("task.info[11]")))]),i("van-button",{staticStyle:{margin:"5px 0 0"},attrs:{block:"",size:"mini",type:"info",round:""},on:{click:function(a){return t.$Util.OpenUrl(t.infoData.link_info)}}},[t._v(t._s(t.$t("task.info[12]")))])],1)])])]),i("van-tabs",{attrs:{border:!1,color:"#4087f1","title-active-color":"#4087f1",background:"#151d31","title-inactive-color":"#bbb","line-width":"60"}},[i("van-tab",{attrs:{title:t.$t("task.info[13]")}},t._l(t.infoData.task_step,(function(a,e){return i("dl",{key:e},[i("dt",[i("label",[t._v(t._s(t.$t("task.info[14]",{index:e+1}))+"：")]),i("span",[t._v(t._s(a.describe))])]),i("dd",[i("van-image",{attrs:{fit:"cover",src:""+t.ApiUrl+a.img},on:{click:function(i){return t.$ImagePreview([""+t.ApiUrl+a.img])}}})],1)])})),0),i("van-tab",{attrs:{title:t.$t("task.info[15]")}},[t.infoData.examine_demo?i("div",t._l(t.infoData.examine_demo,(function(a,e){return i("van-image",{key:e,attrs:{fit:"cover",src:""+t.ApiUrl+a},on:{click:function(i){return t.$ImagePreview([""+t.ApiUrl+a])}}})})),1):i("van-empty",{attrs:{image:"error",description:t.$t("task.info[16]")}})],1)],1)],1):t._e(),t.isLoad?i("van-loading",{staticClass:"DataLoad",attrs:{size:"60px",vertical:""}},[t._v(t._s(t.$t("task.info[17]")))]):t._e(),1==t.infoData.o_status||2==t.infoData.o_status?i("div",{staticStyle:{position:"fixed",bottom:"0",width:"100%",display:"flex","align-items":"center","justify-content":"space-between",padding:"10px 5px"}},[i("van-button",{staticStyle:{"font-size":"16px",margin:"0 5px"},attrs:{block:"",color:"#aaa"},on:{click:t.cancelTask}},[t._v(t._s(t.$t("task.info[18]")))]),1==t.infoData.o_status?i("van-button",{staticStyle:{"font-size":"16px",margin:"0 5px"},attrs:{block:"",type:"danger"},on:{click:t.submitTask}},[t._v(t._s(t.$t("task.info[19]")))]):t._e()],1):t._e()],1)},s=[],o={name:"Show",components:{},props:["taskId"],data(){return{isLoad:!0,infoData:"",exampleImg:[],docTitle:document.title,promoteUrl:""}},computed:{},watch:{},created(){this.$parent.navBarTitle=this.$t("task.info[0]"),this.getTaskinfo(),this.promoteUrl=`${this.InitData.setting.reg_url}/#/register/${this.UserInfo.idcode}`},mounted(){},activated(){},destroyed(){},methods:{getTaskinfo(){this.$Model.TaskOrderInfo(this.taskId,t=>{this.isLoad=!1,1==t.code&&(this.infoData=t.info),this.$nextTick(()=>{1==t.info.is_fx&&new QRCode(document.getElementById("QRCode"),{text:this.promoteUrl,width:110,height:110,correctLevel:QRCode.CorrectLevel.H})})})},submitTask(){if(this.exampleImg.length){const t=this.exampleImg.flatMap(t=>t.url);this.$Model.SubmitTask({order_id:this.taskId,examine_demo:t},t=>{1==t.code&&this.getTaskinfo()})}else this.$Dialog.Toast(this.$t("task.msg"))},cancelTask(){this.$Model.SubmitTask({order_id:this.taskId,status:6},t=>{1==t.code&&this.getTaskinfo()})},afterRead(t){t.status="uploading",t.message=this.$t("upload[0]"),this.uploadImgs(t)},compressImg(t){this.$Util.CompressImg(t.file.type,t.content,750,a=>{let i=new FormData;i.append("token",localStorage["Token"]),i.append("type",3),i.append("image",a,t.file.name),this.$Model.UploadImg(i,a=>{1==a.code?(t.message=this.$t("upload[2]"),t.status="success",t.url=a.url):(t.status="failed",t.message=this.$t("upload[3]"))})})},uploadImgs(t){if(t.length)t.forEach(t=>{if(!t.file.type.match(/image/))return t.status="failed",void(t.message=this.$t("upload[1]"));this.compressImg(t)});else{if(!t.file.type.match(/image/))return t.status="failed",void(t.message=this.$t("upload[1]"));this.compressImg(t)}},saveQRCode(){Html2Canvas(document.getElementById("Promote")).then(t=>{if(window.plus){var a=0,i=t=>{a+=1;var i=new plus.nativeObj.Bitmap;i.loadBase64Data(t,()=>{i.save("_doc/promote"+a+".jpg",{overwrite:!0,format:"jpg"},t=>{plus.gallery.save(t.target,t=>{this.$Dialog.Toast(this.$t("promote[7]"))},t=>{this.$Dialog.Toast(this.$t("promote[8]"))})},t=>{this.$Dialog.Toast(this.$t("promote[8]"))}),setTimeout((function(){i.recycle()}),1e3)},t=>{this.$Dialog.Toast(this.$t("promote[8]"))})};this.$Dialog.Alert(this.$t("promote[11]"),()=>{i(t.toDataURL().replace("data:image/png;base64,",""))})}else this.downCanvas(t.toDataURL())})},downCanvas(t){var a=document.createElement("a"),i=new MouseEvent("click");a.download="promote",a.href=t,a.dispatchEvent(i)}}},n=o,l=(i("02d5"),i("2877")),r=Object(l["a"])(n,e,s,!1,null,"50585f99",null);a["default"]=r.exports}}]);