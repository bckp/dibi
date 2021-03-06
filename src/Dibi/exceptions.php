<?php

/**
 * This file is part of the "dibi" - smart database abstraction layer.
 * Copyright (c) 2005 David Grudl (https://davidgrudl.com)
 */

namespace Dibi;


/**
 * dibi common exception.
 */
class Exception extends \Exception
{
	use Strict;

	/** @var string */
	private $sql;


	/**
	 * Construct a dibi exception.
	 * @param  string  Message describing the exception
	 * @param  int     Some code
	 * @param  string SQL command
	 */
	public function __construct($message = NULL, $code = 0, $sql = NULL)
	{
		parent::__construct($message, (int) $code);
		$this->sql = $sql;
	}


	/**
	 * @return string  The SQL passed to the constructor
	 */
	final public function getSql()
	{
		return $this->sql;
	}


	/**
	 * @return string  string represenation of exception with SQL command
	 */
	public function __toString()
	{
		return parent::__toString() . ($this->sql ? "\nSQL: " . $this->sql : '');
	}

}


/**
 * database server exception.
 */
class DriverException extends Exception
{

	/********************* error catching ****************d*g**/


	/** @var string */
	private static $errorMsg;


	/**
	 * Starts catching potential errors/warnings.
	 * @return void
	 */
	public static function tryError()
	{
		set_error_handler([__CLASS__, '_errorHandler'], E_ALL);
		self::$errorMsg = NULL;
	}


	/**
	 * Returns catched error/warning message.
	 * @param  string  catched message
	 * @return bool
	 */
	public static function catchError(& $message)
	{
		restore_error_handler();
		$message = self::$errorMsg;
		self::$errorMsg = NULL;
		return $message !== NULL;
	}


	/**
	 * Internal error handler. Do not call directly.
	 * @internal
	 */
	public static function _errorHandler($code, $message)
	{
		restore_error_handler();

		if (ini_get('html_errors')) {
			$message = strip_tags($message);
			$message = html_entity_decode($message);
		}

		self::$errorMsg = $message;
	}

}


/**
 * PCRE exception.
 */
class PcreException extends \Exception
{
	use Strict;

	public function __construct($message = '%msg.')
	{
		static $messages = [
			PREG_INTERNAL_ERROR => 'Internal error',
			PREG_BACKTRACK_LIMIT_ERROR => 'Backtrack limit was exhausted',
			PREG_RECURSION_LIMIT_ERROR => 'Recursion limit was exhausted',
			PREG_BAD_UTF8_ERROR => 'Malformed UTF-8 data',
			5 => 'Offset didn\'t correspond to the begin of a valid UTF-8 code point', // PREG_BAD_UTF8_OFFSET_ERROR
		];
		$code = preg_last_error();
		parent::__construct(str_replace('%msg', isset($messages[$code]) ? $messages[$code] : 'Unknown error', $message), $code);
	}
}


class NotImplementedException extends Exception
{}


class NotSupportedException extends Exception
{}


/**
 * Database procedure exception.
 */
class ProcedureException extends Exception
{
	/** @var string */
	protected $severity;


	/**
	 * Construct the exception.
	 * @param  string  Message describing the exception
	 * @param  int     Some code
	 * @param  string SQL command
	 */
	public function __construct($message = NULL, $code = 0, $severity = NULL, $sql = NULL)
	{
		parent::__construct($message, (int) $code, $sql);
		$this->severity = $severity;
	}


	/**
	 * Gets the exception severity.
	 * @return string
	 */
	public function getSeverity()
	{
		$this->severity;
	}

}
