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
 * @since              10.12.2017
 */

declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';

use Xajax\Factory;

set_error_handler('\Xajax\Errors\Handler::addError');

$xConfig = Factory::getInstance()->getConfig();
$xConfig->setExitAllowed(true);
$xConfig->setErrorHandler('\Xajax\Errors\Handler::addException')->setToHtml(true);

/**JS Configuration**/
$xScripts = Factory::getScripts();
$xScripts->getConfiguration()->setDeferScriptGeneration(false)->setUseUncompressedScripts(true)->setDebug(false);

Factory::getResponseInstance()->alert('yes, call via cms');

$xajaxCmsGet  = Xajax\Plugins\Cms\Request::autoRegister('get');
$xajaxCmsPost = Xajax\Plugins\Cms\Request::autoRegister('post');

// at this point all objResponse Commands must be finished
Factory::processRequest();
?>

	<!DOCTYPE html>
	<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Method Test</title>
	    <?php Factory::getInstance()->printJavascript(); ?>
	</head>
	<body>
	<h3>no Function need, just an javascript-files demo</h3>
	<a href="javascript:void(null)" onclick="<?php $xajaxCmsGet->printScript() ?>">Get</a>
	<a href="javascript:void(null)" onclick="<?php $xajaxCmsPost->printScript() ?>">Post</a>

	</body>
	</html>
<?php
