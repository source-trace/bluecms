<?php
/*
 * [Skymps]��Ȩ���� ��׼���磬��������Ȩ��
 * This is not a freeware, use is subject to license terms
 *
 * $Id��admin_log.php
 * $author��lucks
 */
 define('IN_BLUE', true);

 require(dirname(__FILE__).'/include/common.inc.php');
 $act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'list';
 if($act == 'list'){
 	$perpage = '20';
 	$page = new page(array('total'=>get_log_total(), 'perpage'=>$perpage));
 	$currenpage=$page->nowindex;
 	$offset=($currenpage-1)*$perpage;

 	$log_list = get_log($offset, $perpage);
 	template_assign(array('log_list', 'current_act', 'page'), array($log_list, '������־�б�', $page->show(3)));
 	$smarty->display('admin_log.htm');
 }
 elseif($act == 'del'){
 	if($_POST['checkboxes']!=''){
	 	if(is_array($_POST['checkboxes'])){
			foreach($_POST['checkboxes'] as $key=>$val){
		 		$sql = "delete from ".table('admin_log')." where log_id = ".$val;
				if(!$db->query($sql)){
					showmsg('ɾ����־����');
				}
		 	}
	 	}else{
	 		if(!$db->query("DELETE FROM ".table('admin_log')." WHERE log_id=".intval($_GET['log_id']))){
	 			showmsg('ɾ����־����');
	 		}
	 	}
 	}else{
			showmsg('û��ѡ��ɾ������');
		}
	showmsg('ɾ����־�ɹ�', 'admin_log.php');
 }





















?>