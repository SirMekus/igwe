<?php

namespace App\Igwe;

use App\Igwe\IgweTrait;

// import the Intervention Image Manager Class
use Intervention\Image\ImageManagerStatic as Image;

class Uploader
{
	use IgweTrait;

	public function __construct()
	{
		$this->width = settings("width") ?? 1280;
		
		$this->height = settings("height") ?? 1280;
		
		$this->valid_mimes = settings("mime_type") ?? [
			"image/jpeg", 
            "image/png", 
            "image/gif", 
            "image/webp",
      ];
		
		$this->max_file_upload_size = settings("max_size") ?? 5000000;
		
		$this->max_no_of_file_to_upload = settings("max_to_upload") ?? 1;

		$this->sub_folder = settings("sub_folder");

		$this->docParentFolder = settings('directory') ?? 'uploads';

		$this->prefix = settings('prefix') ?? 'igwe';

		$this->name_of_file = settings('name_of_file');

		$this->log_path = settings("log_path");
	}

	//Returns an array/string with the renamed file as value.
	public function upload($file_upload_name = "stock", $ignore = false)
	{
		$file = $this->check($file_upload_name, $ignore);

		return $this->save($file);
	}

	public function check($file_upload_name, $ignore = false)
	{
		if (!isset($_FILES[$file_upload_name]) or empty($_FILES[$file_upload_name]["name"])) {
			if ($ignore == true) {
				return;
			} else {
				response("Please upload an image or file.", 422);
			}
		}

		$file = $_FILES[$file_upload_name];

		//We expect a maximum number of $max_no_of_file_to_upload images to upload, if it's more than then we issue an error warning.
		if (is_array($file["name"]) and (count($file["name"]) > $this->max_no_of_file_to_upload)) {
			response("Exceeded maximum number of images/files to upload. You can only upload " . $this->max_no_of_file_to_upload . " maximum number of images/files.", 422);
		}

		if (is_array($file["name"])) {

			//Users are allowed to upload a certain number of files so that the $_FILES array is filled accordingly. But User can skip the first box for where to place the file in the html form and go for the second, third, etc. In this case all the arrays will be set and with the same exact count but array["Name"][0] will be empty alongside other than keys pertaining the 0-th file and this will cause a bug in the script so we wanna carter for it first.
			for ($i = 0; $i < count($file["name"]); $i++) {
				if (!empty($file["name"][$i])) {
					if (!isset($file["tmp_name"][$i])) {
						continue;
					}

					switch ($file['error'][$i]) {
						case UPLOAD_ERR_OK:
							break;
						case UPLOAD_ERR_NO_FILE:
							throw new \RuntimeException("No file sent.");
						case UPLOAD_ERR_INI_SIZE:
						case UPLOAD_ERR_FORM_SIZE:
							throw new \RuntimeException("{$file["name"][$i]} exceeds filesize limit.");
						default:
							throw new \RuntimeException("Unknown error");
					}

					$typeOfFile = $this->isImage($file["tmp_name"][$i]) ? 'image' : 'file';

					$file_type = $this->getType($file["tmp_name"][$i]);

					//if file type is not any of these specified types
					if (!in_array($file_type, $this->valid_mimes)) {
						response("Invalid $typeOfFile format detected for " . $file["name"][$i] . ". Accepted format(s) is/are:" . $this->getAllowedTypes(), 422);
					}

					//if file is larger than a specified size.
					if (filesize($file["tmp_name"][$i]) > $this->max_file_upload_size) 
					{
						response($file["name"][$i] . " is too large. Please make sure any uploaded image is less than or equal to {$this->calculateFileSize($this->max_file_upload_size)}. Current size is {$this->calculateFileSize($file["size"][$i])}", 422);
					}
				}
			}
		}

		//This should accomodate for single Upload(s) that don't need to be joined together or kept in an array
		else {
			if (!empty($file["name"])) {
				switch ($file['error']) {
					case UPLOAD_ERR_OK:
						break;
					case UPLOAD_ERR_NO_FILE:
						throw new \RuntimeException("No file sent.");
					case UPLOAD_ERR_INI_SIZE:
					case UPLOAD_ERR_FORM_SIZE:
						throw new \RuntimeException("{$file["name"]} exceeded filesize limit.");
					default:
						throw new \RuntimeException("Unknown errors");
				}

				$typeOfFile = $this->isImage($file["tmp_name"]) ? 'image' : 'file';

				$file_type = $this->getType($file["tmp_name"]);

				//if file type is not any of these specified types
				if (!in_array($file_type, $this->valid_mimes)) {
					response("Invalid $typeOfFile format detected for " . $file["name"] . ". Accepted format(s) is/are:" . $this->getAllowedTypes(), 422);
				}

				//if file is larger than a specified size.
				if (filesize($file["tmp_name"]) > $this->max_file_upload_size) 
				{
					response($file["name"] . " is too large. Please make sure any uploaded image is less than or equal to {$this->calculateFileSize($this->max_file_upload_size)}. Current size is {$this->calculateFileSize($file["size"])}", 422);
				}
			}
		}

		return $file;
	}

	public function save($file)
	{
		if (is_array($file["name"])) {

			//This will house the new name of the images and will be sent back to the Controller.
			$photos = [];

			for ($i = 0; $i < count($file["name"]); $i++) {
				if (!empty($file["name"][$i])) {
					if (!isset($file["tmp_name"][$i])) {
						continue;
					}

					//This is the default dir on server where files are stored.
					$tmp_name = $file["tmp_name"][$i]; 

					$extension = strtolower(pathinfo($file["name"][$i],PATHINFO_EXTENSION));

					if (empty($this->name_of_file)) {
						//a unique name for files to be stored on server to avoid file overwriting.
						$new_name = $this->unique_name(10); 
					} else {
						//The name may be typically set when it involves uploading of document. This will help to prevent overriding.
						$index = $i + 1;
						$new_name = $this->name_of_file . $index;
					}

					$new_name = $new_name . "." . $extension; //joins new file name now with base-name/extension of the image.

					if ($this->isImage($tmp_name)) {
						$img = Image::make($tmp_name)->resize($this->width, $this->height, function ($constraint) {
							$constraint->aspectRatio();
						})->encode($extension);

						$path = $img->save($this->getDirectory() . $new_name, 75);
					} else {
						$path = move_uploaded_file($tmp_name, $this->getDirectory() . $new_name);
					}

					if ($path) {
						$photos[] = $new_name;
					} else {
						response("Couldn't upload this file", 422);
					}
				}
			}
			return $photos;
		}

		//This should accomodate for single Upload(s) that don't need to be joined together or kept in an array
		else {
			if (!empty($file["name"])) {
				$tmp_name = $file["tmp_name"]; //This is the default dir on server where files are stored.

				$extension = strtolower(pathinfo($file["name"],PATHINFO_EXTENSION));

				if (empty($this->name_of_file)) {
					$new_name = $this->unique_name(10); //a unique name for files to be stored on server to avoid file overwriting.
				} else {
					$new_name = $this->name_of_file;
				}

				$new_name = $new_name . "." . $extension; //joins new file name now with base-name/extension of the image.

				if ($this->isImage($tmp_name)) {
					$img = Image::make($tmp_name)->resize($this->width, $this->height, function ($constraint) {
						$constraint->aspectRatio();
					})->encode($extension);

					$path = $img->save($this->getDirectory() . $new_name, 75);
				} else {
					$path = move_uploaded_file($tmp_name, $this->getDirectory() . $new_name);
				}

				if ($path) {
					return $new_name;
				} else {
					response("Couldn't upload this file", 422);
				}
			}
		}
	}

	public function remove($file)
	{
		if (is_array($file)) {
			for ($i = 0; $i < count($file); $i++) {
				if (!@unlink($this->getDirectory() . $file[$i])) //if file couldn't be deleted then exit and do nothing which also means it won't be deleted from database.
				{
					//We log it. We'll manually delete it ourselves.
					$content = "Failed to delete " . $this->full_path . "/{$file[$i]} ' on " . date("Y-m-d H:i:s") . ". Please delete it manually.\n";

					$this->writeToFile($content);
				}
			}
		} else {
			if (!@unlink($this->getDirectory() . "$file")) //if file couldn't be deleted then exit and do nothing which also means it won't be deleted from database.
			{
				//We log it. We'll manually delete it ourselves.
				$content = "Failed to delete " . $this->getDirectory() . "/$file, ' on " . date("Y-m-d H:i:s") . ". Please delete it manually.\n";

				$this->writeToFile($content);
			}
		}
		return;
	}


	function writeToFile($content)
	{
		if(!empty($this->log_path) and file_exists(rootDir().$this->log_path)){
			$fp = fopen(rootDir().$this->log_path, 'a');

		    fwrite($fp, $content);
		    fclose($fp);
		    return;
		}
	}
}
