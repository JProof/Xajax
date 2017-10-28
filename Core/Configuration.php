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

use Xajax\Configuration\Base;
use Xajax\Configuration\Deprecated;
use Xajax\Configuration\Language;
use Xajax\Configuration\Uri;
use Xajax\Core\Errors\Call;
use Xajax\Core\Helper\Encoding;

/**
 * Class Config
 *
 * @package Xajax
 */
class Configuration extends Base
{
	/** Useless Stuff to Remove next Version**/
	use Deprecated;

	/** Handling the Uri's**/
	use Uri;

	/** Language for errors an explanations **/
	use Language;

	/** error handling **/
	use Call;
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
	 * @return self
	 */
	public static function getInstance(): Configuration
	{
		static $instance;
		if (!$instance)
		{
			$instance = new self();
		}

		return $instance;
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
	 *
	 * @return \Xajax\Core\Configuration
	 */
	public function setCharacterEncoding(?string $characterEncoding = null): Configuration
	{
		// @todo check the Setter, the encoding is valid
		if (Encoding::getEncoding($characterEncoding, true))
		{
			$this->characterEncoding = (string) $characterEncoding;
		}

		return $this;
	}

	/**
	 * @return string
	 */
	public static function getDefaultCharacterEncoding(): string
	{
		return self::$defaultCharacterEncoding;
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
	 *
	 * @return \Xajax\Core\Configuration
	 */
	public function setDecodeUTF8Input(?bool $decodeUTF8Input = null): Configuration
	{
		$this->decodeUTF8Input = (bool) $decodeUTF8Input;

		return $this;
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
	 *
	 * @return \Xajax\Core\Configuration
	 */
	public function setOutputEntities(?bool $outputEntities = null): Configuration
	{
		$this->outputEntities = (bool) $outputEntities;

		return $this;
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
	 * @return \Xajax\Core\Configuration has set or not
	 */
	public function setResponseType(?string $responseType = null): Configuration
	{
		$responseType = strtoupper($responseType);

		if ('XML' === $responseType)
		{
			$this->responseType = $responseType;
			$this->setContentType('text/xml');
		}
		else
		{
			$this->responseType = 'JSON';
			$this->setContentType('application/json');
		}

		return $this;
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
	 *
	 * @return \Xajax\Core\Configuration
	 */
	protected function setContentType(string $contentType): Configuration
	{
		$this->contentType = $contentType;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getWrapperPrefix(): string
	{
		return (string) $this->wrapperPrefix;
	}

	/**
	 * @todo  explain
	 * @todo  check against prefixes
	 *
	 * @param string $wrapperPrefix
	 *
	 * @return \Xajax\Core\Configuration
	 */
	public function setWrapperPrefix(?string $wrapperPrefix = null): Configuration
	{
		$this->wrapperPrefix = (string) $wrapperPrefix;

		return $this;
	}

	/**
	 * @todo explain
	 * @return bool
	 */
	public function isDebug(): bool
	{
		return (bool) $this->debug;
	}

	/**
	 * enable debug
	 *
	 * @return \Xajax\Core\Configuration
	 */
	public function enableDebug(): Configuration
	{
		$this->setDebug(true);

		return $this;
	}

	/**
	 * disable debug
	 *
	 * @return \Xajax\Core\Configuration
	 */
	public function disableDebug(): Configuration
	{
		$this->setDebug(false);

		return $this;
	}

	/**
	 * @param bool $debug
	 *
	 * @return \Xajax\Core\Configuration
	 */
	public function setDebug(?bool $debug = null): Configuration
	{
		$this->debug = (bool) $debug;

		return $this;
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
	 *
	 * @return \Xajax\Core\Configuration
	 */
	public function setVerbose(?bool $verbose = null): Configuration
	{
		$this->verbose = (bool) $verbose;

		return $this;
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
	 *
	 * @return \Xajax\Core\Configuration
	 */
	public function setExitAllowed(?bool $exitAllowed = null): Configuration
	{
		$this->exitAllowed = (bool) $exitAllowed;

		return $this;
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
	 *
	 * @return \Xajax\Core\Configuration
	 */
	public function setErrorHandler(?bool $errorHandler = null): Configuration
	{
		$this->errorHandler = (bool) $errorHandler;

		return $this;
	}

	/**
	 * @since 7.0.1 Logfile has his own class
	 * @todo  refacture this parameter
	 * @return string
	 */
	public function getLogFile(): ?string
	{
		return (string) $this->logFile;
	}

	/**
	 * @since 7.0.1 Logfile has his own class
	 * @todo  refacture this parameter
	 *
	 * @param string $logFile
	 *
	 * @return \Xajax\Core\Configuration
	 */
	public function setLogFile(?string $logFile = null): Configuration
	{
		$this->logFile = $logFile;

		return $this;
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
	 *
	 * @return \Xajax\Core\Configuration
	 */
	public function setCleanBuffer(?bool $cleanBuffer = null): Configuration
	{
		$this->cleanBuffer = (bool) $cleanBuffer;

		return $this;
	}
}