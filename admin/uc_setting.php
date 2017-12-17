<?php
define('IN_BLUE', true);

require_once(dirname(__FILE__) . '/include/common.inc.php');
if(file_exists(BLUE_ROOT.'uc_client/client.php'))
{
	if(!defined('UC_API')) define('UC_API', '');
	require_once(BLUE_ROOT.'uc_client/client.php');
}
else
{
	showmsg('�����������ļ�������');
}
$act = !empty($_REQUEST['act']) ? $_REQUEST['act'] : 'show';

if(!function_exists('file_put_contents')){ function file_put_contents($filename, $content)
{
	$fp = @fopen($filename, 'w');
	@fwrite($fp, $content);
	@fclose($fp);
	return true;
}}

if($act == 'install'){
	$uc_config['uc_api'] = $_POST['uc_api'];
	if(empty($uc_config['uc_api']) || !preg_match("/^(http:\/\/)/i", $uc_config['uc_api'])){
		showmsg('����˵�ַ������http://��ͷ');
	} else {
		if(!$_POST['uc_ip']){
			$temp = @parse_url($uc_config['uc_api']);
			$uc_config['uc_api'] = gethostbyname($temp['host']);
			if(ip2long($uc_config['uc_api']) == -1 || ip2long($uc_config['uc_api']) === FALSE){
				$uc_config['uc_ip'] = '127.0.0.1';
			}
		}
	}
	if(!isset($_POST['uc_admin_pwd']) || empty($_POST['uc_admin_pwd'])){
		showmsg('����дuc��ʼ������');
	}
	$uc_info = uc_open($uc_config['uc_api'].'/index.php?m=app&a=ucinfo', 500, '', '', 1, $uc_config['uc_ip']);
	list($status, $ucversion, $ucrelease, $uccharset, $ucdbcharset, $apptypes) = explode('|', $uc_info);
		
	if($status != 'UC_STATUS_OK'){
		showmsg('uc����˵�ַ��Ч,����ϸ�������װ��uc����˵�ַ');
	}else{
		$ucdbcharset = strtolower($ucdbcharset ? str_replace('-', '', $ucdbcharset) : $ucdbcharset);
		if(UC_CLIENT_VERSION > $ucversion){
			showmsg('uc����˰汾��һ��,����ǰ��uc�ͷ��˰汾Ϊ:'.UC_CLIENT_VERSION.',������˰汾Ϊ:'.$ucversion.'!');
		}
		elseif($ucdbcharset != 'gbk'){
			showmsg('uc����˱�����BlueCMS���벻һ��!Ҫ������uc����˱���Ϊ:gbk����.');
		}
		//��ǩӦ��ģ��
		$app_tagtemplates = 'apptagtemplates[template]='.urlencode('<a href="{url}" target="_blank">{title}</a>').'&'.
		'apptagtemplates[fields][title]='.urlencode('����').'&'.
		'apptagtemplates[fields][writer]='.urlencode('����').'&'.
		'apptagtemplates[fields][pubdate]='.urlencode('ʱ��').'&'.
		'apptagtemplates[fields][url]='.urlencode('��ַ');
			
		$postdata = 'm=app&a=add&ucfounder=&ucfounderpw='.urlencode($_POST['uc_admin_pwd']).'&apptype=OTHER&appname='.urlencode($_CFG['site_name']).'&appurl='.urlencode($_CFG['site_url']).'&appip=&appcharset=gbk&appdbcharset=gbk&'.$app_tagtemplates.'&release='.UC_CLIENT_RELEASE;
		
		$ucconfig = uc_open($_POST['uc_api'].'/index.php', 500, $postdata, '', 1, $_POST['uc_ip']);
		//echo $ucconfig;
			
		if(strstr($ucconfig,'<?xml')){
			$temp = explode('<?xml', $ucconfig);
			$ucconfig = $temp[0]; unset($temp);
		}
		
		if(empty($ucconfig)){
			showmsg('����д��Ч��������Ϣ');
		}
		elseif($ucconfig == '-1'){
			showmsg('��ʼ���������');
		}
		else{
			list($appauthkey, $appid) = explode('|', $ucconfig);
			if(empty($appauthkey) || empty($appid)){
				showmsg('���ݻ�ȡʧ��');
			}
			elseif($succeed = uc_write_config($ucconfig."|".$uc_config['uc_api']."|".$uc_config['uc_ip'],BLUE_ROOT.'data/config.php')){
				showmsg('��װ�ɹ�', 'uc_setting.php');
			}else{
				showmsg('д����������ʧ��!'.BLUE_ROOT.'data/config.php'.' �����ÿ�дȨ��!');
			}
		}
	}
}
elseif($act == 'show'){
	uc_show();
	
}
elseif($act == 'edit'){
	uc_edit($_POST['uc_config']);
}



function uc_open($url, $limit = 0, $post = '', $cookie = '', $bysocket = FALSE, $ip = '', $timeout = 15, $block = true)
{
	$return = '';
	$matches = parse_url($url);
	$host = $matches['host'];
	$path = $matches['path'] ? $matches['path'].($matches['query'] ? '?'.$matches['query'] : '') : '/';
	$port = !empty($matches['port']) ? $matches['port'] : 80;
	if($post)
	{
		$out = "POST $path HTTP/1.0\r\n";
		$out .= "Accept: */*\r\n";
		$out .= "Accept-Language: zh-cn\r\n";
		$out .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$out .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
		$out .= "Host: $host\r\n";
		$out .= 'Content-Length: '.strlen($post)."\r\n";
		$out .= "Connection: Close\r\n";
		$out .= "Cache-Control: no-cache\r\n";
		$out .= "Cookie: $cookie\r\n\r\n";
		$out .= $post;
	}else{
		$out = "GET $path HTTP/1.0\r\n";
		$out .= "Accept: */*\r\n";
		$out .= "Accept-Language: zh-cn\r\n";
		$out .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
		$out .= "Host: $host\r\n";
		$out .= "Connection: Close\r\n";
		$out .= "Cookie: $cookie\r\n\r\n";
	}

	$fp = @fsockopen(($host ? $host : $ip), $port, $errno, $errstr, $timeout);
	if(!$fp)
	{
		return '';
	}else{
		stream_set_blocking($fp, $block);
		stream_set_timeout($fp, $timeout);
		@fwrite($fp, $out);
		$status = stream_get_meta_data($fp);
		if(!$status['timed_out'])
		{
			while (!feof($fp))
			{
				if(($header = @fgets($fp)) && ($header == "\r\n" ||  $header == "\n"))
				{
					break;
				}
			}

			$stop = false;
			while(!feof($fp) && !$stop)
			{
				$data = fread($fp, ($limit == 0 || $limit > 8192 ? 8192 : $limit));
				$return .= $data;
				if($limit)
				{
					$limit -= strlen($data);
					$stop = $limit <= 0;
				}
			}
		}
		@fclose($fp);
		return $return;
	}
}

function uc_write_config($config, $file)
{
	$success = false;
	list($appauthkey, $appid, $ucdbhost, $ucdbname, $ucdbuser, $ucdbpw, $ucdbcharset, $uctablepre, $uccharset, $ucapi, $ucip) = explode('|', $config);
		
	if($content = file_get_contents($file))
	{
		$content = trim($content);
		$content = substr($content, -2) == '?>' ? substr($content, 0, -2) : $content;
		$link = mysql_connect($ucdbhost, $ucdbuser, $ucdbpw, 1);
		$uc_connnect = $link && mysql_select_db($ucdbname, $link) ? 'mysql' : '';
		$content = uc_insert_config($content, "/define\('UC_CONNECT',\s*'.*?'\);/i", "define('UC_CONNECT', '$uc_connnect');");
		$content = uc_insert_config($content, "/define\('UC_DBHOST',\s*'.*?'\);/i", "define('UC_DBHOST', '$ucdbhost');");
		$content = uc_insert_config($content, "/define\('UC_DBUSER',\s*'.*?'\);/i", "define('UC_DBUSER', '$ucdbuser');");
		$content = uc_insert_config($content, "/define\('UC_DBPW',\s*'.*?'\);/i", "define('UC_DBPW', '$ucdbpw');");
		$content = uc_insert_config($content, "/define\('UC_DBNAME',\s*'.*?'\);/i", "define('UC_DBNAME', '$ucdbname');");
		$content = uc_insert_config($content, "/define\('UC_DBCHARSET',\s*'.*?'\);/i", "define('UC_DBCHARSET', '$ucdbcharset');");
		$content = uc_insert_config($content, "/define\('UC_DBTABLEPRE',\s*'.*?'\);/i", "define('UC_DBTABLEPRE', '`$ucdbname`.$uctablepre');");
		$content = uc_insert_config($content, "/define\('UC_DBCONNECT',\s*'.*?'\);/i", "define('UC_DBCONNECT', '0');");
		$content = uc_insert_config($content, "/define\('UC_KEY',\s*'.*?'\);/i", "define('UC_KEY', '$appauthkey');");
		$content = uc_insert_config($content, "/define\('UC_API',\s*'.*?'\);/i", "define('UC_API', '$ucapi');");
		$content = uc_insert_config($content, "/define\('UC_CHARSET',\s*'.*?'\);/i", "define('UC_CHARSET', '$uccharset');");
		$content = uc_insert_config($content, "/define\('UC_IP',\s*'.*?'\);/i", "define('UC_IP', '$ucip');");
		$content = uc_insert_config($content, "/define\('UC_APPID',\s*'?.*?'?\);/i", "define('UC_APPID', '$appid');");
		$content = uc_insert_config($content, "/define\('UC_PPP',\s*'?.*?'?\);/i", "define('UC_PPP', '20');");
		$content .= "\r\n".'?>';
		
		if(@file_put_contents($file, $content))
		{
			$success = true;
		}
	}
	return $success;
}

function uc_insert_config($s, $find, $replace)
{
	if(preg_match($find, $s))
	{
		$s = preg_replace($find, $replace, $s);
	}else{
		$s .= "\r\n".$replace;
	}
	return $s;
}

function uc_show(){
	global $smarty;
	if(!defined('UC_APPID')){
		template_assign(array('current_act', 'act'), array('��װUC����', 'install'));
	}else{
		$uc_api_open = false;			
		$ucapparray = uc_app_ls();
		foreach($ucapparray as $apparray){
			if($apparray['appid'] == UC_APPID){
				$uc_api_open = true;
				break;
			}
		}
	
		if(!$uc_api_open){
			showmsg("BlueCMSû�ҵ���ȷ��uc���ã�");
		}
			
		list($dbname,$dbtablepre) = explode('.',str_replace('`','',UC_DBTABLEPRE));	
		$uc_config = array('appid' => UC_APPID, 'ucapi' => UC_API, 'connect' => UC_CONNECT, 'dbhost' => UC_DBHOST, 'dbuser' => UC_DBUSER,'dbpass' => UC_DBPW, 'dbname' => $dbname, 'dbtablepre' => $dbtablepre,'ucip' => UC_IP,'uckey' => UC_KEY);

		template_assign(array('current_act', 'act', 'uc_config'), array('�༭UC������Ϣ', 'show', $uc_config));
	}
	$smarty->display('uc.htm');
}

function uc_edit($uc_config){
	$uc_dbpass = $uc_config['dbpass'] == '********' ? UC_DBPW : $uc_config['dbpass'];	
	$fp = fopen(BLUE_ROOT.'data/config.php', 'r');
	$content = fread($fp, filesize(BLUE_ROOT.'data/config.php'));
	$content = trim($content);
	$content = substr($content, -2) == '?>' ? substr($content, 0, -2) : $content;
	fclose($fp);
		
	$connect = '';		
	if($uc_config['connect']){
		$uc_dblink = @mysql_connect($uc_config['dbhost'], $uc_config['dbuser'], $uc_dbpass, 1);
		if(!$uc_dblink){
			showmsg('���ݿ�����ʧ��');
		}else{
			mysql_close($uc_dblink);
		}
		
		$connect = 'mysql';
		$content = uc_insert_config($content, "/define\('UC_DBHOST',\s*'.*?'\);/i", "define('UC_DBHOST', '".$uc_config['dbhost']."');");
		$content = uc_insert_config($content, "/define\('UC_DBUSER',\s*'.*?'\);/i", "define('UC_DBUSER', '".$uc_config['dbuser']."');");
		$content = uc_insert_config($content, "/define\('UC_DBPW',\s*'.*?'\);/i", "define('UC_DBPW', '".$uc_dbpass."');");
		$content = uc_insert_config($content, "/define\('UC_DBNAME',\s*'.*?'\);/i", "define('UC_DBNAME', '".$uc_config['dbname']."');");
		$content = uc_insert_config($content, "/define\('UC_DBTABLEPRE',\s*'.*?'\);/i", "define('UC_DBTABLEPRE', '`".$uc_config['dbname'].'`.'.$uc_config['dbtablepre']."');");
	}
		
	$content = uc_insert_config($content, "/define\('UC_CONNECT',\s*'.*?'\);/i", "define('UC_CONNECT', '".$uc_config['connect']."');");
	$content = uc_insert_config($content, "/define\('UC_KEY',\s*'.*?'\);/i", "define('UC_KEY', '".$uc_config['uckey']."');");
	$content = uc_insert_config($content, "/define\('UC_API',\s*'.*?'\);/i", "define('UC_API', '".$uc_config['ucapi']."');");
	$content = uc_insert_config($content, "/define\('UC_IP',\s*'.*?'\);/i", "define('UC_IP', '".$uc_config['ucip']."');");
	$content = uc_insert_config($content, "/define\('UC_APPID',\s*'?.*?'?\);/i", "define('UC_APPID', '".UC_APPID."');");
	$content .= '?>';

	if($fp = @fopen(BLUE_ROOT.'data/config.php', 'w')){
		@fwrite($fp, trim($content));
		@fclose($fp);
		showmsg('����UC ���ñ༭�ɹ�', 'uc_setting.php');
	}else{
		showmsg('д����������ʧ��!'.BLUE_ROOT.'data/config.php'.' �����ÿ�дȨ��');
	}
}


?>