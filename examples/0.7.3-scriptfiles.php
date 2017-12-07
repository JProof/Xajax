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
 * @since              30.09.2017
 */
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';

use Xajax\Factory;
set_error_handler('\Xajax\Errors\Handler::addError');

$xConfig = Factory::getInstance()->getConfig();
$xConfig->setErrorHandler('\Xajax\Errors\Handler::addException')->setToHtml(true);

// Registers an Function (earlier xajax->registerFunction())
$anXajaxUserFunction = Xajax\Plugins\Userfunction\Request::autoRegister('listDirectory');


/**JS Configuration**/
$xScripts = Factory::getScripts();
$xScripts->getConfiguration()->setDeferScriptGeneration(false)->setUseUncompressedScripts(true)->setDebug(true);
// register an alternative JS Directory where all Xajax Scripts can be found. If is an Script missing,
// xajax will try to find the wanted script in an lower-priority Location

$buggyDir = '/neverFoundthisRelativeDir/examples/demo/assets/override-js-dir/';
if (!$xScripts->addScriptDir($buggyDir))
{
	echo '<p>Buggy-Directory does not exists an can not be add to xajax as override js directory' . $buggyDir . '</p>';
}


$goodDir = __DIR__ . '/demo/assets/override-js-dir';
if (!$xScripts->addScriptDir($goodDir))
{
	echo '<p>Good Directory does not exists an can not be add to xajax as override js directory' . $goodDir . '</p>';
}
else
{
	echo '<p>Good Directory exists as absolute dir ' . $goodDir . '</p>';
}


// override the core Script-Location
$xScripts->addScript(new Xajax\Scripts\Core(['scriptName' => 'xajax', 'fileName' => 'xajax_core2.js']));
#$xScripts->addScript(new Core(['scriptName' => 'xajax', 'fileName' => 'xajax_core3.js', 'dir' => '/media/scripts']));

// lock the core-script completely
#$xScripts->setLockScript('xajax');

var_dump($xScripts->getScriptUrl('xajax'));
return;
function listDirectory()
{
	$objResponse = Factory::getResponseInstance();
	$objResponse->assign('get', 'innerHTML', 'test');

	try
	{
		// error method call
		$objResponse->calls();
	}
	catch (Exception $exception)
	{

		// handles specified Error-Handler
		\Xajax\Errors\Handler::addError($exception);
	}

	return $objResponse;
}

Factory::getInstance()->processRequest();

$anXajaxUserFunction->useSingleQuote()->setParameter('test', 'roman')->addParameterArray('text', [
    'roman'  => 'test',
    'weiter' => 'more',
    'my'     => ['romans' => 'more']]);
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Title</title>
	<?php Factory::getInstance()->printJavascript(); ?>
</head>
<body>
<a onclick="<?php $anXajaxUserFunction->printScript() ?>">test</a>
</body>
</html>
<?php
