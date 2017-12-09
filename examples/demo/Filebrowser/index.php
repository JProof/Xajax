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
 * @since              26.09.2017
 */
declare(strict_types=1);

use Xajax\Examples\Demo\Filebrowser\Directories;
use Xajax\Factory;
use Xajax\Response\Response;

require_once dirname(__DIR__) . '/bootstrap.php';
$xajax                    = Xajax\Factory::getInstance();
$xajaxScriptConfiguration = Factory::getScripts()->getConfiguration();
$xajaxConfig              = $xajax->getConfig();
$xajaxScriptConfiguration->setUseUncompressedScripts(true);
$xajaxScriptConfiguration->setDeferScriptGeneration(false);
/**
 * Simple instance
 *
 * @return Directories
 */
function getDirectories()
{
	static $directories;
	if ($directories)
	{
		return $directories;
	}
	$directories = new Directories();
	$directories->setDocRoot(dirname(__DIR__, 2))->setScriptRoot(dirname(__DIR__, 2));

	return $directories;
}

/**
 * Method where xajax sends the request against. After Running the Method the objResponse goes back to Xajax an will be send to the browser
 *
 * @param string $params
 *
 * @return Response
 */
function listDir(?string $params = null)
{
	$objResponse = Factory::getResponseInstance();

	$directories = getDirectories();

	$directories->getRecursiveDir($params, true);

	$openButton = Directories::prefixLastPart($params, 'open_');

	$tmpl = htmlCreateNode($directories, $params);
	if ('' === $tmpl)
	{
		$objResponse->attrReplace($openButton, 'aria-disabled', 'disabled', 'true');
		$objResponse->assign($openButton, 'className', 'btn badge badge-warning disabled');
		$objResponse->append($openButton, 'innerHTML', ' There are no directories deeper');
	}
	else
	{
		// attaching the whole html template
		$objResponse->assign(Directories::getParentNodeForNewChild($params), 'innerHTML', $tmpl);
	}

	return $objResponse;
}

function htmlCreateNode(Directories $directories, ? string $parent = null)
{
	ob_start();
	$dirs              = $directories->getDirectoriesFromDir();
	$simpleExclude = ['.', '..', '.git'];
	$fromDir           = $directories->getDocFromDir();

	if (null === $parent)
	{
		$parent = Directories::getHash($fromDir);
	}
	else
	{
		$n = Directories::splitPathway($parent);
		//	array_pop($n);
		$parent = Directories::stringifyPathway($n);
	}

	?>
	<?php
	foreach ($dirs as $dir)
	{

		if (!in_array($dir->getFilename(), $simpleExclude, true) && is_dir($dir->getPathname()))
		{

			$node = Directories::getHash($dir->getPathname());

			?>
					<li><span><?php echo $node ?></span> - <span><?php echo basename($dir->getPathname()); ?></span>
						<div class="btn-group-justified">
							<a role="button" id="<?php echo Directories::prefixLastPart($node, 'open_') ?>" class="btn badge badge-info"
							   href="javascript:void(null)"
							   onclick="xajax_listDir('<?php echo Directories::compilePathway($node, $parent) ?>')"><i class="fa fa-plus"></i></a>
						</div>
						<div id="<?php echo Directories::getChildNodeHashId($dir->getPathname()) ?>">
						</div>
					</li>
			<?php
		}
		?>
		<?php
	}

	$content = ob_get_contents();
	ob_end_clean();
	$content = trim($content);

	return ('' === $content) ? '' : '<ul id="' . Directories::getParentNodeHashId($fromDir) . '">' . $content . '</ul>';
}

$xajaxFunctionListDir = Xajax\Plugins\Userfunction\Request::autoRegister('listDir');

$xajax->processRequest();

$directories = getDirectories();

?>
	<!DOCTYPE html>
	<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title><?php echo basename(__DIR__) ?></title>
		<link rel="stylesheet" href="../assets/font-awesome-4.7.0/css/font-awesome.min.css">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css"
			  integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
	</head>
	<body>
	<div class="container-fluid">
	    <?php
	    echo htmlCreateNode($directories);
	    ?>
		<a href="javascript:void(null)" onclick="<?php echo $xajaxFunctionListDir->getScript() ?>">list</a>
	</div>
    <?php $xajax->printJavascript(); ?>
	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
			crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"
			integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js"
			integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>
	</body>
	</html>
<?php