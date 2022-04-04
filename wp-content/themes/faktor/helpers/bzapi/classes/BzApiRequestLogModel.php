<?php

namespace BzApi;

use \Contao\Model as ContaoModel;
use Contao\Model;

class BzApiRequestLogModel extends ContaoModel
{
	protected static $strTable = 'tl_bzapi_requestlog';
	
	function __construct($objResult = null)
	{
		$this->tstamp = time();
		parent::__construct($objResult);
	}
	
	public static function getListLabel($row, $label)
	{
		return date('Y.m.d H:i:s', $row['tstamp']);
	}
}