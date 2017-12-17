<?php
/**
 * [bluecms]��Ȩ���� ��׼���磬��������Ȩ��
 * This is not a freeware, use is subject to license terms
 *
 * $Id��category.php
 * $author��lucks
 */
define('IN_BLUE', true);
require dirname(__FILE__) . '/include/common.inc.php';
require BLUE_ROOT . 'include/page.class.php';
	
do_task('update_info');
//����
$act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'step1';
//��ǰ��ĿID
$cid = !empty($_REQUEST['cid']) ? intval($_REQUEST['cid']) : '';
//��ǰ��Ŀ����ĿID
$pid = get_parentid($cid);
//����ID
$aid = !empty($_REQUEST['aid']) ? intval($_REQUEST['aid']) : '';

//���÷�ҳ
$perpage = 10;
$page = new page(array('total'=>get_info_total($cid, $pid, $aid), 'perpage'=>$perpage, 'action'=>'category', 'cid'=>$cid, 'aid'=>$aid));
$currenpage=$page->nowindex;
$offset=($currenpage-1)*$perpage;

//��Ŀ�б�
$cat_nav = read_static_cache('cat_list_0');
//��������
$add_nav_list = read_static_cache('add_nav');
$bot_nav = read_static_cache('bot_nav');

//�ײ�����
$cat_option = get_option(1);
//�����б�
$area_option = get_area_option(1);
//����ID
$cache_id = $cid.$pid.$aid.$currenpage;

function smarty_block_dynamic($param, $content, &$smarty)
{
	return $content;
}

$smarty->register_block('dynamic', 'smarty_block_dynamic', false);

if($cid && $pid == 0)
{
 	if(!$smarty->is_cached('category',$cache_id))
 	{
 		$cat_info = $db->getone("SELECT cat_name, title, keywords, description 
 								FROM " . table('category') . " WHERE cat_id=" . intval($cid));
		//�����б�
	 	$cat_list = read_static_cache('cat_list_1');
	 	$cat_list = $cat_list[$cid];
		//�����б�
	 	$area_list = read_static_cache('area_list');
	 	$area_list = $area_list[$cid];

		//�����ö�������Ϣ
		if($currenpage ==1)
		{
			$sql1 = "SELECT a.post_id, a.cat_id, b.cat_name, a.title, a.content, a.lit_pic, a.click, 
							a.comment, a.pub_date, a.is_recommend, a.top_type, a.top_time 
					FROM ".table('post') . " AS a 
					LEFT JOIN ".table('category')." AS b ON a.cat_id = b.cat_id 
					WHERE a.cat_id IN(SELECT cat_id 
										FROM ".table('category')." 
										WHERE parentid =" . $cid . ") and a.is_check = 1 and a.top_type = 2 
												and a.top_start + a.top_time > $timestamp";

			$top_info = $db->getall($sql1);
		}
		else
		{
			$top_info = array();
		}

		//�ö���Ϣ��
		$top_info_num = count($top_info);
		//����ö���С��ÿҳ��ʾ�������ٵ�����ͨ����䣬������ʾĬ����
		if($top_info_num >= $perpage)
		{
			$limit = $perpage;
		}
		else
		{
			$limit = $perpage - $top_info_num;
		}
		//��������Ų�Ϊ�գ�����ϵ���������ѯ
		
		if(!empty($aid))
		{
			$condition = " and a.area_id = $aid ";	
		}
		else
		{
			$condition = '';
		}
		$sql2 = "SELECT a.post_id, a.cat_id, b.cat_name, a.title, a.content, a.lit_pic, a.click, a.comment, 
						a.pub_date, a.is_recommend, a.top_type, a.top_time 
				FROM ".table('post') . " AS a 
				LEFT JOIN ".table('category')." AS b ON a.cat_id = b.cat_id 
				WHERE a.cat_id IN(SELECT cat_id 
									FROM ".table(category).
									" WHERE parentid=" . $cid . ") and a.top_type != 2 ".$condition.
											" and a.is_check=1 
									ORDER BY a.pub_date DESC LIMIT $offset,$limit";

		$info_list = $db->getall($sql2);
		//��Ҫ��ʾ����������ϳ��µ�����
		if(count($info_list)>0)
		{
			for($i=0;$i<count($info_list); $i++)
			{
				$top_info[] = $info_list[$i];
			}
		}
		$info_list = $top_info;
		//�ӹ���������ʱ������ʱ���ʽת�����������ӡ���Ϣ����
		for($i=0;$i<count($info_list);$i++)
		{
			$info_list[$i]['sub_day'] = sub_day(time(),$info_list[$i]['pub_date']);
			$info_list[$i]['pub_date'] = date("Y-m-d H:i:s",$info_list[$i]['pub_date']);
			$info_list[$i]['cat_url'] = url_rewrite('category', array('cid'=>$info_list[$i]['cat_id']));
			$info_list[$i]['info_url'] = url_rewrite('post', array('id'=>$info_list[$i]['post_id']));
		}
		//ȡ���Ƽ���Ϣ
		$sql = "SELECT post_id, title 
				FROM " . table('post') . 
				" WHERE cat_id IN(SELECT cat_id 
									FROM " . table('category').
									" WHERE parentid=" . intval($cid) . ") 
						and is_check = 1 and is_recommend=1 and rec_start + rec_time*24*3600 > $timestamp 
				ORDER BY pub_date DESC 
				LIMIT 10";
		$rec_info = $db->getall($sql);

		template_assign(
			array(
				'act', 				//����
				'cat_info', 		//��ǰ�������ϸ��Ϣ
				'cat_nav', 			//������Ŀ����
				'add_nav_list', 	//��̨��ӵ����б�
				'cat_list', 		//�Ҳ���Ŀ�б�
				'area_list', 		//�Ҳ�����б�
				'info_list', 		//������Ϣ�б�
				'rec_info',			//�Ƽ���Ϣ�б�
		 		'current_act', 		//
		 		'bot_nav', 			//�ײ�����
		 		'cat_option', 		//��Ŀѡ��
		 		'area_option', 		//����ѡ��
		 		'page'				//��ҳ
			), 
			array(
				'top2', 
				$cat_info, 
				$cat_nav, 
				$add_nav_list,
		 		$cat_list, 
		 		$area_list, 
		 		$info_list, 
		 		$rec_info, 
		 		$cat_info['cat_name'], 
		 		$bot_nav, 
		 		$cat_option, 
		 		$area_option, 
		 		$page->show(3)
		 	)
		 );

	 	$smarty->cache_lifetime = $cache_set['list'];
 	}
	if($cache_set['list_pow'])
	{
		$smarty->cache_lifetime = $cache_set['list'];
	}
	else
	{
		$smarty->caching = false;
	}
 	$smarty->display('category.htm', $cache_id);
}
/**
 * ����Ŀ������ʾ
 */
elseif($cid && $pid != 0)
{
	if(!$smarty->is_cached("category.htm", $cache_id))
	{
 		//��ǰ��Ŀ��Ϣ
 		$cat_info = $db->getone("SELECT cat_name, title, keywords, description 
 								FROM " . table('category') . " WHERE cat_id=".intval($cid));

		//С���ö���Ϣ
		if($currenpage == 1)
		{
			$sql1 = "SELECT a.post_id, a.cat_id, b.cat_name, a.title, a.content, a.lit_pic, a.click, 
							a.comment, a.pub_date, a.is_recommend, a.top_type, a.top_time 
					FROM " . table('post') . " AS a 
					LEFT JOIN ".table('category')." AS b 
					ON a.cat_id = b.cat_id 
					WHERE a.cat_id = '$cid' and a.is_check = 1 and a.top_type = 1 
							and a.top_start + a.top_time > $timestamp";

		$top_info = $db -> getall($sql1);
		}
		else
		{
			$top_info = array();
		}

		$top_info_num = count($top_info);
		if($top_info_num >= $perpage)
		{
			$limit = $perpage;
		}
		else
		{
			$limit = $perpage - $top_info_num;
		}

	 	$cat_list = read_static_cache('cat_list_1');
	 	$cat_list = $cat_list[$pid];

	 	$area_list = read_static_cache('area_list');
	 	$area_list = $area_list[$cid];
		
	 	//���Ӳ�ѯ����-����
		if(!empty($aid))
		{
			$condition = " and a.area_id = $aid ";
		}
		else
		{
			$condition = '';
		}
		$info_sql = "SELECT a.post_id, a.cat_id, b.cat_name, a.title, a.content, a.lit_pic, a.click, 
							a.comment, a.pub_date 
					FROM ".table('post')." AS a 
					LEFT JOIN " . table('category') . " AS b 
					ON a.cat_id = b.cat_id 
					WHERE a.cat_id = ".$cid.$condition." and a.is_check=1 
					ORDER BY pub_date DESC 
					LIMIT $offset,$limit";
		
	 	$info_list = $db->getall($info_sql);

	 	if(count($info_list)>0)
	 	{
			for($i = 0; $i < count($info_list); $i++)
			{
				$top_info[] = $info_list[$i];
			}
		}
		$info_list = $top_info;

	 	for($i = 0; $i < count($info_list); $i++){
	 		$info_list[$i]['sub_day'] = sub_day(time(),$info_list[$i]['pub_date']);
	 		$info_list[$i]['pub_date'] = date("Y-m-d H:i:s",$info_list[$i]['pub_date']);
	 		$info_list[$i]['cat_url'] = url_rewrite('category', array('cid'=>$info_list[$i]['cat_name']));
	 		$info_list[$i]['info_url'] = url_rewrite('post', array('id'=>$info_list[$i]['post_id']));
	 	}
		
	 	//�Ƽ���Ϣ
	 	$sql = "SELECT post_id, title 
	 			FROM ".table('post').
	 			" WHERE cat_id = ".$cid." and is_recommend=1 and rec_start + rec_time*24*3600 > $timestamp 
	 			ORDER BY pub_date DESC 
	 			LIMIT 10";
		$rec_info = $db->getall($sql);

	 	template_assign(
	 		array(
	 			'act', 
	 			'cat_info', 
	 			'cat_nav', 
	 			'add_nav_list', 
	 			'cat_list', 
	 			'area_list', 
	 			'rec_info', 
	 			'info_list', 
	 			'pre_act', 			//����·��
	 			'pre_url',			//·������
	 	 		'current_act', 
	 	 		'bot_nav', 
	 	 		'cat_option', 
	 	 		'area_option', 
	 	 		'page'
	 		), 
	 		array(
	 			'top1', 
	 			$cat_info, 
	 			$cat_nav, 
	 			$add_nav_list,
	 	 		$cat_list, 
	 	 		$area_list, 
	 	 		$rec_info, 
	 	 		$info_list, 
	 	 		get_cat_name($pid), 
	 	 		url_rewrite('category',array('cid'=>$pid)),
	 	 		$cat_info['cat_name'], 
	 	 		$bot_nav, 
	 	 		$cat_option, 
	 	 		$area_option, 
	 	 		$page->show(3)
	 	 	)
	 	 );
	 	$cache_id = "$cid.$pid.$aid";
	 	$smarty->cache_lifetime = $cache_set['list'];
 	}
	if($cache_set['list_pow'])
	{
		$smarty->cache_lifetime = $cache_set['list'];
	}
	else
	{
		$smarty->caching = false;
	}
 	$smarty->display('category.htm', $cache_id);
}


?>