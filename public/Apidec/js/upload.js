/* 橘子科技旗下 A4源码  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */
function upload_show_thumb(obj){
	$(obj).parent().find("img").remove();
	var val = $(obj).val();
	var vals = val.split(',');
	$.each(vals,function(i,r){
		if(r.indexOf('.jpg')!=-1 || r.indexOf('.gif')!=-1 || r.indexOf('.png')!=-1 ){
			if (r.indexOf('http')!=-1) {
				$(obj).after(" <img src='"+r+"' rel='"+r+"' height='40px' onclick='upload_file_remove(this)' onMouseOver=\"this.style.borderColor='red'\" onMouseOut=\"this.style.borderColor='#CCC'\" />");	
			} else {
				$(obj).after(" <img src='/"+r+"' rel='"+r+"' height='40px' onclick='upload_file_remove(this)' onMouseOver=\"this.style.borderColor='red'\" onMouseOut=\"this.style.borderColor='#CCC'\" />");	
			}
		}else{
			$(obj).after(" <img style=\" padding:3px 8px; display:inline-block; vertical-align:middle;\" src='/static//common/images/picico.png' rel='"+r+"'  onclick='upload_file_remove(this)' onMouseOver=\"this.style.borderColor='red'\" onMouseOut=\"this.style.borderColor='#CCC'\" />");	
		}
	})
}

function upload_file_remove(obj){
	if(!confirm("删除？")) return;
	imgsrc = $(obj).attr("rel");
	var input = $(obj).parent().find("input");
	var ipval = input.val();
	ipval = ipval.replace(","+imgsrc,'');
	ipval = ipval.replace(imgsrc+',','');
	ipval = ipval.replace(imgsrc,'');
	input.val(ipval);
	$(obj).remove();
}

// 文件上传
$(function(){
	$("input[_type=file]").each(function(){
		var name = $(this).attr("id");
		$(this).after(' <span style=" border:2px solid #999;border-radius:5px; height:18px;padding:3px 8px; line-height:18px; display:inline-block; vertical-align:middle;"><input type="button" value="上传" id="spanButtonPlaceholder_'+name+'"></span><div id="divFileProgressContainer_'+name+'" ></div>');	
		var swfu;
		swfu = new SWFUpload({
			upload_url: "/index/upload",
			post_params: {},
			
			file_types: "*.*",
			file_size_limit : "800",
	
			file_queue_error_handler : fileQueueError,
			file_dialog_complete_handler : fileDialogComplete,
			upload_progress_handler : uploadProgress,
			upload_error_handler : uploadError,
			upload_success_handler : uploadSuccess,
			upload_complete_handler : uploadComplete,
	
			// Button Settings
			button_image_url : "",
			button_placeholder_id : "spanButtonPlaceholder_"+name,
			button_width: 40,
			button_height: 20,
			button_text : '上传',
			//button_text_style : '',
			button_text_top_padding: 0,
			button_text_left_padding: 5,
			button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
			button_cursor: SWFUpload.CURSOR.HAND,
			
			// Flash Settings
			flash_url : "/static/swfupload/swfupload.swf",
	
			custom_settings : {
				content_box : this,
				upload_target : "divFileProgressContainer_"+name
			},
			
			// Debug Settings
			debug: false
		});
	})

	// 加载图片预览
	$("input[_type=file]").each(function(){
		upload_show_thumb(this);
	})
	$("input[_type=file]").change(function(){
		upload_show_thumb(this);
	})
	
})



// 加载编辑器
$(function(){
	$("textarea[_type=minieditor]").each(function(){
		var name = $(this).attr("id");
		var editorOption = {
			toolbars:[['FullScreen', 'Source', 'Undo', 'Redo','Bold','JustifyLeft','JustifyCenter','JustifyRight','InsertImage','RemoveFormat', 'FontFamily', 'FontSize']],
			minFrameHeight:120,
			autoHeightEnabled:false,
			elementPathEnabled:false,
			wordCount:false
		}
		var editor_a = new baidu.editor.ui.Editor(editorOption);
		editor_a.render( name );
	})

	$("textarea[_type=editor]").each(function(){
		var name = $(this).attr("id");	
		var editorOption = {
			initialFrameWidth:860,
			initialFrameHeight:420
		}
		var editor_a = new baidu.editor.ui.Editor(editorOption);
		editor_a.render( name );
	})
})

