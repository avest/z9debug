<?php
/**
 * This file is part of "Modernizing Legacy Applications in PHP".
 *
 * @copyright 2014 Paul M. Jones <pmjones88@gmail.com>
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */

// http://mlaphp.com/code/

namespace Mlaphp;

use DomainException;
use InvalidArgumentException;
use debug;

/**
 * A data structure object to encapsulate superglobal references.
 */
class Request
{
	/**
	* A copy of $_COOKIE.
	*
	* @var array
	*/
	public $_COOKIE = array();

	/**
	* A copy of $_ENV.
	*
	* @var array
	*/
	public $_ENV = array();

	/**
	* A copy of $_FILES.
	*
	* @var array
	*/
	public $_FILES = array();

	/**
	* A copy of $_GET.
	*
	* @var array
	*/
	public $_GET = array();

	/**
	* A copy of $_POST.
	*
	* @var array
	*/
	public $_POST = array();

	/**
	* A copy of $_REQUEST.
	*
	* @var array
	*/
	public $_REQUEST = array();

	/**
	* A copy of $_SERVER.
	*
	* @var array
	*/
	public $_SERVER = array();

	/**
	* A **reference** to $GLOBALS. We keep this so we can have late access to
	* $_SESSION.
	*
	* @var array
	*/
	protected $GLOBALS;

	/**
	* A **reference** to $_SESSION. We use a reference because PHP uses
	* $_SESSION for all its session_*() functions.
	*
	* @var array
	*/
	protected $_SESSION;

	/**
	* Constructor.
	*
	* @param array $globals A reference to $GLOBALS.
	*/
	public function __construct(&$globals)
	{
		$this->GLOBALS = $globals;

		$properties = array(
			'_COOKIE' => '_COOKIE',
			'_ENV' => '_ENV',
			'_FILES' => '_FILES',
			'_GET' => '_GET',
			'_POST' => '_POST',
			'_REQUEST' => '_REQUEST',
			'_SERVER' => '_SERVER',
		);

		foreach ($properties as $property => $superglobal)
		{
			if (isset($globals[$superglobal]))
			{
				$this->$property = $globals[$superglobal];
			}
		}
	}

	/**
	* Provides a magic **reference** to $_SESSION.
	*
	* @param string $property The property name; must be '_SESSION'.
	* @return array A reference to $_SESSION.
	* @throws InvalidArgumentException for any $name other than '_SESSION'.
	* @throws DomainException when $_SESSION is not set.
	*/
	public function &__get($name)
	{
		//debug::on(false);
		//debug::variable($name, 'name');
		if ($name != '_SESSION')
		{
			//throw new InvalidArgumentException($name);
		}

		if (! isset($this->GLOBALS['_SESSION']))
		{
			//throw new DomainException('$_SESSION is not set');
		}

		if (! isset($this->_SESSION))
		{
			if (isset($this->GLOBALS['_SESSION']))
			{
				$this->_SESSION = &$this->GLOBALS['_SESSION'];
			}
			else
			{
				$this->_SESSION = array();
			}
		}

		return $this->_SESSION;
	}

	/**
	* Provides magic isset() for $_SESSION and the related property.
	*
	* @param string $name The property name; must be '_SESSION'.
	* @return bool
	*/
	public function __isset($name)
	{
		if ($name != '_SESSION')
		{
			throw new InvalidArgumentException;
		}

		if (isset($this->GLOBALS['_SESSION']))
		{
			$this->_SESSION = &$this->GLOBALS['_SESSION'];
		}

		return isset($this->_SESSION);
	}

	/**
	* Provides magic unset() for $_SESSION; unsets both the property and the
	* superglobal.
	*
	* @param string $name The property name; must be 'session'.
	* @return null
	*/
	public function __unset($name)
	{
		if ($name != '_SESSION')
		{
			//throw new InvalidArgumentException;
		}

		$this->_SESSION = null;
		unset($this->GLOBALS['_SESSION']);
	}
}
?>