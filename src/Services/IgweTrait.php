<?php

namespace App\Igwe;

trait IgweTrait
{
	public $docParentFolder;

	public $sub_folder;

	public $width;

	public $height;

	public $valid_mimes;

	public $max_file_upload_size;

	public $max_no_of_file_to_upload;

	public $name_of_file;

	public $prefix;

	public $log_path;

	public function unique_name($length = 30) 
	{
		$prefix = $this->prefix."_".date("Ymd");
		
        if (function_exists("random_bytes"))
		{
			$bytes = random_bytes(ceil($length / 2));
        }   
		elseif(function_exists("openssl_random_pseudo_bytes")) 
		{
			$bytes = openssl_random_pseudo_bytes(ceil($length / 2));
        } 
		else 
		{
			throw new \Exception("no cryptographically secure random function available");
        }

        return substr($prefix.bin2hex($bytes), 0, $length);
    }

	public function getDirectory()
	{
		return rootDir().'/'. (!empty($this->sub_folder) ? $this->docParentFolder.'/'.$this->sub_folder : $this->docParentFolder).'/';
	}

	public function isImage($filetype)
	{
		$info = new \finfo(FILEINFO_MIME_TYPE);

		return (explode('/', $info->file($filetype))[0] == 'image') ? true : false;	
	}

	public function getAllowedTypes()
	{
		$mimes = "";
		
		foreach($this->valid_mimes as $mime)
		{
			$mimes .= explode('/', $mime)[1]. ", ";
		}

		return trim($mimes, ", ''");
	}

	public function calculateFileSize($bytes)
	{
		$bytes = floatval($bytes);
        $arBytes = array(
            0 => array(
                "UNIT" => "TB",
                "VALUE" => pow(1024, 4)
            ),
            1 => array(
                "UNIT" => "GB",
                "VALUE" => pow(1024, 3)
            ),
            2 => array(
                "UNIT" => "MB",
                "VALUE" => pow(1024, 2)
            ),
            3 => array(
                "UNIT" => "KB",
                "VALUE" => 1024
            ),
            4 => array(
                "UNIT" => "B",
                "VALUE" => 1
            ),
        );

		$result= "";

        foreach($arBytes as $arItem)
        {
			if($bytes >= $arItem["VALUE"])
            {
				$result = $bytes / $arItem["VALUE"];
			    $result = strval(round($result, 2))." ".$arItem["UNIT"];
                break;
            }
        }
        return $result;
    }

	public function getType($file)
	{
		$info = new \finfo(FILEINFO_MIME_TYPE);

		return $info->file($file);
	}
}