#### Handling JavaScriptFiles

All Javascript files can be changed at any time at any point.
The following class is responsible for processing all Javascripts. 

```php
$xajaxScripts = Xajax\Factory::getScripts();
```

#### General setting options
```php
// Long way
$xajaxScriptConfig = $xajaxScripts->getConfiguration();
```

##### Uncompressed Scripts

Xajax Javascripts in Development Mode. Here javascripts are delivered uncompressed.
The name range is as in JQuery *scriptname. min. js* or *scriptname. js*.

```php
// for debugging uncompressed scripts scriptname.js
$xajaxScripts->getConfiguration()->setUseUncompressedScripts(true);
```

##### Xajax Debugging Script

Old Xajax Debugging JavaScript
```php
// for debugging uncompressed scripts scriptname.js
$xajaxScripts->getConfiguration()->setDebug(true);
```

##### Short for development
```php
$xajaxScripts->getConfiguration()->setUseUncompressedScripts(true)->setDebug(true)
```

In Production-Mode you don't need the configuration above


##### Override Scripts and other Script-Directories

Xajax Javascripts are no longer permanently integrated in Xajax. You can use your own Javascript files instead of the Xajax-Javascripts,
without having to change the original Xajax scripts. 

##### Create new javascript Directory

It makes sense to copy the Javascript files installed via Composer from this directory and insert them in a better place.
The background is that all publicly accessible files should be located in one directory. The remaining directories should, as far as possible 
cannot be called. 

original Javascript directory
/vendor/jproof/xajax/src/assets/ 

Examples of more useful locations for scripts
/templates/myTemplate/assets/js/
/media/scripts

```php
Xajax\Factory::getScripts()->addScriptDir('/templates/myTemplate/assets/js/');
// or
Xajax\Factory::getScripts()->addScriptDir('/media/scripts');
// or
Xajax\Factory::getScripts()->addScriptDir('FULL-SERVER-DIRECTORY/media/scripts');
// wherever the place is
```

All Javascript directories can be set relative or absolute. Xajax\Factory:: getScripts () works internally with the absolute paths 
to the respective directories or files. The output of the relative urls organizes the script class independently.

```
/home/users/myUserAccount/public_html/myTemplate/js/xajax.js
 will be out to Browser as
/myTemplate/js/xajax.js

this method maybe is handy for the following case
Xajax\Factory::getScripts()->addScriptDir(dirname(__DIR__,2)'/myAssets');
```

In each newly added ScriptDirectory the scripts requested by Xajax are searched first (priority)

##### Adding an JavasScript

Depending on the application, it may be useful to deliver other Javascripts to the browser with xajax. This saves work in the Html header.
jQuery should serve as an example.
```php 
// short Way
Xajax\Factory::getScripts()->addScript(new Xajax\Scripts\Core(['scriptName' => 'jQuery', 'fileName' => 'jquery.js']));  
```
This file is then searched in all set JavaScriptDirectories. 

If JavaScripts are wildly distributed in the application, the parameter *dir* can be used to specify that this JavaScript file may only be retrieved from this directory.
```php 
Xajax\Factory::getScripts()->addScript(new Xajax\Scripts\Core(['scriptName' => 'jQuery', 'fileName' => 'jquery.js', 'dir' => '/media/scripts/jQuery-3.0']));
```

Like the configuration above, the extra JavaScripts try to load the *scriptfile. min. js* or in development-mode the *scriptfile. js*.

##### Blocking JavaScripts

Depending on the situation, it may be necessary that JavaScripts are requested by a part of the application, but it is not necessary or desired.
to deliver these JavaScriptFiles to the browser as well. 
In this case, a script can be prevented from being output.

```php 
Xajax\Factory::getScripts()->->setLockScript('jquery');
```