<?php

use Xajax\plugin_layer\RequestIface;

require_once __DIR__ . '/bootstrap.php';

$xajax = new xajax();

//$xajax->configure('debug', true);
$xajax->configure('javascript URI', '../');

//require_once $core . '/xajaxPlugin.inc.php';
//require_once $core . '/xajaxPluginManager.inc.php';

class testPlugin extends xajaxResponsePlugin implements RequestIface
{
	protected $sDefer;

	public function __construct()
	{
		$this->sDefer = '';
	}

	public function getName(): string
	{
		return 'testPlugin';
	}

	public function generateClientScript()
	{
		/**
		 * @since xajax 7.0.1 so it will be disabled does not work with deferred
		 * @todo  check the parent class should populate the 'deferScriptGeneration' to the child classes
		 * */

		#	echo "\n<script type='text/javascript' " . $this->sDefer . "charset='UTF-8'>\n";
		echo "/* <![CDATA[ */\n";

		echo "xajax.command.handler.register('testPlg', function(args) { \n";
		echo "\talert('Test plugin command received: ' + args.data);\n";
		echo "});\n";

		echo "/* ]]> */\n";
		#	echo "</script>\n";
	}

	public function testMethod()
	{
		$this->addCommand(['cmd' => 'testPlg'], 'abcde]]>fg');
	}

	/**
	 * Registers an Single Request
	 *
	 * @since 7.0
	 *
	 * @param array $aArgs
	 *
	 * @return xajaxRequest
	 */
	public function registerRequest(array $aArgs = []): xajaxRequest
	{
		// TODO: Implement registerRequest() method.
	}

	public function generateHash()
	{
		// TODO: Implement generateHash() method.
		return '';
	}
}

$objPluginManager = xajaxPluginManager::getInstance();
$objPluginManager->registerPlugin(new testPlugin(), 300);

function showOutput()
{
	$testResponse = xajaxResponse::getInstance();
	$testResponse->alert('Edit this test and uncomment lines in the showOutput() method to test plugin calling');
	// PHP4 & PHP5
	//$testResponse->plugin('testPlugin', 'testMethod');

	// PHP5 ONLY - Uncomment to test
	$testResponse->plugin('testPlugin')->testMethod();

	// PHP5 ONLY - Uncomment to test
	//$testResponse->testPlugin->testMethod();

	$testResponseOutput = htmlspecialchars($testResponse->getOutput());

	$objResponse = xajaxResponse::getInstance();
	$objResponse->assign('submittedDiv', 'innerHTML', $testResponseOutput);
	$objResponse->plugin('testPlugin', 'testMethod');

	return $objResponse;
}

$reqShowOutput = $xajax->register(XAJAX_FUNCTION, 'showOutput');

//$xajax->configure('responseType', 'JSON');
$xajax->processRequest();
$xajax->getConfig()->setJavascriptURI('../');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
		"http://www.w3.org/TR/2000/REC-xhtml1-20000126/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Basic Plugin Test | xajax Tests</title>
	<?php $xajax->printJavascript(true) ?>
</head>
<body>
<h2><a href="index.php">xajax Tests</a></h2>
<h1>Basic Plugin Test</h1>
<form id="testForm1" onsubmit="return false;">
	<p><input type="button" id="btnShowOutput" value="Show Response" onclick="xajax_showOutput();" /></p>
</form>
<div id="submittedDiv"></div>
</body>
</html>