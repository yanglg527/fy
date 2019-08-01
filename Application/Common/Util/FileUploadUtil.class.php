<?php
namespace Common\Util;

use Admin\Util\AdminUtil;
use Center\Util\CenterUtil;
use Think\Image;

class FileUploadUtil {

	/**
	 * 配合 webuploader 使用的
	 *
	 *
	 * @param $sub_save_path
	 * @param string $file_name post 路径
	 * @return array{
	 'cover',
	 *  'show_path',//显示图片的路径
	 *  'save_path',//实际文件保存地址
	 * }
	 */
	public static function upload($sub_save_path, $file_name = 'file') {

		//        ajaxMsg(1,$_POST['avatar_data']);
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
			ajaxMsg(1, "上传失败");
		}
		if (!empty($_REQUEST['debug'])) {
			$random = rand(0, intval($_REQUEST['debug']));
			if ($random === 0) {
				header("HTTP/1.0 500 Internal Server Error");
				ajaxMsg(1, "上传失败");
			}
		}

		@set_time_limit(10 * 60);
		mkDirs("./Uploads/file/" . $sub_save_path);
		mkDirs("./Uploads/file/" . $sub_save_path . "temp/");
		$targetDir = './Uploads/file/' . $sub_save_path . 'temp/';
		$uploadDir = './Uploads/file/' . $sub_save_path;
		$cleanupTargetDir = true;
		// Remove old files
		$maxFileAge = 5 * 3600;
		// Temp file age in seconds
		// Get a file name
		if (isset($_REQUEST["name"])) {
			$fileName = $_REQUEST["name"];
		} elseif (!empty($_FILES)) {
			$fileName = $_FILES[$file_name]["name"];
		} else {
			$fileName = uniqid("file_") . '.jpg';
		}

		$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
		$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 1;
		$newname = time() . '-' . mt_rand();
		$oldext = substr(strrchr($fileName, '.'), 1);

		$filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;
		$fileName = $newname . '.' . $oldext;
		$uploadPath = $uploadDir . DIRECTORY_SEPARATOR . $fileName;

		if ($cleanupTargetDir) {
			if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
				ajaxMsg(1, "Failed to open temp directory");
			}
			while (($file = readdir($dir)) !== false) {
				$tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;
				if ($tmpfilePath == "{$filePath}_{$chunk}.part" || $tmpfilePath == "{$filePath}_{$chunk}.parttmp") {
					continue;
				}
				if (preg_match('/\.(part|parttmp)$/', $file) && (@filemtime($tmpfilePath) < time() - $maxFileAge)) {
					@unlink($tmpfilePath);
				}
			}
			closedir($dir);
		}
		if (!$out = @fopen("{$filePath}_{$chunk}.parttmp", "wb")) {

			ajaxMsg(1, "Failed to open output stream");
		}

		if (!empty($_FILES)) {
			if ($_FILES[$file_name]["error"] || !is_uploaded_file($_FILES[$file_name]["tmp_name"])) {
				ajaxMsg(1, "Failed to move uploaded file");
			}
			// Read binary input stream and append it to temp file
			if (!$in = @fopen($_FILES[$file_name]["tmp_name"], "rb")) {
				ajaxMsg(1, "Failed to open input stream");
			}
		} else {
			if (!$in = @fopen("php://input", "rb")) {
				ajaxMsg(1, "Failed to open input stream");
			}
		}
		while ($buff = fread($in, 4096)) {
			fwrite($out, $buff);
		}

		@fclose($out);
		@fclose($in);
		rename("{$filePath}_{$chunk}.parttmp", "{$filePath}_{$chunk}.part");
		$done = true;
		for ($index = 0; $index < $chunks; $index++) {
			if (!file_exists("{$filePath}_{$index}.part")) {
				$done = false;
				break;
			}
		}

		if ($done) {
			if (!$out = @fopen($uploadPath, "wb")) {
				ajaxMsg(1, "Failed to open input stream");
			}
			if (flock($out, LOCK_EX)) {
				for ($index = 0; $index < $chunks; $index++) {
					if (!$in = @fopen("{$filePath}_{$index}.part", "rb")) {
						break;
					}
					while ($buff = fread($in, 4096)) {
						fwrite($out, $buff);
					}
					@fclose($in);
					@unlink("{$filePath}_{$index}.part");
				}
				flock($out, LOCK_UN);
			}
			@fclose($out);
		}

		if ($done) {
			$excel = ".doc,.xls,.docx,.xlsx,.zip,.rar,.jpg,.bmp,.gif,.png,.jpeg";
			$oldext = strtolower($oldext);
			if (strpos($excel, $oldext)) {

				//文件上传记录
//				$file = array('name' => $fileName, 'ext' => "file/" . $oldext, 'savename' => $fileName, 'type' => 'file', 'savepath' => $uploadDir,
//				//                    'uid' => uid()?uid():(CenterUtil::center_uid()?CenterUtil::center_uid():AdminUtil::admin_uid()),
//				'size' => 0, 'create_time' => time());
				//                D('UploadFileLog')->add($file);


				return array('save_path' => '/Uploads/file/' . $sub_save_path . $fileName, 'show_path' => __ROOT__ . '/Uploads/file/' . $sub_save_path . $fileName , 'file_name' => $fileName, 'size' => $size);

			} else {
//				                @unlink($_SERVER['DOCUMENT_ROOT'] . __ROOT__ . '/Uploads/image/'.$sub_save_path . $fileName);
				ajaxMsg(1, "格式不支持！");
			}
		} else {
			return false;
		}
	}


}
