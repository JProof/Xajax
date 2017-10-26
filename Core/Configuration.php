<?php
/**
 * PHP version php7
 *
 * @category
 * @package            xajax-php-7
 * @author             ${JProof}
 * @copyright          ${copyright}
 * @license            ${license}
 * @link
 * @see                ${docu}
 * @since              21.09.2017
 */

declare(strict_types=1);

namespace Xajax\Core;

use BadMethodCallException;
use Xajax\Configuration\Deprecated;
use Xajax\Configuration\Language;
use Xajax\Configuration\Scripts;
use Xajax\Configuration\Uri;
use Xajax\Core\Helper\Encoding;

/**
 * Class Config
 *
 * @package Xajax
 */
class Configuration
{
	/** Useless Stuff to Remove next Version**/
	use Deprecated;
	/** Better Overview in the configuration Class **/
	use Scripts;
	/** Handling the Uri's**/
	use Uri;

	/** Language for errors an explanations **/
	use Language;
	/**
	 * @var self
	 */
	private static $instance;
	/**
	 * Legacy-Mode can be used to refacture xajax 6 versions. The Legacy-Flag allows to get and set vars without type checking
	 *
	 * @deprecated jproof/xajax 0.7.2 Legacy Mode will be removed
	 * @var bool
	 */
	static protected $legacy = false;
	/**
	 * String: XAJAX_DEFAULT_CHAR_ENCODING UTF-8
	 * Default character encoding used by both the <xajax> and
	 * <xajaxResponse> classes.
	 *
	 * @var string
	 */
	protected $characterEncoding;
	/**
	 * @since xajax 7.0.1 Replaces the XAJAX_DEFAULT_CHAR_ENCODING
	 * @var string
	 */
	private static $defaultCharacterEncoding = 'UTF-8';
	/**
	 * A configuration option used to indicate whether input data should be UTF8 decoded automatically.
	 * Boolean: bDecodeUTF8Input
	 *
	 * @var bool
	 * @see xajaxArgumentManager.inc.php
	 */
	protected $decodeUTF8Input;
	/**
	 * Convert special characters to the HTML equivalent.  See also <xajax->bOutputEntities> and <xajax->configure>.
	 * Called by the xajax object when configuration options are set in the main script.  Option
	 * values are passed to each of the main xajax components and stored locally as needed.  The
	 * <xajaxResponseManager> will track the characterEncoding and outputEntities settings.
	 *
	 * @var bool
	 */
	protected $outputEntities;
	/**
	 * JSON or XML (format to send after (xajax)request response back to the browser) JSON is xajax-default
	 *
	 * @var string
	 */
	protected $responseType;
	/**
	 * The MIME Type for Responses
	 * http header
	 *
	 * @var string
	 */
	protected $contentType;
	/**
	 * @var array
	 */
	protected static $modes = ['asynchronous', 'synchronous',];
	/**
	 * The request mode.
	 * 'asynchronous' - The request will immediately return, the
	 * response will be processed when (and if) it is received.
	 * 'synchronous' - The request will block, waiting for the
	 * response.  This option allows the server to return
	 * a value directly to the caller.
	 *
	 * @var string
	 */
	protected $defaultMode;
	/**
	 * POST or GET case-insensitive automatic default is post
	 *
	 * @var string
	 */
	protected $defaultMethod;
	/**
	 * JS-Method they was rendered during Xajax have there own method Prefix
	 *
	 * @var string
	 */
	protected $wrapperPrefix = 'xajax_';
	/**
	 * Debug Flag for Xajax. Set to true only during development.
	 *
	 * @var bool
	 */
	protected $debug = false;
	/**
	 * If debug is true xajax will explain more debug-messages
	 *
	 * @var bool
	 */
	protected $verbose = false;
	/**
	 * A configuration option that is tracked by the main <xajax>object.  Setting this
	 * to true allows <xajax> to exit immediatly after processing a xajax request.  If
	 * this is set to false, xajax will allow the remaining code and HTML to be sent
	 * as part of the response.  Typically this would result in an error, however,
	 * a response processor on the client side could be designed to handle this condition.
	 *
	 * @var bool
	 */
	protected $exitAllowed = true;
	/**
	 * This is a configuration setting that the main xajax object tracks.  It is used
	 * to enable an error handler function which will trap php errors and return them
	 * to the client as part of the response.  The client can then display the errors
	 * to the user if so desired.
	 *
	 * @see ../examples/tests/errorHandlingTest.php
	 * @var bool
	 */
	protected $errorHandler = false;
	/**
	 * A configuration setting tracked by the main <xajax> object.  Set the name of the
	 * file on the server that you wish to have php error messages written to during
	 * the processing of <xajax> requests.
	 *
	 * @todo refacture this parameter
	 * @var string
	 */
	protected $logFile;
	/**
	 * A configuration option that is tracked by the main <xajax> object.  Setting this
	 * to true allows <xajax> to clear out any pending output buffers so that the
	 * <xajaxResponse> is (virtually) the only output when handling a request.
	 *
	 * @var bool
	 */
	protected $cleanBuffer = false;
	/**
	 * @var string
	 */
	protected $version = 'jproof/xajax 0.7.2';

	/**
	 * Config constructor.
	 */
	protected function __construct()
	{
	}

	/**
	 *  Getter
	 *
	 * @param $name
	 *
	 * @return mixed
	 * @throws BadMethodCallException
	 * @throws BadMethodCallException
	 */
	public function __get($name)
	{
		$name = self::deprecatedNameAlias($name);
		if (self::isLegacy())
		{
			return $this->{$name};
		}

		$method = self::getMethodName('get', $name);
		if (method_exists($this, $method))
		{
			return $this->{$method()};
		}

		// never giveback an parameter without getter
		addError(new BadMethodCallException(__CLASS__ . '::' . __METHOD__ . ' Method ' . $method . ' for variable ' . $name . ' does not exists'));

		return null;
	}

	/**
	 * Setter
	 *
	 * @param $name
	 * @param $value
	 *
	 * @return mixed
	 */
	public function __set($name, $value)
	{
		$name = self::deprecatedNameAlias($name);

		if (self::isLegacy())
		{
			return $this->{$name} = $value;
		}

		$method = self::getMethodName('set', $name);
		if (method_exists($this, $method))
		{
			return $this->$method($value);
		}
		// never overload the setter! Make sure you have an
		addError(new BadMethodCallException(__CLASS__ . '::' . __METHOD__ . ' Method ' . $method . ' for variable ' . $name . ' does not exists'));

		return null;
	}

	/**
	 * @param $name
	 *
	 * @example create an method isTestVar() then  isset(Config::getInstance()->testVar) will be checked by the method
	 * @return bool|mixed
	 */
	public function __isset($name)
	{
		$name = self::deprecatedNameAlias($name);
		if (self::isLegacy())
		{
			return isset($this->{$name});
		}

		$method = self::getMethodName('is', $name);
		if (method_exists($this, $method))
		{
			return $this->{$method()};
		}

		return isset($this->{$name});
	}

	/**
	 * Old Array Key names
	 *
	 * @param string $name
	 *
	 * @deprecated jproof/xajax 0.7.2
	 * @return string
	 */
	protected static function deprecatedNameAlias(?string $name = null)
	{
		switch ($name)
		{
			case 'javascript URI':
				return 'javascriptUri';
			default:
				return $name;
		}
	}

	/**
	 * Internal MethodName Compiler
	 *
	 * @param string $type get|set|is
	 * @param string $name variable-name which should interact with the method
	 *
	 * @return string
	 */
	private static function getMethodName(?string $type = null, ?string $name = null): string
	{
		return (string) $type . ucfirst((string) $name);
	}

	/**
	 * @return string
	 */
	public function getCharacterEncoding(): string
	{
		if ('' === $this->characterEncoding)
		{
			// todo perhaps log
			$this->setCharacterEncoding(self::getDefaultCharacterEncoding());
		}

		return $this->characterEncoding;
	}

	/**
	 * @param string $characterEncoding
	 */
	public function setCharacterEncoding(?string $characterEncoding = null)
	{
		// @todo check the Setter, the encoding is valid
		if (Encoding::getEncoding($characterEncoding, true))
		{
			$this->characterEncoding = (string) $characterEncoding;
		}
	}

	/**
	 * @return string
	 */
	public static function getDefaultCharacterEncoding(): string
	{
		return self::$defaultCharacterEncoding;
	}

	/**
	 * @return Configuration
	 */
	public static function getInstance(): Configuration
	{
		if (!self::$instance instanceof self)
		{
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * @return bool
	 */
	public function isDecodeUTF8Input(): bool
	{
		return (bool) $this->decodeUTF8Input;
	}

	/**
	 * @param bool $decodeUTF8Input
	 */
	public function setDecodeUTF8Input(?bool $decodeUTF8Input = null)
	{
		$this->decodeUTF8Input = (bool) $decodeUTF8Input;
	}

	/**
	 * @return bool
	 */
	public function isOutputEntities(): bool
	{
		return (bool) $this->outputEntities;
	}

	/**
	 * @param bool $outputEntities
	 */
	public function setOutputEntities(?bool $outputEntities = null)
	{
		$this->outputEntities = (bool) $outputEntities;
	}

	/**
	 * @return string
	 */
	public function getResponseType(): string
	{
		// Automatic Setup to XajaxDefault JSON
		if (null === $this->responseType)
		{
			$this->setResponseType('');
		}

		return $this->responseType;
	}

	/**
	 * XML or JSON Response Detector
	 * JSON is default
	 *
	 * @param string $responseType case-insensitive xMl|JsON ..always valid
	 *
	 * @return bool has set or not
	 */
	public function setResponseType(string $responseType = ''): bool
	{
		$responseType = strtoupper($responseType);

		if ('XML' === $responseType)
		{
			$this->responseType = $responseType;
			$this->setContentType('text/xml');

			return true;
		}

		$this->responseType = 'JSON';
		$this->setContentType('application/json');

		return true;
	}

	/**
	 * Mime
	 *
	 * @return string
	 */
	public function getContentType(): string
	{
		// autoSetup
		if (null === $this->contentType)
		{
			$this->setResponseType('');
		}

		return $this->contentType;
	}

	/**
	 * Mime
	 * to Change the Content-Type use:
	 *
	 * @example $xajax->getConfiguration()->setResponseType(Json|Xml)
	 *
	 * @param string $contentType
	 */
	protected function setContentType(string $contentType)
	{
		$this->contentType = $contentType;
	}

	/**
	 * @return string
	 */
	public function getDefaultMode(): string
	{
		// Automatic setup
		if (null === $this->defaultMode)
		{
			return $this->setDefaultMode(self::getJSDefaultMode());
		}

		return $this->defaultMode;
	}

	/**
	 * @param string $defaultMode
	 *
	 * @return string the set'd default mode
	 */
	public function setDefaultMode(string $defaultMode): string
	{
		$defaultMode = strtolower($defaultMode);
		if (in_array($defaultMode, self::getModes(), true))
		{
			$this->defaultMode = $defaultMode;
		}
		else
		{
			$this->defaultMode = self::getJSDefaultMode();
		}

		return $this->defaultMode;
	}

	/**
	 * internal modes
	 *
	 * @see $defaultMode
	 * @return array
	 */
	public static function getModes(): array
	{
		return self::$modes;
	}

	/**
	 * @return string
	 */
	public static function getJSDefaultMode(): string
	{
		return self::getModes()[0];
	}

	/**
	 * @todo test
	 * @return string
	 */
	public function getDefaultMethod(): string
	{
		return $this->defaultMethod ?? $this->setDefaultMethod('');
	}

	/**
	 * @param string $defaultMethod
	 *
	 * @return string
	 */
	public function setDefaultMethod(string $defaultMethod = ''): string
	{
		$defaultMethod = strtoupper($defaultMethod);

		return $this->defaultMethod = $defaultMethod === 'GET' ? 'GET' : 'POST';
	}

	/**
	 * @return bool
	 * @deprecated jproof/xajax 0.7.2 will be removed
	 */
	public static function isLegacy(): bool
	{
		return self::$legacy;
	}

	/**
	 * @param bool $legacy
	 *
	 * @deprecated jproof/xajax 0.7.2 will be removed
	 */
	public static function setLegacy($legacy = true)
	{
		self::$legacy = (bool) $legacy;
	}

	/**
	 * @return string
	 */
	public function getWrapperPrefix(): string
	{
		return $this->wrapperPrefix;
	}

	/**
	 * @todo  explain
	 *
	 * @param string $wrapperPrefix
	 */
	public function setWrapperPrefix(string $wrapperPrefix)
	{
		$this->wrapperPrefix = $wrapperPrefix;
	}

	/**
	 * @todo explain
	 * @return bool
	 */
	public function isDebug(): bool
	{
		return $this->debug;
	}

	/**
	 * enable debug
	 */
	public function enableDebug()
	{
		$this->setDebug(true);
	}

	/**
	 * disable debug
	 */
	public function disableDebug()
	{
		$this->setDebug(false);
	}

	/**
	 * @param bool $debug
	 */
	public function setDebug(bool $debug = true)
	{
		$this->debug = $debug;
	}

	/**
	 * @todo explain
	 * @return bool
	 */
	public function isVerbose(): bool
	{
		return $this->verbose;
	}

	/**
	 * @param bool $verbose
	 */
	public function setVerbose(bool $verbose = true)
	{
		$this->verbose = $verbose;
	}

	/**
	 * @return bool
	 */
	public function isExitAllowed(): bool
	{
		return $this->exitAllowed;
	}

	/**
	 * @param bool $exitAllowed
	 */
	public function setExitAllowed(bool $exitAllowed = true)
	{
		$this->exitAllowed = $exitAllowed;
	}

	/**
	 * @return bool
	 */
	public function isErrorHandler(): bool
	{
		return $this->errorHandler;
	}

	/**
	 * @param bool $errorHandler
	 */
	public function setErrorHandler(bool $errorHandler = true)
	{
		$this->errorHandler = $errorHandler;
	}

	/**
	 * @since 7.0.1 Logfile has his own class
	 * @todo  refacture this parameter
	 * @return string
	 */
	public function getLogFile(): string
	{
		return $this->logFile;
	}

	/**
	 * @since 7.0.1 Logfile has his own class
	 * @todo  refacture this parameter
	 *
	 * @param string $logFile
	 */
	public function setLogFile(string $logFile = '')
	{
		$this->logFile = $logFile;
	}

	/**
	 * @return bool
	 */
	public function isCleanBuffer(): bool
	{
		return $this->cleanBuffer;
	}

	/**
	 * @param bool $cleanBuffer
	 */
	public function setCleanBuffer(bool $cleanBuffer = true)
	{
		$this->cleanBuffer = $cleanBuffer;
	}

	/**
	 * Current Version
	 *
	 * @return string
	 */
	public function getVersion(): string
	{
		return $this->version;
	}
}