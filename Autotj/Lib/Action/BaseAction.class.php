<?php

class BaseAction extends Action
{

    /**
     * 初始化入口
     */
    function _initialize()
    {
        if (!session('?USER_NAME')) {
            $this->assign('error_msg', 'session 过期');
            $this->redirect("Public/doLogout");
        }
        $this->_setPage();
    }

    /**
     * 设置grid的分页信息
     */
    private function _setPage()
    {
        if (isset($_REQUEST['page']) && !empty($_REQUEST['page'])) {
            $this->page = intval($_REQUEST['page']);
        }

        if (isset($_REQUEST['rows']) && !empty($_REQUEST['rows'])) {
            $this->rows = intval($_REQUEST['rows']);
        }

        if (isset($_REQUEST['sort']) && !empty($_REQUEST['sort'])) {
            $this->order = $_REQUEST['sort'];
            if (isset($_REQUEST['order']) && !empty($_REQUEST['order'])) {
                $this->order .= ' ' . $_REQUEST['order'];
            }
        }
    }

    function logUserHandle($method, $handle, $record = '')
    {
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

    public function send_post($url, $type, $post_data)
    {
        $postdata = http_build_query($post_data);
        $options = array(
            'http' => array(
                'method' => $type,//or GET
                'header' => 'Content-type:application/x-www-form-urlencoded',
                'content' => $postdata,
                'timeout' => 15 * 60 // 超时时间（单位:s）
            )
        );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        return $result;
    }

    public function send_get($url, $post_data)
    {
        $data = http_build_query($post_data);
        $result = file_get_contents($url . '?' . $data);
        return json_decode($result, true);
    }

}

?>
