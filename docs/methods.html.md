Html Methoden

```php
// The Following Method is an Replacemant for the old long  $objResponse->assing($htmlId,'innerHTML','<p>something</p>');

$objResponse->html($htmlId,'<p>something</p>');

/**
	 * Setting in Html Content
	 *
	 * @since 0.7.3
	 *
	 * @param string      $sTarget html Element id
	 * @param null|string $content leave empty got get the content from the html element
	 *
	 * @return \Xajax\Response\Response
	 */
	public function html(string $sTarget, ?string $content = null): Response

// call
    $objResponse->html('elementID','content');
```
Method can be used in Javascript 
````javascript 1.5
// set Html
xajax.html('elementId','<p>content</p>');
// empties content
xajax.html('elementId','');
// or
xajax.html('elementId',null);

// getting Html without change
var htmlContent = xajax.html('elementId');
````