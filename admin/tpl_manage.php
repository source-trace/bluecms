<?php
/*
 * [bluecms]��Ȩ���� ��׼���磬��������Ȩ��
 * This is not a freeware, use is subject to license terms
 *
 * $Id��tpl_manage.php
 * $author��lucks
 */
 define('IN_BLUE', true);

 require_once(dirname(__FILE__).'/include/common.inc.php');

 $act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'list';

 if($act == 'list'){
 	$tpl = '';
 	$dir = BLUE_ROOT.'templates/default';
 	if($handle = @opendir($dir)){
 		$i = 0;
 		while(false !== ($file = @readdir($handle))){
 			if($file != 'css' && $file != 'images' && $file != '.' && $file != '..'){
 				$tpl[$i]['name'] = $file;
 				$tpl[$i]['modify_time'] = date('Y-m-d H:i:s',filemtime($dir.'/'.$file));
 				$tpl[$i]['size'] = filesize($dir.'/'.$file);
 				$i++;
 			}
 		}
 	}else{
 		echo '��ȡģ��Ŀ¼��������Ȩ��';
 		exit;
 	}
 	template_assign(array('current_act', 'tpl_list'), array('ǰ̨ģ���б�', $tpl));
 	$smarty->display('tpl.htm');
 }
 elseif($act == 'edit'){
	$file = $_GET['tpl_name'];
	if(!$handle = @fopen(BLUE_ROOT.'templates/default/'.$file, 'rb')){
		showmsg('��Ŀ��ģ���ļ�ʧ��');
	}
	$tpl['content'] = fread($handle, filesize(BLUE_ROOT.'templates/default/'.$file));
	$tpl['content'] = htmlentities($tpl['content'], ENT_QUOTES, GB2312);
	fclose($handle);
	$tpl['name'] = $file;
	template_assign(array('current_act', 'tpl'), array('�༭ģ��', $tpl));
	$smarty->display('tpl_info.htm');
 }
 elseif($act == 'do_edit'){
 	$tpl_name = !empty($_POST['tpl_name']) ? trim($_POST['tpl_name']) : '';
 	$tpl_content = !empty($_POST['tpl_content']) ? deep_stripslashes($_POST['tpl_content']) : '';
 	if(empty($tpl_name)){
 		return false;
 	}
 	$tpl = BLUE_ROOT.'templates/default/'.$tpl_name;
 	if(!$handle = @fopen($tpl, 'wb')){
		showmsg("��Ŀ��ģ���ļ� $tpl ʧ��");
 	}
 	if(fwrite($handle, $tpl_content) === false){
 		showmsg('д��Ŀ�� $tpl ʧ��');
 	}
 	fclose($handle);
 	showmsg('�༭ģ��ɹ�', 'tpl_manage.php');
 }



?>
