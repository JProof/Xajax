<?php
/**
 * PHP version $phpversion$
 *
 * @category
 * @package            Jybrid Core  Jybrid
 * @author             ${JProof}
 * @copyright          ${copyright}
 * @license            ${license}
 * @link
 * @see                ${docu}
 * @since              02.01.2018
 */

declare(strict_types=1);

namespace Jybrid\Input;

/**
 * Class Input
 */
class Input
{
	/**
	 * @var array
	 */
	protected static $possibleInputMethods = ['request', 'post', 'get'];
	/**
	 * Current Request Method
	 *
	 * @var string
	 */
	private $method;
	/**
	 * @var Parameters
	 */
	private $inputs;

	/**
	 * Input constructor.
	 */
	public function __construct()
	{
		// Just init it
		$this->setInputs(new Parameters());
	}

	/**
	 * @param $name
	 * @param $arguments
	 */
	public function __call($name, $arguments)
	{
		$args = func_get_args();
	}

	/**
	 * @param null|string $method
	 *
	 * @return \Jybrid\Input\Parameter
	 */
	public function getInput(?string $method = null): Parameter
	{
		if (null === $method)
		{
			return $this->getDefaultInput();
		}

		return $this->_getInput($method);
	}

	/**
	 * @param string $method
	 *
	 * @return Parameter
	 */
	private function _getInput(string $method): Parameter
	{
		$method = self::sanitizeRequestName($method);
		if (!$this->inputs->offsetExists($method))
		{
			$this->inputs->offsetSet($method, new Parameter(self::getGlobalFromVar($method)));
		}

		return $this->inputs->offsetGet($method);
	}

	/**
	 * @return Parameter
	 */
	protected function getDefaultInput(): Parameter
	{
		return $this->_getInput($this->getCurrentMethod());
	}

	/**
	 * @return string
	 */
	protected function getCurrentMethod(): string
	{
		return $this->method ?? $this->method = $this->getDetectInputMethod();
	}

	/**
	 * Setting an Input-Parameter
	 *
	 * @param string        $method post $_POST _POST POST
	 * @param iterable|null $inputs
	 *
	 * @return Parameter
	 */
	public function setInput(string $method, ?iterable $inputs = null): Parameter
	{
		$method = self::sanitizeRequestName($method);
		$this->inputs->offsetSet($method, new Parameter($inputs));

		return $this->inputs->offsetGet($method);
	}

	/**
	 * todo harmonize unify with
	 *
	 * @see \Jybrid\Header\Header::autodetectRequestMethod();
	 * @return string
	 */
	protected function getDetectInputMethod(): string
	{
		if ( $_SERVER && array_key_exists( 'REQUEST_METHOD', $_SERVER ) ) {
			return self::sanitizeRequestName( $_SERVER['REQUEST_METHOD'] );
		}

		// buggy alternate
		if ( $_POST && 0 < \count( $_POST ) ) {
			return 'post';
		}
		if ($_GET && 0 < \count($_GET))
		{
			return 'get';
		}

		return 'request';
	}

	/**
	 * @param null|string $method
	 *
	 * @return array|null
	 */
	protected static function getGlobalFromVar(?string $method = null): ?array
	{
		switch ($method)
		{
			case 'get':
				return $_GET ?? [];
			case 'post':
				return $_POST ?? [];
			case 'server':
				return $_SERVER ?? [];
			case 'session':
				return $_SESSION ?? [];
			case 'files':
				return $_FILES ?? [];
			case 'request':
				return $_REQUEST ?? [];
			default:
				return [];
		}
	}

	/**
	 * @param string $name
	 *
	 * @return string
	 */
	protected static function sanitizeRequestName(string $name): string
	{
		return strtolower(str_replace(['$', '_'], [], $name));
	}

	/**
	 * Internal Construction Method
	 *
	 * @param Parameters $inputs
	 *
	 * @return Parameters
	 */
	private function setInputs(Parameters $inputs): Parameters
	{
		return $this->inputs = $inputs;
	}
}