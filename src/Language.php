<?php

namespace Jybrid {

	/*
		File: Language.inc.php

		Contains the code that manages the inclusion of alternate language support
		files; so debug and error messages can be shown in a language other than
		the default (english) language.

		Title: Language class

		Please see <copyright.inc.php> for a detailed description, copyright
		and license information.
	*/

	/*
		@package jybrid
		@version $Id: Language.inc.php 362 2007-05-29 15:32:24Z calltoconstruct $
		@copyright Copyright (c) 2005-2007 by Jared White & J. Max Wilson
		@copyright Copyright (c) 2008-2010 by Joseph Woolley, Steffen Konerow, Jared White  & J. Max Wilson
		@license http://www.jybridproject.org/bsd_license.txt BSD License
	*/

	/*
		Class: Language

		This class contains the default language (english) and the code used to supply
		debug and error messages upon request; as well as the code used to load alternate
		language text as requested via the <jybrid::configure> function.
	*/

	final class Language
	{
		/*
			Array: aMessages

			An array of the currently registered languages.
		*/
		private $aMessages;
		/*
			String: sLanguage

			The currently configured language.
		*/
		private $sLanguage;

		/*
			Function: Language

			Construct and initialize the one and only jybrid language manager object.
		*/
		private function __construct()
		{
			$this->aMessages = [];

			$this->aMessages['en'] = [
				'LOGHDR:01'         => '** jybrid Error Log - ',
				'LOGHDR:02'         => " **\n",
				'LOGHDR:03'         => "\n\n\n",
				'LOGERR:01'         => "** Logging Error **\n\njybrid was unable to write to the error log file:\n",
				'LOGMSG:01'         => "** PHP Error Messages: **",
				'CMPRSJS:RDERR:01'  => 'The jybrid uncompressed Javascript file could not be found in the <b>',
				'CMPRSJS:RDERR:02'  => '</b> folder.  Error ',
				'CMPRSJS:WTERR:01'  => 'The jybrid compressed javascript file could not be written in the <b>',
				'CMPRSJS:WTERR:02'  => '</b> folder.  Error ',
				'CMPRSPHP:WTERR:01' => 'The jybrid compressed file <b>',
				'CMPRSPHP:WTERR:02' => '</b> could not be written to.  Error ',
				'CMPRSAIO:WTERR:01' => 'The jybrid compressed file <b>',
				'CMPRSAIO:WTERR:02' => '/jybridAIO.inc.php</b> could not be written to.  Error ',
				'DTCTURI:01'        => 'jybrid Error: jybrid failed to automatically identify your Request URI.',
				'DTCTURI:02'        => 'Please set the Request URI explicitly when you instantiate the jybrid object.',
				'ARGMGR:ERR:01'     => 'Malformed object argument received: ',
				'ARGMGR:ERR:02'     => ' <==> ',
				'ARGMGR:ERR:03'     => 'The incoming jybrid data could not be converted from UTF-8',
				'JYBCTL:IAERR:01'   => 'Invalid attribute [',
				'JYBCTL:IAERR:02'   => '] for element [',
				'JYBCTL:IAERR:03'   => '].',
				'JYBCTL:IRERR:01'   => 'Invalid request object passed to jybridControl::setEvent',
				'JYBCTL:IEERR:01'   => 'Invalid attribute (event name) [',
				'JYBCTL:IEERR:02'   => '] for element [',
				'JYBCTL:IEERR:03'   => '].',
				'JYBCTL:MAERR:01'   => 'Missing required attribute [',
				'JYBCTL:MAERR:02'   => '] for element [',
				'JYBCTL:MAERR:03'   => '].',
				'JYBCTL:IETERR:01'  => "Invalid end tag designation; should be forbidden or optional.\n",
				'JYBCTL:ICERR:01'   => "Invalid class specified for html control; should be %inline, %block or %flow.\n",
				'JYBCTL:ICLERR:01'  => 'Invalid control passed to addChild; should be derived from jybridControl.',
				'JYBCTL:ICLERR:02'  => 'Invalid control passed to addChild [',
				'JYBCTL:ICLERR:03'  => '] for element [',
				'JYBCTL:ICLERR:04'  => "].\n",
				'JYBCTL:ICHERR:01'  => 'Invalid parameter passed to jybridControl::addChildren; should be array of jybridControl objects',
				'JYBCTL:MRAERR:01'  => 'Missing required attribute [',
				'JYBCTL:MRAERR:02'  => '] for element [',
				'JYBCTL:MRAERR:03'  => '].',
				'JYBPLG:GNERR:01'   => 'Response plugin should override the getName function.',
				'JYBPLG:PERR:01'    => 'Response plugin should override the process function.',
				'JYBPM:IPLGERR:01'  => 'Attempt to register invalid plugin: ',
				'JYBPM:IPLGERR:02'  => ' should be derived from jybridRequestPlugin or jybridResponsePlugin.',
				'JYBPM:MRMERR:01'   => 'Failed to locate registration method for the following: ',
				'JYBRSP:EDERR:01'   => 'Passing character encoding to the jybridResponse constructor is deprecated, instead use $jybrid->configure("characterEncoding", ...);',
				'JYBRSP:MPERR:01'   => 'Invalid or missing plugin name detected in call to jybridResponse::plugin',
				'JYBRSP:CPERR:01'   => "The \$sType parameter of addCreate has been deprecated.  Use the addCreateInput() method instead.",
				'JYBRSP:LCERR:01'   => "The jybrid response object could not load commands as the data provided was not a valid array.",
				'JYBRSP:AKERR:01'   => 'Invalid tag name encoded in array.',
				'JYBRSP:IEAERR:01'  => 'Improperly encoded array.',
				'JYBRSP:NEAERR:01'  => 'Non-encoded array detected.',
				'JYBRSP:MBEERR:01'  => 'The jybrid response output could not be converted to HTML entities because the mb_convert_encoding function is not available',
				'JYBRSP:MXRTERR'    => 'Error: Cannot mix types in a single response.',
				'JYBRSP:MXCTERR'    => 'Error: Cannot mix content types in a single response.',
				'JYBRSP:MXCEERR'    => 'Error: Cannot mix character encodings in a single response.',
				'JYBRSP:MXOEERR'    => 'Error: Cannot mix output entities (true/false) in a single response.',
				'JYBRM:IRERR'       => 'An invalid response was returned while processing this request.',
				'JYBRM:MXRTERR'     => 'Error:  You cannot mix response types while processing a single request: ',
			];

			$this->sLanguage = 'en';
		}

		/*
			Function: getInstance

			Implements the singleton pattern: provides a single instance of the jybrid
			language manager object to all object which request it.
		*/
		public static function &getInstance()
		{
			static $obj;
			if (!$obj)
			{
				$obj = new self();
			}

			return $obj;
		}



		/*
			Function: register

			Called to register an array of alternate language messages.

			Parameters:

			sLanguage - (string): the character code which represents the language being registered.
			aMessages - (array): the array of translated debug and error messages
		*/
		public function register($sLanguage, $aMessages)
		{
			$this->aMessages[$sLanguage] = $aMessages;
		}

		/*
			Function: getText

			Called by the main jybrid object and other objects during the initial page generation
			or request processing phase to obtain language specific debug and error messages.

			sMessage - (string):  A code indicating the message text being requested.
		*/
		public function getText($sMessage): string
		{
			if (isset($this->aMessages[$this->sLanguage], $this->aMessages[$this->sLanguage][$sMessage]))
			{
				return (string) $this->aMessages[$this->sLanguage][$sMessage];
			}

			return '(Unknown language or message identifier)'
			    . $this->sLanguage
			    . '::'
			    . $sMessage;
		}
	}
}
