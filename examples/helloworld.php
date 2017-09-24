<?php
/*
	File: helloworld.php

	Test / example page demonstrating the basic xajax implementation.
	
	Title: Hello world sample page.
	
	Please see <copyright.inc.php> for a detailed description, copyright
	and license information.
*/

/*
	@package xajax
	@version $Id: helloworld.php 362 2007-05-29 15:32:24Z calltoconstruct $
	@copyright Copyright (c) 2005-2006 by Jared White & J. Max Wilson
	@license http://www.xajaxproject.org/bsd_license.txt BSD License
*/

/*
	Section: Standard xajax startup
	
	- include <xajax.inc.php>
	- instantiate main <xajax> object
*/

use Xajax\Factory;

require_once 'bootstrap.php';

$xajax = Factory::getInstance();

/*
	- enable deubgging if desired
	- set the javascript uri (location of xajax js files)
*/
//$xajax->configure('debug', true);
$xajax->configure('javascript URI', '../');

/*
	Function: helloWorld
	
	Modify the innerHTML of div1.
*/
function helloWorld($isCaps)
{
	if ($isCaps)
    {
	    $text = 'HELLO WORLD!';
    }
	else
    {
	    $text = 'Hello World!';
    }

	$objResponse = xajaxResponse::getInstance();
	$objResponse->assign('div1', 'innerHTML', $text);

	return $objResponse;
}

/*
	Function: setColor
	
	Modify the style.color of div1
*/
function setColor($sColor)
{
	$objResponse = xajaxResponse::getInstance();
	$objResponse->assign('div1', 'style.color', $sColor);

	return $objResponse;
}

/*
	Section:  Register functions
	
	- <helloWorld>
	- <setColor>
*/

/** @var \Xajax\plugin_layer\RequestIface $xajaxRequestPluginFunction */
$xajaxRequestPluginFunction = $xajax->getPlugin('function');
$reqHelloWorldMixed         = $xajaxRequestPluginFunction->registerRequest((array) 'helloWorld');
// or direct
//$reqHelloWorldMixed= $xajax->getPlugin('function')->registerRequest((array) 'helloWorld');

// old Way
//$reqHelloWorldMixed = $xajax->register(XAJAX_FUNCTION, 'helloWorld');
$reqHelloWorldMixed->setParameter(0, XAJAX_JS_VALUE, 0);

/** @var \xajaxRequest $reqHelloWorldAllCaps */
$reqHelloWorldAllCaps = $xajax->register(XAJAX_FUNCTION, 'helloWorld');
if ($reqHelloWorldAllCaps)
{
	$reqHelloWorldAllCaps->setParameter(0, XAJAX_JS_VALUE, 1);
}

/** @var \xajaxRequest $reqSetColor */
$reqSetColor = $xajax->register(XAJAX_FUNCTION, 'setColor');
if ($reqSetColor)
{
	$reqSetColor->setParameter(0, XAJAX_INPUT_VALUE, 'colorselect');
}

/*
	Section: processRequest
	
	This will detect an incoming xajax request, process it and exit.  If this is
	not a xajax request, then it is a request to load the initial contents of the page
	(HTML).
	
	Everything prior to this statement will be executed upon each request (whether it
	is for the initial page load or a xajax request.  Everything after this statement
	will be executed only when the page is first loaded.
*/

$xajax->processRequest();

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>xajax example</title>
	<?php
	// output the xajax javascript. This must be called between the head tags
	$xajax->printJavascript();
	?>
	<script type='text/javascript'>
		/* <![CDATA[ */
		window.onload = function(){
			// call the helloWorld function to populate the div on load
		<?php $reqHelloWorldAllCaps->printScript(); ?>
			// call the setColor function on load
		<?php $reqSetColor->printScript(); ?>
		}
		/* ]]> */
	</script>
</head>
<body style="text-align:center;">
<div id="div1">&#160;</div>
<form action="javascript:void(null)">
	<fieldset>
		<button onclick='<?php $reqHelloWorldMixed->printScript(); ?>'>Click Me</button>
		<button onclick='<?php $reqHelloWorldAllCaps->printScript(); ?>'>CLICK ME</button>
		<label>Change Color
			<select id="colorselect" name="colorselect"
					onchange='<?php $reqSetColor->printScript(); ?>'>
				<option value="black" selected="selected">Black</option>
				<option value="red">Red</option>
				<option value="green">Green</option>
				<option value="blue">Blue</option>
			</select>
		</label>
	</fieldset>
</form>
</body>
</html>