<?php
/*
 * [bluecms]��Ȩ���� ��׼���磬��������Ȩ��
 * This is not a freeware, use is subject to license terms
 *
 * $Id��cache.fun.php
 * $author��lucks
 */
 if(!defined('IN_BLUE'))
 {
 	die('Access Denied!');
 }

 //���»���
function update_data_cache()
{
	global $db;
 	
 	//վ�����û���
 	$config = $db->getall("SELECT * FROM ".table('config'));

 	if(is_array($config))
 	{
 		foreach($config as $k => $v)
 		{
 			$config_arr[$v[name]] = $v[value];
 		}
 	}
 	write_static_cache('config.cache', deep_stripslashes($config_arr));
 	
 	$model_list = $db->getall("SELECT * FROM ".table('model'));
	if(is_array($model_list))
	{
		foreach($model_list as $k => $v)
		{
		    $cache_arr[$v['model_id']]['must'] = insert_must_att($v['model_id']);
		    $cache_arr[$v['model_id']]['nomust'] = insert_nomust_att($v['model_id']);
		}
	}
 	write_static_cache('model', $cache_arr);
 	//������Ŀ
 	$cat_list_0 = cat_nav();
 	for($i=0;$i<count($cat_list_0);$i++)
 	{
 		$cat_list_0[$i]['url'] = url_rewrite('category', array('cid'=>$cat_list_0[$i]['cat_id']));
 	}
 	write_static_cache('cat_list_0', $cat_list_0);
 	//�Ӽ���Ŀ
	if($cat_list_0)
	{
		reset($cat_list_0);
		foreach($cat_list_0 as $k => $v)
		{
			$cat_list = $db->getall("SELECT cat_id, cat_name FROM ".table('category')." WHERE parentid = '$v[cat_id]'");
			if(is_array($cat_list))
			{
				foreach($cat_list as $k1 => $v1){
					$v1['url'] = url_rewrite('category', array('cid'=>$v1['cat_id']));
					$cat_list_1[$v['cat_id']][] = $v1;
				}
			}
 		}
	}
 	write_static_cache('cat_list_1', $cat_list_1);

	//���ŷ���
	$arc_cat_list = $db->getall("SELECT cat_id, cat_name FROM ".table('arc_cat')." WHERE parent_id=0 ORDER BY show_order");
	if(is_array($arc_cat_list))
	{
		foreach($arc_cat_list as $k=>$v)
		{
			$arc_cat_list[$k]['url'] = url_rewrite('news_cat', array('cid'=>$v['cat_id']));
		}
	}
	write_static_cache('arc_cat_list', $arc_cat_list);
 	//��Ŀoption
 	write_static_cache('cat_option0', get_option(1));
 	//����option
 	write_static_cache('cat_option1', get_option(0));

 	//�����б�
 	$cat_arr = $db->getall("SELECT cat_id FROM ".table('category'));
	if(is_array($cat_arr))
	{
		foreach($cat_arr as $k => $v)
		{
 		    $area_list[$v['cat_id']] = get_area_list($v['cat_id']);
 		}
	}
 	write_static_cache('area_list', $area_list);

	//�����Զ��嵼��
	write_static_cache('add_nav', add_nav_list());

 	//�ײ�����
 	$bot_nav = bot_nav();
 	write_static_cache('bot_nav', $bot_nav);

 	//��ҳ����
 	write_static_cache('ann', get_ann(0, 5));

 	//��ҳ�Ƽ���Ϣ
 	write_static_cache('index_rec', get_rec_info(8));

 	//��ҳ�绰���λ
 	write_static_cache('phone_ad', ad_phone_list());

 	//��ҳ��������
 	write_static_cache('friend_link_text', $db->getall("SELECT * FROM ".table('link')." WHERE linklogo = '' ORDER BY showorder"));
 	write_static_cache('friend_link_img', $db->getall("SELECT * FROM ".table('link')." WHERE linklogo <> '' ORDER BY showorder"));

}

 //����ģ�建��
function update_tpl_cache()
{
 	global $smarty;
 	$smarty->clear_all_cache();
}

function write_to_file($file, $content)
{
 	if(file_exists($file))
 	{
 		showmsg('д�ļ�����Ŀ���ļ�������');
 	}
 	if(is_writable($file))
 	{
 		showmsg('Ŀ���ļ�����д');
 	}
	if (!$fp = @fopen($file, 'w'))
	{
        showmsg("���ܴ��ļ� $file");
    }
    if (@fwrite($fp, $file) === FALSE)
    {
       showmsg("����д�뵽�ļ� $filename");
    }
    @fclose($fp);
}

 //д�뻺���ļ�
function write_static_cache($cache_name, $caches)
{
    $cache_file_path = BLUE_ROOT . 'data/' . $cache_name . '.php';
    $content = "<?php\r\n";
    $content .= "\$data = " . var_export($caches, true) . ";\r\n";
    $content .= "?>";
	$fp = @fopen($cache_file_path, 'wb+');
	if (!$fp)
	{
		showmsg('�򿪻����ļ�ʧ��');
	}
	if (!@fwrite($fp, $content))
	{
		showmsg('д�뻺���ļ�ʧ��');
	}
	@fclose($fp);
}

 //�������ļ�
function read_static_cache($cache_name)
{
    static $result_arr = array();
    if (!empty($result_arr[$cache_name]))
    {
        return $result_arr[$cache_name];
    }
    $cache_file_path = BLUE_ROOT . 'data/' . $cache_name . '.php';
    if (file_exists($cache_file_path))
    {
        include_once($cache_file_path);
        $result_arr[$cache_name] = $data;
        return $result_arr[$cache_name];
    }
    else
    {
        return false;
    }
}


?>
