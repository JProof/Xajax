<?php
/**
 * PHP version php7
 *
 * @category
 * @package            jybrid-php-7
 * @author             ${JProof}
 * @copyright          ${copyright}
 * @license            ${license}
 * @link
 * @see                ${docu}
 * @since              21.09.2017
 */

declare(strict_types=1);

namespace Jybrid;

use Jybrid\Configuration\Base;
use Jybrid\Configuration\Traits\Deprecated;
use Jybrid\Configuration\Traits\Logging;
use Jybrid\Configuration\Traits\Security;
use Jybrid\Configuration\Traits\Uri;
use Jybrid\Errors\TraitCall;
use Jybrid\Helper\Encoding;


/**
 * Class Config
 *
 * @package Jybrid
 */
class Configuration extends Base
{
	/** Useless Stuff to Remove next Version**/
	use Deprecated;

	/** Handling the Uri's**/
	use Uri;
	/**
	 * some security features
	 *
	 * @since 0.7.3*
	 */
	use Security;
	/** Language for errors an explanations **/
	use \Jybrid\Configuration\Traits\Language;

	/**Configure the Error-Logging */
	use Logging;

	/** error handling **/
	use TraitCall;
	/**
	 * @since jybrid 7.0.1 Replaces the JYBRID_DEFAULT_CHAR_ENCODING
	 * @var string
	 */
	private static $defaultCharacterEncoding = 'UTF-8';
	/**
	 * String: JYBRID_DEFAULT_CHAR_ENCODING UTF-8
	 * Default character encoding used by both the <jybrid> and
	 * <jybridResponse> classes.
	 *
	 * @var string
	 */
	protected $characterEncoding;
	/**
	 * A configuration option used to indicate whether input data should be UTF8 decoded automatically.
	 * Boolean: bDecodeUTF8Input
	 *
	 * @var bool
	 * @see jybridArgumentManager.inc.php
	 */
	protected $decodeUTF8Input;
	/**
	 * JSON or XML (format to send after (jybrid)request response back to the browser) JSON is jybrid-default
	 *
	 * @deprecated jybrid use only json
	 * @var string
	 */
	protected $responseType;
	/**
	 * @todo check queue-size in js
	 * @var int
	 */
	protected $responseQueueSize;
	/**
	 * from Manager
	 *
	 * @todo check connectivity
	 * @var string
	 */
	protected $debugOutputID;
	/**
	 * @var int
	 */
	protected $scriptLoadTimeout;
	/**
	 * The MIME Type for Responses
	 * http header
	 *
	 * @var string
	 */
	protected $contentType;
	/**
	 * A configuration option that is tracked by the main <jybrid>object.  Setting this
	 * to true allows <jybrid> to exit immediately after processing a jybrid request.  If
	 * this is set to false, jybrid will allow the remaining code and HTML to be sent
	 * as part of the response.  Typically this would result in an error, however,
	 * a response processor on the client side could be designed to handle this condition.
	 *
	 * @var bool
	 */
	protected $exitAllowed = true;
	/**
	 * This is a configuration setting that the main jybrid object tracks.  It is used
	 * to enable an error handler function which will trap php errors and return them
	 * to the client as part of the response.  The client can then display the errors
	 * to the user if so desired.
	 *
	 * @since 0.7.2 error_handling can be set with an callable_class
	 * @see   ../examples/tests/errorHandlingTest.php
	 * @var string
	 */
	protected $errorHandler;
	/**
	 * A configuration setting tracked by the main <jybrid> object.  Set the name of the
	 * file on the server that you wish to have php error messages written to during
	 * the processing of <jybrid> requests.
	 *
	 * @todo refacture this parameter
	 * @var string
	 */
	protected $logFile;
	/**
	 * A configuration option that is tracked by the main <jybrid> object.  Setting this
	 * to true allows <jybrid> to clear out any pending output buffers so that the
	 * <jybridResponse> is (virtually) the only output when handling a request.
	 *
	 * @var bool
	 */
	protected $cleanBuffer = false;

	/**
	 * @return self
	 */
	public static function getInstance(): self
	{
		static $instance;
		if (!$instance)
		{
			$instance = new self();
		}

		return $instance;
	}

	/**
	 * Function: getCharacterEncoding
	 * Called automatically by new response objects as they are constructed to obtain the
	 * current character encoding setting.  As the character encoding is changed, the <jybridResponseManager>
	 * will automatically notify the current response object since it would have been constructed
	 * prior to the setting change, see <jybridResponseManager::configure>.
	 *
	 * @return string
	 */
	public function getCharacterEncoding(): string
	{
		if ( null === $this->characterEncoding || '' === $this->characterEncoding )
		{
			// todo perhaps log
			$this->setCharacterEncoding(self::getDefaultCharacterEncoding());
		}

		return $this->characterEncoding;
	}

	/**
	 * @param string $characterEncoding
	 *
	 * @return \Jybrid\Configuration
	 */
	public function setCharacterEncoding(?string $characterEncoding = null): Configuration
	{
		// @todo check the Setter, the encoding is valid otherwise give back an Message
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
	 * @return \Jybrid\Configuration
	 */
	public function setDecodeUTF8Input(?bool $decodeUTF8Input = null): Configuration
	{
		$this->decodeUTF8Input = (bool) $decodeUTF8Input;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getResponseType(): string
	{
		// Automatic Setup to JybridDefault JSON
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
	 * @deprecated jybrid works json
	 * @return \Jybrid\Configuration has set or not
	 */
	public function setResponseType(?string $responseType = null): Configuration
	{
		$responseType = strtoupper($responseType);

		if ('XML' === $responseType)
		{
			$this->responseType = $responseType;
			$this->setContentType('text/xml');
		} else
		{
			$this->responseType = 'JSON';
			$this->setContentType('application/json');
		}

		return $this;
	}

	/**
	 * ex Manager
	 *
	 * @return null|int
	 */
	public function getResponseQueueSize(): ?int
	{
		return $this->responseQueueSize;
	}

	/**
	 * Ex Manager
	 *
	 * @param int $responseQueueSize
	 *
	 * @return self
	 */
	public function setResponseQueueSize(?int $responseQueueSize = null): self
	{
		$this->responseQueueSize = $responseQueueSize;

		return $this;
	}

	/**
	 * @return null|string
	 */
	public function getDebugOutputID(): ?string
	{
		return $this->debugOutputID;
	}

	/**
	 * @param string $debugOutputID
	 *
	 * @return self
	 */
	public function setDebugOutputID(?string $debugOutputID = null): self
	{
		$this->debugOutputID = $debugOutputID;

		return $this;
	}

	/**
	 * @return null|int
	 */
	public function getScriptLoadTimeout(): ?int
	{
		return $this->scriptLoadTimeout;
	}

	/**
	 * @see https://jybrid.com/de/configuration#setscriptloadtimeout
	 *
	 * @param int $scriptLoadTimeout
	 *
	 * @return self
	 */
	public function setScriptLoadTimeout(?int $scriptLoadTimeout = null): self
	{
		$this->scriptLoadTimeout = $scriptLoadTimeout;

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
	 * @example $jybrid->getConfiguration()->setResponseType(Json|Xml)
	 *
	 * @param string $contentType
	 *
	 * @return \Jybrid\Configuration
	 */
	protected function setContentType(string $contentType): Configuration
	{
		$this->contentType = $contentType;

		return $this;
	}

	/**
	 * Boolean: bExitAllowed
	 * A configuration option that is tracked by the main <jybrid>object.  Setting this
	 * to true allows <jybrid> to exit immediatly after processing a jybrid request.  If
	 * this is set to false, jybrid will allow the remaining code and HTML to be sent
	 * as part of the response.  Typically this would result in an error, however,
	 * a response processor on the client side could be designed to handle this condition.
	 *
	 * @return bool
	 */
	public function isExitAllowed(): bool
	{
		return $this->exitAllowed;
	}

	/**
	 * @param bool $exitAllowed
	 *
	 * @return \Jybrid\Configuration
	 */
	public function setExitAllowed(?bool $exitAllowed = null): Configuration
	{
		$this->exitAllowed = (bool) $exitAllowed;

		return $this;
	}

	/**
	 * Getting the Error-Handler Class
	 *
	 * @return string
	 * @throws \InvalidArgumentException
	 */
	public function getErrorHandler(): string
	{
		if (null === $this->errorHandler)
		{
			throw new \InvalidArgumentException('ErrorHandler is Null. Please check before with ->isErrorHandler(); or set ->setErroHandler(\'callable_object\')');
		}

		return $this->errorHandler;
	}

	/**
	 * @param string $errorHandler
	 *
	 * @return \Jybrid\Configuration
	 * @throws \InvalidArgumentException
	 */
	public function setErrorHandler(?string $errorHandler = null): Configuration
	{
		if (null !== $errorHandler)
		{

			if (\is_string($errorHandler) && \is_callable($errorHandler))
			{
				$this->errorHandler = $errorHandler;
			} else
			{
				throw new \InvalidArgumentException('ErrorHandler must be an callable Object such as MyErrorHandlerClass::myErrorMethod');
			}
		} else
		{
			$this->errorHandler = null;
		}

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isErrorHandler(): bool {
		return null !== $this->errorHandler;
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
	 * Check the Logfile Parameter was set
	 *
	 * @return bool
	 */
	public function isLogFile(): bool {
		return 0 < \strlen( $this->getLogFile() );
	}

	/**
	 * @since 7.0.1 Logfile has his own class
	 * @todo  refacture this parameter
	 *
	 * @param string $logFile
	 *
	 * @return \Jybrid\Configuration
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
	 * @return \Jybrid\Configuration
	 */
	public function setCleanBuffer(?bool $cleanBuffer = null): Configuration
	{
		$this->cleanBuffer = (bool) $cleanBuffer;

		return $this;
	}
}