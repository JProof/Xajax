Dieses Beispiel soll verdeutlichen wie flexibel die Verwendung von eigenen bzw. angepassten Xajax-Skripten ist.


Set specific JavaScript to an explicit Directory

Adding an script with specified directory.
with parameter <code> dir=>'' </code>
it will only render this file from this location. Other files and ScriptDirs will be untouched<br/>


<code>$xScripts->addScript(new Xajax\Scripts\Core(['scriptName' => 'xajax', 'fileName' => 'xajax_core3.js', 'dir' => '/media/scripts']));</code>

<code>$scriptUrl = $xScripts->getScriptUrl('xajax');</code>

<code>echo  $scriptUrl;</code>





