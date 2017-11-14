window._bd_share_config={"common":{"bdSnsKey":{},"bdText":"","bdMini":"2","bdMiniList":false,"bdPic":"","bdStyle":"0","bdSize":"32"},"share":{}};with(document)0[(getElementsByTagName('head')[0]||body).appendChild(createElement('script')).src='http://bdimg.share.baidu.com/static/api/js/share.js?v=89860593.js?cdnversion='+~(-new Date()/36e5)];

$(function(){
	var hdI = 1;
	var hdLen = $(".hdbox ul li").length;
	$("#prev").click(function(){
		if(hdI>2){
			$(".hdbox ul li").hide();
			hdI-=2;
			$(".hdbox ul li").eq(hdI).show();
			$(".hdbox ul li").eq(hdI-1).show();
		}
	});
	$("#next").click(function(){
		if(hdI<hdLen-1){
			$(".hdbox ul li").hide();
			hdI+=2;
			$(".hdbox ul li").eq(hdI-1).show();
			$(".hdbox ul li").eq(hdI).show();
		}
	});
	
	
	$("#rank_control1 li.rank-item").hover(function(){
		$(this).addClass("current").siblings().removeClass("current");	
	});
	$("#rank_control2 li.rank-item").hover(function(){
		$(this).addClass("current").siblings().removeClass("current");	
	});	
	$(".play_rank").hover(function(){
		$(".ost_rank").toggle();	
	});

	$(window).scroll(function(){
		var S = $(window).scrollTop();
		if( S > 100){
			$(".fixedTop").addClass("fixed");	
		}else{
			$(".fixedTop").removeClass("fixed");	
		}
	});

		//幻灯
		var sWidth = $(".flash").width();
		var len = $(".flash ul li").length;
		var index = 0;
		var picTimer;

		$(".flash dd").mouseenter(function () {
			index = $(".flash dd").index(this);
			showPics(index);
		}).eq(0).trigger("mouseenter");

		$(".flash ul").css("width", sWidth * (len));

		$(".flash").hover(function () {
			clearInterval(picTimer);
		}, function () {
			picTimer = setInterval(function () {
					showPics(index);
					index++;
					if (index == len) {
						index = 0;
					}
				}, 4000);
		}).trigger("mouseleave");

		function showPics(index) {
			var nowLeft = -index * sWidth;
			$(".flash ul").stop(true, false).animate({
				"left": nowLeft
			}, 300);
			$(".flash dd").removeClass("cur").eq(index).addClass("cur");
		}
		//头条新闻
	function qiehuan(qhan,qhshow,qhon){
		$(qhan).hover(function(){
		  $(qhan).removeClass(qhon);
		  $(this).addClass(qhon);
		  var i = $(this).index(qhan);
		  $(qhshow).eq(i).show().siblings(qhshow).hide();
		});
	   }
	   qiehuan(".textnav li",".textlink","cur");
	   qiehuan(".yxsp .vlist li",".yxsp .vlistcon","cur");
	   qiehuan(".xjtj .vlist li",".xjtj .vlistcon","cur");
	//五屏切换
	$(".play_ts").find("li").click(function(){
		if($(this).hasClass("cur")){return false;}
		var li = $(this);
		sibling = li.siblings();
		li.find("p").fadeOut(200,function(){
			li.animate({"width":"640px"},500,function(){
				li.addClass("cur");
			});
			sibling.animate({"width":"125px"},500,function(){
				sibling.removeClass("cur");
				sibling.find("p").fadeIn(200);
			});
		});
	})
	//角色切换
	$(".tablebtn div").hover(function(){
		var index = $(this).index();
		var index2 = $(this).parents(".role_con").index()-1;
		$(this).addClass("cur").siblings().removeClass("cur");
		$(".table_con").eq(index2).find("img").hide();
		//console.log(index,index2)
		$(".table_con").eq(index2).find("img").eq(index).show();
		return false;
	});

	function timer1(prev_index){
		$(".role_con").eq(prev_index).hide();
	}

	function timer2(now_index){
		$(".role_con").eq(now_index).show().addClass("in");
	}
	$(".qh_btn dd").click(function(){
		var prev_index = parseInt($(".role_con.in").index())-1;
		$(".qh_btn dd").removeClass("cur");
		$(this).addClass("cur");
		var j = $(this).index(".qh_btn dd");
		if($(".role_con").hasClass("in")){
			$(".role_con").eq(prev_index).removeClass("in").addClass("out");
			setTimeout(timer1,600,prev_index);
			setTimeout(timer2,600,j);
		}
	})
	//资料居中
	//$(".gl_list dd").height();

	$(".gl_list").each(function(index, element) {
		var height = $(this).find("dd").height()/2;
		$(".gl_list").eq(index).find("dd").css({"position":"relative","top":"50%","margin-top":-height});
	});

	//漂浮
	var tips; var theTop = 200; var old = theTop;
	
	function initFloatTips() {
	   tips = document.getElementById('floatnav');
	   moveTips();
	};
	
	function moveTips(){
		var tt=200;
		if (window.innerHeight) {
			pos = window.pageYOffset
		}else if (document.documentElement && document.documentElement.scrollTop){
			pos = document.documentElement.scrollTop
		}else if (document.body) {
			pos = document.body.scrollTop;
		}
		pos=pos-tips.offsetTop+theTop;
		pos=tips.offsetTop+pos/10;
		if (pos < theTop) pos = theTop;
		if (pos != old) {
			tips.style.top = pos+"px";
			tt=10;
		}
		old = pos;
		setTimeout(moveTips,tt);
	}
	initFloatTips();

    if($(".nextpage").length==0)
    {
        $(".morepic").remove();

	}

	var page = 1;
    $(".morepic").click(function ()
	{
    	if($(".nextpage").length==0)
		{
            $(".morepic a").html('没有更多图片了！');
            $(".morepic").unbind("click");

			return false;
		}
		var nextpage = location.href+'/'+$(".nextpage").attr("href");
    	$.getJSON('http://cmsledou.uu.cc/plus/ajaxarc.php?callback=?',{nextpage:nextpage},function (s) {
            if(s!='')
            {
                $(".pic_list_con").append(s['content']);
                $(".pagehtml").html(s['pages']);
            }
            else
			{
				$(".morepic a").html('没有更多图片了！');
                $(".morepic").unbind("click");
			}
        });
    });

    var tid1 = '';
    var tid2 = '';
    var tid3 = '';
    $(".tid1 a").click(function () {
		tid1 = $(this).html();
		loadVideo(1);
    });
    $(".tid2 a").click(function () {
        tid2 = $(this).html();
        loadVideo(1);
    });
    $(".tid3 a").click(function () {
        tid3 = $(this).html();
        loadVideo(1);
    });
    $(".vlink").live('click',function (e) {
        var page = $(this).attr('hrefs').split('pageno=')[1];
        loadVideo(page);
        return false;
    });

    $("#preArrow").hover(function() {
		$("#preArrow_A").css("display", "block");
		$(".pictxt").css("display", "block");
	},
	function() {
		$("#preArrow_A").css("display", "none");
		$(".pictxt").css("display", "block");
	});
    $("#nextArrow").hover(function() {
		$("#nextArrow_A").css("display", "block");
		$(".pictxt").css("display", "block");
	},
	function() {
		$("#nextArrow_A").css("display", "none");
		$(".pictxt").css("display", "block");
	});

    $(".showVideo").click(function () {
		var lockDiv = '<div id="lockDiv" style="width: 100%; height: 100%; position: fixed; z-index: 1992; top: 0px; left: 0px; overflow: hidden;"><div style="height: 100%; background: rgb(0, 0, 0); opacity: 0.7; z-index: 1993;"></div></div>';
		$("body").append(lockDiv);
		$(".videowrap").show();
    });

    $("#lockDiv").live('click',function () {
		$("#lockDiv").remove();
        $(".videowrap").hide();
    });
    
    function loadVideo(page)
	{
        $.getJSON('http://ledou.com/plus/ajaxvideo.php?callback=?',{tid1:tid1,tid2:tid2,tid3:tid3,page:page},function (s) {
            if(s['content']!='')
            {
            	var _html = '';
            	for(var i in s['content'])
				{
					_html += '<li> <a href="'+s['content'][i]['url']+'" target="_blank" class="pica"> <i></i> <code></code> <img width="242" height="148" src="'+s['content'][i]['litpic']+'"> </a> <p><a target="_blank" href="'+s['content'][i]['url']+'">'+s['content'][i]['title']+'</a></p> </li>';
				}
                $(".pic_list_con").html(_html);
                $(".videopages").html(s['pages']);

            }
            else
            {
            	if(page==1)
				{
                    $(".pic_list_con").html('');
                    $(".pages").html('');
				}
            }
        });
    }

    //var tid1 = [''];
	var tname = $("body").attr('tid');
    $(".nav li a:contains("+tname+")").addClass('navcur');

    /*$(".table_con").each(function (i) {
    	console.log(i);
		$(this).eq(i).find('img').eq(0).show();
    });*/
    $(".table_con img:even").show();
});