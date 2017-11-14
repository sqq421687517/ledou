<?php
/**
 * 栏目编辑
 *
 * @version        $Id: catalog_edit.php 1 14:31 2010年7月12日Z tianya $
 * @package        DedeCMS.Administrator
 * @copyright      Copyright (c) 2007 - 2010, DesDev, Inc.
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
require_once(dirname(__FILE__)."/config.php");
require_once(DEDEINC."/typelink.class.php");
if(empty($dopost)) $dopost = '';
$id = isset($id) ? intval($id) : 0;

//检查权限许可
CheckPurview('t_Edit,t_AccEdit');

//检查栏目操作许可
CheckCatalog($id, '你无权更改本栏目！');

$dsql->SetQuery("SELECT max(`topid`) as maxid FROM `#@__arctype`");
$myrow = $dsql->GetOne();
$maxid = $myrow['maxid'];

$fields = 'reid,topid,sortrank,typename,typedir,isdefault,defaultname,issend,channeltype,
    tempindex,templist,temparticle,modname,namerule,namerule2,
    ispart,corank,description,keywords,seotitle,moresite,siteurl,sitepath,ishidden,`cross`,`crossid`,`content`,`smalltypes`,`myname`,`myflag`,`myvalue`';
$topRow = $dsql->GetOne("SELECT {$fields} FROM `#@__arctype` WHERE id='{$maxid}'");
$topRow['typename'] .= '复制栏目';
foreach ($topRow as $k=>$v)
{
    $topRow[$k] = "'".$topRow[$k]."'";
}

$topSql = implode(',',$topRow);
//顶级栏目插入
$in_query1 = "INSERT INTO `#@__arctype`(reid,topid,sortrank,typename,typedir,isdefault,defaultname,issend,channeltype,
    tempindex,templist,temparticle,modname,namerule,namerule2,
    ispart,corank,description,keywords,seotitle,moresite,siteurl,sitepath,ishidden,`cross`,`crossid`,`content`,`smalltypes`,`myname`,`myflag`,`myvalue`)
    VALUES({$topSql})";
$result1 = $dsql->ExecuteNoneQuery($in_query1);
if(!$result1)
{
    ShowMsg("顶级栏目复制失败","-1");
    exit();
}
//插入返回的ID，即topid
$lastID = $dsql->GetLastID();

$dsql->SetQuery("SELECT * FROM `#@__arctype` WHERE reid='{$maxid}' AND reid=topid");
$dsql->Execute('mainRow');
while($mainRow = $dsql->GetArray('mainRow'))
{
    $sonReid = $mainRow['id'];
    $mainRowReid = $mainRow[''];
    //二级栏目插入
    $mainRow['reid'] = $mainRow['topid'] = $lastID;

    unset($mainRow['id']);
    unset($mainRow['maxpage']);

    foreach ($mainRow as $k2=>$v2)
    {
        $mainRow[$k2] = "'" . $mainRow[$k2] . "'";
    }

    $mainSql = implode(',',$mainRow);
    $in_query2 = "INSERT INTO `#@__arctype`(reid,topid,sortrank,typename,typedir,isdefault,defaultname,issend,channeltype,
                tempindex,templist,temparticle,modname,namerule,namerule2,
                ispart,corank,description,keywords,seotitle,moresite,siteurl,sitepath,ishidden,`cross`,`crossid`,`content`,`smalltypes`,`myname`,`myflag`,`myvalue`)
                VALUES({$mainSql})";
    //插入二级栏目
    $result2 = $dsql->ExecuteNoneQuery($in_query2);
    if(!$result2)
    {
        ShowMsg("二级栏目复制失败","-1");
        exit();
    }
    //插入返回的ID，即二级栏目id
    $lastID2 = $dsql->GetLastID();

    //查询当前二级栏目下的子栏目
    $dsql->SetQuery("SELECT {$fields} FROM `#@__arctype` WHERE reid='{$sonReid}'");
    $dsql->Execute('sonRow');
    while($sonRow = $dsql->GetArray('sonRow'))
    {
        $sonRow['reid'] = $lastID2;
        $sonRow['topid'] = $lastID;
        foreach ($sonRow as $k3=>$v3)
        {
            $sonRow[$k3] = "'" . $sonRow[$k3] . "'";
        }
        $sonSql = implode(',', $sonRow);
        $in_query3 = "INSERT INTO `#@__arctype`(reid,topid,sortrank,typename,typedir,isdefault,defaultname,issend,channeltype,
            tempindex,templist,temparticle,modname,namerule,namerule2,
            ispart,corank,description,keywords,seotitle,moresite,siteurl,sitepath,ishidden,`cross`,`crossid`,`content`,`smalltypes`,`myname`,`myflag`,`myvalue`)
            VALUES({$sonSql})";
        //插入三级栏目
        $result3 = $dsql->ExecuteNoneQuery($in_query3);
        if(!$result3)
        {
            ShowMsg("三级栏目复制失败","-1");
            exit();
        }

    }

}

ShowMsg("栏目复制成功！","catalog_main.php");
exit();
