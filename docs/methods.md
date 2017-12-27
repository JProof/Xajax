

| Area	| Command	| PHP	| JS	| Description |notices|
| ------------- |-------------| -----| -----| -------------|---|
| HTML 
| | html | $objResponse->html('eleId','content'); |xajax.html('eleId','content');| insert Content into existing html-tag|replace the old assign
| | html | $objResponse->html('eleId'); | xajax.html('eleId',''); | Empties existing html-tag|
| | html |  | xajax.html('eleId'); | Getting innerHTML from existing element |
| HTML/CSS 
| | addClass | $objResponse->addClass('eleId','myClassName'); |xajax.addClass('eleId','myClassName');| adding an class value to html Element
| | removeClass | $objResponse->removeClass('eleId','myClassName'); |xajax.removeClass('eleId','myClassName');| Removes an class from html Element