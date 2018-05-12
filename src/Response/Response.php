<?php

namespace Jybrid\Response;

/*
	File: jybridResponse.inc.php

	Contains the response class.

	Title: jybrid response class

	Please see <copyright.inc.php> for a detailed description, copyright
	and license information.
*/

/*
	@package jybrid
	@version $Id: jybridResponse.inc.php 361 2007-05-24 12:48:14Z calltoconstruct $
	@copyright Copyright (c) 2005-2007 by Jared White & J. Max Wilson
	@copyright Copyright (c) 2008-2010 by Joseph Woolley, Steffen Konerow, Jared White  & J. Max Wilson
	@license http://www.jybridproject.org/bsd_license.txt BSD License
*/

/*
	Class: jybridResponse

	Collect commands to be sent back to the browser in response to a jybrid
	request.  Commands are encoded and packaged in a format that is acceptable
	to the response handler from the javascript library running on the client
	side.

	Common commands include:
		- <jybridResponse->assign>: Assign a value to an elements property.
		- <jybridResponse->append>: Append a value on to an elements property.
		- <jybridResponse->script>: Execute a portion of javascript code.
		- <jybridResponse->call>: Execute an existing javascript function.
		- <jybridResponse->alert>: Display an alert dialog to the user.

	Elements are identified by the value of the HTML id attribute.  If you do
	not see your updates occuring on the browser side, ensure that you are
	using the correct id in your response.
*/

use Jybrid\Configuration;
use Jybrid\Errors\TraitCall;
use Jybrid\Language;

/**
 * Class Response
 * modified
 *
 * @todo    make abstract such as Request Class
 * @package Jybrid\Response
 */
class Response
{
	use TraitCall;
	use Attributes;
	/** Events as an own */
	use Events;
	use Html;
	use Scripts;
	use Context;
	use Input;
	use Css;
	use Dom;
	/**
	 * Primary Response instance
	 *
	 * @var int
	 */
	private static $mainInstanceId = 50;
	private static $instances;
	/**
	 * Own instance id
	 *
	 * @since 0.7.6.2
	 * @var int
	 */
	protected $instanceId;
	/*
		Array: aCommands

		Stores the commands that will be sent to the browser in the response.
	*/
	public $aCommands;
	/*
		String: characterEncoding

		The name of the encoding method you wish to use when dealing with
		special characters.  See <jybrid->setEncoding> for more information.
	*/
	private $characterEncoding;
	/*
		Mixed: returnValue

		A string, array or integer value to be returned to the caller when
		using 'synchronous' mode requests.  See <jybrid->setMode> for details.
	*/
	private $returnValue;
	// sorry but this config is static atm
	private $sContentType = 'application/json'; //'text/xml';

	/**
	 * Response constructor.
	 * Singleton Pattern
	 *
	 * @param int $instanceId
	 */
	private function __construct( int $instanceId )
	{
		//SkipDebug
		/*if (0 < \func_num_args())
		{
			$objLanguageManager = Language::getInstance();
			trigger_error(
			    $objLanguageManager->getText('JYBRSP:EDERR:01')
			    , E_USER_ERROR
			);
		}*/
		//EndSkipDebug

		$this->aCommands = [];

		$this->setInstanceId( $instanceId );
	}

	/**
	 * Getting a FactoryPattern instance
	 *
	 * @since 7.0
	 *
	 * @param null|int $instanceNr
	 *
	 * @return Response
	 */
	public static function getInstance(?int $instanceNr = null): Response
	{
		$instanceNr = $instanceNr ?: self::getMainInstanceId();
		$instances  = self::getInstances();
		if (!array_key_exists($instanceNr, $instances))
		{
			$instances[ $instanceNr ] = new Response( $instanceNr );
			self::setInstances($instances);
		}

		return self::$instances[$instanceNr];
	}

	/**
	 * jybridResponse Stack
	 *
	 * @todo on final response Rendering all responses will be enqueued into final
	 * @return array
	 */
	private static function getInstances(): array
	{
		return (array) self::$instances;
	}

	/**
	 * jybridResponse Stack
	 *
	 * @since 7.0
	 *
	 * @param array $instances
	 */
	private static function setInstances( array $instances ): void
	{
		self::$instances = $instances;
	}


	/*
		Function: replace

		Replace a specified value with another value within the given
		element's property.

		Parameters:

		sTarget - (string):  The id of the element to update.
		sAttribute - (string):  The property to be updated.
		sSearch - (string):  The needle to search for.
		sData - (string):  The data to use in place of the needle.
	*/
	public function replace($sTarget, $sAttribute, $sSearch, $sData): Response
	{
		return $this->addCommand(
		    [
			'cmd'  => 'rp',
			'id'   => $sTarget,
			'prop' => $sAttribute,
		    ],
		    [
			's' => $sSearch,
			'r' => $sData,
		    ]
		);
	}

	/*
		Function: alert

		Response command that is used to display an alert message to the user.

		Parameters:

		sMsg - (string):  The message to be displayed.

		Returns:

		object : The <jybridResponse> object.
	*/
	public function alert($sMsg)
	{
		return $this->addCommand(
		    [
			'cmd' => 'al',
		    ],
		    $sMsg
		);
	}

	public function debug($sMessage)
	{
		return $this->addCommand(
		    [
			'cmd' => 'dbg',
		    ],
		    $sMessage
		);
	}

	/*
		Function: remove

		Response command used to remove an element from the document.

		Parameters:

		sTarget - (string):  The id of the element to be removed.

		Returns:

		object : The <jybridResponse> object.
	*/
	public function remove($sTarget)
	{
		return $this->addCommand(
		    [
			'cmd' => 'rm',
			'id'  => $sTarget],
		    ''
		);
	}

	/*
		Function: create

		Response command used to create a new element on the browser.

		Parameters:

		sParent - (string):  The id of the parent element.
		sTag - (string):  The tag name to be used for the new element.
		sId - (string):  The id to assign to the new element.


		Returns:

		object : The <jybridResponse> object.
	*/

	public function create($sParent, $sTag, $sId)
	{


		return $this->addCommand(
		    [
			'cmd'  => 'ce',
			'id'   => $sParent,
			'prop' => $sId,
		    ],
		    $sTag
		);
	}

	/*
		Function: insert

		Response command used to insert a new element just prior to the specified
		element.

		Parameters:

		sBefore - (string):  The element used as a reference point for the
			insertion.
		sTag - (string):  The tag to be used for the new element.
		sId - (string):  The id to be used for the new element.

		Returns:

		object : The <jybridResponse> object.
	*/
	public function insert($sBefore, $sTag, $sId)
	{
		return $this->addCommand(
		    [
			'cmd'  => 'ie',
			'id'   => $sBefore,
			'prop' => $sId,
		    ],
		    $sTag
		);
	}

	/*
		Function: insertAfter

		Response command used to insert a new element after the specified
		one.

		Parameters:

		sAfter - (string):  The id of the element that will be used as a reference
			for the insertion.
		sTag - (string):  The tag name to be used for the new element.
		sId - (string):  The id to be used for the new element.

		Returns:

		object : The <jybridResponse> object.
	*/
	public function insertAfter($sAfter, $sTag, $sId)
	{
		return $this->addCommand(
		    [
			'cmd'  => 'ia',
			'id'   => $sAfter,
			'prop' => $sId,
		    ],
		    $sTag
		);
	}

	/*
		Function: waitFor
		
		Response command instructing jybrid to delay execution of the response
		commands until a specified condition is met.  Note, this returns control
		to the browser, so that other script operations can execute.  jybrid
		will continue to monitor the specified condition and, when it evaulates
		to true, will continue processing response commands.
		
		Parameters:
		
		script - (string):  A piece of javascript code that evaulates to true 
			or false.
		tenths - (integer):  The number of 1/10ths of a second to wait before
			timing out and continuing with the execution of the response
			commands.
		
		Returns:
		
		object : The <jybridResponse> object.
	*/
	public function waitFor($script, $tenths)
	{
		return $this->addCommand(
		    [
			'cmd'  => 'wf',
			'prop' => $tenths,
		    ],
		    $script
		);
	}

	/*
		Function: sleep
		
		Response command which instructs jybrid to pause execution
		of the response commands, returning control to the browser
		so it can perform other commands asynchronously.  After
		the specified delay, jybrid will continue execution of the 
		response commands.
		
		Parameters:
		
		tenths - (integer):  The number of 1/10ths of a second to
			sleep.
		
		Returns:
		
		object : The <jybridResponse> object.
	*/
	public function sleep($tenths)
	{
		$this->addCommand(
		    [
			'cmd'  => 's',
			'prop' => $tenths,
		    ],
		    ''
		);

		return $this;
	}

	/*
		Function: setReturnValue
		
		Stores a value that will be passed back as part of the response.
		When making synchronous requests, the calling javascript can
		obtain this value immediately as the return value of the
		<jybrid.call> function.
		
		Parameters:
		
		value - (mixed):  Any value.
		
		Returns:
		
		object : The <jybridResponse> object.
	*/
	public function setReturnValue($value)
	{
		$this->returnValue = 'JSON' === Configuration::getInstance()->getContentType() ? $value : null;

		return $this;
	}

	/*
		Function: getContentType
		
		Returns the current content type that will be used for the
		response packet.  (typically: "text/xml")
		
		Returns:
		
		string : The content type.
	*/
	public function getContentType(): string
	{
		return $this->sContentType;
	}

	/*
		Function: getOutput
	*/
	public function getOutput()
	{
		ob_start();

		if ( 'application/json' === $this->getContentType() )
		{
			$this->_printResponse_JSON();
		} else
		{
			//todo: trigger Error
		};

		return ob_get_clean();
	}

	/*
		Function: printOutput
		
		Prints the output, generated from the commands added to the response,
		that will be sent to the browser.
		
		Returns:
		
		string : The textual representation of the response commands.
	*/
	public function printOutput()
	{
		$this->_sendHeaders();
		if ( 'application/json' === $this->getContentType() )
		{
			echo $this->_printResponse_JSON();
		} else
		{
			//todo: trigger Error
		}
	}

	/**
	 * Function: _sendHeaders
	 * Used internally to generate the response headers.
	 *
	 * @since 0.7.7 headers in Manager
	 */
	public function _sendHeaders(): void {
		Manager::getInstance()->getHeader()->sendHeaders();
	}

	/*
		Function: getCommandCount
		
		Returns:
		
		integer : The number of commands in the response.
	*/
	public function getCommandCount(): int
	{
		return count($this->aCommands);
	}

	/*
		Function: appendResponse
		
		Merges the response commands from the specified <jybridResponse>
		object with the response commands in this <jybridResponse> object.
		
		Parameters:
		
		mCommands - (object):  <jybridResponse> object.
		bBefore - (boolean):  Add the new commands to the beginning 
			of the list.
			
	*/
	/**
	 * todo check array is $mCommands
	 *
	 * @param Response|array $mCommands
	 * @param bool           $bBefore
	 */
	public function appendResponse($mCommands = null, $bBefore = null)
	{
		if ($mCommands instanceof self)
		{
			$this->returnValue = $mCommands->returnValue;

			if ($bBefore)
			{
				$this->aCommands = array_merge($mCommands->aCommands, $this->aCommands);
			} else
			{
				$this->aCommands = array_merge($this->aCommands, $mCommands->aCommands);
			}
		} else if ( \is_array( $mCommands ) )
		{
			if ($bBefore)
			{
				$this->aCommands = array_merge($mCommands, $this->aCommands);
			} else
			{
				$this->aCommands = array_merge($this->aCommands, $mCommands);
			}
		} else
		{
			//SkipDebug
			if (!empty($mCommands))
			{
				$objLanguageManager = Language::getInstance();
				trigger_error(
					$objLanguageManager->getText( 'JYBRSP:LCERR:01' )
				    , E_USER_ERROR
				);
			}
			//EndSkipDebug
		}
	}

	/*
		Function: addPluginCommand
		
		Adds a response command that is generated by a plugin.
		
		Parameters:
		
		objPlugin - (object):  A reference to a plugin object.
		aAttributes - (array):  Array containing the attributes for this
			response command.
		mData - (mixed):  The data to be sent with this command.
		
		Returns:
		
		object : The <jybridResponse> object.
	*/
	public function addPluginCommand($objPlugin, $aAttributes, $mData): Response
	{
		$aAttributes['plg'] = $objPlugin->getName();

		return $this->addCommand($aAttributes, $mData);
	}

	/*
		Function: addCommand
		
		Add a response command to the array of commands that will
		be sent to the browser.
		
		Parameters:
		
		aAttributes - (array):  Associative array of attributes that
			will describe the command.
		mData - (mixed):  The data to be associated with this command.
		
		Returns:
		
		object : The <jybridResponse> command.
	*/
	public function addCommand($aAttributes, $mData)
	{


		/* merge commands if possible */
		if (in_array($aAttributes['cmd'], ['js', 'ap']))
		{
			if ($aLastCommand = array_pop($this->aCommands))
			{
				if ($aLastCommand['cmd'] === $aAttributes['cmd'])
				{
					if ('js' === $aLastCommand['cmd'])
					{
						$mData = $aLastCommand['data'] . '; ' . $mData;
					} elseif ( 'ap' === $aLastCommand['cmd'] && $aLastCommand['id'] === $aAttributes['id'] && $aLastCommand['prop'] === $aAttributes['prop'] )
					{
						$mData = $aLastCommand['data'] . ' ' . $mData;
					} else
					{
						$this->aCommands[] = $aLastCommand;
					}
				} else
				{
					$this->aCommands[] = $aLastCommand;
				}
			}
		}
		$aAttributes['data'] = $mData;
		$this->aCommands[]   = $aAttributes;

		return $this;
	}

	private function _printResponse_JSON()
	{
		$response = [];

		if (null !== $this->returnValue)
		{
			$response['jybrv'] = $this->returnValue;
		}

		$response['jybobj'] = [];

		foreach (array_keys($this->aCommands) as $sKey)
		{
			$response['jybobj'][] = $this->aCommands[ $sKey ];
		}

		return json_encode($response);
	}

	/**
	 * ObjResponse instances can be ordered by getInstance($int);
	 * The MainInstanceId is the default instanceId
	 *
	 * @see
	 * @return int
	 */
	public static function getMainInstanceId(): int
	{
		return self::$mainInstanceId;
	}

	/**
	 * @return null|int
	 */
	public function getInstanceId(): ?int {
		return $this->instanceId;
	}

	/**
	 * @param int $instanceId
	 */
	public function setInstanceId( ?int $instanceId = null ): void {
		$this->instanceId = (int) $instanceId;
	}
}// end class jybridResponse

