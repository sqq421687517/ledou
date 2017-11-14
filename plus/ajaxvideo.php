<?php
/**
 *
 * ajax
 *
 * @version        $Id: feedback.php 2 15:56 2012年10月30日Z tianya $
 * @package        DedeCMS.Site
 * @copyright      Copyright (c) 2007 - 2010, DesDev, Inc.
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
require_once(dirname(__FILE__)."/../include/common.inc.php");
//获得当前网址
function GetCurUrl()
{
    if(!empty($_SERVER["REQUEST_URI"]))
    {
        $nowurl = $_SERVER["REQUEST_URI"];
        $nowurls = explode("?",$nowurl);
        $nowurl = $nowurls[0];
    }
    else
    {
        $nowurl = $_SERVER["PHP_SELF"];
    }
    return $nowurl;
}


$lang_pre_page = '上页';
$lang_next_page = '下页';
$lang_index_page = '首页';
$lang_end_page = '末页';
$lang_record_number = '条记录';
$lang_page = '页';
$lang_total = '共';

$tid1 = !empty($_REQUEST['tid1']) ? $_REQUEST['tid1'] : '';
$tid2 = !empty($_REQUEST['tid2']) ? $_REQUEST['tid2'] : '';
$tid3 = !empty($_REQUEST['tid3']) ? $_REQUEST['tid3'] : '';
$page = !empty($_REQUEST['page']) ? $_REQUEST['page'] : 1;
$typeids = '14,15,16,17';
$cacheTime = 600;//缓存十分钟
$pageSize = 9;

$searchKey = md5('video'.$tid1.$tid2.$tid3.$page);
$data = file_get_contents('cache/'.$searchKey);
if($data && time() - filemtime('cache/'.$searchKey) > $cacheTime)
{
    $return = unserialize($data);
}
else
{
    $getValues = [
        'tid1' => $tid1,
        'tid2' => $tid2,
        'tid3' => $tid3,
        'page' => $page,
    ];

    $pageStart = ($page-1)*$pageSize;
    $tids = "'{$tid1}','{$tid2}','{$tid3}'";
    $tidSql = "";
    if(!empty($tid1) && empty($tid2) && empty($tid3))
    {
        $tidSql .= "AND keywords LIKE '{$tid1}%'";
    }
    else if(empty($tid1) && !empty($tid2) && empty($tid3))
    {
        $tidSql .= "AND keywords LIKE '%{$tid2}%'";
    }
    else if(empty($tid1) && empty($tid2) && !empty($tid3))
    {
        $tidSql .= "AND keywords LIKE '%{$tid3}'";
    }
    else if(!empty($tid1) && !empty($tid2) && empty($tid3))
    {
        $tidSql .= "AND keywords LIKE '{$tid1},{$tid2}%'";
    }
    else if(!empty($tid1) && empty($tid2) && !empty($tid3))
    {
        $tidSql .= "AND keywords LIKE '{$tid1}%{$tid3}'";
    }
    else if(empty($tid1) && !empty($tid2) && !empty($tid3))
    {
        $tidSql .= "AND keywords LIKE '%{$tid2},{$tid3}'";
    }
    else if(!empty($tid1) && !empty($tid2) && !empty($tid3))
    {
        $tidSql .= "AND keywords = '{$tid1},{$tid2},{$tid3}'";
    }

    $countQuery = "SELECT count(*) as dd FROM  #@__archives where typeid in({$typeids}) {$tidSql}";
    $row = $dsql->GetOne($countQuery);

    $totalResult = $row['dd'];
    $return = ['content'=>''];
    if($totalResult > 0)
    {
        $dataList = [];
        $query = "SELECT id,title,shorttitle,litpic FROM #@__archives where typeid in({$typeids}) {$tidSql} LIMIT {$pageStart},{$pageSize}";

        $dsql->SetQuery($query);
        $dsql->Execute();
        while ($row = $dsql->GetArray())
        {
            $arcInfo = GetOneArchive($row['id']);
            $row['url'] = $arcInfo['arcurl'];
            $row['litpic'] = 'http://ledou.com'.$row['litpic'];
            $dataList[] = $row;
        }

        //分页处理
        $prepage = $nextpage = $geturl= $hidenform = '';
        $purl = GetCurUrl();
        $prepagenum = $page-1;
        $nextpagenum = $page+1;
        $listsize = 5;


        $totalpage = ceil($totalResult/$pageSize);

        //无结果或只有一页的情况
        if($totalpage<=1 && $totalResult > 0)
        {
            //$plist = "<a class='pages_total'>{$lang_total} 1 {$lang_page}/".$totalResult.$lang_record_number."</a>";
        }
        elseif ($totalResult == 0)
        {
            //$plist = "<a class='pages_total'>{$lang_total} 0 {$lang_page}/".$totalResult.$lang_record_number."</a>";
        }
        else
        {
            //$infos = "<a class='pages_total'>{$lang_total} {$totalpage} {$lang_page}/{$totalResult}{$lang_record_number} </a>";
            $infos = "";
            if($totalResult!=0)
            {
                $getValues['totalresult'] = $totalResult;
            }

            $purl .= "?".$geturl;

            //获得上一页和下一页的链接
            if($page != 1)
            {
                $prepage .= "<a class='pages_gray prePage vlink' style='cursor:pointer' hrefs='".$purl."pageno=$prepagenum'>$lang_pre_page</a> \n";
                $indexpage = "<a class='pages_gray vlink' style='cursor:pointer' hrefs='".$purl."pageno=1'>$lang_index_page</a> \n";
            }
            else
            {
                $indexpage = "<a class='pages_gray'>"."$lang_index_page \n"."</a>";
            }
            if($page != $totalpage && $totalpage > 1)
            {
                $nextpage.="<a class='pages_gray nextPage vlink' style='cursor:pointer' hrefs='".$purl."pageno=$nextpagenum'>$lang_next_page</a> \n";
                $endpage="<a class='pages_gray endPage vlink' style='cursor:pointer' hrefs='".$purl."pageno=$totalpage'>$lang_end_page</a> \n";
            }
            else
            {
                $endpage=" <a class='pages_gray current'>$lang_end_page</a> \n";
            }

            //获得数字链接
            $listdd = "";
            $total_list = $listsize * 2 + 1;
            if($page >= $total_list)
            {
                $j = $page - $listsize;
                $total_list=$page + $listsize;
                if($total_list > $totalpage)
                {
                    $total_list = $totalpage;
                }
            }
            else
            {
                $j=1;
                if($total_list > $totalpage)
                {
                    $total_list = $totalpage;
                }
            }
            for($j; $j<=$total_list; $j++)
            {
                $listdd .= $j==$page ? "<a class='pages_gray current'>$j</a>\n" : "<a class='pages_gray vlink' style='cursor:pointer' hrefs='".$purl."pageno=$j'>".$j."</a>\n";
            }

            $plist = $infos;
            $plist .= $indexpage;
            $plist .= $prepage;
            $plist .= $listdd;
            $plist .= $nextpage;
            $plist .= $endpage;
        }

        $return = [
            'pages' => $plist,
            'content' => $dataList,
            'total' => $totalResult,
        ];
    }
    file_put_contents('cache/'.$searchKey,serialize($return));
}

$datas = json_encode($return);
die($callback.'('.$datas.')');