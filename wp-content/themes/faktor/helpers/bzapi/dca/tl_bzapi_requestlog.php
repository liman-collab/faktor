<?php

/**
 * Table tl_manualorders_order
 */
$GLOBALS['TL_DCA']['tl_bzapi_requestlog'] = array(
	// Config
	'config' => array(
		'dataContainer' => 'Table',
		'enableVersioning' => false,
		'sql' => array(
			'keys' => array(
				'id' => 'primary'
			)
		)
	),
	'list' => array(
		'sorting' => array(
			'mode' => 1,
			'fields' => array(
				'tstamp DESC'
			),
			'flag' => 1,
			'panelLayout' => 'sort',
			'disableGrouping' => true
		),
		'label' => array(
			'fields' => array(
				'tstamp'
			),
			'showColumns' => true,
			'label_callback'  => array('BzApi\BzApiRequestLogModel', 'getListLabel'),
		),
		'global_operations' => null,
		'operations' => array(
			'delete' => array(
				'label' => &$GLOBALS['TL_LANG']['tl_bzapi_requestlog']['delete'],
				'href' => 'act=delete',
				'icon' => 'delete.gif',
				'attributes' => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"',
				'button_callback' => array(
					'ManualOrders\tl_manualorders_order',
					'getButton'
				)
			),
			'show' => array(
				'label' => &$GLOBALS['TL_LANG']['tl_bzapi_requestlog']['show'],
				'href' => 'act=show',
				'icon' => 'show.gif'
			)
		)
	), // list end
	
	'palettes' => array(
		'default' => 'created_at,request,response,result;'
	),
	
	'fields' => array(
		'id' => array(
			'sql' => "int(10) unsigned NOT NULL auto_increment"
		),
		'tstamp' => array(
			'sql' => "int(10) unsigned NOT NULL default '0'"
		),
		'request' => array(
			'label' => &$GLOBALS['TL_LANG']['tl_bzapi_requestlog']['request'],
			'inputType' => 'text',
			'search' => false,
			'eval' => array(
				'mandatory' => true,
				'doNotShow' => true,
			),
			'sql' => "text NULL"
		),
		'response' => array(
			'label' => &$GLOBALS['TL_LANG']['tl_bzapi_requestlog']['response'],
			'inputType' => 'text',
			'search' => false,
			'eval' => array(
				'mandatory' => true,
				'doNotShow' => true,
			),
			'sql' => "text NULL"
		),
		'result' => array(
			'label' => &$GLOBALS['TL_LANG']['tl_bzapi_requestlog']['result'],
			'inputType' => 'text',
			'search' => false,
			'eval' => array(
				'mandatory' => true,
				'attributes' => array(
					'systemColumn' => true,
					'inherit' => true
				)
			),
			'sql' => "text NULL"
		)
	)
);
