[back to Readme](README.md)

Current development xajax 0.7.3
* Codstyles and Namespaces
* php-7
* LF
* UTF-8
* remove procedurally "php-echo" inside Scripts

* js removed windows.status  // @see https://www.w3schools.com/jsref/prop_win_status.asp 
* removed the JSON-Extralibrary

* Request-Plugins will be load if they need automatically.  

Changes

Javascript-Structure:

Files with the extensions' *_uncompressed. js' have been removed.
Minified files are the files without comment intended for live operation
Files without "min" are the annotated files.

```php
/**
* @since 0.7.1 enable the uncompressed files
**/
$xajax->getConfig()->setUseUncompressedScripts(true);
```

In old Xajax the submitted Post and Get vars was as json-string encoded! This is removed
Each POST or GET Parameter will be send such in regular Request(as without ajax).

Reason:
xjxargs[]="a lot of parameters" can not handle directly from cms's because there routers or other cms functionality does not know xjxargs




todo
* update modern js
* remove useless stuff
* Factory Pattern
* CMS-usable

[back to Readme](README.md)
