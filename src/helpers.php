<?php
/**********************************************************************¦
            /\                                                         ¦
           /  \                                                        ¦
          /    \                                                       ¦
         /      \                                                      ¦
        /        \                                                     ¦
	   /          \                                                    ¦
	  /            \                                                   ¦
	 /              \                                                  ¦
	/    SIRMEKUS  	 \                                                 ¦
   /     Coded        \                                                ¦
  /                    \                                               ¦
 /                      \                                              ¦
/_______________________ \                                             ¦
Written By: SIRMEKUS                                                   ¦
@copyright SirMekus 2022                                                        !
                                                                    ¦
************************************************************************/

function config($key)
{
	$config = include rootDir().'/config/igwe.php';
    
    if(file_exists(rootDir().'/config/igwe.php'))
    {
        $config = include rootDir().'/config/igwe.php';
    }
    else if(file_exists(rootDir().'/igwe.php'))
    {
        $config = include rootDir().'/igwe.php';
    }
    else
    {
        throw new \RuntimeException("You need to create an 'igwe.php' file as specified in the documentation. Please consult the ReadMe file for more information.");
    }

	return isset($config[$key]) ? $config[$key]: null;
}
?>