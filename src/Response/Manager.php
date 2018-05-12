<?php

namespace Jybrid\Response {

	/*
		File: jybridResponseManager.inc.php

		Contains the jybridResponseManager class

		Title: jybridResponseManager class

		Please see <copyright.inc.php> for a detailed description, copyright
		and license information.
	*/

	/*
		@package jybrid
		@version $Id: jybridResponseManager.inc.php 362 2007-05-29 15:32:24Z calltoconstruct $
		@copyright Copyright (c) 2005-2007 by Jared White & J. Max Wilson
		@copyright Copyright (c) 2008-2010 by Joseph Woolley, Steffen Konerow, Jared White  & J. Max Wilson
		@license http://www.jybridproject.org/bsd_license.txt BSD License
	*/

	/*
		Class: jybridResponseManager

		This class stores and tracks the response that will be returned after
		processing a request.  The response manager represents a single point
		of contact for working with <jybridResponse> objects as well as
		<jybridCustomResponse> objects.
	*/

	use Jybrid\Header\PhpResponse;
	use Jybrid\Language;

	final class Manager
	{
		use \Jybrid\Errors\TraitCall;

		/**@since  0.7.7 Header* */

		/*
			Object: objResponse

			The current response object that will be sent back to the browser
			once the request processing phase is complete.
		*/
		/**
		 * @var \Jybrid\Response\Response
		 */
		private $objResponse;
		/*
			Array: aDebugMessages
		*/
		private $aDebugMessages = [];
		/**
		 * @var PhpResponse
		 */
		private $header;

		private function __construct() {
			$this->header = new PhpResponse();
		}

		/*
			Function: getInstance

			Implementation of the singleton pattern: provide a single instance of the <jybridResponseManager>
			to all who request it.
		*/
		public static function &getInstance(): Manager
		{
			static $obj;
			if (!$obj)
			{
				$obj = new self();
			}

			return $obj;
		}

		/*
			Function: clear

			Clear the current response.  A new response will need to be appended
			before the request processing is complete.
		*/
		public function clear()
		{
			$this->objResponse = null;
		}

		/*
			Function: append

			Used, primarily internally, to append one response object onto the end of another.  You can
			append one jybridResponse to the end of another, or append a jybridCustomResponse onto the end of
			another jybridCustomResponse.  However, you cannot append a standard response object onto the end
			of a custom response and likewise, you cannot append a custom response onto the end of a standard
			response.

			Parameters:

			$mResponse - (object):  The new response object to be added to the current response object.

			If no prior response has been appended, this response becomes the main response object to which other
			response objects will be appended.
		*/
		public function append($mResponse)
		{
			if ( $mResponse instanceof \Jybrid\Response\Response )
			{
				if (null === $this->objResponse)
				{
					$this->objResponse = $mResponse;
				} else if ( $this->objResponse instanceof \Jybrid\Response\Response )
				{
					if ($this->objResponse !== $mResponse)
					{
						$this->objResponse->appendResponse($mResponse);
					}
				} else
				{
					$objLanguageManager = Language::getInstance();
					$this->debug(
						$objLanguageManager->getText( 'JYBRM:MXRTERR' )
						. get_class($this->objResponse)
						. ')'
					);
				}
			} else
			{
				$objLanguageManager = Language::getInstance();
				$this->debug( $objLanguageManager->getText( 'JYBRM:IRERR' ) );
			}
		}

		/*
			Function: debug

			Appends a debug message on the end of the debug message queue.  Debug messages
			will be sent to the client with the normal response (if the response object supports
			the sending of debug messages, see: <jybridResponse>)

			Parameters:

			$sMessage - (string):  The text of the debug message to be sent.
		*/
		public function debug(?string $sMessage = null)
		{
			if (null !== $sMessage)
			{
				$this->aDebugMessages[] = $sMessage;
			}
		}

		/*
			Function: send

			Prints the response object to the output stream, thus sending the response to the client.
		*/
		public function send()
		{
			if (null !== ($objResponse = $this->getObjResponse()))
			{
				foreach ($this->aDebugMessages as $sMessage)
				{
					$objResponse->debug($sMessage);
				}
				$this->aDebugMessages = [];

				$objResponse->printOutput();
			}
		}

		/**
		 * @return \Jybrid\Response\Response
		 */
		public function getObjResponse(): ?\Jybrid\Response\Response
		{
			return $this->objResponse;
		}

		/**
		 * @return PhpResponse
		 */
		public function getHeader(): PhpResponse {
			return $this->header;
		}
	}
}