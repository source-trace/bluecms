<?php
/*
 * [bluecms]��Ȩ���� ��׼���磬��������Ȩ��
 * This is not a freeware, use is subject to license terms
 *
 * $Id��login.php
 * $author��lucks
 */
 define('IN_BLUE', true);

 require_once(dirname(__FILE__) . '/include/common.inc.php');
 $act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'login';

 if($act == 'login'){
 	if($_SESSION['admin_id']){
 		showmsg('���ѵ�¼�������ٴε�¼', 'index.php');
 	}
 	template_assign('current_act', '��¼');
 	$smarty->display('login.htm');
 }
 elseif($act == 'do_login'){
 	$admin_name = isset($_POST['admin_name']) ? trim($_POST['admin_name']) : '';
 	$admin_pwd = isset($_POST['admin_pwd']) ? trim($_POST['admin_pwd']) : '';
 	$remember = isset($_POST) ? intval($_POST['rememberme']) : 0;
 	if($admin_name == ''){
 		showmsg('�û�������Ϊ��');
 	}
 	if($admin_pwd == ''){
 		showmsg('�û����벻��Ϊ��');
 	}
 	if(check_admin($admin_name, $admin_pwd)){
 		update_admin_info($admin_name);
 		if($remember == 1){
 			setcookie('Blue[admin_id]', $_SESSION['admin_id'], time()+86400);
 			setcookie('Blue[admin_name]', $admin_name, time()+86400);
			setcookie('Blue[admin_pwd]', md5(md5($admin_pwd).$_CFG['cookie_hash']), time()+86400);
 		}
 	}else{
 		showmsg('��������û�������������');
 	}
 	showmsg('��ӭ�� '.$admin_name.' ���������ڽ�ת���������...', 'index.php');
 }
 elseif($act == 'logout'){
 	$_SESSION['admin_id'] = '';
 	$_SESSION['admin_name'] = '';
 	$_SESSION['admin_purview'] = '';
	setcookie('Blue[admin_id]', '', 1);
	setcookie('Blue[admin_name]', '', 1);
	setcookie('Blue[admin_pwd]', '', 1);
	showmsg('�˳���¼�ɹ�', 'index.php');
 }























?>
