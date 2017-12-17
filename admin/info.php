<?php
/*
 * [bluecms]��Ȩ���� ��׼���磬��������Ȩ��
 * This is not a freeware, use is subject to license terms
 *
 * $Id��info.php
 * $author��lucks
 */
define('IN_BLUE', true);
require_once dirname(__FILE__) . '/include/common.inc.php';
require_once BLUE_ROOT.'include/index.fun.php';
$act = $_REQUEST['act'] ? trim($_REQUEST['act']) : 'list';
$post_id = !empty($_REQUEST['post_id']) ? intval($_REQUEST['post_id']) : '';
$cid = !empty($_REQUEST['cid']) ? intval($_REQUEST['cid']) : '';

if($act == 'list')
{
	if(empty($cid))
	{
 		return false;
 	}
 	$perpage = '20';
 	$page = new page(array('total'=>get_info_total($cid, 0, '', true), 'perpage'=>$perpage));
 	$currenpage=$page->nowindex;
 	$offset=($currenpage-1)*$perpage;
 	$info_list = get_info_deep($cid, $offset, $perpage);
 	for($i=0;$i<count($info_list);$i++)
 	{
		$info_list[$i]['info_url'] = url_rewrite('post', array('id'=>$info_list[$i]['post_id']));
	}
 	template_assign(
		array(
			'current_act', 
			'info_list', 
			'page', 
			'cid'
		), 
		array(
			'�༭������Ϣ', 
			$info_list, 
			$page->show(3), 
			$cid
		)
	);
 	$smarty->display('info.htm');
}

elseif($act == 'add1')
{
 	$cat_list = get_cat_html2();
 	template_assign(
 		array(
 			'cat_list', 
 			'current_act'
 		), 
 		array(
 			$cat_list, 
 			'ѡ�����'
 		)
 	);
 	$smarty->display('info_add1.htm');
}

elseif($act == 'add2')
{
 	$model_id = get_model_id($cid);
 	$area_option = get_area_option(1);
 	$arr = read_static_cache('model');
 	$insert_must_att = $arr[$model_id]['must'];
 	$insert_nomust_att = $arr[$model_id]['nomust'];
 	$cat = get_cat_name($cid);
 	template_assign(
 		array(
 			'cat', 
 			'current_act', 
 			'insert_must_att', 
 			'insert_nomust_att', 
 			'area_option', 
 			'cid'
 		),
 		array(
 			$cat, 
 			'����������Ϣ', 
 			$insert_must_att, 
 			$insert_nomust_att, 
 			$area_option, 
 			$cid
 		)
 	);
	$smarty->display('info_add2.htm');
}

elseif($act == 'do_add')
{
 	$must_att_arr = array();
 	$nomust_att_arr = array();
 	$title = !empty($_POST['title']) ? trim($_POST['title']) : '';
 	if($title == '')
 	{
 		showmsg('��Ϣ���ⲻ��Ϊ��');
 	}
 	$area = !empty($_POST['area']) ? intval($_POST['area']) : '';
 	if($area == '')
 	{
		showmsg('����û��ѡ�����');
	}
 	$useful_time = intval($_POST['useful_time']);
 	$content = !empty($_POST['content']) ? trim($_POST['content']) : '';
	if(!empty($content))
	{
		$content = str_replace(' ', '&nbsp;', str_replace(array("\r\n", "\r", "\n"), "<br/>", $content));
	}
 	if($content == '')
 	{
 		showmsg('������Ϣ����̫����');
 	}
 	
	$cid = !empty($_POST['cid']) ? intval($_POST['cid']) : '';
	if(empty($cid))
	{
		return false;
	}
	$is_recommend = !empty($_POST['is_recommend']) ? intval($_POST['is_recommend']) : 0;
	if($is_recommend == 1)
	{
		$rec_start = $timestamp;
	}
	else
	{
		$rec_start = 0;
	}
	$rec_time = $_POST['rec_time'];
	
 	$top_type = !empty($_POST['top_type']) ? intval($_POST['top_type']) : 0;
 	if($top_type != 0)
 	{
 		$top_start = $timestamp;
 	}
 	else
 	{
 		$top_start = 0;
 	}
 	$top_time = $_POST['top_time'];

	$is_head_line = !empty($_POST['is_head_line']) ? intval($_POST['is_head_line']) : 0;
 	if($is_head_line == 0)
 	{
 		$head_line_start = $timestamp;
 	}
 	else
 	{
 		$head_line_start = 0;
 	}
 	$head_line_time = $_POST['head_line_time'];
 	
	if ($is_recommend == 1 && !preg_match('/^[1-9][0-9]*$/', $rec_time))
	{
		showmsg('�����Ƽ�ʱ���ʽ����');
	}
	elseif ($top_type != 0 && !preg_match('/^[1-9][0-9]*$/', $top_time))
	{
		showmsg('�����ö�ʱ���ʽ����');
	}
	elseif ($is_head_line == 1 && !preg_match('/^[1-9][0-9]*$/', $head_line_time))
	{
		showmsg('����ͷ��ʱ���ʽ����');
	}

 	$link_man = !empty($_POST['link_man']) ? trim($_POST['link_man']) : '';
 	$link_phone = !empty($_POST['link_phone']) ? trim($_POST['link_phone']) : 0;
 	$link_email = !empty($_POST['link_email']) ? trim($_POST['link_email']) : '';
 	$link_qq = !empty($_POST['link_qq']) ? trim($_POST['link_qq']) : 0;
 	$link_address = !empty($_POST['link_address']) ? trim($_POST['link_address']) : '';
 	
 	if($link_man=='')
 	{
 		showmsg('��ϵ����������Ϊ��');
 	}
 	if($link_phone=='')
 	{
 		showmsg('Ϊ��������Ϣ��ʵ����ϵ�绰��ҪΪ��');
 	}
 	$must_att_arr = get_att($model_id, $_POST['att1'], 'must_att');
 	$nomust_att_arr = get_att($model_id, $_POST['att2']);

	if($_CFG['info_is_check'] == 0)
	{
		$is_check = 1;
	}
	else
	{
		$is_check = 0;
	}
 	$sql = "INSERT INTO ".table('post')." (post_id, cat_id, user_id, area_id, title, keywords, content, 
 	link_man, link_phone, link_email, link_qq, link_address, pub_date, useful_time, click, comment, 
 	is_check, is_recommend, rec_start, rec_time, top_type, top_start, top_time, is_head_line, head_line_start, head_line_time) VALUES ('', '$cid'," .
 			" '$_SESSION[user_id]', '$area', '$title', '', '$content', '$link_man', '$link_phone', 
 			'$link_email', '$link_qq', '$link_address', '$timestamp', '$useful_time', '0', '0', 
 			'$is_check', '$is_recommend', '$rec_start', '$rec_time', '$top_type', '$top_start', '$top_time', '$is_head_line', '$head_line_start', '$head_line_time')";
 	$db->query($sql);
 	$post_id = $db->insert_id();
 	insert_att_value($must_att_arr, $post_id);
 	insert_att_value($nomust_att_arr, $post_id);
 	for($i=0;$i<4;$i++)
 	{
 		if($_POST['pic'.$i] && file_exists(BLUE_ROOT.$_POST['pic'.$i]))
 		{
 			$sql = "INSERT INTO ".table('post_pic')." (pic_id, post_id, pic_path) VALUES ('', '$post_id', '".$_POST['pic'.$i]."')";
 			$db->query($sql);
 		}
 	}
	if($_POST['pic0'])
	{
		include_once(BLUE_ROOT."include/upload.class.php");
		$image = new upload();
		$lit_pic = $image->small_img($_POST['pic0'],126, 80);
		$db->query("UPDATE ".table('post')." SET lit_pic='$lit_pic' WHERE post_id='$post_id'");
	}
 	$post_url = url_rewrite('post', array('id'=>$post_id));
 	template_assign(array('current_act', 'post_url'), array('�����ɹ�', $post_url));
 	$smarty->display('info_add3.htm');
}

elseif($act == 'edit')
{
 	if(empty($post_id))
 	{
 		return false;
 	}
 	$basic_info = $db->getone("SELECT post_id, cat_id, area_id, title, keywords, content, link_man, 
 	link_phone, link_qq, link_email, link_address, useful_time, is_check, is_recommend, rec_start, rec_time, 
 	top_type, top_start, top_time, is_head_line, head_line_start, head_line_time  FROM ".table('post')." WHERE post_id = ".intval($post_id));

 	$area_option = get_area_option(1, $basic_info['area_id']);

 	$cat_id = $basic_info['cat_id'];
 	$model_id = get_model_id($cat_id);
 	$insert_must_att = insert_must_att($model_id, true, $post_id);
 	$insert_nomust_att = insert_nomust_att($model_id, true, $post_id);

 	$parentid = get_parentid($cat_id);
 	$cat_option = get_child($parentid, $cat_id);
	
 	//��ȡͼƬ��Ϣ
 	$pic_arr = $db->getall("SELECT pic_path FROM ".table('post_pic')." WHERE post_id = ".intval($post_id));
 	$pic_list = '';
 	for($i=0;$i<4;$i++)
 	{
 		if($pic_arr[$i]['pic_path'])
 		{
 			$pic_list .= "<input type=\"hidden\" name=\"pic".$i."\" value=\"".$pic_arr[$i]['pic_path']."\" />";
 		}
 		else
 		{
 			$pic_list .= "<input type=\"hidden\" name=\"pic".$i."\" value=\"\" />";
 		}
 	}
 	template_assign(
 		array(
 			'current_act', 
 			'area_option', 
 			'cat_option', 
 			'basic_info', 
 			'insert_must_att', 
 			'insert_nomust_att', 
 			'pic_list'
 		),
 		array(
 			'�༭������Ϣ', 
 			$area_option, 
 			$cat_option, 
 			$basic_info, 
 			$insert_must_att, 
 			$insert_nomust_att, 
 			$pic_list
 		)
 	);
 	$smarty->display('info_edit.htm');
}

elseif($act == 'do_edit')
{
	$must_att_arr = array();
 	$nomust_att_arr = array();
 	$title = !empty($_POST['title']) ? trim($_POST['title']) : '';
 	$cat_id = !empty($_POST['cat_id']) ? trim($_POST['cat_id']) : '';
 	$area = !empty($_POST['area']) ? intval($_POST['area']) : '';
 	$useful_time = intval($_POST['useful_time']);
 	$content = !empty($_POST['content']) ? trim($_POST['content']) : '';
	if(!empty($content))
	{
		$content = str_replace(' ', '&nbsp;', str_replace(array("\r\n", "\r", "\n"), "<br/>", $content));
	}
	$is_check		= !empty($_POST['is_check']) ? intval($_POST['is_check']) : 0;
 	$is_recommend	= !empty($_POST['is_recommend']) ? intval($_POST['is_recommend']) : 0;
 	if($_POST['is_recommend1'] == 0)
 	{
	 	if ($is_recommend == 1)
	 	{
	 		$rec_start	= $timestamp;
	 		$rec_time	= $_POST['rec_time'];
			if(!preg_match('/^[1-9][0-9]*$/', $rec_time))
			{
	 			showmsg('�Ƽ�ʱ���ʽ����');
	 		}
	 		$condition	= " ,rec_start='$rec_start', rec_time='$rec_time' ";
	 	}
	 	else
	 	{
	 		$rec_time	= '';
	 		$condition	= '';
	 	}
	}
	else
	{
 		$rec_time = '';
		if ($is_recommend == 0)
		{
 			$condition = " ,rec_start='', rec_time='' ";
		}
		else
		{
			$condition = '';
		}
 	}
 	$top_type = !empty($_POST['top_type']) ? intval($_POST['top_type']) : 0;
 	if ($_POST['top_type1'] == 0)
 	{
 		if ($top_type != 0)
 		{
 			$top_start	= $timestamp;
 			$top_time	= $_POST['top_time'];
			if(!preg_match('/^[1-9][0-9]*$/', $top_time))
			{
	 			showmsg('�ö�ʱ���ʽ����');
	 		}
 			$condition	.= ",top_start='$top_start', top_time='$top_time' ";
 		}
 		else
 		{
 			$top_time	= '';
 			$condition .= '';
 		}
 	}
 	else
 	{
 		$top_time	= '';
 		if ($top_type == 0)
 		{
 			$condition = " ,top_start='', top_time='' ";
 		}
 		else
 		{
 			$condition = '';
 		}
 	}
	$is_head_line = intval($_POST['is_head_line']);
	if($_POST['is_head_line1'] == 0)
	{
	 	if($is_head_line == 1)
	 	{
			$confirm_head = 1;
	 		$head_line_start	= $timestamp;
	 		$head_line_time	= $_POST['head_line_time'];
	 		if(!preg_match('/^[1-9][0-9]*$/', $head_line_time))
	 		{
	 			showmsg('ͷ��ʱ���ʽ����');
	 		}
	 		$condition	.= " ,head_line_start='$head_line_start', head_line_time='$head_line_time' ";
	 	}
	 	else
	 	{
	 		$head_line_time	= 0;
	 		$condition	.= '';
	 	}
 	}
 	else
 	{
 		$head_line_time	= 0;
 		$condition	.= '';
 	}

 	$link_man = !empty($_POST['link_man']) ? trim($_POST['link_man']) : '';
 	$link_phone = !empty($_POST['link_phone']) ? trim($_POST['link_phone']) : 0;
 	$link_email = !empty($_POST['link_email']) ? trim($_POST['link_email']) : '';
 	$link_qq = !empty($_POST['link_qq']) ? trim($_POST['link_qq']) : 0;
 	$link_address = !empty($_POST['link_address']) ? trim($_POST['link_address']) : '';

 	if($title == '')
 	{
 		showmsg('��Ϣ���ⲻ��Ϊ��');
 	}
 	
 	if($top_type==0 && $top_time > 0)
 	{
 		showmsg('ֻ���ڿ����ö�����ʱ�����������ö�ʱ��');
 	}

 	if($link_man=='')
 	{
 		showmsg('��ϵ����������Ϊ��');
 	}
 	if($link_phone=='')
 	{
 		showmsg('Ϊ��������Ϣ��ʵ����ϵ�绰��ҪΪ��');
 	}
 	$must_att_arr = get_att($model_id, $_POST['att1'], 'must_att');
 	$nomust_att_arr = get_att($model_id, $_POST['att2']);
 	
 	$sql = "UPDATE ".table('post')." 
 				SET cat_id='$cat_id', area_id='$area', title='$title', keywords='$keywords', 
 					content='$content', link_man='$link_man', link_phone='$link_phone', 
 					link_email='$link_email', link_qq='$link_qq', link_address='$link_address', 
 					useful_time='$useful_time', is_check='$is_check', is_recommend='$is_recommend' ".
 					$condition.", top_type='$top_type', is_head_line='$is_head_line' 
 	 			WHERE post_id=".intval($post_id);
 	$db->query($sql);
 	
 	$db->query("DELETE FROM ".table('post_att')." WHERE post_id =".intval($post_id));
 	insert_att_value($must_att_arr, $post_id);
 	insert_att_value($nomust_att_arr, $post_id);
	$db->query("DELETE FROM ".table('post_pic')." WHERE post_id=".intval($post_id));
 	for($i=0;$i<4;$i++)
 	{
 		if($_POST['pic'.$i] && file_exists(BLUE_ROOT.$_POST['pic'.$i]))
 		{
 			$sql = "INSERT INTO ".table('post_pic')." (pic_id, post_id, pic_path) VALUES ('', '$post_id', '".$_POST['pic'.$i]."')";
 			$db->query($sql);
 		}
 	}
	if (file_exists(BLUE_ROOT.$_POST['lit_pic']))
	{
		@unlink(BLUE_ROOT.$_POST['lit_pic']);
	}
	if($_POST['pic0'])
	{
		include_once(BLUE_ROOT."include/upload.class.php");
		$image = new upload();
		$lit_pic = $image->small_img($_POST['pic0'],126, 80);
		$db->query("UPDATE ".table('post')." SET lit_pic='$lit_pic' WHERE post_id='$post_id'");
	}
	else
	{
		$db->query("UPDATE ".table('post')." SET lit_pic='' WHERE post_id='$post_id'");
	}
 	showmsg('�༭��Ϣ�ɹ�', 'info.php?cid='.get_parentid($cat_id));
}

elseif($act == 'del')
{
	if (empty($post_id))
	{
		$post_id = $_POST['checkboxes'];
		if(count($_POST['checkboxes']) > 1)
		{
			for($i=0;$i<count($post_id);$i++)
			{
				$db->query("DELETE FROM ".table('post')." WHERE post_id=".intval($post_id[$i]));
				$pic_arr = $db->getall("SELECT pic_path FROM ".table('post_pic')." WHERE post_id = ".intval($post_id[$i]));
		 		for($j=0;$j<count($pic_arr);$j++)
		 		{
		 			@unlink(BLUE_ROOT.$pic_arr[$j][pic_path]);
		 		}
		 		$db->query("DELETE FROM ".table('post_pic')." WHERE post_id = ".intval($post_id[$i]));
		 		$db->query("DELETE FROM ".table('post_att')." WHERE post_id = ".intval($post_id[$i]));
			}
		}
		else
		{
			$db->query("DELETE FROM ".table('post')." WHERE post_id = ".intval($post_id[0]));
 			$pic_arr = $db->getall("SELECT pic_path FROM ".table('post_pic')." WHERE post_id = ".intval($post_id[0]));
 			for($i=0;$i<count($pic_arr);$i++)
 			{
 				@unlink(BLUE_ROOT.$pic_arr[$i][pic_path]);
 			}
 			$db->query("DELETE FROM ".table('post_pic')." WHERE post_id = ".intval($post_id[0]));
 			$db->query("DELETE FROM ".table('post_att')." WHERE post_id = ".intval($post_id[0]));
		}
	}
	else
	{
		$db->query("DELETE FROM ".table('post')." WHERE post_id = ".$post_id);
		$pic_arr = $db->getall("SELECT pic_path FROM ".table('post_pic')." WHERE post_id = ".intval($post_id[0]));
 		for($i=0;$i<count($pic_arr);$i++)
 		{
 			@unlink(BLUE_ROOT.$pic_arr[$i][pic_path]);
 		}
 		$db->query("DELETE FROM ".table('post_pic')." WHERE post_id = ".intval($post_id[0]));
 		$db->query("DELETE FROM ".table('post_att')." WHERE post_id = ".intval($post_id[0]));
	}
 	showmsg('ɾ��������Ϣ�ɹ�', 'info.php?cid='.$cid);
}

elseif($act == 'check')
{
 	$com_id = $_POST['checkboxes'];
 	if(empty($com_id))
 	{
 		showmsg('��ѡ���������');
 	}
 	for($i=0;$i<count($com_id);$i++)
 	{
 		$db->query("UPDATE ".table('post')." SET is_check = 1");
 	}
 	showmsg('������۳ɹ�','info.php?cid='.$cid);
}

elseif($act == 'upload')
{
 	template_assign();
 	$smarty->display('upload.htm');
}

elseif($act == 'do_upload')
{
 	require_once(BLUE_ROOT . "include/upload.class.php");
	$image = new upload();
 	if(isset($_FILES['upload_file']['error']) && $_FILES['upload_file']['error'] == 0)
 	{
		$upload_pic = $image->img_upload($_FILES['upload_file']);
	}
	template_assign('add_pic', $upload_pic);
	$smarty->display('upload.htm');
}

elseif($act == 'del_pic')
{
 	$id = $_REQUEST['id'];
 	$db->query("DELETE FROM ".table('post_pic')." WHERE pic_path='$id'");
 	if(file_exists(BLUE_ROOT.$id))
 	{
 		@unlink(BLUE_ROOT.$id);
 	}
}

?>
