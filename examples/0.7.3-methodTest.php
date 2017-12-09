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
 * @since              08.12.2017
 */

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

use Xajax\Factory;

set_error_handler('\Xajax\Errors\Handler::addError');

$xConfig = Factory::getInstance()->getConfig();
$xConfig->setErrorHandler('\Xajax\Errors\Handler::addException')->setToHtml(true);
$xufListDirectory1 = Xajax\Plugins\Userfunction\Request::autoRegister('listDirectory1');

/**
 * Call directly an method during method-name
 *
 * @return \Xajax\Response\Response
 */
function listDirectory()
{
	$objResponse = Factory::getResponseInstance();
	$objResponse->assign('directoryOutput', 'innerHTML', '<i>cleared ...this message will be overridden</i>');

	if ($handle = opendir(__DIR__))
	{
		$objResponse->assign('information', 'innerHTML', '<i>' . $handle . '</i>');

		$cntFiles = 0;
		/* Das ist der korrekte Weg, ein Verzeichnis zu durchlaufen. */
		while (false !== ($entry = readdir($handle)))
		{
			if (is_file(__DIR__ . '/' . $entry))
			{
				$objResponse->append('directoryOutput', 'innerHTML', '<div>' . $entry . '</div>');
				$cntFiles ++;
			}
		}

		$objResponse->append('information', 'innerHTML', 'count Files: ' . $cntFiles);
		closedir($handle);
	}
	else
	{
		$objResponse->alert('Could not open handle');
	}
	return $objResponse;
}

Factory::getInstance()->processRequest();
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
<a href="javascript:void(null)" onclick="<?php $xufListDirectory1->printScript() ?>">List Directory via method-name</a>
<div id="information">

</div>
<div id="directoryOutput">

</div>
<div id="filesOutput"></div>
</body>
</html>