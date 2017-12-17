<?php
/*
 * [bluecms]��Ȩ���� ��׼���磬��������Ȩ��
 * This is not a freeware, use is subject to license terms
 *
 * $Id��upload.class.php
 * $author��lucks
 */
class upload {
	private $allow_image_type = array('image/jpeg', 'image/gif', 'image/png', 'image/pjpeg');
	private $extension_name_arr = array('jpg', 'gif', 'png', 'pjpeg');

	function __construct(){
		$this->upload();
	}

    function upload(){

    }

	/**
	 * �ϴ�ͼƬ
	 */
    function img_upload($file, $dir = '', $imgname = ''){
    	if(empty($dir)){
    		$dir = BLUE_ROOT.DATA.UPLOAD.date("Ym")."/";
    	}else{
    		$dir = BLUE_ROOT.DATA.UPLOAD.$dir."/";
    	}
		if(!in_array($file['type'],$this->allow_image_type)){
    		echo '<font style="color:red;">�������ͼƬ����</font>';
			exit;
    	}
		if(empty($imgname)){
    		$imgname = $this->create_tempname().'.'.$this->get_type($file['name']);
    	}
    	if(!file_exists($dir)){
    		if(!mkdir($dir)){
    			echo '<font style="color:red;">�ϴ������д���Ŀ¼ʧ��</font>';
				exit;
    		}
    	}
    	$imgname = $dir . $imgname;

    	if($this->uploading($file['tmp_name'], $imgname)){
    		return str_replace(BLUE_ROOT, '', $imgname);
    	}else{
    		echo '<font style="color:red;">�ϴ�ͼƬʧ��</font>';
			exit;
    	}

    }

    function get_info($img) {
		$image = BLUE_ROOT.$img;
		$image_info = getimagesize($image);
		$image_info['width'] = $image_info[0];
		$image_info['height'] = $image_info[1];
		$image_info['type'] = $image_info[2];
		$image_info['name'] = basename($image);
		$image_info['dir'] = substr($img, 0, strrpos($img, '/')).'/';
		return $image_info;
	}

	function small_img($img,$width,$height) {
		$img_info = $this->get_info($img);
		$image = BLUE_ROOT.$img;
		$new_name = substr($img_info['name'],0,strrpos($img_info['name'], '.')).'_lit.jpg';
		if($img_info['type'] == 1) {
			$im = imagecreatefromgif($image);
		} elseif($img_info['type'] == 2) {
			$im = imagecreatefromjpeg($image);
		} elseif($img_info['type'] == 3) {
			$im = imagecreatefrompng($image);
		} else {
			$im = '';
		}
		if(empty($im)) return false;

		$width = ($width > $img_info['width']) ? $img_info['width'] : $width;
		$height = ($height > $img_info['height']) ? $img_info['height'] : $height;

		if (function_exists("imagecreatetruecolor")) {
			$new_img = imagecreatetruecolor($width, $height);
			imagecopyresampled($new_img, $im, 0, 0, 0, 0, $width, $height, $img_info['width'], $img_info['height']);
		} else {
			$new_img = imagecreate($width, $height);
			imagecopyresized($new_img, $im, 0, 0, 0, 0, $width, $height, $img_info['width'], $img_info['height']);
		}

		if (file_exists(BLUE_ROOT.'/'.$img_info['dir'].$new_name)) @unlink(BLUE_ROOT.'/'.$img_info['dir'].$new_name);
		imagejpeg($new_img,BLUE_ROOT.'/'.$img_info['dir'].$new_name);
		imagedestroy($new_img);
		imagedestroy($im);
		return $img_info['dir'].$new_name;
	}


	/**
	 * ȡ���ļ�����չ��������"."
	 */
    function get_type($filepath){
    	$pos = strrpos($filepath,'.');
    	if($pos !== false){
    		$extension_name = substr($filepath,$pos+1);
    	}
		if(!in_array($extension_name, $this->extension_name_arr)){
			echo '<font style="color:red;">���ϴ����ļ�������Ҫ��,������</font>';
			exit;
		}
		return $extension_name;
    }

	/**
	 * ����һ���ļ���
	 */
    function create_tempname(){
    	return time().mt_rand(0,9);
    }

	/**
	 * �ϴ��ļ�
	 */
    function uploading($tempfile, $target){
    	if(isset($file['error']) && $file['error'] > 0){
    		showmsg('�ϴ�ͼƬ����');
    	}
    	if(!move_uploaded_file($tempfile, $target)){
    		return false;
    	}
    	return true;
    }

}

?>