<?php
/*
 * [bluecms]��Ȩ���� ��׼���磬��������Ȩ��
 * This is not a freeware, use is subject to license terms
 *
 * $Id��ad_phone.php
 * $author��lucks
 */
define('IN_BLUE', true);

require_once dirname(__FILE__) . '/include/common.inc.php';
$act = $_REQUEST['act'] ? trim($_REQUEST['act']) : 'list';
if($act == 'list')
{
 	$perpage = '20';
 	$page = new page(array('total'=>get_total("SELECT COUNT(*) AS num FROM ".table('ad_phone')), 'perpage'=>$perpage));
 	$currenpage=$page->nowindex;
 	$offset=($currenpage-1)*$perpage;

	$ad_phone_list = get_list("SELECT id, content, title, color, start_time, end_time, show_order FROM ".table('ad_phone'), $offset, $perpage);
 	template_assign(array('ad_phone_list', 'current_act', 'page'),array($ad_phone_list, '�绰����б�', $page->show(3)));
 	$smarty->display('ad_phone.htm');
}
elseif($act == 'add')
{
 	template_assign(
 		array(
 			'act', 
 			'current_act'
 		), 
 		array(
 			$act, 
 			'���һ���绰���'
 		)
 	);
 	$smarty->display('ad_phone_info.htm');
}
elseif($act == 'doadd')
{
 	$content = !empty($_POST['content']) ? trim($_POST['content']) : '';
 	$title = !empty($_POST['title']) ? trim($_POST['title']) : '';
 	$color = !empty($_POST['color']) ? trim($_POST['color']) : '';
 	$start_time = !empty($start_time) ? explode('-',$_POST['start_time']) : '';
 	if($start_time)
 	{
 		if(!is_array($start_time))
 		{
 			showmsg('��ʼʱ���ʽ����');
 		}
 		$start_time = mktime(0, 0, 0, $start_time[1], $start_time[2], $start_time[0]);
 	}
 	else
 	{
 		$start_time = time();
 	}

 	$end_time = !empty($end_time) ? explode('-', $_POST['end_time']) : 0;
 	if($end_time)
 	{
 		if(!is_array($end_time))
 		{
 			showmsg('����ʱ���ʽ����');
 		}
 		$end_time = mktime(0, 0, 0, $end_time[1], $end_time[2], $end_time[0]);
 	}

 	$show_order = !isset($_POST['show_order']) ? intval($_POST['show_order']) : '';
 	if($content == '')
 	{
 		showmsg('��ʾ���Ʋ���Ϊ��');
 	}
 	if(!$db->query("INSERT INTO ".
 					table('ad_phone')." (id, content, title, color, start_time, end_time, is_show, show_order) 
 					VALUES ('', '$content', '$title', '$color', '$start_time', '$end_time', '1', '$show_order')"))
 	{
 		showmsg('����绰������', true);
 	}else{
 		showmsg('����绰���ɹ�', 'ad_phone.php', true);
 	}

}

elseif($act == 'edit'){
 	$phone = $db->getone("SELECT id, content, title, color, start_time, end_time, show_order FROM ".table('ad_phone')." WHERE id=".intval($_GET['id']));
 	template_assign(
 		array(
 			'phone', 
 			'act', 
 			'current_act'
 		), 
 		array(
 			$phone, 
 			$act, 
 			'�༭�绰���'
 		)
 	);
 	$smarty->display('ad_phone_info.htm');
}

elseif($act == 'doedit')
{
 	$content = !empty($_POST['content']) ? trim($_POST['content']) : '';
 	$title = !empty($_POST['title']) ? trim($_POST['title']) : '';
 	$color = !empty($_POST['color']) ? trim($_POST['color']) : '#000000';
 	$start_time = !empty($start_time) ? explode('-',$_POST['start_time']) : '';
 	if($start_time)
 	{
 		if(!is_array($start_time))
 		{
 			showmsg('��ʼʱ���ʽ����');
 		}
 		$start_time = mktime(0, 0, 0, $start_time[1], $start_time[2], $start_time[0]);
 	}
 	else
 	{
 		$start_time = time();
 	}

 	$end_time = !empty($end_time) ? explode('-', $_POST['end_time']) : 0;
 	if($end_time)
 	{
 		if(!is_array($end_time))
 		{
 			showmsg('����ʱ���ʽ����');
 		}
 		$end_time = mktime(0, 0, 0, $end_time[1], $end_time[2], $end_time[0]);
 	}
 	$show_order = isset($_POST['show_order']) ? intval($_POST['show_order']) : '';
 	if($content == '')
 	{
 		showmsg('��ʾ���Ʋ���Ϊ��');
 	}
 	if(!$db->query("UPDATE ".
 						table('ad_phone'). " 
 					SET content='$content', title='$title', color='$color', start_time='$start_time', 
 						end_time='$end_time', show_order='$show_order' 
 					WHERE id=".$_POST['id']))
 	{
 		showmsg('���µ绰������', true);
 	}
 	else
 	{
 		showmsg('���µ绰���ɹ�', 'ad_phone.php', true);
 	}
}

elseif($act == 'del')
{
 	if(!$db->query("DELETE FROM ".table('ad_phone')." WHERE id=".$_GET['id']))
 	{
 		showmsg('ɾ���绰������', true);
 	}
 	else
 	{
 		showmsg('ɾ���绰���ɹ�', 'ad_phone.php', true);
 	}
}

?>