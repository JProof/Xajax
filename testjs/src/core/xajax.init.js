/*
	File: xajax_core.js
	
	This file contains the definition of the main xajax javascript core.
	
	This is the client side code which runs on the web browser or similar
	web enabled application.  Include this in the HEAD of each page for
	which you wish to use xajax.
	
	Title: xajax core javascript library
	
	Please see <copyright.inc.php> for a detailed description, copyright
	and license information.
*/
/*
	@package xajax
	@version $Id: xajax_core_uncompressed.js 327 2007-02-28 16:55:26Z calltoconstruct $
	@copyright Copyright (c) 2005-2007 by Jared White & J. Max Wilson
	@copyright Copyright (c) 2008-2010 by Joseph Woolley, Steffen Konerow, Jared White  & J. Max Wilson
	@license http://www.xajaxproject.org/bsd_license.txt BSD License
*/
/*
	Class: xajax.config
	
	This class contains all the default configuration settings.  These
	are application level settings; however, they can be overridden
	by including a xajax.config definition prior to including the
	<xajax_core.js> file, or by specifying the appropriate configuration
	options on a per call basis.
*/
if ("undefined" === typeof xajax) {
    var xajax = {};
}