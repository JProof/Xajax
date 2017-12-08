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
$xScripts->addScript(new Xajax\Scripts\Core(['scriptName' => 'xajax', 'fileName' => 'nothing.js']));
// override the core Script-Location
$xScripts->addScript(new Xajax\Scripts\Core(['scriptName' => 'xajax', 'fileName' => 'isThirdButHasDirxajax_core2.js', 'dir' => $goodDir]));
// File exists in Demo-Data and will be used
$xScripts->addScript(new Xajax\Scripts\Core(['scriptName' => 'xajax', 'fileName' => 'xajax_core_maybe_custom.js']));

// lock the core-script completely
#$xScripts->setLockScript('xajax');

$xScripts->addScript(new Xajax\Scripts\Core(['scriptName' => 'xajax', 'fileName' => 'nothing2.js']));
Factory::getInstance()->processRequest();
$xScripts->addScript(new Xajax\Scripts\Core(['scriptName' => 'xajax', 'fileName' => 'nothing3.js']));
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Title</title>
	<?php Factory::getInstance()->printJavascript(); ?>
</head>
<body>
<h3>no Function need, just an javascript-files demo</h3>
</body>
</html>
<?php
