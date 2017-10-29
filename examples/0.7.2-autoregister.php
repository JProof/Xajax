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

use Xajax\Core\Factory;use Xajax\Plugins\Userfunction\Request;
set_error_handler('\Xajax\Core\Errors\Handler::addError');

$xConfig = Factory::getInstance()->getConfig();



$anXajaxUserFunction = Request::autoRegister('listDirectory');
$xConfig->setErrorHandler('\Xajax\Core\Errors\Handler::addException')->setToHtml(true);


$xScripts = Factory::getScripts();
$xScripts->getConfiguration()->setDeferScriptGeneration(false)->setUseUncompressedScripts(true)->setDebug(true)->setVerbose(false);
$xScripts->addScriptDir('/xajax-php-7/scripts');

// override the core Script-Location
#$xScripts->addScript(new Core(['scriptName' => 'xajax', 'fileName' => 'xajax_core2.js']));
#$xScripts->addScript(new Core(['scriptName' => 'xajax', 'fileName' => 'xajax_core3.js', 'dir' => '/media/scripts']));

// lock the core-script completely
#$xScripts->setLockScript('xajax');

echo $xScripts->getScriptUrl('xajax');

function listDirectory()
{
	$objResponse = Factory::getResponseInstance();
	$objResponse->assign('geth', 'innerHTML', 'test');

	try
	{
		$objResponse->calls();
	}
	catch (Exception $exception)
	{

	    \Xajax\Core\Errors\Handler::addError($exception);
	}

	return $objResponse;
}

Factory::getInstance()->processRequest();

$anXajaxUserFunction->useSingleQuote()->setParameter('test', 'roman')->addParameterArray('text', ['roman'  => 'test',
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
<a onclick="<?php echo $anXajaxUserFunction->printScript() ?>">test</a>
</body>
</html><?php
