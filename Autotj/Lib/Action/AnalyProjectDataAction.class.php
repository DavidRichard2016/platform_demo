<?php


Class AnalyProjectDataAction extends BaseAction {

    //生成故障相关的报表
    public function showList()
    {
        $result = $this->dataGridGetData();

        //渲染页面
        $this->assign('chartMonth', $result["chartMonth"]);
        $this->assign('chartWeek', $result["chartWeek"]);
        $this->assign('chartDay',$result["chartDay"]);
        $this->assign('time', $result["time"]);
        $this->assign('status', $result["status"]);
        $this->assign('chartDayBus',$result["chartDayBus"]);
        $this->assign('chartMonthBus',$result["chartMonthBus"]);
        $this->assign('chartWeekDeptBus',$result["chartWeekDeptBus"]);

        if (($_SESSION['USER_MENU_ARR'][1] != "0") && ($_SESSION['USER_MENU_ARR'][1] != "")) {
            $this->display("ProjectReport/analyProjectData");
        } else {
            $this->display("Index/noright");
        }
    }

    public function dataGridGetData() {
        $report = D('project_report');
        $condition['is_delete'] = 0;

        if (I("start_date") && I("end_date")) {
            $condition['start_date'] = array(array('egt', I("start_date")), array('elt', I("end_date")));
        }
        else if (I("start_date")) {
            $condition['start_date'] = array('egt', I("start_date"));
        }
        else if (I("end_date")) {

            $condition['start_date'] = array('elt', I("end_date"));
        }
        else
        {
            $end_date = date("Y-m-d");
            $start_date = date("Y-m-d", strtotime("-1 month"));
            $condition['start_date'] = array(array('egt', $start_date), array('elt', $end_date));
        }

        //按天查询
        $result = $report->where($condition)->field("count(*) as count,start_date as dayflg")->group("dayflg")->order('dayflg')->select();
        $chartDayData  =json_encode($result);

        //按周查询
        $result = $report->where($condition)->field("count(*) as count,week(start_date) as weekflg ")->group('weekflg')->order('weekflg')->select();
        $chartWeekData =json_encode($result);

        //按月
        $result = $report->where($condition)->field("count(*) as count,month(start_date) as monthflg")->group("monthflg")->order('monthflg')->select();
        $chartMonthData  =json_encode($result);

        //按天查询业务需求
        $condition['tracker']='业务需求';
        $result = $report->where($condition)->field("count(*) as count,start_date as dayflg")->group("dayflg")->order('dayflg')->select();
        $chartDayBusData  =json_encode($result);

        //按月查询业务需求
        $condition['tracker']='业务需求';
        $result = $report->where($condition)->field("count(*) as count,onth(start_date) as monthflg")->group("monthflg")->order('monthflg')->select();
        $chartMonthBusData  =json_encode($result);

        //查询部门组内需求
        $condition['tracker']='组内需求';
        $result = $report->where($condition)->field("count(*) as count,dept_name")->group("dept_name")->select();
        $chartWeekDeptBusData  =json_encode($result);

        //项目状态分布
        $result = $report->where($condition)->field("status,count(*) as count")->group("status")->order("status")->select();
        $levelData = json_encode($result);

        $result = array();
        $result["chartMonth"] = $chartMonthData;
        $result["chartWeek"] = $chartWeekData;
        $result["chartDay"] = $chartDayData;
        $result["status"] = $levelData;
        $result["chartDayBus"] = $chartDayBusData;
        $result["chartMonthBus"] = $chartMonthBusData;
        $result["chartWeekDeptBus"] = $chartWeekDeptBusData;
        print_r(json_encode($result));

        return $result;

    }

    function doSelect($start_date, $end_date, $field) {
        $report = D('project_report');

        $condition['is_delete'] = 0;
        if ($start_date) {
            $condition['start_date'] = array('egt', I("start_date"));
        }
        if ($end_date) {
            $condition['start_date'] = array('elt', I("end_date"));
        }
        if ($start_date && $end_date) {
            $condition['start_date'] = array(array('egt', $start_date), array('elt', $end_date));
        }

        //查询并返回结果
        //$condition['tracker'] =array('neq','Bug');
        $result = $report->where($condition)->field($field . ",count(*) as count")->group($field)->order($field)->select();
        return json_encode($result);
    }

    function doSelectWithoutFilter($report_sql) {
        $report = D('project_report');
        //查询并返回结果
        $result = $report->query($report_sql);
        return json_encode($result);
    }

    function getChartYearSql($year) {
        return "select count(*) as count,month(start_date) as monthflg from project_report where year (start_date)=$year group by monthflg";
    }

    //获取某个月份每一周的数据
    function getChartMonthSql($year,$month) {
        return "select count(*) as count,week(start_date) as weekflg from project_report where  year (start_date)=$year and month (start_date)=$month group by weekflg";
    }


}

?>