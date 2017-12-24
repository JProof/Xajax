<?php /**
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

require_once __DIR__ . '/bootstrap.php';

function testForm($formData)
{
	$objResponse = Factory::getResponseInstance();
	$objResponse->alert("formData: " . print_r($formData, true));
	$objResponse->assign("submittedDiv", "innerHTML", nl2br(print_r($formData, true)));
	return $objResponse;
}

require_once __DIR__ . '/bootstrap.php';

use Xajax\Factory;

set_error_handler('\Xajax\Errors\Handler::addError');

$xConfig = Factory::getInstance()->getConfig();
$xConfig->setExitAllowed(true);
$xConfig->setErrorHandler('\Xajax\Errors\Handler::addException')->setToHtml(true);

// Response will be handled only if it is an Xajax Request
$objResponse = Factory::getResponseInstance();
$objResponse->html('testForm1', '<p>found</p>');

/**JS Configuration**/
$xScripts = Factory::getScripts();
$xScripts->getConfiguration()->setDeferScriptGeneration(false)->setUseUncompressedScripts(true)->setDebug(false);

$xajaxCmsGet  = Xajax\Plugins\Userfunction\Request::autoRegister('get');
$xajaxCmsPost = Xajax\Plugins\Cms\Request::autoRegister('xajax_post');

Factory::getScripts()->addScript(new Xajax\Scripts\Core(['scriptName' => 'xajax',
                                                         'fileName'   => 'xajax_core.js',
                                                         'dir'        => dirname(__DIR__) . '/javascript/dist']));
// at this point all objResponse Commands must be finished
Factory::processRequest();
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Form Submission Test| xajax Tests</title>
	<?php Factory::getInstance()->printJavascript(); ?>
	<style type="text/css">
		fieldset > div {
			border           : 1px solid gray;
			padding          : 5px;
			background-color : white;
		}
	</style>

</head>

<body>
<h2><a href="index.php">xajax Tests</a></h2>

<h1>Form Submission Test</h1>

<div>
	<form id="testForm1" onsubmit="return false;">
		<fieldset style="display:inline; background-color: rgb(230,230,230);">
			<legend>
				Test Form
			</legend>
			<div style="margin: 3px;">
				<label>
					Text Input

					<input type="text" autocomplete="username" id="textInput" name="textInput" value="text"/> </label>
			</div>
			<div style="margin: 3px;">
				<label>
					Password Input

					<input type="password" autocomplete="current-password" id="textInput2" name="passwordInput" value="2br!2b"/>
				</label>
			</div>

			<div style="margin: 3px;">
				<label>
					Textarea
					<textarea id="textarea" name="textarea">text text</textarea>
				</label>
			</div>
			<div style="margin: 3px;">
				<div>
					<input type="checkbox"
						   id="checkboxInput1" name="checkboxInput[]" value="1" checked="checked"/>
					<label for="checkboxInput1">Checkbox Input 1</label>
				</div>
				<div>
					<input type="checkbox"
						   id="checkboxInput2" name="checkboxInput[]" value="2" checked="checked"/>
					<label for="checkboxInput2">Checkbox Input 2</label>
				</div>
			</div>
			<div style="margin: 3px;">
				<div>
					<input type="checkbox" id="checkboxMethodTwoInput1"
						   name="checkboxMethodTwoInput[0]" value="1" checked="checked"/>
					<label for="checkboxMethodTwoInput1">Checkbox Method 2 Input 1</label>
				</div>
				<div>
					<input type="checkbox" id="checkboxMethodTwoInput2"
						   name="checkboxMethodTwoInput[1]" value="2" checked="checked"/>
					<label for="checkboxMethodTwoInput2">Checkbox Method 2 Input 2</label>
				</div>
				<div>
					<input type="checkbox" id="checkboxMethodTwoInput3"
						   name="checkboxMethodTwoInput[3]" value="4" checked="checked"/>
					<label for="checkboxMethodTwoInput3">Checkbox Method 2 Input 3</label>
				</div>
			</div>
			<div style="margin: 3px;"><h3>Multi Dimensions Array</h3>

				<div>
					<input type="checkbox" id="multi1"
						   name="multi[0]['test']" value="1" checked="checked"/>
					<label for="multi1">multi[0]['test']</label>
				</div>
				<div>
					<input type="checkbox" id="multi2"
						   name="multi[1]['test1']" value="2" checked="checked"/>
					<label for="multi2">multi[1]['test1']</label>
				</div>
				<div>
					<input type="checkbox" id="multi3"
						   name="multi[1]['test2']" value="4" checked="checked"/>
					<label for="multi3">multi[1]['test2']</label>
				</div>
			</div>
			<div style="margin: 3px;">
				<div>
					Radio Input
				</div>
				<div>
					<input type="radio"
						   id="radioInput1" name="radioInput" value="1" checked="checked"/>
					<label for="radioInput1">One</label>
				</div>
				<div>
					<input type="radio" id="radioInput2" name="radioInput" value="2"/>
					<label for="radioInput2">Two</label>
				</div>
			</div>
			<div style="margin: 3px;">
				<label>
					Select
					<select id="select1" name="select[one]">
						<option value="1">One</option>
						<option value="2">Two</option>
						<option value="3">Three</option>
						<option value="4">Four</option>
					</select>
					<select id="select2" name="select[two]">
						<option value="1">One</option>
						<option value="2">Two</option>
						<option value="3">Three</option>
						<option value="4">Four</option>
					</select>
				</label>
			</div>
			<div style="margin: 3px;">
				<label>
					Multiple Select

					<select id="multipleSelect" name="multipleSelect[]" multiple="multiple" size=4>
						<option value="1" selected="selected">One</option>
						<option value="2">Two</option>
						<option value="3">Three</option>
						<option value="4">Four</option>
					</select>
				</label>
			</div>
			<span style="margin: 3px;">
                    <input type="submit" value="submit through GET xajax"
						   onclick="<?php $xajaxCmsGet->printScript() ?>"/> </span>
			<span style="margin: 3px;">
			<input type="submit" value="submit through GET xajax"
				   onclick="xajax_post(xajax.getFormValues('testForm1')); return false;"/> </span>
		</fieldset>
	</form>
</div>
<div id="submittedDiv" style=" margin: 3px;">
</div>
</body>
</html>

