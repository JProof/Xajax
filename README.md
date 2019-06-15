XAJAX has been moved to jybrid

The modernization/extension of xajax is already well advanced.
As the next independent relase "jybrid" will be the follow-up to the xajax-version listed here.

Mirror-Repository for jybrid-library 

jybrid examples under https://github.com/JProof/jybrid-examples

https://jybrid.com 

current Version 0.7.9.2

javascript testings:

acceptance-test
* html-append
* html-assign
* html-attributes
* html-class-names
* html-prepend
* js-defer
* js-min-full-switch

### Ajax Html 

|type|command| initial page load | ajax request| ajax response | short description | reference pages |
|---|---|---|---|---|---|---|
| ajax response |`$objResponse->html($element,'Text or Html-Tags');`|-| - | x |Inserts Text/Html into the given html-tag-id|<https://jybrid.com/ajax-response/html-append-and-prepend>|
| ajax response |`$objResponse->prependHtml($parentElement, 'Text or Html-Tags');`|-| - | x |Inserts Text/Html at first position in parentElement|<https://jybrid.com/ajax-response/html-append-and-prepend>|
| ajax response |`$objResponse->appendHtml($parentElement, 'Text or Html-Tags');`|-| - | x |Inserts Text/Html at last position in parentElement|<https://jybrid.com/ajax-response/html-append-and-prepend>|


### Ajax html css classname handling

|type|command| initial page load | ajax request| ajax response | short description | reference pages |
|---|---|---|---|---|---|---|
| ajax response |`$objResponse->classSet($element, $classNameToTouch);`|-| - | x |set an classname to html-attribute class="" if exists|<https://demo.jybrid.com/schematic-ajax-classNames.php>|
| ajax response |`$objResponse->classClear($element);`|-| - | x |clear all classes from the html-attribute class=""|<https://demo.jybrid.com/schematic-ajax-classNames.php>|
| ajax response |`$objResponse->classAdd($element, $classNameToTouch);`|-| - | x |add classname to html-attribute class=""|<https://demo.jybrid.com/schematic-ajax-classNames.php>|
| ajax response |`$objResponse->classRemove($element, $classNameToTouch);`|-| - | x |remove the classname from html-attribute class="" if exists|<https://demo.jybrid.com/schematic-ajax-classNames.php>|


### Ajax html attributes

|type|command| initial page load | ajax request| ajax response | short description | reference pages |
|---|---|---|---|---|---|---|
| ajax response |`$objResponse->attribSet($element, 'disabled', 'disabled');`|-| - | x |set attribute value |<https://demo.jybrid.com/schematic-ajax-attributes>|
| ajax response |`$objResponse->attribPrepend($element, 'value', ' +Value');`|-| - | x |prepends attribute value |<https://demo.jybrid.com/schematic-ajax-attributes>|
| ajax response |`$objResponse->attribAppend($element, 'value', ' +Value');`|-| - | x |appends attribute value |<https://demo.jybrid.com/schematic-ajax-attributes>|
| ajax response |`$objResponse->attribRemove($element, 'disabled');`|-| - | x |remove attribute if exists|<https://demo.jybrid.com/schematic-ajax-attributes>|
| ajax response |`$objResponse->attribClear($element, 'disabled');`|-| - | x |empties attribute value if exists|<https://demo.jybrid.com/schematic-ajax-attributes>|

### Ajax javascript events during response

|type|command| initial page load | ajax request| ajax response | short description | reference pages |
|---|---|---|---|---|---|---|
| ajax response |`$objResponse->setEvent($element, 'click', 'myJsMethodToCall');`|-| - | x |set event to element which executes 'myJsMethodToCall' (removes other 'click' events)|<https://demo.jybrid.com/schematic-ajax-events-dom.php>|
| ajax response |`$objResponse->addEvent($element, 'click', 'myJsMethodToCall');`|-| - | x |append/add event to element which executes 'myJsMethodToCall'|<https://demo.jybrid.com/schematic-ajax-events-dom.php>|
| ajax response |`$objResponse->fireEvent($element, 'click');`|-| - | x |fire event (exists)|<https://demo.jybrid.com/schematic-ajax-events-dom.php>|
| ajax response |`$objResponse->removeEvent($element, 'click', 'myJsMethodToCall');`|-| - | x |remove single click event 'myJsMethodToCall' from element|<https://demo.jybrid.com/schematic-ajax-events-dom.php>|
| ajax response |`$objResponse->removeEvents($element, 'click');`|-| - | x |remove all click events from element|<https://demo.jybrid.com/schematic-ajax-events-dom.php>|
| ajax response |`$objResponse->removeEvents($element, 'click');`|-| - | x |remove all click events from element|<https://demo.jybrid.com/schematic-ajax-events-dom.php>|

### Ajax respons calls with php javascript-method

|type|command| initial page load | ajax request| ajax response | short description | reference pages |
|---|---|---|---|---|---|---|
| ajax response |`$objResponse->safeExecuteFunction('myJsMethodToCall');`|js method must be load in browser| - | x |calls an javascript method without eval |<https://demo.jybrid.com/schematic-ajax-events-dom.php>|


### Ajax functional helper

|type|command| initial page load | ajax request| ajax response | short description | reference pages |
|---|---|---|---|---|---|---|
|optional|`Factory::getInstance()->getConfig()->setCleanBuffer(true);`|-| x | x |tries to catch echo'd content|<https://demo.jybrid.com/schematic-ajax-response-cleanbuffer.php>|
|required|`Factory::processRequest(true);`|-| x | x |sends the ajax response back to the client-browser| |

### Ajax http-request header 

all Request / single request

|type|command| initial page load | ajax request| ajax response | short description | reference pages |
|---|---|---|---|---|---|---|
| ajax request all/global |`Factory::getHeaders()->addHeaderCommon('jybrid-Ajax-Request-Common-Header', 'Post/GetHeaderValue');`|x| - | - |request GET or POST header(based upon the request-method)|<https://demo.jybrid.com/schematic-ajax-http-request-response-header.php>|
| ajax request all/global |`Factory::getHeaders()->addHeaderPost('jybrid-Ajax-Request-Post-Header', 'Request-POST-Header');`|x| - | - |request POST header|<https://demo.jybrid.com/schematic-ajax-http-request-response-header.php>|
| ajax request all/global |`Factory::getHeaders()->addHeaderGet('jybrid-Ajax-Request-Get-Header', 'Post/GetHeaderValue');`|x| - | - |request GET header|<https://demo.jybrid.com/schematic-ajax-http-request-response-header.php>|
| ajax request single/individual|`Request::gi()->addHeaderCommon('jybrid-Ajax-Request-Common-Header', 'Post/GetHeaderValue');`|x| - | - |request GET or POST header(based upon the request-method) particular request|<https://demo.jybrid.com/schematic-ajax-http-request-response-header.php>|
| ajax request single/individual|`Request::gi()->addHeaderPost('jybrid-Ajax-Request-Post-Header', 'Request-POST-Header');`|x| - | - |request POST header particular request|<https://demo.jybrid.com/schematic-ajax-http-request-response-header.php>|
| ajax request single/individual|`Request::gi()->addHeaderGet('jybrid-Ajax-Request-Get-Header', 'Post/GetHeaderValue');`|x| - | - |request GET header particular request|<https://demo.jybrid.com/schematic-ajax-http-request-response-header.php>|

### Ajax http-response header 

all Responses / single response 

|type|command| initial page load | ajax request| ajax response | short description | reference pages |
|---|---|---|---|---|---|---|
| ajax response |`Jybrid\Response\Manager::getInstance()->getHeader()->addResponseHeader('response-header-GET-and-POST', 'Post/GetHeaderValue');`|-| - | x |response GET or POST header(based upon the request-method)|<https://demo.jybrid.com/schematic-ajax-http-request-response-header.php>|
| ajax response |`Jybrid\Response\Manager::getInstance()->getHeader()->addHeaderPost('during-POST-Request-Response-Header', 'PostHeaderValue');`|-| - | x |response POST header|<https://demo.jybrid.com/schematic-ajax-http-request-response-header.php>|
| ajax response |`Jybrid\Response\Manager::getInstance()->getHeader()->addHeaderGet('during-GET-Request-Response-Header', 'GetHeaderValue');`|-| - | x |Response GET header|<https://demo.jybrid.com/schematic-ajax-http-request-response-header.php>|


### Ajax Css-Files

add / remove css resource in/from browser.

|type|command| initial page load | ajax request| ajax response | short description | reference pages |
|---|---|---|---|---|---|---|
| ajax response |`$objResponse->includeCSS('assets/test-css/test1.css')`|-| - | x |adding an css-file into the browser head ||
| ajax response |`$objResponse->removeCSS('assets/test-css/test1.css');`|-| - | x |remove an css-file from browser||


examples for the new version under @see https://github.com/JProof/jybrid-examples

@see https://github.com/JProof/jybrid 




next release(unofficial): 0.7.3 jybrid is not a part of the xajax-organisation


[Relaunch-Notices](Relaunch.md)


current version: 0.6 beta1

xajax is an open source PHP class library for easily creating powerful PHP-
driven, web-based Ajax Applications. Using xajax, you can asynchronously call
PHP functions and update the content of your your webpage without reloading the
page.

Project Website
http://xajax-project.org

Online documentation & examples
http://xajax-project.org/en/docs-tutorials/

Community forums
http://community.xajax-project.org/

Or chat with us
#xajax on irc.freenode.net


Copyright (c) 2006, Jared White & J. Max Wilson
portions copyright (c) 2006-2012 by Joseph Woolley & Steffen Konerow
portions copyright (c) 2012 by romacron@JProof


xajax is released under the terms of the BSD license

All rights reserved.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

    * Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
    * Neither the name of xajax nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
