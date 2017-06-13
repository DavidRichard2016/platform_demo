<?php
import('Library.ORG.Util.PHPMailer');

class IndexAction extends Action
{

    public function index()
    {
        //检查用户是否登录
        if (isset($_SESSION['USER_ID'])) {
            $this->assign('login_user_name', iconv("gb2312", "UTF-8", $_SESSION['USER_NAME']));
            $this->display("main");   //如果用户已经登录，跳转到main.html页面
        } else {
            $this->display('login');  //如果用户未登录，跳转到login.html页面
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



   //登录
    public function do_login_ldap()
    {
        $error_msg = "";
        $login_right = false;
        $user_username = $_POST['username'];
        $user_password = $_POST['userpwd'];
        $user_table = D('user');
        $condition['user_name'] = $user_username;
        if (!empty($user_username) && !empty($user_password)) {
                $user_list = $user_table->where('user_name = \'' . $user_username . '\' and password = \'' . md5($user_password) . '\'')->find();
                if (count($user_list) >= 1) {
                    $login_right = true;
                    $user_id = $user_list['id'];
                    $user_cname = $user_list['real_name'];;
                } else {
                    $error_msg = '温馨提示：用户名密码错误.';
                }
        } else {
            $error_msg = '温馨提示：用户名或密码不能为空. 请输入账号登录.';
        }
        $this->assign('error_msg', $error_msg);
        $this->display('Index/login');
    }
}

?>