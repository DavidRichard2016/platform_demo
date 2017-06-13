$(function() {
    //菜单树处理
    $("#main_menu").tree({
        url:'Public/Json/menu.json',
        lines : true,
        onContextMenu: function (e, title) {
            e.preventDefault();              
            $("#tabsMenu").menu('show', {
                left: e.pageX,
                top: e.pageY
            }).data("tabTitle", title.text);
        },
        onClick : function (node) {
            $(this).tree('toggle', node.target);
            if (node.attributes) {
                Open(node.text, node.attributes.url);
            }
        }
    });

    //绑定tabs的右键菜单
    $("#tabs").tabs({
        onContextMenu : function (e, title) {
            e.preventDefault();
            $('#tabsMenu').menu('show', {
                left : e.pageX,
                top : e.pageY
            }).data("tabTitle", title);
        }
    });
  
    //实例化menu的onClick事件
    $("#tabsMenu").menu({
        onClick : function (item) {
            CloseTab(this, item.name);
        }
    });

});   
    
//在右边center区域打开菜单，新增tab
function Open(text, url) {
    if(text == "退出系统"){
        window.location.href = 'index.php' + url;
    }else{
        var $tabs = $('#tabs').tabs();
        //获得选中的tab
        var selectedTab = $('#tabs').tabs('getSelected');
        //获得原有属性
        var titleInfo = selectedTab.panel('options').title;
        if(titleInfo != '首页')
            $('#tabs').tabs('close',titleInfo);

        $('#tabs').tabs('add', {
            title : text,
            closable : true,
            href: 'index.php' + url
        });
    }
}
    
//几个关闭事件的实现
function CloseTab(menu, type) {
    var curTabTitle = $(menu).data("tabTitle");
    var tabs = $("#tabs");
        
    if (type === "close") {
        tabs.tabs("close", curTabTitle);
        return;
    }
        
    var allTabs = tabs.tabs("tabs");
    var closeTabsTitle = [];
        
    $.each(allTabs, function () {
        var opt = $(this).panel("options");
        if (opt.closable && opt.title != curTabTitle && type === "Other") {
            closeTabsTitle.push(opt.title);
        } else if (opt.closable && type === "All") {
            closeTabsTitle.push(opt.title);
        }
    });
        
    for (var i = 0; i < closeTabsTitle.length; i++) {
        tabs.tabs("close", closeTabsTitle[i]);
    }
}   

//某个日期属于一年中第几周
function getYearWeek (str_date) {
    var a = str_date.split("-")[0];
    var b = str_date.split("-")[1];
    var c = str_date.split("-")[2];

    var d1 = new Date(a, b-1, c), d2 = new Date(a, 0, 1), 
    d = Math.round((d1 - d2) / 86400000);
    alert(Math.ceil((d + ((d2.getDay() + 1) - 1)) / 7));
    return Math.ceil((d + ((d2.getDay() + 1) - 1)) / 7); 
}

//查询时，序列化查询条件
function toJson(obj) {
    var arrayValue = $(obj).serializeArray();
    var json = {};
    $.each(arrayValue, function() {
        var item = this;
        if (json[item["name"]]) {
            json[item["name"]] += "," + item["value"];
        } else {
            json[item["name"]] = item["value"];
        }
    });
    return json;
}