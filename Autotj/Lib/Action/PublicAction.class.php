<?php

header("Content-Type:text/html;   charset=utf-8");

class PublicAction extends Action {

    //公用json方法
    function getJSONData($param) {
//            $data = self::Curl_http(array($param), '10');
//            $jsondecode = json_decode($data[0], true);
//            return $jsondecode;
        $data = file_get_contents($param);
        $jsondecode = json_decode($data, true);
        print_r($jsondecode);
        return($jsondecode);
    }


    /**
     * curl 多线程 
     *  
     * @param array $array 并行网址 
     * @param int $timeout 超时时间
     * @return array 
     */
    function Curl_http($array, $timeout) {
        $res = array();
        $mh = curl_multi_init(); //创建多个curl语柄
        $startime = self::getmicrotime();
        foreach ($array as $k => $url) {
            $conn[$k] = curl_init($url);
            curl_setopt($conn[$k], CURLOPT_TIMEOUT, $timeout); //设置超时时间
            curl_setopt($conn[$k], CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
            curl_setopt($conn[$k], CURLOPT_MAXREDIRS, 7); //HTTp定向级别
            curl_setopt($conn[$k], CURLOPT_HEADER, 0); //这里不要header，加快效率
            curl_setopt($conn[$k], CURLOPT_FOLLOWLOCATION, 1); // 302 redirect
            curl_setopt($conn[$k], CURLOPT_RETURNTRANSFER, 1);
            curl_multi_add_handle($mh, $conn[$k]);
        }

        //防止死循环耗死cpu 这段是根据网上的写法
        do {
            $mrc = curl_multi_exec($mh, $active); //当无数据，active=true
        } while ($mrc == CURLM_CALL_MULTI_PERFORM); //当正在接受数据时

        while ($active and $mrc == CURLM_OK) {//当无数据时或请求暂停时，active=true
            if (curl_multi_select($mh) != -1) {
                do {
                    $mrc = curl_multi_exec($mh, $active);
                } while ($mrc == CURLM_CALL_MULTI_PERFORM);
            }
        }

        foreach ($array as $k => $url) {
            curl_error($conn[$k]);
            $res[$k] = curl_multi_getcontent($conn[$k]); //获得返回信息
            $header[$k] = curl_getinfo($conn[$k]); //返回头信息
            curl_close($conn[$k]); //关闭语柄
            curl_multi_remove_handle($mh, $conn[$k]);   //释放资源  
        }

        curl_multi_close($mh);
        $endtime = self::getmicrotime();
        $diff_time = $endtime - $startime;
        return $res;
    }

    //计算当前时间
    function getmicrotime() {
        list($usec, $sec) = explode(" ", microtime());
        return ((float) $usec + (float) $sec);
    }

    //退出登录
    public function doLogout() {
//        if (!session('?USER_NAME')) {
//            $this->logUserHandle(__METHOD__, "logout", $_SESSION['USER_ID']);
//        }
        $this->logUserHandle(__METHOD__, "logout", $_SESSION['USER_ID']);
        session(null);
        $this->redirect("/");
    }

    function logUserHandle($method, $handle, $record = '') {
        $data["ip_address"] = get_client_ip();
        $data["user_id"] = $_SESSION['USER_ID'];
        $data["user_name"] = $_SESSION['USER_NAME'];
        $data["function_name"] = $method;
        $data["event"] = $handle;
        $data["record"] = $record;
        $data["add_time"] = date('Y-m-d H:i:s', time());
        $log = M("log_user");
        $log->data($data)->add();
    }

}

?>
