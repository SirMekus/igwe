# Igwe

Igwe is a PHP file uploader with validation support to add power to an exisitng web app. One of the additional benefits include enforcing (or constraining) image dimension. For instance, imagine your application expects image dimension to be 300/300px (height/width) and a user uploads an image with a dimension of 500/200px. Strictly constraining the dimension (using traditional approach) may result in low quality and a not-too-nice image. This library takes care of this effectively without sacrificing quality of the uploaded image.

> Please note that this package is not limited to image upload; It takes care of any type of file upload in general - pdf, doc, audio, video, image, etc, as you will learn shortly.

## Installation

To get started all you need to do is:

```php
composer require sirmekus/igwe
```

>Please note that this package stores uploaded files/images relative to your web root.

That's all.

>Igwe, in Igbo language, means **"King/Chief"**.

---

After installing the package you should then create an `igwe.php` configuration file and place it in your root directory or your **config** directory. This file will contain "directives" on how to  configure the package for your service. You can just copy and paste the content below into your own `igwe.php` file for starters (remember that you can always configure the settings):

```php
return [
      //relative to the root directory
      'directory'=>'uploads',

      //The height resolution for your image file
      'height'=>1280,

      //The width resolution for your image file
      'width'=>1280,

      //Allowed mime types users can upload
      'mime_type'=>[
            "image/jpeg", 
            "image/png", 
            "image/gif", 
            "image/webp",
            //"application/pdf",
            //"video/mp4"
      ],

      //Maximum file size (in bytes) that is accepted in your application
      'max_size'=>5000000,//5mb

      //Maximum no. of files user(s) can upload at once. If you expect multiple files to uploade (like a gallery upload) then you can increase this value
      'max_to_upload'=>1,//

      //Files will be stored in the /uploads/sub_folder directory. If you have a subdirectory where you group your files then you should put it here. If null then uploaded files will be saved directly in the 'uploads' directory
      'sub_folder'=>null,

      //This will be prepended to every new name generated for each file
      'prefix'=>'uploads',

      //Where errors will be logged. Should be relative to the web root too. If it doesn't exist please create it if you want logging enabled
      'log_path'=>null,
]
```

Please note that if you don't create the file the package will use its default settings. The default settings are the values in the array above.

---

## Usage

---

## Receiving/Accepting file/image from client

Example:

```php
require_once 'vendor/autoload.php';

use App\Igwe\Uploader;

$image = (new Uploader)->upload('image');
```

Just that single line of code is what is needed to use this package. The package assumes the name (parameter) of the incoming file from the client is `image`. If it isn't then you can specify the name as the first argument. Example:

```html
<input type="file" accept="image/*" name='image' />
```

It doesn't matter if the file is an array or a single file, the package takes care of that, stores the uploaded file in disk and returns the new file name. If an array was detected the returned value will be an array containing the uploaded file names else a string that contains the file which you will want to save in the database.

You can set the configuration during runtime (if you choose to not use the configuration settings at any point in time) or overwrite the public properties of the class. The properties are:

- `docParentFolder` : This is typically the public directory relative to the root folder.

- `sub_folder` : In the `docParentFolder` folder if there is another folder there where you'll like the image to be uploaded you can specify it here. You can specify it as like a file path (`subfolder/path/folder`) or just a single entry like (`subfolder_name`).

- `width` : The allowed width for this image.

- `height` : The allowed height for this image.

- `valid_mimes` : The allowed mime types for this image (should be an array).

- `max_file_upload_size` : The max allowed size for this image in bytes.

- `max_no_of_file_to_upload` : The maximum number of file that can be uploaded for this image. This is useful if your users can upload multiple images.

- `name_of_file` : If specified, instead of randomly generated names this particular name will be used to rename the file. Be careful when setting this cause in a case of multiple file upload the files may be overwritten.

By default the `upload()` method checks if the specified key exists in the incoming request and is filled. If it is not it throws an error. In certain cases image upload may be optional, thus the application should still continue with the request processing. To enable this the method takes a second argument that suppresses the error. Example

```php
require_once 'vendor/autoload.php';

use App\Igwe\Uploader;

$image = (new Uploader)->upload('image', true);
```

or, if you use use PHP 8.0 and above and the parameter name from the request is `image`.

```php
require_once 'vendor/autoload.php';

use App\Igwe\Uploader;

$image = (new Uploader)->upload(ignore:true);
```

## Deleting Image/File

To delete an image it follows the same logic and configuration as if uploading the image/file. Simply pass the image/file name(s) - string or array - to the `remove()` method and the job is done. Example:

```php
require_once 'vendor/autoload.php';

use App\Igwe\Uploader;

$image = (new Uploader)->remove($image);
```

## Meanwhile

 You can connect with me on [LinkedIn](https://www.linkedin.com/in/sirmekus) for insightful tips and so we can grow our networks together.

 Patronise us on [Webloit](https://www.webloit.com).

 And follow me on [Twitter](https://www.twitter.com/Sire_Mekus).

 I encourage contribution even if it's in the documentation. Thank you, and I really hope you find this package helpful.
