<?php
$z[0]=Array("field","",387,410);
$z[0][4]['name']="seotitle";
$z[1]=Array("field","",489,512);
$z[1][4]['name']="keywords";
$z[2]=Array("field","",620,646);
$z[2][4]['name']="description";
$z[3]=Array("type","[field:siteurl /]",810,850);
$z[4]=Array("field","",1028,1082);
$z[4][4]['name']="typeid";
$z[4][4]['function']="GetParentTypename(@me)";
$z[5]=Array("include","",1086,1142);
$z[5][4]['filename']="ledou/commom_paihangbang.html";
$z[6]=Array("include","",1198,1246);
$z[6][4]['filename']="ledou/commom_top.html";
$z[7]=Array("include","",1320,1370);
$z[7][4]['filename']="ledou/commom_video.html";
$z[8]=Array("field","",1464,1494);
$z[8][4]['name']="typename";
$z[9]=Array("field","",1548,1578);
$z[9][4]['name']="position";
$z[10]=Array("field","\r\n                        \$a = getTypeConfig(@me,'搜索标签第1行');\r\n                        \$ss = '';\r\n                        \$a = str_replace('，',',',\$a);\r\n                        \$aa = explode(',',\$a);\r\n                        foreach(\$aa as \$k=>\$v)\r\n                        {\r\n                            \$ss .='<a href=\"javascript:void(0)\">'.\$v.'</a>';\r\n                        }\r\n                        @me = \$ss;\r\n                        ",1768,2274);
$z[10][4]['name']="typeid";
$z[10][4]['runphp']="yes";
$z[11]=Array("field","\r\n                        \$a = getTypeConfig(@me,'搜索标签第2行');\r\n                        \$ss = '';\r\n                        \$a = str_replace('，',',',\$a);\r\n                        \$aa = explode(',',\$a);\r\n                        foreach(\$aa as \$k=>\$v)\r\n                        {\r\n                        \$ss .='<a href=\"javascript:void(0)\">'.\$v.'</a>';\r\n                        }\r\n                        @me = \$ss;\r\n                        ",2451,2953);
$z[11][4]['name']="typeid";
$z[11][4]['runphp']="yes";
$z[12]=Array("field","\r\n                        \$a = getTypeConfig(@me,'搜索标签第3行');\r\n                        \$ss = '';\r\n                        \$a = str_replace('，',',',\$a);\r\n                        \$aa = explode(',',\$a);\r\n                        foreach(\$aa as \$k=>\$v)\r\n                        {\r\n                        \$ss .='<a href=\"javascript:void(0)\">'.\$v.'</a>';\r\n                        }\r\n                        @me = \$ss;\r\n                        ",3128,3630);
$z[12][4]['name']="typeid";
$z[12][4]['runphp']="yes";
$z[13]=Array("list","\r\n                <li>\r\n                    <a href=\"[field:arcurl /]\" target=\"_blank\" class=\"pica\">\r\n                        <i></i>\r\n                        <code></code>\r\n                        <img width=\"242\" height=\"148\" src=\"[field:litpic /]\" />\r\n                    </a>\r\n                    <p><a href=\"[field:arcurl /]\" target=\"_blank\">[field:title /]</a></p>\r\n                </li>\r\n                ",3760,4241);
$z[13][4]['orderby']="pubdate";
$z[13][4]['pagesize']="9";
$z[13][4]['orderway']="desc";
$z[14]=Array("pagelist","",4731,4797);
$z[14][4]['listsize']="5";
$z[14][4]['listitem']="index,pre,pageno,next,end";
$z[15]=Array("include","",5298,5350);
$z[15][4]['filename']="ledou/commom_erweima.html";
$z[16]=Array("include","",5364,5417);
$z[16][4]['filename']="ledou/commom_rightarc.html";
$z[17]=Array("include","",5478,5529);
$z[17][4]['filename']="ledou/commom_footer.html";
$z[18]=Array("include","",5546,5599);
$z[18][4]['filename']="ledou/commom_rightNav.html";
$z[19]=Array("type","[field:siteurl /]",5640,5680);

?>