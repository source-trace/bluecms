<?php
/*
 * [bluecms]��Ȩ���� ��׼���磬��������Ȩ��
 * This is not a freeware, use is subject to license terms
 *
 * $Id��index.php
 * $author��lucks
 */
define('IN_BLUE', true);

require_once(dirname(__FILE__) . "/include/common.inc.php");
//require_once(dirname(__FILE__). "/include/menu.fun.php");

$act=!empty($_REQUEST['act']) ? trim($_REQUEST['act']) : '';

if($act=='')
{
$smarty->display('index.htm');
}
elseif($act=='top')
{
	$smarty->assign('admin_name', $_SESSION['admin_name']);
	$smarty->display('top.htm');
}
elseif($act=='menu')
{
	$cat_list = $db->getall("SELECT cat_id, cat_name FROM ".table('category')." WHERE parentid=0 ORDER BY show_order,cat_id");
	$arc_cat_list = $db->getall("SELECT cat_id, cat_name FROM ".table('arc_cat')." WHERE parent_id=0 ORDER BY show_order, cat_id");
	template_assign(array('cat_list', 'arc_cat_list'), array($cat_list, $arc_cat_list));
	/*if($_SESSION['admin_purview'][0] != 'all'){
		if(!file_exists(BLUE_ROOT.DATA.'admin/menu'.$_SESSION['admin_id'].'.html')){
			create_menu($_SESSION['admin_id']);
		}
		//read_menu($_SESSION['admin_id']);
		$smarty->display(BLUE_ROOT.'data/admin/menu2.html');
	}else{*/
		$smarty->display('menu.htm');
	//}
}
elseif($act == 'main')
{
	if (file_exists('../install'))
    {
        $install_warning = "����û��ɾ�� install �ļ��У����ڰ�ȫ�Ŀ��ǣ����ǽ�����ɾ�� install �ļ��С�";
    }

    if (file_exists('../update'))
    {
        $update_warning = "����û��ɾ�� upgrade �ļ��У����ڰ�ȫ�Ŀ��ǣ����ǽ�����ɾ�� upgrade �ļ��С�";
    }
	if(!file_exists(BLUE_ROOT.DATA.'update_log.txt')){
		echo '��ϵͳ�ĸ��¼�¼�ļ��Ѿ���ʧ�������޷�֪ͨ����ȷ�ĸ���';
		exit;
	}
	$fp = @fopen(BLUE_ROOT.DATA.'update_log.txt', 'rb');
	if(!$fp){
		echo '�򿪸�����־�ļ�ʧ��,������';
		exit;
	}
	if(!$str = @fread($fp, 10)){
		echo '��ȡ������־�ļ�ʧ�ܣ�������';
		exit;
	}
	@fclose;
    $from = get_version();
    $system_info = array();
    $system_info['version'] = BLUE_VERSION;
    $system_info['update_no'] = $str;
    $system_info['os'] = PHP_OS;
    $system_info['ip'] = $_SERVER['SERVER_ADDR'];
    $system_info['web_server'] = $_SERVER['SERVER_SOFTWARE'];
    $system_info['php_ver'] = PHP_VERSION;
    $system_info['mysql_ver'] = $db->dbversion();
    $system_info['max_filesize'] = ini_get('upload_max_filesize');

	template_assign(array('install_warning', 'update_warning', 'from', 'system_info'), array($install_warning, $update_warning, $from, $system_info));
	$smarty->display('main.htm');
}

?>