<?php
/*
 * [bluecms]��Ȩ���� ��׼���磬��������Ȩ��
 * This is not a freeware, use is subject to license terms
 *
 * $Id��user.php
 * $author��lucks
 */
 define('IN_BLUE', true);
 require_once dirname(__FILE__) . '/include/common.inc.php';
 require_once dirname(__FILE__) . '/include/filter.inc.php';
 require_once BLUE_ROOT.'include/upload.class.php';
 $image = new upload();
 
 $act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'default';
 $from = !empty($_REQUEST['from']) ? $_REQUEST['from'] : '';

 $smarty->caching=false;
 if(!$_SESSION['user_id'] && !in_array($act, array('login', 'do_login', 'reg', 'do_reg', 'check_user_name', 'index_login'))) $act = 'login';

 $bot_nav = read_static_cache('bot_nav');
 $ann_cat = $db->getall("SELECT * FROM ".table('ann_cat')." ORDER BY show_order, cid");

 template_assign(array('bot_nav', 'ann_cat'), array($bot_nav, $ann_cat));

 if($act == 'default'){
 	/*$new_info = $db->getall("SELECT a.post_id, a.title, a.pub_date, b.user_name FROM ".table('post')." AS a,".table('user')." AS b WHERE a.user_id = b.user_id ORDER BY pub_date DESC LIMIT 8");
	if($new_info){
		foreach($new_info as $k => $v){
			$new_info[$k]['url'] = url_rewrite('post', array('id'=>$v['post_id']));
		}
	}*/
	$user = $db->getone("SELECT * FROM ".table('user')." WHERE user_id=".intval($_SESSION['user_id']));
	$ann_arr = get_ann(0, 8);

	template_assign(array(
				'act', 
				'user', 
				'current_act', 
				'ann_arr'
			), 
			array(
				$act, 
				$user, 
				'��Ա����',
				$ann_arr
			)
		);
 	$smarty->display('user.htm');
 }

 elseif($act == 'login'){
	
 	if($_SESSION['user_id']){
 		showmsg('���Ѿ���¼������Ҫ���µ�¼', 'user.php');
 	}
	template_assign(array('current_act', 'from'), array('��¼', $from));
	$smarty->display('login.htm');
 }

 elseif($act == 'do_login'){
 	$user_name  	= 	!empty($_POST['user_name']) ? trim($_POST['user_name']) : '';
 	$pwd 			= 	!empty($_POST['pwd']) ? trim($_POST['pwd']) : '';
 	$safecode   	= 	!empty($_POST['safecode']) ? trim($_POST['safecode']) : '';
 	$useful_time	=	intval($_POST['useful_time']);
 	$from = !empty($from) ? base64_decode($from) : 'user.php';

 	if($user_name == ''){
 		showmsg('�û�������Ϊ��');
 	}
 	if($pwd == ''){
 		showmsg('���벻��Ϊ��');
 	}
 	if($safecode == '' || strtolower($safecode) != strtolower($_SESSION['safecode'])){
 		showmsg('��֤�����');
 	}
	$row = $db->getone("SELECT COUNT(*) AS num FROM ".table('admin')." WHERE admin_name='$user_name'");
	if($row['num'] == 1){
		showmsg('ϵͳ�û��鲻�ܴ�ǰ̨��¼');
	}
	$w = login($user_name, $pwd);

	if(defined('UC_API') && @include_once(BLUE_ROOT.'uc_client/client.php')){
		list($uid, $username, $password, $email) = uc_user_login($user_name, $pwd);
		if($uid>0){
			$password = md5($password);
			if(!$w){
				$db->query("INSERT INTO ".table('user')." (user_name, pwd, email, reg_time) VALUES ('$username', '$password', '$email', '$timestamp')"); 
				$w = 1;
			}
			$ucsynlogin = uc_user_synlogin($uid);
		}
		elseif($uid === -1){
			if($w){
				$user_info = $db->getone("SELECT email FROM ".table('user')." WHERE user_name='$user_name'");
				$uid = uc_user_register($user_name, $pwd, $user_info['email']);
				if($uid > 0) $ucsynlogin = uc_user_synlogin($uid);
			}else $w = -1;
		}
	}
	if($w == -1 || $w==0){
		showmsg('��������û��������벻��ȷ');
	}
	if($w){
		update_user_info($user_name);
 		if($useful_time !=0){
 			setcookie('BLUE[user_id]', $_SESSION['user_id'], time()+$useful_time, $cookiepath, $cookiedomain);
 			setcookie('BLUE[user_name]', $user_name, time()+$useful_time, $cookiepath, $cookiedomain);
			setcookie('BLUE[user_pwd]', md5(md5($pwd).$_CFG['cookie_hash']), time()+$useful_time, $cookiepath, $cookiedomain);
 		}
		echo $ucsynlogin;
 		showmsg('��ӭ�� '.$user_name.' ���������ڽ�ת��...', $from);
	}
 }

elseif($act == 'reg')
{
	if (!empty($_SESSION['user_id']) && $_SESSION['user_id'] != 1)
	{
		showmsg('���Ѿ���¼�������˳���¼��ע��!');
	}
	if (!isset($_SESSION['last_reg']))
	{
		$_SESSION['last_reg'] = 0;
	}
	elseif ($timestamp - $_SESSION['last_reg'] < 30) 
	{
		showmsg('Ϊ��ֹ����ע�ᣬ����30�������ע�ᣡ');
	}
	template_assign(array('current_act', 'from'), array('ע�����û�', $from));
 	$smarty->display('reg.htm');
}

 elseif($act == 'do_reg'){
	$user_name 		=	!empty($_POST['user_name']) ? trim($_POST['user_name']) : '';
	$pwd       		= 	!empty($_POST['pwd']) ? trim($_POST['pwd']) : '';
	$pwd1 	   		= 	!empty($_POST['pwd1']) ? trim($_POST['pwd1']) : '';
	$email     		= 	!empty($_POST['email']) ? trim($_POST['email']) : '';
	$safecode  		= 	!empty($_POST['safecode']) ? trim($_POST['safecode']) : '';
	$from = !empty($from) ? base64_decode($from) : 'user.php';

	if(strlen($user_name) < 4 || strlen($user_name) > 16){
		showmsg('�û����ַ����Ȳ���');
	}
	if(strlen($pwd) < 6){
		showmsg('���벻������6���ַ�');
	}
	if($pwd != $pwd1){
		showmsg('�����������벻һ��');
	}
	if(strtolower($safecode) != strtolower($_SESSION['safecode'])){
		showmsg('��֤�����');
	}
	if($db->getone("SELECT * FROM ".table('user')." WHERE user_name='$user_name'")){
		showmsg('���û����Ѵ���');
	}
	if($db->getone("SELECT * FROM ".table('admin')." WHERE admin_name='$user_name'")){
		showmsg('���û����Ѵ���');
	}
	$sql = "INSERT INTO ".table('user')." (user_id, user_name, pwd, email, reg_time, last_login_time) VALUES ('', '$user_name', md5('$pwd'), '$email', '$timestamp', '$timestamp')";
	if(!$db->query($sql)){
		showmsg('���ź���ע���г�����');
	}else{
		$_SESSION['user_id'] = $db->insert_id();
		$_SESSION['user_name'] = $user_name;
		update_user_info($_SESSION['user_name']);
		setcookie('BLUE[user_id]', $_SESSION['user_id'], time()+3600, $cookiepath, $cookiedomain);
		setcookie('BLUE[user_name]', $user_name, time()+3600, $cookiepath, $cookiedomain);
		setcookie('BLUE[user_pwd]', md5(md5($pwd).$_CFG['cookie_hash']), time()+3600, $cookiepath, $cookiedomain);
		if(defined('UC_API') && @include_once(BLUE_ROOT.'uc_client/client.php'))
		{
		$uid = uc_user_register($user_name, $pwd, $email);
		if($uid <= 0)
		{
			if($uid == -1)
			{
				showmsg('�û������Ϸ���');
			}
			elseif($uid == -2)
			{
				showmsg('����������ע��Ĵ��');
			}
			elseif($uid == -3)
			{
				showmsg('��ָ�����û��� '.$user_name.' �Ѵ��ڣ���ʹ�ñ���û�����');
			}
			elseif($uid == -4){
				showmsg('��ʹ�õ�Email��ʽ���ԣ�');
			}
			elseif($uid == -5)
			{
				showmsg('��ʹ�õ�Email ������ע�ᣡ');
			}
			else
			{
				showmsg('ע��ʧ�ܣ�');
			}
		}
		else
		{
			$ucsynlogin = uc_user_synlogin($uid);
			echo $ucsynlogin;
		}
		}
		$_SESSION['last_reg'] = $timestamp;
		showmsg('��ϲ��ע��ɹ�,���ڽ�ת��...', $from);
	}
 }
 
 elseif ($act == 'news_manage') {
 	include 'include/page.class.php';
 	$perpage = 10;
 	$page = new page(array('total'=>$db->getfirst("SELECT COUNT(*) AS num FROM ".table('article')." WHERE user_id=".$_SESSION['user_id']), 'perpage'=>$perpage));
 	$currenpage=$page->nowindex;
 	$offset=($currenpage-1)*$perpage;
 	$news_list = get_news($offset, $perpage, '', FALSE, $_SESSION['user_id']);
 	template_assign(
 		array(
 			'act', 
 			'current_act', 
 			'news_list',
 			'page'
 		), 
 		array(
 			$act,
 			'��������',
 			$news_list,
 			$page->show(3)
 		)
 	);
 	$smarty->display('user.htm');
 }
 
 elseif ($act == 'add_news') {
 	include 'admin/include/common.fun.php';
	create_editor('content');
 	$cat_option = get_arc_cat(0);
 	template_assign(
 		array(
 			'act', 
 			'act1',
 			'current_act', 
 			'cat_option'
 		), 
 		array(
 			$act,
 			'add', 
 			'�������', 
 			$cat_option
 		)
 	);
 	$smarty->display('user.htm');
 }
 
 elseif ($act == 'do_add_news') {
 	include_once 'include/upload.class.php';
 	$image = new upload();
 	$title = !empty($_POST['title']) ? htmlspecialchars(trim($_POST['title'])) : '';
 	$color = !empty($_POST['color']) ? htmlspecialchars(trim($_POST['color'])) : '';
 	$cid = !empty($_POST['cid']) ? intval($_POST['cid']) : '';
 	if(empty($cid)){
 		showmsg('���ŷ��಻��Ϊ��');
 	}
 	$author = !empty($_POST['author']) ? htmlspecialchars(trim($_POST['author'])) : $_SESSION['admin_name'];
 	$source = !empty($_POST['source']) ? htmlspecialchars(trim($_POST['source'])) : '';
	$content = !empty($_POST['content']) ? filter_data($_POST['content']) : '';
	$descript = !empty($_POST['descript']) ? mb_substr($_POST['descript'], 0, 90) : mb_substr(html2text($_POST['content']),0, 90);
 	if(isset($_FILES['lit_pic']['error']) && $_FILES['lit_pic']['error'] == 0){
		$lit_pic = $image->img_upload($_FILES['lit_pic'],'lit_pic');
	}
    $lit_pic = empty($lit_pic) ? '' : $lit_pic;
	if(!empty($lit_pic)){
		$lit_pic = $image->small_img($lit_pic, 200, 115);
    }

 	if($title == ''){
 		showmsg('���ű��ⲻ��Ϊ��');
 	}
 	if($content == ''){
 		showmsg('�������ݲ���Ϊ��');
 	}
 	
 	if ($_CFG['news_is_check'] == 1) {
 		$is_check = 0;
 	} else {
 		$is_check = 1;
 	}
 	
 	$sql = "INSERT INTO ".table('article')." (id, cid, user_id, title, color, author, source, pub_date, lit_pic, 
 	descript, content, click, comment, is_recommend, is_check) VALUES ('', '$cid', '$_SESSION[user_id]', '$title', '$color', 
 	'$author', '$source', '$timestamp', '$lit_pic', '$descript', '$content', '0', '0', '0', '$is_check')";
 	$db->query($sql);
	if ($_CFG['news_is_check'] == 1) {
		showmsg('������Ϣ�ѷ����ɹ�����ȴ���˳ɹ�', 'user.php?act=news_manage');
	} else {
 		showmsg('�������ųɹ�', 'user.php?act=news_manage');
	}
 }
 
 elseif ($act == 'edit_news') {
 	if (empty($_GET['id'])) {
 		return false;
 	}
 	include 'admin/include/common.fun.php';
 	$news = $db->getone("SELECT id, cid, user_id, title, color, author, source, lit_pic, descript, content,								is_recommend 
							FROM ".table('article')." 
								WHERE id = ".intval($_GET['id']));
	create_editor('content', $news['content']);
 	if ($_SESSION['user_id'] != $news['user_id']) {
 		showmsg('�Բ�����û�б༭�������ŵ�Ȩ��', 'user.php?act=news_manage');
 	}
 	$arc_content = $edit->Value=$news['content'];
 	$cat_option = get_arc_cat(0, $news['cid']);
 	template_assign(
 		array(
 			'act',
 			'act1', 
 			'current_act', 
 			'news', 
 			'cat_option'
 		), 
 		array(
 			$act,
 			'edit', 
 			'�༭�ҷ���������', 
 			$news, 
 			$cat_option
 		)
 	);
 	$smarty->display('user.htm');
 }
 
 elseif ($act == 'do_edit_news')
 {
 	$title = !empty($_POST['title']) ? htmlspecialchars(trim($_POST['title'])) : '';
 	$color = !empty($_POST['color']) ? htmlspecialchars(trim($_POST['color'])) : '';
 	$cid = !empty($_POST['cid']) ? intval($_POST['cid']) : '';
 	if (empty($cid))
 	{
 		showmsg('���ŷ��಻��Ϊ��');
 	}
 	$author = !empty($_POST['author']) ? htmlspecialchars(trim($_POST['author'])) : $_SESSION['admin_name'];
 	$source = !empty($_POST['source']) ? htmlspecialchars(trim($_POST['source'])) : '';

 	if($_POST['lit_pic1'] && $_POST['lit_pic2'] || $_POST['list_pic2'])
 	{
 		if(isset($_FILES['lit_pic']['error']) && $_FILES['lit_pic']['error'] == 0)
 		{
		    $lit_pic = $image->img_upload($_FILES['lit_pic'],'lit_pic');
		}
	    $lit_pic = empty($lit_pic) ? '' : $lit_pic;
		if(!empty($lit_pic))
		{
			$lit_pic = $image->small_img($lit_pic, 200, 115);
	    }
 	}
 	else
 	{
 		$lit_pic = $_POST['lit_pic1'] ? $_POST['lit_pic1'] :'';
 	}
 	$content = !empty($_POST['content']) ? filter_data(trim($_POST['content'])) : '';
	$descript = !empty($_POST['descript']) ? 
	            mb_substr($_POST['descript'], 0, 90) : mb_substr(html2text($_POST['content']),0, 90);

 	if($title == '')
 	{
 		showmsg('���ű��ⲻ��Ϊ��');
 	}
 	if($content == '')
 	{
 		showmsg('�������ݲ���Ϊ��');
 	}
 	$sql = "UPDATE ".table('article').
 			" SET cid='$cid', title='$title', color='$color', author='$author', source='$source', 
 					lit_pic='$lit_pic', descript='$descript', content='$content' 
 			WHERE id=".intval($_POST['id']);
 	$db->query(($sql));
 	showmsg('�༭���ųɹ�', 'user.php?act=news_manage');
 }
 
 elseif ($act == 'del_news') {
 	if (empty($_GET['id'])) {
 		return false;
 	}
 	$news = $db->getone("SELECT user_id, lit_pic FROM ".table('article')." WHERE id=".intval($_GET['id']));
 	if ($_SESSION['user_id'] != $news['user_id']) {
 		showmsg('�Բ�����û��Ȩ��ɾ����������', 'user.php?act=news_manage');
 	}
 	$sql = "DELETE FROM ".table('article')." WHERE id=".intval($_GET['id']);
 	$db->query($sql);
 	if (file_exists(BLUE_ROOT.$news['lit_pic'])) {
 		@unlink(BLUE_ROOT.$news['list_pic']);
 	}
 	showmsg('ɾ��һ���������ųɹ�', 'user.php?act=news_manage');
 }

 elseif($act == 'manage'){
 	$sql = "SELECT post_id, title, pub_date, click, comment, useful_time FROM ".table('post').
 	" WHERE user_id = ".intval($_SESSION['user_id'])." ORDER BY pub_date DESC";
 	$myinfo = $db->getall($sql);
	if($myinfo){
		foreach($myinfo as $k => $v){
			$myinfo[$k]['url'] = url_rewrite('post', array('id'=>$v['post_id']));
		}
	}
 	template_assign(array('act', 'current_act', 'myinfo'), array('manage', '�����ҷ�������Ϣ', $myinfo));
 	$smarty->display('user.htm');
 }
 //�༭�����ķ�����Ϣ
 elseif($act == 'edit_info'){
 	$post_id = $_REQUEST['post_id'];
 	if(empty($post_id)){
 		return false;
 	}
 	$basic_info = $db->getone("SELECT post_id, cat_id, user_id, lit_pic, area_id, title, keywords, content, 
 										link_man, link_phone, link_qq, link_email, link_address, useful_time,
 										is_recommend, rec_start, rec_time, top_type, top_start, top_time  
 								FROM ".table('post').
 								" WHERE post_id = ".intval($post_id));
	if($basic_info['user_id'] != $_SESSION['user_id']){
		showmsg('��û��Ȩ�ޱ༭��ƪ����', 'index.php');
	}
 	$area_option = get_area_option(1, $basic_info['area_id']);

 	$cat_id = $basic_info['cat_id'];
 	$model_id = get_model_id($cat_id);
 	$insert_must_att = insert_must_att($model_id, true, $post_id);
 	$insert_nomust_att = insert_nomust_att($model_id, true, $post_id);

 	$parentid = get_parentid($cat_id);
 	$cat_option = get_child($parentid, $cat_id);

 	$pic_arr = $db->getall("SELECT pic_path FROM ".table('post_pic')." WHERE post_id = ".intval($post_id));
 	$pic_list = '';
 	for($i=0;$i<4;$i++){
 		if($pic_arr[$i]['pic_path']){
 			$pic_list .= "<input type=\"hidden\" name=\"pic".$i."\" value=\"".$pic_arr[$i]['pic_path']."\" />";
 		}else{
 			$pic_list .= "<input type=\"hidden\" name=\"pic".$i."\" value=\"\" />";
 		}
 	}
	$service_arr = array();
	$service_result = $db->query("SELECT service, price FROM ".table('service')." WHERE type='info' ORDER BY id");
	while ($row = $db->fetch_array($service_result)) {
		$service_arr[] = $row['price'];
	}
	$service_arr = implode(',', $service_arr);
 	template_assign(
		array(
			'act', 
			'current_act', 
			'area_option', 
			'cat_option', 
			'basic_info', 
			'insert_must_att', 
			'insert_nomust_att', 
			'pic_list',
			'service_arr'
		),
 		array(
			$act, 
			'�༭������Ϣ', 
			$area_option, 
			$cat_option, 
			$basic_info, 
			$insert_must_att, 
			$insert_nomust_att, 
			$pic_list,
			$service_arr
		)
	);
 	$smarty->display('user.htm');
 }

 //�ύ�༭��ķ�����Ϣ
 elseif($act == 'do_info_edit'){
	 $post_id = intval($_REQUEST['post_id']);
	 if(empty($post_id)){
		 return false;
	 }
 	$must_att_arr = array();
 	$nomust_att_arr = array();
 	$title = !empty($_POST['title']) ? trim($_POST['title']) : '';
 	if($title == ''){
 		showmsg('��Ϣ���ⲻ��Ϊ��');
 	}
 	$cat_id = !empty($_POST['cat_id']) ? trim($_POST['cat_id']) : '';
 	$area = !empty($_POST['area']) ? intval($_POST['area']) : '';
 	$useful_time = intval($_POST['useful_time']);
 	$content = !empty($_POST['content']) ? trim($_POST['content']) : '';
 	if(!empty($content)){
		$content = str_replace(' ', '&nbsp;', str_replace(array("\r\n", "\r", "\n"), "<br/>", $content));
	}
 	
 	$is_recommend	= !empty($_POST['is_recommend']) ? intval($_POST['is_recommend']) : 0;
 	if($_POST['is_recommend1'] == 0){
	 	if($is_recommend == 1){
			$confirm_rec = 1;
	 		$rec_start	= $timestamp;
	 		$rec_time	= $_POST['rec_time'];
	 		if(!preg_match('/^[1-9][0-9]*$/', $rec_time)){
	 			showmsg('�Ƽ�ʱ���ʽ����');
	 		}
	 		$condition	= " ,rec_start='$rec_start', rec_time='$rec_time' ";
	 	} else {
	 		$rec_time	= 0;
	 		$condition	= '';
	 	}
 	} else {
 		$rec_time	= 0;
 		$condition	= '';
 	}
 	$top_type		= intval($_POST['top_type']);
 	if($_POST['top_type1'] == 0){
 		if($top_type != 0){
			$confirm_top = 1;
 			$top_start	= $timestamp;
 			$top_time	= $_POST['top_time'];
 			if(!preg_match('/^[1-9][0-9]*$/', $top_time)){
	 			showmsg('�ö�ʱ���ʽ����');
	 		}
 			$condition	.= ",top_start='$top_start', top_time='$top_time' ";
 		} else {
 			$top_time	= 0;
 			$condition .= '';
 		}
 	} else {
 		$top_time	= 0;
 		$condition	.= '';
 	}
	$is_head_line = intval($_POST['is_head_line']);
	if($_POST['is_head_line1'] == 0){
	 	if($is_head_line == 1){
			$confirm_head = 1;
	 		$head_line_start	= $timestamp;
	 		$head_line_time	= $_POST['head_line_time'];
	 		if(!preg_match('/^[1-9][0-9]*$/', $head_line_time)){
	 			showmsg('�Ƽ�ʱ���ʽ����');
	 		}
	 		$condition	.= " ,head_line_start='$head_line_start', head_line_time='$head_line_time' ";
	 	} else {
	 		$head_line_time	= 0;
	 		$condition	.= '';
	 	}
 	} else {
 		$head_line_time	= 0;
 		$condition	.= '';
 	}

 	$link_man = !empty($_POST['link_man']) ? trim($_POST['link_man']) : '';
 	$link_phone = !empty($_POST['link_phone']) ? trim($_POST['link_phone']) : 0;
 	$link_email = !empty($_POST['link_email']) ? trim($_POST['link_email']) : '';
 	$link_qq = !empty($_POST['link_qq']) ? trim($_POST['link_qq']) : 0;
 	$link_address = !empty($_POST['link_address']) ? trim($_POST['link_address']) : '';
	
 	if($link_man==''){
 		showmsg('��ϵ����������Ϊ��');
 	}
 	if($link_phone==''){
 		showmsg('Ϊ��������Ϣ��ʵ����ϵ�绰��ҪΪ��');
 	}
 	$must_att_arr = get_att($model_id, $_POST['att1'], 'must_att');
 	$nomust_att_arr = get_att($model_id, $_POST['att2']);
 	
 	//���׹���
 	$rec_service = $db->getone("SELECT id, price FROM ".table('service')." WHERE type='info' and service='rec'");
	if($top_type == 1){
		$service = 'top1';
	} else {
		$service = 'top2';
	}
 	$top_service = $db->getone("SELECT id, price FROM ".table('service')." WHERE type='info' and service='$service'");

	$head_line_service = $db->getone("SELECT id, price FROM ".table('service')." WHERE type='info' and service='head_line'");
	$money = $_SESSION['money'] - $rec_service['price'] * $rec_time - $top_service['price'] * $top_time - $head_line_service['price'] * $head_line_time;
	if ($money < 0){
		showmsg('�Բ����������㣬���ֵ');
	}
	if	($confirm_rec == 1) {
		$db->query("INSERT INTO ".table('buy_record')." (id, user_id, aid, pid, exp, time) 
		VALUES ('', '$_SESSION[user_id]', '$post_id', '$rec_service[id]', '$rec_time', '$timestamp'");
	}
	if ($confirm_top == 1) {
		$db->query("INSERT INTO ".table('buy_record')." (id, user_id, aid, pid, exp, time)
		VALUES ('', '$_SESSION[user_id]', '$post_id', '$top_service[id]', '$top_time', '$timestamp'");
	}
	if ($confirm_head == 1) {
		$db->query("INSERT INTO ".table('buy_record')." (id, user_id, aid, pid, exp, time)
		VALUES ('', '$_SESSION[user_id]', '$post_id', '$top_service[id]', '$head_line_time', '$timestamp'");
	}
	//���û��˻��۳����ѽ��
	$db->query("UPDATE ".table('user')." SET money='$money' WHERE user_id=$_SESSION[user_id]");
 	
	//����post��SQL���
 	$sql = "UPDATE ".table('post')." SET cat_id='$cat_id', area_id='$area', title='$title', 
 	keywords='$keywords', content='$content', link_man='$link_man', link_phone='$link_phone', 
 	is_recommend='$is_recommend', top_type='$top_type', is_head_line='$is_head_line' ".$condition.", link_email='$link_email', 
 	link_qq='$link_qq', link_address='$link_address', useful_time='$useful_time' WHERE post_id=".$post_id;
 	$db->query($sql);
 	
 	//����������
 	$db->query("DELETE FROM ".table('post_att')." WHERE post_id =".$post_id);
 	insert_att_value($must_att_arr, $post_id);
 	insert_att_value($nomust_att_arr, $post_id);
 	
 	//������ͼƬ
	$db->query("DELETE FROM ".table('post_pic')." WHERE post_id = ".$post_id);
 	for($i=0;$i<4;$i++){
 		if($_POST['pic'.$i] && file_exists(BLUE_ROOT.$_POST['pic'.$i])){
 			$sql = "INSERT INTO ".table('post_pic')." (pic_id, post_id, pic_path) VALUES ('', '$post_id', '".$_POST['pic'.$i]."')";
 			$db->query($sql);
 		}
 	}
	//���û��ͼƬ������Ϣ����ͼ����ΪĬ��ͼƬ
	if (file_exists(BLUE_ROOT.$_POST['lit_pic'])) {
		@unlink(BLUE_ROOT.$_POST['lit_pic']);
	}
	if($_POST['pic0']){
		
		$lit_pic = $image->small_img($_POST['pic0'], 126, 80);
		$db->query("UPDATE ".table('post')." SET lit_pic='$lit_pic' WHERE post_id='$post_id'");
	}else{
		$db->query("UPDATE ".table('post')." SET lit_pic='' WHERE post_id='$post_id'");
	}
	
 	showmsg('�༭��Ϣ�ɹ�', 'user.php?act=manage');
 }

 elseif($act == 'del'){
	 $post_id = intval($_REQUEST['post_id']);
	 if(empty($post_id)){
		 return false;
	 }
	 $info = $db->getone("SELECT user_id FROM ".table('post')." WHERE post_id =".$post_id);
	 if($_SESSION['user_id'] != $info['user_id']){
		 showmsg('��û��Ȩ��ɾ��������', 'index.php');
	 }
 	$db->query("DELETE FROM ".table('post')." WHERE post_id = ".$post_id);
 	$db->query("DELETE FROM ".table('post_att')." WHERE post_id = ".$post_id);
 	$pic_arr = $db->getall("SELECT pic_path FROM ".table('post_pic')." WHERE post_id = ".$post_id);
 	if($pic_arr){
		foreach($pic_arr as $v){
 			if(file_exists(BLUE_ROOT . $v['pic_path'])){
 				unlink(BLUE_ROOT . $v['pic_path']);
 			}
 		}
	}
 	$db->query("DELETE FROM ".table('post_att')." WHERE post_id = ".$post_id);
 	showmsg('ɾ����Ϣ�ɹ�', 'user.php?act=manage');
 }
 
 /*��Ա�˻�����*/
 elseif($act == 'account_manage'){
 	$total_money = '';
 	$result = $db->query("SELECT * FROM ".table('card_order')." WHERE user_id=".$_SESSION['user_id']." ORDER BY time DESC");
 	while ($row = $db->fetch_array($result)) {
 		$card_order_list[] = $row;
 		if ($row['is_pay'] == 1)
 		$total_money += $row['value'];
 	}
	$total_money = !empty($total_money) ? $total_money : 0;
 	$sql = "SELECT a.exp*c.price AS total_price, a.time, b.title, c.name FROM (".table('buy_record')." AS a LEFT JOIN ".table('post').
 	" AS b ON a.aid=b.post_id) LEFT JOIN ".table('service')." AS c ON a.pid=c.id WHERE a.user_id=$_SESSION[user_id]";
 	$pay_list = $db->getall($sql);
 	template_assign(
 		array(
 			'act', 
 			'current_act',
 			'money',
 			'total_money',
 			'pay_money', 
 			'card_order_list',
 			'pay_list'
 		), 
 		array(
 			$act, 
 			'��Ա���׹���', 
 			$_SESSION['money'],
 			$total_money,
 			$total_money - $_SESSION['money'],
 			$card_order_list,
 			$pay_list
 		)
 	);
 	$smarty->display('user.htm');	
 }

 elseif($act == 'buy'){
	 $card_list = $db->getall("SELECT * FROM ".table('card_type')." WHERE is_close=0");
	 template_assign(array('act', 'current_act', 'card_list'), array($act, '�����ֵ��', $card_list));
	 $smarty->display('user.htm');
 }

 elseif($act == 'do_buy'){
	 include_once(BLUE_ROOT.'data/pay.cache.php');
	 $id = !empty($_POST['id']) ? intval($_POST['id']) : '';
	 if(empty($id)){
		 showmsg('����û��ѡ����Ҫ����ĳ�ֵ��');
	 }
	 $card_order = $db->getone("SELECT time FROM ".table('card_order')." WHERE user_id=".$_SESSION['user_id']);
	 if($timestamp-$card_order['time'] < 60){
		 showmsg('Ϊ�˷�ֹˢ���ظ��ύ������1���Ӻ��������');
	 }
	 $card = $db->getone("SELECT name, value, price FROM ".table('card_type')." WHERE id=".$id);
	 $order_id = 'B'.$timestamp.'E';

	 $sql = "INSERT INTO ".table('card_order')." (id, user_id, order_id, name, value, price, time, 
	 is_pay) VALUES ('', '$_SESSION[user_id]', '$order_id', '$card[name]', '$card[value]', '$card[price]', 
	 '$timestamp', 0)";
	 if(!$db->query($sql)){
		 showmsg('���ݿ���������³���');
	 }

	 $new_pay = array();
	 for($i=0; $i < count($data) ; $i++){
	 	if($data[$i]['is_open'] == 1){
	 		$new_pay[] = $data[$i];
	 	}
	 }
	 template_assign(array(
	 					'act', 
	 					'current_act', 
	 					'order_id', 
	 					'name', 
	 					'value',
	 					'price', 
	 					'pay_list'
	 				), 
	 				 array(
	 				 	$act, 
	 				 	'��ѡ��֧����ʽ', 
	 				 	$order_id,
	 				 	$card['name'],
	 				 	$card['value'],
	 				 	$card['price'],
	 				 	$new_pay
	 				 )
	 );
	 $smarty->display('user.htm');
 }
 
 elseif ($act == 'pay'){
 	include 'data/pay.cache.php';
 	$price = $_POST['price'];
 	$id = $_POST['id'];
 	$name = $_POST['name'];
 	if (empty($_POST['pay'])) {
 		showmsg('�Բ�����û��ѡ��֧����ʽ');
 	}
 	include 'include/payment/'.$_POST['pay']."/index.php";
 }

 //�ҵĸ�������
 elseif($act == 'my_info'){
	$sql = "SELECT * FROM ".table('user')." WHERE user_id=".intval($_SESSION['user_id']);
	$user = $db->getone($sql);
	if($user['user_id'] != $_SESSION['user_id']){
		return false;
	}
	template_assign(array('act', 'user', 'bot_nav', 'current_act'), array($act, $user, $bot_nav, '��Ա��������'));
	$smarty->display('user.htm');
 }
 //�༭��������
 elseif($act == 'edit_user_info'){
	 $user_id = intval($_SESSION['user_id']);
	 if(empty($user_id)){
		 return false;
	 }
	$birthday = trim($_POST['birthday']);
	$sex = intval($_POST['sex']);
    $email = !empty($_POST['email']) ? trim($_POST['email']) : '';
    $msn = !empty($_POST['msn']) ? trim($_POST['msn']) : '';
    $qq = !empty($_POST['qq']) ? trim($_POST['qq']) : '';
    $mobile_phone = !empty($_POST['mobile_phone']) ? trim($_POST['mobile_phone']) : '';
    $office_phone = !empty($_POST['office_phone']) ? trim($_POST['office_phone']) : '';
    $home_phone   = !empty($_POST['home_phone']) ? trim($_POST['home_phone']) : '';
	$address = !empty($_POST['address']) ? htmlspecialchars($_POST['address']) : '';

	if (!empty($_POST['face_pic1'])){
        if (strpos($_POST['face_pic1'], 'http://') != false && strpos($_POST['face_pic1'], 'https://') != false){
           showmsg('ֻ֧�ֱ�վ���·����ַ');
         }
        else{
           $face_pic = trim($_POST['face_pic1']);
        }
    }else{
		if(file_exists(BLUE_ROOT.$_POST['face_pic3'])){
			@unlink(BLUE_ROOT.$_POST['face_pic3']);
		}
	}

	if(isset($_FILES['face_pic2']['error']) && $_FILES['face_pic2']['error'] == 0){
		$face_pic = $image->img_upload($_FILES['face_pic2'],'face_pic');
	}
    $face_pic = empty($face_pic) ? '' : $face_pic;

	$sql = "UPDATE ".table('user')." SET birthday = '$birthday', sex = '$sex', face_pic = '$face_pic', email = '$email', msn = '$msn', qq = '$qq'," .
			" mobile_phone = '$mobile_phone', office_phone = '$office_phone', home_phone = '$home_phone', address='$address' WHERE user_id = ".intval($_SESSION['user_id']);
	$db->query($sql);
	showmsg('���¸������ϳɹ�', 'user.php');
 }

 elseif($act == 'edit_pwd'){
	 if(!isset($_SESSION['user_id'])){
		 showmsg('����û�е�¼', 'user.php?act=login');
	 }
 	template_assign(array('act', 'current_act'), array($act, '��Ա�����޸�'));
 	$smarty->display('user.htm');
 }

 elseif($act == 'do_edit_pwd'){
 	$old_pwd 		= 	!empty($_POST['old_pwd']) ? trim($_POST['old_pwd']) : '';
 	$new_pwd 		= 	!empty($_POST['new_pwd']) ? trim($_POST['new_pwd']) : '';
 	$confirm_pwd 	= 	!empty($_POST['confirm_pwd']) ? trim($_POST['confirm_pwd']) : '';
 	if(strlen($new_pwd) < 6 || strlen($confirm_pwd) < 6){
 		showmsg('�����롢ȷ�����볤�ȶ���������6λ');
 	}
 	if($new_pwd != $confirm_pwd){
 		showmsg('�������������벻һ��');
 	}
 	if(!check_user($_SESSION['user_id'], $old_pwd)){
 		showmsg('�������ԭ���벻��ȷ');
 	}
	if(defined('UC_API') && @include_once(BLUE_ROOT.'uc_client/client.php')){
		$ucresult = uc_user_edit($_SESSION['user_name'], $old_pwd, $new_pwd, '');
		if($ucresult>0){
			echo $ucresult;
		}elseif($ucresult == -1){
			showmsg('�����벻��ȷ');
		}
	}
 	edit_pwd($_SESSION['user_id'], $new_pwd);
 	if($_COOKIE['BLUE']['user_pwd'] != md5(md5($new_pwd).$_CFG['cookie_hash'])){
 		$_COOKIE['BLUE']['user_pwd'] = md5(md5($new_pwd).$_CFG['cookie_hash']);
 	}
 	showmsg('�޸�����ɹ�', 'user.php');
 }

 elseif($act == 'logout'){
 	$_SESSION['user_id'] = '';
 	$_SESSION['user_name'] = '';
	$_SESSION['last_login_time'] = '';
	$_SESSION['last_login_ip'] = '';
	setcookie('BLUE[user_id]', '', time()-3600, $cookiepath, $cookiedomain);
	setcookie('BLUE[user_name]', '', time()-3600, $cookiepath, $cookiedomain);
	setcookie('BLUE[user_pwd]', '', time()-3600, $cookiepath, $cookiedomain);
	if(defined('UC_API') && @include_once(BLUE_ROOT.'uc_client/client.php')){
		$ucsynlogin = uc_user_synlogout();
		echo $ucsynlogin;
	}
	showmsg('ע����¼�ɹ�', 'index.php');
 }

 /*Ajax*/
 elseif($act == 'check_user_name'){
 	$user_name = !empty($_GET['user_name']) ? trim($_GET['user_name']) : '';
 	if(check_user_name($user_name)){
 		echo "<span style='color:red'>���û����Ѵ���</span>";
 	}else{
 		echo "<span style='color:#006CCE'>���û�������ʹ��</span>";
 	}
 }
 elseif($act == 'index_login'){
 	$user_name = !empty($_REQUEST['user_name']) ? trim($_REQUEST['user_name']) : '';
 	$pwd = !empty($_REQUEST['pwd']) ? trim($_REQUEST['pwd']) : '';
 	$remember = isset($_REQUEST['remember']) ? intval($_REQUEST['remember']) : 0;
 	if($user_name == ''){
 		showmsg('�û�������Ϊ��');
 	}
 	if($pwd == ''){
 		showmsg('���벻��Ϊ��');
 	}
	$row = $db->getone("SELECT COUNT(*) AS num FROM ".table('admin')." WHERE admin_name='$user_name'");
	if($row['num'] == 1){
		showmsg('ϵͳ�û��鲻�ܴ�ǰ̨��¼');
	}
	$w = login($user_name, $pwd);

	if(defined('UC_API') && @include_once(BLUE_ROOT.'uc_client/client.php')){
		list($uid, $username, $password, $email) = uc_user_login($user_name, $pwd);
		if($uid>0){
			$password = md5($password);
			if(!$w){
				$db->query("INSERT INTO ".table('user')." (user_name, pwd, email, reg_time) VALUES ('$username', '$password', '$email', '$timestamp')"); 
				$w = 1;
			}
			$ucsynlogin = uc_user_synlogin($uid);
		}
		elseif($uid === -1){
			if($w == 1){
				$user_info = $db->getone("SELECT email FROM ".table('user')." WHERE user_name='$user_name'");
				$uid = uc_user_register($user_name, $pwd, $user_info['email']);
				if($uid > 0) $ucsynlogin = uc_user_synlogin($uid);
			}else $w = -1;
		}
		elseif($uid == -2){
			showmsg('�������');
		}
		echo $ucsynlogin;
	}
	if($w == -1 || $w == 0){
		showmsg('��������û��������벻��ȷ');
	}
	elseif($w == 1){
		update_user_info($user_name);
 		if($remember==1){
 			setcookie('BLUE[user_id]', $_SESSION['user_id'], time()+172800, $cookiepath, $cookiedomain);
 			setcookie('BLUE[user_name]', $user_name, time()+172800, $cookiepath, $cookiedomain);
			setcookie('BLUE[user_pwd]', md5(md5($pwd).$_CFG['cookie_hash']), time()+172800, $cookiepath, $cookiedomain);
 		}
 		showmsg('��ӭ�� '.$user_name.' ���������ڽ�ת����Ա����', 'user.php');
 	}
 }
 
 elseif($act == 'upload') {
	 template_assign();
	 $smarty->display('upload_c_photo.htm');
 }

 elseif($act == 'do_upload') {
	 if(isset($_FILES['upload_file']['error']) && $_FILES['upload_file']['error'] == 0) {
		 $upload_pic = $image->img_upload($_FILES['upload_file'], 'company');
	 }
	 $smarty->assign('add_pic', $upload_pic);
	 $smarty->display('upload_c_photo.htm');
 }
 
 elseif ($act == 'check_price'){
 	$type = $_GET['type'];
 	$service = $_GET['service'];
 	$exp = $_GET['exp'];

 	if (!preg_match('/^[1-9][0-9]*$/', $exp)){
 		echo "<span style='color:red'>������ĸ�ʽ����</span>";
 		exit;
 	}
 	$service_price = $db->getone("SELECT price FROM ".table('service')." WHERE type='$type' and service='$service'");
	$money = $_SESSION['money'] - $service_price['price'] * $exp;
 	if($money < 0)
 		echo "<span style='color:red'>��Ľ�Ҳ�������</span>";
 	else 
 		echo "<span style='color:#006CCE;'>��Ľ�һ��ܳ�ԣ��</span>";
 }

elseif($act == 'get_price') {
	$service_list = array();
	$result = $db->query("SELECT * FROM ".table('service'));
	while($row = $db->fetch_array($result)) {
		$service_list[$row['service']] = $row['price'];
	}
}

elseif($act == 'del_pic'){
 	$id = $_REQUEST['id'];
 	$db->query("DELETE FROM ".table('company_image')." WHERE path='$id'");
 	if(file_exists(BLUE_ROOT.$id)){
 		@unlink(BLUE_ROOT.$id);
 	}
 }
 

?>
