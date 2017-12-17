<?php
/*
 * [bluecms]��Ȩ���� ��׼���磬��������Ȩ��
 * This is not a freeware, use is subject to license terms
 *
 * $Id��cache.php
 * $author��lucks
 */
 define('IN_BLUE', true);

 require_once(dirname(__FILE__) . '/include/common.inc.php');
 require_once(BLUE_ROOT . 'include/index.fun.php');
 $act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'clean';

 if($act == 'clean'){
 	template_assign(array('current_act'), array('���»���'));
 	$smarty->display('cache_clean.htm');
 }

 elseif($act == 'do_clean'){
 	if(in_array('data', $_POST['type'])){
 		update_data_cache();
 	}
 	if(in_array('tpl', $_POST['type'])){
 		update_tpl_cache();
 	}
 	showmsg('���»���ɹ�', 'cache.php');
 }

 elseif($act == 'set'){
 	$cache_arr = read_static_cache('cache_set');
	template_assign(array('current_act', 'cache_arr'), array('��������', $cache_arr));
	$smarty->display('set_cache.htm');
 }

 elseif($act == 'do_save'){
 	foreach($_POST as $k => $v){
 		$_POST[$k] = intval($v);
 	}
 	write_static_cache('cache_set', $_POST);
 	showmsg('���»������óɹ�', 'cache.php?act=set');
 }

























?>
