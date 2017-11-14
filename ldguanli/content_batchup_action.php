<?php
/**
 * 内容处理函数
 *
 * @version        $Id: content_batch_up.php 1 14:31 2010年7月12日Z tianya $
 * @package        DedeCMS.Administrator
 * @copyright      Copyright (c) 2007 - 2010, DesDev, Inc.
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_ArcBatch');
require_once(DEDEINC."/typelink.class.php");
require_once(DEDEADMIN."/inc/inc_batchup.php");
@set_time_limit(0);

//typeid,startid,endid,seltime,starttime,endtime,action,newtypeid
//批量操作
//check del move makehtml
//获取ID条件
if(empty($startid)) $startid = 0;
if(empty($endid)) $endid = 0;
if(empty($seltime)) $seltime = 0;
if(empty($typeid)) $typeid = 0;
if(empty($userid)) $userid = '';

//生成HTML操作由其它页面处理
if($action=="makehtml")
{
    $jumpurl  = "makehtml_archives_action.php?endid=$endid&startid=$startid";
    $jumpurl .= "&typeid=$typeid&pagesize=20&seltime=$seltime";
    $jumpurl .= "&stime=".urlencode($starttime)."&etime=".urlencode($endtime);
    header("Location: $jumpurl");
    exit();
}

$gwhere = " WHERE 1 ";
if($startid >0 ) $gwhere .= " AND id>= $startid ";
if($endid > $startid) $gwhere .= " AND id<= $endid ";
$idsql = '';

if($typeid!=0)
{
    $ids = GetSonIds($typeid);
    $gwhere .= " AND typeid IN($ids) ";
}
if($seltime==1)
{
    $t1 = GetMkTime($starttime);
    $t2 = GetMkTime($endtime);
    $gwhere .= " AND (senddate >= $t1 AND senddate <= $t2) ";
}
if(!empty($userid))
{
	$row = $dsql->GetOne("SELECT `mid` FROM #@__member WHERE `userid` LIKE '$userid'");
	if(is_array($row))
	{
		$gwhere .= " AND mid = {$row['mid']} ";
	}
}
//特殊操作
if(!empty($heightdone)) $action=$heightdone;

//指量审核
if($action=='check')
{
    if(empty($startid) || empty($endid) || $endid < $startid)
    {
        ShowMsg('该操作必须指定起始ID！','javascript:;');
        exit();
    }
    $jumpurl  = "makehtml_archives_action.php?endid=$endid&startid=$startid";
    $jumpurl .= "&typeid=$typeid&pagesize=20&seltime=$seltime";
    $jumpurl .= "&stime=".urlencode($starttime)."&etime=".urlencode($endtime);
    $dsql->SetQuery("SELECT id,arcrank FROM `#@__arctiny` $gwhere");
    $dsql->Execute('c');
    while($row = $dsql->GetObject('c'))
    {
        if($row->arcrank==-1)
        {
            $dsql->ExecuteNoneQuery("UPDATE `#@__arctiny` SET arcrank=0 WHERE id='{$row->id}'");
            $dsql->ExecuteNoneQuery("UPDATE `#@__archives` SET arcrank=0 WHERE id='{$row->id}'");
        }
    }
    ShowMsg("完成数据库的审核处理，准备更新HTML...",$jumpurl);
    exit();
}
//批量删除
else if($action=='del')
{
    if(empty($startid) || empty($endid) || $endid < $startid)
    {
        ShowMsg('该操作必须指定起始ID！','javascript:;');
        exit();
    }
    $dsql->SetQuery("SELECT id FROM `#@__archives` $gwhere");
    $dsql->Execute('x');
    $tdd = 0;
    while($row = $dsql->GetObject('x'))
    {
        if(DelArc($row->id)) $tdd++;
    }
    ShowMsg("成功删除 $tdd 条记录！","javascript:;");
    exit();
}
//删除空标题文档
else if($action=='delnulltitle')
{
    $dsql->SetQuery("SELECT id FROM `#@__archives` WHERE trim(title)='' ");
    $dsql->Execute('x');
    $tdd = 0;
    while($row = $dsql->GetObject('x'))
    {
        if(DelArc($row->id)) $tdd++;
    }
    ShowMsg("成功删除 $tdd 条记录！","javascript:;");
    exit();
}
//删除空内容文章
else if($action=='delnullbody')
{
    $dsql->SetQuery("SELECT aid FROM `#@__addonarticle` WHERE LENGTH(body) < 10 ");
    $dsql->Execute('x');
    $tdd = 0;
    while($row = $dsql->GetObject('x'))
    {
        if(DelArc($row->aid)) $tdd++;
    }
    ShowMsg("成功删除 $tdd 条记录！","javascript:;");
    exit();
}
//修正缩略图错误
else if($action=='modddpic')
{
    $dsql->ExecuteNoneQuery("UPDATE `#@__archives` SET litpic='' WHERE trim(litpic)='litpic' ");
    ShowMsg("成功修正缩略图错误！","javascript:;");
    exit();
}
//批量移动
else if($action=='move')
{
    if(empty($typeid))
    {
        ShowMsg('该操作必须指定栏目！','javascript:;');
        exit();
    }
    $typeold = $dsql->GetOne("SELECT * FROM #@__arctype WHERE id='$typeid'; ");
    $typenew = $dsql->GetOne("SELECT * FROM #@__arctype WHERE id='$newtypeid'; ");
    if(!is_array($typenew))
    {
        ShowMsg("无法检测移动到的新栏目的信息，不能完成操作！", "javascript:;");
        exit();
    }
    if($typenew['ispart']!=0)
    {
        ShowMsg("你不能把数据移动到非最终列表的栏目！", "javascript:;");
        exit();
    }
    if($typenew['channeltype']!=$typeold['channeltype'])
    {
        ShowMsg("不能把数据移动到内容类型不同的栏目！","javascript:;");
        exit();
    }
    $gwhere .= " And channel='".$typenew['channeltype']."' And title like '%$keyword%'";

    $ch = $dsql->GetOne("SELECT addtable FROM `#@__channeltype` WHERE id={$typenew['channeltype']} ");
    $addtable = $ch['addtable'];

    $dsql->SetQuery("SELECT id FROM `#@__archives` $gwhere");
    $dsql->Execute('m');
    $tdd = 0;
    while($row = $dsql->GetObject('m'))
    {
        $rs = $dsql->ExecuteNoneQuery("UPDATE `#@__arctiny` SET typeid='$newtypeid' WHERE id='{$row->id}'");
        $rs = $dsql->ExecuteNoneQuery("UPDATE `#@__archives` SET typeid='$newtypeid' WHERE id='{$row->id}'");
        if($addtable!='')
        {
            $dsql->ExecuteNoneQuery("UPDATE `$addtable` SET typeid='$newtypeid' WHERE aid='{$row->id}' ");
        }
        if($rs) $tdd++;
        //DelArc($row->id,true); //2011.07.06根据论坛反馈，修正使用批量文档维护后文档被移动到回收站(by:织梦的鱼)
    }

    if($tdd>0)
    {
        $jumpurl  = "makehtml_archives_action.php?endid=$endid&startid=$startid";
        $jumpurl .= "&typeid=$newtypeid&pagesize=20&seltime=$seltime";
        $jumpurl .= "&stime=".urlencode($starttime)."&etime=".urlencode($endtime);
        ShowMsg("成功移动 $tdd 条记录，准备重新生成HTML...",$jumpurl);
    }
    else
    {
        ShowMsg("完成操作，没移动任何数据...","javascript:;");
    }
}
//删除空标题内容
else if($action=='delnulltitle')
{
    $dsql->SetQuery("SELECT id FROM #@__archives WHERE trim(title)='' ");
    $dsql->Execute('x');
    $tdd = 0;
    while($row = $dsql->GetObject('x'))
    {
        if(DelArc($row->id)) $tdd++;
    }
    ShowMsg("成功删除 $tdd 条记录！","javascript:;");
    exit();
}
//修正缩略图错误
else if($action=='modddpic')
{
    $dsql->ExecuteNoneQuery("UPDATE #@__archives SET litpic='' WHERE trim(litpic)='litpic' ");
    ShowMsg("成功修正缩略图错误！","javascript:;");
    exit();
}

else if($action=='copy')
{
  if(empty($typeid))
  {
         ShowMsg('该操作必须指定栏目！','javascript:;');    
         exit();
    }
  $typeold = $dsql->GetOne("Select * From `#@__arctype` where id='$typeid'; ");
  $typenew = $dsql->GetOne("Select * From `#@__arctype` where id='$newtypeid'; ");
  if(!is_array($typenew))
  {
      $dsql->Close();
    ShowMsg("无法检测复制到的新栏目的信息，不能完成操作！","javascript:;");
      exit();
  }
  if($typenew['ispart']!=0)
  {
      $dsql->Close();
    ShowMsg("你不能把数据复制到非最终列表的栏目！","javascript:;");
      exit();
  }
  if($typenew['channeltype']!=$typeold['channeltype'])
  {
      $dsql->Close();
    ShowMsg("不能把数据复制到内容类型不同的栏目！","javascript:;");
      exit();
  }
    $gwhere .= " And channel='".$typenew['channeltype']."' And title like '%$keyword%'";

    $ch = $dsql->GetOne("Select addtable From `#@__channeltype` where id={$typenew['channeltype']} ");
    $addtable = $ch['addtable'];

    $dsql->SetQuery("Select * From `#@__archives` where typeid='$typeid'");
    $dsql->Execute('c');
    $tdd = 0;
    while($row = $dsql->GetObject('c'))
    {
        $senddate = time();
        $sortrank = AddDay($senddate,0);//第二个参数是排序值，参考article_add.php
      $ID = $row->id;

        $typeid = $newtypeid;//$newtypeid
        $sortrank = $row->sortrank;
        $flag = $row->flag;
        $ismake = $row->ismake;
        $channelid = $row->channel;
        $arcrank = $row->arcrank;
        $click = $row->click;
        $money = $row->money;
        $title = addslashes($row->title);//需要添加addslashes()转换； adan;090508
        $shorttitle = $row->shorttitle;
        $color = $row->color;
        $writer = $row->writer;
        $source = $row->source;
        $litpic = $row->litpic;
        $pubdate = $row->pubdate;
        $adminid = $cuserLogin->getUserID();
        $notpost = $row->notpost;
        $description = addslashes($row->description);//需要添加addslashes()转换； adan;090508
        $keywords = $row->keywords;

      require_once(DEDEADMIN."/inc/inc_archives_functions.php");
      //生成文档ID
      $arcID = GetIndexKey($arcrank,$typeid,$sortrank,$channelid,$senddate,$adminid);

      if(empty($arcID))
      {
          ShowMsg("无法获得主键，因此无法进行后续操作！","-1");
          exit();
      }
        //加入数据表dede_archives的SQL语句
        //----------------------------------
        $inQuery = "INSERT INTO `#@__archives`(id,typeid,typeid2,sortrank,flag,ismake,channel,arcrank,click,money,title,shorttitle,
        color,writer,source,litpic,pubdate,senddate,mid,lastpost,scores,goodpost,badpost,notpost,description,keywords,filename) 
        VALUES ('$arcID','$typeid','','$sortrank','$flag','$ismake','$channelid','$arcrank','0','$money',
        '$title','$shorttitle','$color','$writer','$source','$litpic','$pubdate','$senddate',
        '$adminid','0','0','0','0','0','$description','$keywords','');";

      if(!$dsql->ExecuteNoneQuery($inQuery))
      {
          $gerr = $dsql->GetError();
          $dsql->ExecuteNoneQuery("Delete From `dede_arctiny` where id='$arcID'");
          ShowMsg("把数据保存到数据库主表 `dede_archives` 时出错，请把相关信息提交给DedeCms官方。".str_replace('"','',$gerr),"javascript:;");
          exit();
      }
  
      //保存到附加表
      $cts = $dsql->GetOne("Select addtable From `#@__channeltype` where id='$channelid' ");
      $addtable = trim($cts['addtable']);
      if(empty($addtable))
      {
          $dsql->ExecuteNoneQuery("Delete From `#@__archives` where id='$arcID'");
          $dsql->ExecuteNoneQuery("Delete From `#@__arctiny` where id='$arcID'");
          ShowMsg("没找到当前模型[{$channelid}]的主表信息，无法完成操作！。","javascript:;");
          exit();
      }
      $useip = GetIP();
        $redirecturl = $addRow['redirecturl'];
        $templet = $addRow['templet'];
        $addRow = $dsql->GetOne("Select * from `{$addtable}` where aid='$ID'");
        $body = addslashes($addRow["body"]);

      $query = "INSERT INTO `{$addtable}`(aid,typeid,redirecturl,templet,userip,body) Values('$arcID','$typeid','$redirecturl','$templet','$useip','$body')";
      if(!$dsql->ExecuteNoneQuery($query))
      {
          $gerr = $dsql->GetError();
          $dsql->ExecuteNoneQuery("Delete From `#@__archives` where id='$arcID'");
          $dsql->ExecuteNoneQuery("Delete From `#@__arctiny` where id='$arcID'");
          ShowMsg("把数据保存到数据库附加表 `{$addtable}` 时出错，请把相关信息提交给DedeCms官方。".str_replace('"','',$gerr),"javascript:;");
          exit();
      }
      else $tdd++;
    }
  if($tdd>0)
  {
      $jumpurl  = "makehtml_archives_action.php?endid=$endid&startid=$startid";
    $jumpurl .= "&typeid=$newtypeid&pagesize=20&seltime=$seltime";
    $jumpurl .= "&stime=".urlencode($starttime)."&etime=".urlencode($endtime);
      ShowMsg("成功复制了 $tdd 条记录，准备重新生成HTML...",$jumpurl);
  }
  else ShowMsg("完成操作，没复制任何数据...","javascript:;");
  exit();
}