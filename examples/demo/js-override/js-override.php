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
 * @since              05.12.2017
 */
declare(strict_types=1);

use Xajax\Factory;

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Title</title>
	<link href="../assets/kube/dist/css/kube.min.css" rel="stylesheet" type="text/css">
	<script src="../assets/kube/dist/js/kube.min.js" type="text/javascript"></script>
</head>
<body>
<div class="row ">
	<div class="col col-2"></div>
	<div class="col col-8 align-center">

	    <?php
	    // include the composer autoloader-file into this example
	    require_once dirname(__DIR__, 2) . '/bootstrap.php';

	    $xScripts = Factory::getScripts();
	    $xScripts->getConfiguration()->setDeferScriptGeneration(false)->setUseUncompressedScripts(true)->setDebug(true)->setVerbose(false);

	    /**
	     * register an alternative JS Directory where all Xajax Scripts can be found. If is an Script missing,
	     * xajax will try to find the wanted script in an lower-priority Location
	     **/
	    $xScripts->addScriptDir('/examples/demo/js-override/myTemplateDir/assets/js');

	    /**
	     * Adding an Script means:
	     * If the scriptName=>xajax was not registered as js-script it registers new script with override name (the regular xajax_core.js or
	     * *_debug will not used currently) If scriptName=>xajax was already registered. For xajax_core.js the replacement-script
	     * myXajaxCustom.js will be rendered
	     **/
	    $xScripts->addScript(new Xajax\Scripts\Core(['scriptName' => 'xajax', 'fileName' => 'myXajaxCustom.js']));
	    echo $xScripts->getScriptUrl('xajax');
	    ?><br/><?php

	    $str   = '$xScripts->addScript(new Xajax\Scripts\Core([\'scriptName\' => \'xajax\', \'fileName\' => \'xajax_core3.js\', \'dir\' => \'/media/scripts\']));';
	    $html0 = <<<EOT
<h3>Set specific JavaScript to an explicit Directory</h3>
 <code>$str</code> <br/>
Adding an script with specified directory.<br/>
with parameter <code> dir=>'' </code><br/>
it will only render this file from this location. Other files and ScriptDirs will be untouched<br/>


 <br/>
EOT;

	    $xScripts->addScript(new Xajax\Scripts\Core(['scriptName' => 'xajax', 'fileName' => 'xajax_core3.js', 'dir' => '/media/scripts']));

	    $scriptUrl = $xScripts->getScriptUrl('xajax');
	    echo $html0 . '<br/>' . $scriptUrl;
	    ?>
	    <?php
	    $str   = '$xScripts->setLockScript(\'xajax\');
<br/>
echo $xScripts->getScriptUrl(\'xajax\');
';
	    $html0 = <<<EOT
<h3>Prevent from Output/Rendering JavaScripts they was Registered with addScript</h3>

/**<br/>
 * Prevent output an registered Js-File which was registered(maybe not)<br/>
 * In case, that you don't want to output the xajax core(or an other registered js file) you can setLockScript('scriptName').<br/>
 * Make sure if you setLockScript('scriptName') to render/deploy the need js-content with an other js-file.<br/>
 * This Case is usefully if you have an CMS/Framework with his own js/assets structure<br/>
 **/
<br/>
<code>$str</code>
<br/>
EOT;
	    // lock the core-script completely
	    $xScripts->setLockScript('xajax');
	    echo $xScripts->getScriptUrl('xajax');
	    echo $html0 . '<br /> Does not Output the scriptUrl to the browser or somewhere. It is locked from outputting';

	    ?>
	</div>
</div>
</body>
</html>