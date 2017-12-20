[back to Readme](README.md)

Current development xajax 0.7.3
* Codstyles and Namespaces
* php-7
* LF
* UTF-8
* remove procedurally "php-echo" inside Scripts

* js removed windows.status  // @see https://www.w3schools.com/jsref/prop_win_status.asp 
* removed the JSON-Extralibrary

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


todo
* update modern js
* remove useless stuff
* Factory Pattern
* CMS-usable

[back to Readme](README.md)
