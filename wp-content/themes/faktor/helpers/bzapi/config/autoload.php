<?php

/**
 * Register classes outside the namespace folder
 */
if (class_exists ( 'NamespaceClassLoader' )) 
{
	NamespaceClassLoader::addClassMap ( array (
			'BzApi\BzApiRequestLogModel' => 'system/modules/bzapi/classes/BzApiRequestLogModel.php',
			'BzApi\BzRequest' => 'system/modules/bzapi/classes/BzRequest.php',
			'BzApi\BzClient' => 'system/modules/bzapi/classes/BzClient.php',
	) );
}
