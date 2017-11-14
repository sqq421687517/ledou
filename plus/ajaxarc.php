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

$callback = $_GET['callback'];
$nextpage = $_REQUEST['nextpage'];
$ss = file_get_contents($nextpage);
preg_match_all("/<div class=\"pagehtml\" style=\"display:none\">(.*)</div>/isU",$ss,$pages);
preg_match_all("/<ul class=\"pic_list_con cc\">(.*)<\/ul>/isU",$ss,$content);
$return = ['pages'=>$pages[0][0],'content'=>$content[0][0]];
$datas = json_encode($return);
die($callback.'('.$datas.')');