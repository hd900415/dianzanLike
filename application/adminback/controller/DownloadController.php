<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\admin\controller;

use app\admin\controller\Common;

class DownloadController extends CommonController{
	/**
	 * 空操作处理
	 */
	public function _empty(){
		return $this->index();
	}
	/**
	 * 首页
	 * @return [type] [description]
	 */
	public function index(){
		return view();
	}

	public function download(){
		$param = input('get.');
		// 获取存放路径
		if (isset($param['word']) && $param['word'] == 'word') $filePath = model('Setting')->value('api_docx');
		if (isset($param['demo']) && $param['demo'] == 'demo') {
			$filePath = model('Setting')->value('api_demo');
			$this->downRar($filePath);
		}
		if (!$filePath || !file_exists($filePath)) die("文件不存在!");
		// 转码
		$filePath = iconv("utf-8","gbk//IGNORE",$filePath);
		// 绝对路径
		$filePath = $_SERVER['DOCUMENT_ROOT'].ltrim($filePath, '.');
		$file = fopen($filePath,"r") or die('打开文件错误'); //   打开文件
		//输入文件标签
		header("Content-type:application/octet-stream");  
		header("Accept-Ranges:bytes");  
		header("Accept-Length:".filesize($filePath));  
		header("Content-Disposition:attachment;filename=".basename($filePath));
		//输出文件(下载)
		echo fread($file, filesize($filePath));
		fclose($file); //关闭打开的文件
	}

	// 下载压缩包
	public function downRar($file_name){
		$file_name = iconv("utf-8", "gbk//IGNORE", $file_name); // 特别注意！特别注意！特别注意这里，windows下必须开转码,不然直接文件不存
		$file_path = $_SERVER['DOCUMENT_ROOT'].ltrim($file_name, '.');// 比如windows下这里我的是 "D:/web/public/uploads/rar/2009323162920-维C银翘片说明书.rar"
		//判断如果文件存在,则跳转到下载路径
		if (!$file_path || !file_exists($file_path)) die("文件不存在!");
		$fp = fopen($file_path, "r+") or die('打开文件错误');  //下载文件必须要将文件先打开。写入内存
		$file_size = filesize($file_path);
		//返回的文件流
		Header("Content-type:application/octet-stream");
		//按照字节格式返回
		Header("Accept-Ranges:bytes");
		//返回文件大小
		Header("Accept-Length:".$file_size);
		//弹出客户端对话框，对应的文件名
		Header("Content-Disposition:attachment;filename=".substr($file_name, strrpos($file_name, '/') + 1));
		//防止服务器瞬间压力增大，分段读取
		$buffer = 1024;
		while (!feof($fp)) {
			$file_data = fread($fp, $buffer);
			echo $file_data;
		}
		fclose($fp);
	}
}