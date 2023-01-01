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

function settings($key)
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
        return null;
    }

	return isset($config[$key]) ? $config[$key]: null;
}
?>