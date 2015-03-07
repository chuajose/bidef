<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * MY Form Validation Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Validation
 * @author		Cristobal Terceiro 
 */
class MY_Form_validation extends CI_Form_validation{

	protected $CI;
	
	/**
	 * Constructor
	 */
	public function __construct($rules = array())
	{
		$this->CI =& get_instance();		
	}

	// --------------------------------------------------------------------

	/**
	 * Required
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function dni($dni)
	{
		if (strlen($str) != 9 || preg_match('/^[XYZ]?([0-9]{7,8})([A-Z])$/i', $str, $matches) !== 1)
		{
        	return false;
	    }
	    $map = 'TRWAGMYFPDXBNJZSQVHLCKE';
	    list(, $number, $letter) = $matches;
	    return strtoupper($letter) === $map[((int) $number) % 23];
	}
}

/* End of file MY_Form_validation.php */
/* Location: ./application/libraries/MY_Form_validation.php */
