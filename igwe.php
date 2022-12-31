<?php 

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
      ],

      //Maximum file size (in bytes) that is accepted in your application
      'max_size'=>5000000,//5mb

      //Maximum no. of files user(s) can upload at once. If you expect multiple files to uploade (like a gallery upload) then you can increase this value
      'max_to_upload'=>1,//

      //Files will be stored in the /uploads/ directory. If you have a subdirectory where you group your files then you should put it here.
      'sub_folder'=>null,

      //This will be prepended to every new name generated for each file
      'prefix'=>'uploads',
]
?>