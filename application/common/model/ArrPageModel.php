<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */


/**
 * 数组分页
 */

namespace app\common\model;

use think\Model;

class ArrPageModel extends Model{
	public $pageArray=array(); //数组
	public $pageSize=10; //每页显示记录数
	public $current= 1; //当前页
	
	private $total=0; //总页数
	private $prev=0; //上一页
	private $next=0; //下一页
	
	public $argumetsOther='';//设置参数
	

	/*通过数组进行初始化
	 * 
	 * 数组为关联数组,参数索引为pageArray,pageSize,current
	 * 
	 */
	function setArguments($arr){
	 if (is_array($arr)){
	  $this->pageArray=$arr['pageArray'];
	  $this->pageSize=$arr['pageSize'];
	  $this->current=$arr['current'];
	 }else{
	  return ;
	 }
	}
	
	//返回链接
	function page($array=array(),$pageSize=10,$current=1,$url=''){
		$this->pageArray = $array;
		$this->pageSize = $pageSize;
		$this->current = $current; 

		$_return=array();
		/*calculator*/
		$this->total=ceil(Count($this->pageArray)/$this->pageSize);
		$this->prev=(($this->current-1)<= 0 ? "1":($this->current-1));
		$this->next=(($this->current+1)>=$this->total ? $this->total:$this->current+1);
	 
		$current=($this->current>($this->total)?($this->total):$this->current);
	 
		$start=($this->current-1)*$this->pageSize;
		$arrleng=count($this->pageArray);

		$_return = array_slice($this->pageArray, $start, $this->pageSize, true);
		
		// for($i=$start;$i<($start+$this->pageSize);$i++){
		// 	if($i >= $arrleng)break;
		// 	array_push($_return,$this->pageArray[$i]);
		// }
		$pagearray["source"]=$_return;
		$pagearray["links"]=$this->linkStyle(2,count($array),$pageSize,7,$current,$url);
		return $pagearray;
	}
	
	
	//链接的样式
	private function linkStyle($number=1,$totalnum,$perpage,$pagenum,$curpage,$url=''){
	$linkStyle='';
	switch ($number){
	  case 1:
		$linkStyle="<a href=\"?page=1\">first</a> <a href=\"?page={$this->prev}\">prev</a> <a href=\"?page={$this->next}\">next</a> <a href=\"?page={$this->total}\">end</a>";
		break;
	  case 2:
	  	$pageNum = '';
	   	$totalpage=ceil($totalnum/$perpage);//页码总数:ceil（总记录数/每页显示的条数）;ceil向上取整
		if($curpage>$totalpage){
		    $curpage=$totalpage; 
		}
		$pre=$curpage-1;    //上一页=当前总页码数 - 1
		$next=$curpage+1;   //下一页=当前总页码数 + 1
		if($pre<1) $pre = 1;
		if($next>$totalpage) $next = $totalpage;

		$floorpage=floor($pagenum/2);//floor() 向下取整
		$start=$curpage - $floorpage;//显示页码数的开始
		$end=$curpage + $floorpage;  //显示页码数的结束

		//判断点击下几页结束的显示
		if($end>$totalpage){
		    $start=$totalpage-$pagenum+1;
		    $end=$totalpage;
		}
		//判断点击上几页开始的显示
		if($start<1){
		    $start=1;
		    $end=$pagenum;
		}
		if($totalpage<$pagenum){
		    $start=1;
		    $end=$totalpage;
		}
	   	for ($i= $start; $i <= $end; $i++) {
		    if($curpage==$i){
		        $pageNum.="<a href=\"javascript:void(0);\"> $i </a>";
		    }else{
		        $pageNum.="<a href=\"?page=$i".$url."\"> $i </a>";
		    }
	   	}
		$linkStyle="<div class=\"page\"><a href=\"?page=1".$url."\"><<</a> <a href=\"?page=".$pre.$url."\"><</a> ".$pageNum." <a href=\"?page=".$next.$url."\">></a> <a href=\"?page=".$totalpage.$url."\">>></a><p style='height:30px;line-height:30px;'>共 ".$totalpage." 页，".$totalnum." 条记录，当前第 ".$curpage." 页</p></div>";
		break;
	  }
	  return $linkStyle;
	 }
}