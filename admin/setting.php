<?php
/*
 * [Skymps]��Ȩ���� ��׼���磬��������Ȩ��
 * This is not a freeware, use is subject to license terms
 *
 * $Id��setconfig.php
 * $author��lucks
 */
 define('IN_BLUE', true);

 require(dirname(__FILE__) . '/include/common.inc.php');
 $act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'list';

 if($act == 'list'){
 	$config = $db->getall("SELECT name, value FROM ".table('config'));
 	if($config){
 		foreach($config as $k=>$v){
 			$config[$v[name]] = $v[value];
 		}
 	}
 	template_assign(array('config', 'act', 'current_act'), array($config, $act, 'վ������'));
 	$smarty->display('setting.htm');
 }
 elseif($act == 'set'){
 	foreach($_POST as $k => $v){
 		if(!$db->query("UPDATE ".table("config")." SET value='$v' WHERE name='$k'")){
 			showmsg('����վ������ʧ��', true);
 		}
		elseif($k != 'act'){
 			$config_arr[$k] = $v;
 		}
 	}
 	showmsg('����վ��ɹ�', 'setting.php', true);
 }






































?>
