
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head><title>
	一卡通自助查询系统--用户首页
</title>
    <script src="../JS/jquery-1.7.2.min.js" type="text/javascript"></script>
    <script src="../JS/keyboard/bigkeyboard.js" type="text/javascript"></script>
    <script src="../JS/artDialog/artDialog.js" type="text/javascript"></script>
    <link href="../JS/artDialog/skins/blue.css" rel="stylesheet" type="text/css" />
    <script src="../JS/artDialog/plugins/iframeTools.js" type="text/javascript"></script>
    <script src="../JS/UniversalSS_Common.js" type="text/javascript"></script>
    <script src="../JS/DisableContextMenu.js" type="text/javascript"></script>
    <script type="text/javascript">
        var SetSoftKey = function (idList) {
            if (isUseSoftKeyBoard) {
                $(idList).bind('mousedown', function () {
                    password1 = this; showkeyboard(); this.readOnly = 1; Calc.password.value = '';
                }).bind('keydown', function () {
                    Calc.password.value = this.value;
                });
            }
        }
        if (document.layers) {
            document.captureEvents(Event.MOUSEDOWN);
            document.onmousedown = clickNS4;
            document.onkeydown = OnDeny();
        } else if (document.all && !document.getElementById) {
            document.onmousedown = clickIE4;
            document.onkeydown = OnDeny();
        }

        document.oncontextmenu = new Function("return false");

        $(function () {
           CreateEmptyTable();
        });

        var CreateEmptyTable = function () {
            var GVEmptyPanel = $('.gvNoRecords');
            if (GVEmptyPanel.length == 1)
            {
                var oriTip = GVEmptyPanel.html();
                var colCount = GVEmptyPanel.parent().attr('colspan');
                var tbodyPanle = GVEmptyPanel.parent().parent().parent();
                GVEmptyPanel.parent().parent().remove();
                var emptyTableHtml = "";
                for(i=0;i<10;i++)
                {
                    emptyTableHtml += "<tr class='" + (i % 2 == 0 ? "RowStyle" : "AltRowStyle") + "'><td colspan='" + colCount + "'></td><tr>";
                }
                tbodyPanle.append(emptyTableHtml);
            }
        }
    </script>
    

    <script language="JavaScript" src="../js/tab.js"></script>

    <style type="text/css">
        body
        {
            background: #FCFDFE;
        }    
        .main_often
        {
            margin: 10px auto;
            text-align: center;
            width: 960px;
            min-width: 960px;
        }
        .main_often ul li
        {
            width: 160px;
            height: 138px;
            margin: 11px;
            border: 1px solid #E3E3E3;
            float: left;
            background: #fff;
        }
        .main_often ul li:hover
        {
            border: 2px solid #F62B0B;
            color: F62B0B;
            margin: 10px;
            background: #FAFBFC;
        }
        .main_often ul li a
        {
            display: block;
        }
        a.img_a
        {
            border: 1px solid #EAEAEA;
            background: #FAFBFC;
            margin: 5px;
            height: 100px;
        }
        .person_news
        {
            position: absolute;
            margin: 20px;
        }
        .person_news h1
        {
            font-size: 14px;
            color: #004080;
            border-bottom: 1px solid #D0DEE9;
            margin-bottom: 16px;
        }
        .person_news img
        {
            float: left;
            padding: 1px;
            border: 1px solid #ccc;
        }
        .person_news ul
        {
            float: right;
            font-size: 12px;
            color: #666;
            margin-left: 20px;
        }
        .person_news ul li
        {
            height: 28px;
            line-height: 28px;
        }
        .person_news ul li span
        {
            width: 220px;
            display: inline-table;
        }
        .main_con
        {
            min-width: 800px;
            position: relative;
        }
        .notices
        {
            position: absolute;
            border: 1px solid #D3BE69;
            background: #FAF3D7;
            width: 260px;
            margin: 20px;
            left: 600px;
            padding-bottom: 10px;
        }
        .notices h1
        {
            font-size: 14px;
            color: #9F8001;
            height: 30px;
            line-height: 30px;
            border-bottom: 1px solid #DBCD91;
            text-indent: 11px;
            position: relative;
            margin: 0 2px;
            margin-bottom: 10px;
        }
        .notices h1 a
        {
            font-size: 12px;
            text-decoration: none;
            font-family: Verdana, Arial, Helvetica, sans-serif;
            font-weight: normal;
            color: #666;
            position: absolute;
            right: 5px;
        }
        .notices h1 a:hover
        {
            color: #333;
        }
        .notices ul li
        {
            height: 25px;
            line-height: 25px;
            background: url(../images/dot.png) no-repeat 5px center;
            overflow:hidden;
        }
        .notices ul li a
        {
            font-size: 12px;
            text-indent: 12px;
            overflow: hidden;
            color: #6F6C5F;
            display: block;
            min-width: 250px;
        }
        .notices ul li a:hover
        {
            color: #333;
        }
        .ltext
        {
            min-width: 900px;
        }
    </style>
<link href="../App_Themes/Default/Css/Default.css" type="text/css" rel="stylesheet" /><link href="../App_Themes/Default/Css/left.css" type="text/css" rel="stylesheet" /><link href="../App_Themes/Default/Css/style.css" type="text/css" rel="stylesheet" /><link href="../App_Themes/Default/Css/tab.css" type="text/css" rel="stylesheet" /><link href="../App_Themes/Default/Css/User.css" type="text/css" rel="stylesheet" /></head>
<body>
    <form method="post" action="Home.aspx" id="form1">
<div class="aspNetHidden">
<input type="hidden" name="__VIEWSTATE" id="__VIEWSTATE" value="/wEPDwUKMTY5ODQ2Nzk3NmRkn8fXrmFfGzxGO2D7HBKuYGjH2KfAyn6EbyDPQhvVkzc=" />
</div>

    <div>
        <div>
            
<div>

<script type="text/javascript">
    var naviBar=" ";
    if(window.top) 
    {
        var naviBarElement= window.top.$('#NaviBar');
        if(naviBarElement&&naviBar!='')
            naviBarElement.html(naviBar);
    }
</script>
</div>
        </div>
        <div class="clear">
        </div>
        
    <table border="0" cellspacing="0" cellpadding="0" width="100%" height="100%" class="main_con">
        <tr>
            <td height="420" align="left" valign="top">
                <div class="person_news">
                    <h1>
                        个人基本信息</h1>
                    <img style=" width:100px; height:133px;" src='Photo.ashx' border="0"  onerror="this.src='../images/defaultHeader.jpg'"  />
                   <ul><li><span>学(工)号：20144633</span><span>卡状态：正常卡</span></li><li><span>姓名：江航</span><span>主钱包余额：121.97元</span></li><li><span>性别：男</span><span>补助余额：0元</span></li><li><span>身份：本科</span></li><li>部门：软件学院/软件工程/软件1403</li></ul>
                </div>
                <div class="notices">
                <h1>通知公告<a href="NewsList.aspx?nc=6">更多&gt;&gt;</a></h1><ul><li><a href="NewsContent.aspx?newscode=15">关于圈存多领款项执行代扣的公告</a></li><li><a href="NewsContent.aspx?newscode=1">关于校园卡系统试运行的通知</a></li><li><a href="NewsContent.aspx?newscode=14">查询密码修改须知</a></li></ul>
                    
                    
                </div>
                <div id="Tab2">
                    <div class="Menubox">
                        
                        <li id="two1" onmouseover="setTab('two',1,3)" class="hover"><a href="NewsList.aspx?nc=9">业务介绍</a></li><li id="two2" onmouseover="setTab('two',2,3)" ><a href="NewsList.aspx?nc=8">使用指南</a></li><li id="two3" onmouseover="setTab('two',3,3)" ><a href="NewsList.aspx?nc=7">常见问题</a></li>
                    </div>
                    <div class="Contentbox">
                    <div id="con_two_1" ><ul><li><span>2014/7/17 13:59:47</span><a href="NewsContent.aspx?newscode=12">POS机刷卡扣费流程</a></li><li><span>2014/7/17 13:58:46</span><a href="NewsContent.aspx?newscode=13">浴室水控器扣费流程</a></li></ul></div><div id="con_two_2" style="display: none"><ul><li><span>2014/8/19 18:25:31</span><a href="NewsContent.aspx?newscode=9">如何修改消费密码?</a></li><li><span>2014/7/15 15:49:52</span><a href="NewsContent.aspx?newscode=8">如何修改消费限额?</a></li><li><span>2014/7/13 15:53:36</span><a href="NewsContent.aspx?newscode=11">校园一卡通使用指南（20140713版）</a></li><li><span>2014/7/13 15:24:12</span><a href="NewsContent.aspx?newscode=10">如何修改查询密码?</a></li></ul></div><div id="con_two_3" style="display: none"><ul><li><span>2014/7/17 14:01:35</span><a href="NewsContent.aspx?newscode=6">什么是校园卡消费密码?</a></li><li><span>2014/7/17 14:00:59</span><a href="NewsContent.aspx?newscode=5">什么是校园卡查询密码?</a></li><li><span>2014/7/13 13:57:45</span><a href="NewsContent.aspx?newscode=7">什么是消费限额？</a></li></ul></div>
                        
                       
                    </div>
                </div>
            </td>
        </tr>
    </table>

    </div>
    </form>
</body>
</html>
