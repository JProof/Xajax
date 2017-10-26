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
$xConfig = Factory::getInstance()->getConfig();

$xConfig->setJavascriptURI('/xajax-php-7/');
$xConfig->setDeferScriptGeneration(false);
$anXajaxUserFunction = Request::autoRegister('listDirectory');
$xConfig->setErrorHandler(true);
$xConfig->setDebug(false);

function listDirectory()
{
	$objResponse = Factory::getResponseInstance();
	$objResponse->alert('geth');
	try
	{
		$objResponse->calls();
	}
	catch (Exception $exception)
	{
		\Xajax\Core\addError($exception);
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
