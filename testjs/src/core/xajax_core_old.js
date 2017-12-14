/**
 * @since 0.7.1
 * getterHelper
 * */
xajax.get = function (obj, ident) {
    if ('object' === typeof obj && 'string' === typeof ident && ident in obj) {
        return obj[ident];
    }
    return void 0;
};
/*
	Function: xajax.tools.$

	Shorthand for finding a uniquely named element within
	the document.

	Parameters:
	sId - (string):
		The unique name of the element (specified by the
		ID attribute), not to be confused with the name
		attribute on form elements.
		
	Returns:
	
	object - The element found or null.
	
	Note:
		This function uses the <xajax.config.baseDocument>
		which allows <xajax> to operate on the main window
		document as well as documents from contained
		iframes and child windows.
	
	See also:
		<xajax.$> and <xjx.$>
*/
xajax.tools.$ = function (sId) {
    var oDoc = xajax.config('baseDocument');//xajax.config.baseDocument;
    if (!sId)
        return null;
    var obj;
    if ('object' === typeof sId) {
        if (undefined !== sId.id) {
            obj = oDoc.getElementById(sId.id);
            if (obj)
                return obj;
        }
    }
//sId not an string so return it maybe its an object.
    if (typeof sId !== 'string') {
        return sId;
    }
    obj = oDoc.getElementById(sId);
    if (obj)
        return obj;
    if (oDoc.all)
        return oDoc.all[sId];
    return obj;
};
/*
	Function xajax.tools.in_array
	
	Looks for a value within the specified array and, if found,
	returns true; otherwise it returns false.
	
	Parameters:
	array - (object):
		The array to be searched.
		
	valueToCheck - (object):
		The value to search for.
		
	Returns:
	
	true : The value is one of the values contained in the
		array.
		
	false : The value was not found in the specified array.
*/
xajax.tools.in_array = function (array, valueToCheck) {
    var i = 0;
    var l = array.length;
    while (i < l) {
        if (array[i] == valueToCheck)
            return true;
        ++i;
    }
    return false;
};
/*
	Function: xajax.tools.doubleQuotes
	
	Replace all occupancy's of the single quote character with a double
	quote character.
	
	Parameters:
	
	haystack - The source string to be scanned.
	
	Returns:  false on error
	
	string - A new string with the modifications applied.
*/
xajax.tools.doubleQuotes = function (haystack) {
    if (typeof haystack === 'undefined') return false;
    return haystack.replace(new RegExp('\'', 'g'), '"');
};
/*
	Function: xajax.tools.singleQuotes
	
	Replace all occupancy's of the double quote character with a single
	quote character.
	
	haystack - The source string to be scanned.
	
	Returns:
	
	string - A new string with the modification applied.
*/
/*
xajax.tools.singleQuotes = function(haystack) {
	return haystack.replace(new RegExp('"', 'g'), "'");
}
*/
/*
	Function: xajax.tools._escape
	
	Determine if the specified value contains special characters and
	create a CDATA section so the value can be safely transmitted.
	
	Parameters:
	
	data - (string or other):
		The source string value to be evaluated or an object of unknown
		type.
		
	Returns:
	
	string - The string value, escaped if necessary or the object provided
		if it is not a string.
		
	Note:
		When the specified object is NOT a string, the value is returned
		as is.
*/
/*
xajax.tools._escape = function(data) {
	if ('undefined' == typeof data)
		return data;
	// 'object' is handled elsewhere,
	// 'string' handled below,
	// 'number' will be returned here
	// 'boolean' will be returned here
	if ('string' != typeof data)
		return data;
	
	var needCDATA = false;
	
	if (encodeURIComponent(data) != data) {
		needCDATA = true;
		
		var segments = data.split('<![CDATA[');
		var segLen = segments.length;
		data = [];
		for (var i = 0; i < segLen; ++i) {
			var segment = segments[i];
			var fragments = segment.split(']]>');
			var fragLen = fragments.length;
			segment = '';
			for (var j = 0; j < fragLen; ++j) {
				if (0 != j)
					segment += ']]]]><![CDATA[>';
				segment += fragments[j];
			}
			if (0 != i)
				data.push('<![]]><![CDATA[CDATA[');
			data.push(segment);
		}
		data = data.join('');
	}
	
	if (needCDATA)
		data = '<![CDATA[' + data + ']]>';
	
	return data;
}
*/
/*
	Function: xajax.tools._objectToXML
	
	Convert a javascript object or array into XML suitable for
	transmission to the server.
	
	Parameters:
	
	obj - The object or array to convert.
	
	guard - An object used to track the level of recursion
		when encoding javascript objects.  When an object
		contains a reference to it's parent and the parent
		contains a reference to the child, an infinite
		recursion will cause some browsers to crash.
		
	Returns:
	
	string - the xml representation of the object or array.
	
	See also:
	
	<xajax.config.maxObjectDepth> and <xajax.config.maxObjectSize>
*/
/*
xajax.tools._objectToXML = function(obj, guard) {
	var aXml = [];
	aXml.push('<xjxobj>');
	for (var key in obj) {
		++guard.size;
		if (guard.maxSize < guard.size)
			return aXml.join('');
		if ('undefined' != typeof obj[key]) {
			if ('constructor' == key)
				continue;
			if ('function' == typeof obj[key])
				continue;
			aXml.push('<e><k>');
			var val = xajax.tools._escape(key);
			aXml.push(val);
			aXml.push('</k><v>');
			if ('object' == typeof obj[key]) {
				++guard.depth;
				if (guard.maxDepth > guard.depth) {
					try {
						aXml.push(xajax.tools._objectToXML(obj[key], guard));
					} catch (e) {
						// do nothing, if the debug module is installed
						// it will catch the exception and handle it
					}
				}
				--guard.depth;
			} else {
				var val = xajax.tools._escape(obj[key]);
				if ('undefined' == typeof val || null == val) {
					aXml.push('*');
				} else {
					var sType = typeof val;
					if ('string' == sType)
						aXml.push('S');
					else if ('boolean' == sType)
						aXml.push('B');
					else if ('number' == sType)
						aXml.push('N');
					aXml.push(val);
				}
			}
			
			aXml.push('</v></e>');
		}
	}
	aXml.push('</xjxobj>');
	
	return aXml.join('');
}
*/
/*
	Function: xajax.tools._enforceDataType
	
	Ensure that the javascript variable created is of the correct data type.
	
	Parameters:
		value (string)

	Returns:
		
		(unknown) - The value provided converted to the correct data type.
*/
xajax.tools._enforceDataType = function (value) {
    value = String(value);
    var type = value.substr(0, 1);
    value = value.substr(1);
    if ('*' == type)
        value = null;
    else if ('N' == type)
        value = value - 0;
    else if ('B' == type)
        value = !!value;
//	else if ('S' == type)
//		value = new String(value);
    return value;
};
/*
	Function: xajax.tools._nodeToObject
	
	Deserialize a javascript object from an XML node.
	
	Parameters:
	
	node - A node, likely from the xml returned by the server.
	
	Returns:
	
		object - The object extracted from the xml node.
*/
xajax.tools._nodeToObject = function (node) {
    if (null == node)
        return '';
    if ('undefined' !== typeof node.nodeName) {
        var data;
        if ('#cdata-section' === node.nodeName || '#text' === node.nodeName) {
            data = '';
            do if (node.data) data += node.data; while (node = node.nextSibling);
            return xajax.tools._enforceDataType(data);
        } else if ('xjxobj' === node.nodeName) {
            var key = null;
            var value = null;
            data = [];
            var child = node.firstChild;
            while (child) {
                if ('e' === child.nodeName) {
                    var grandChild = child.firstChild;
                    while (grandChild) {
                        if ('k' === grandChild.nodeName) {
                            // Don't support objects here, only number, string, etc...
                            key = xajax.tools._enforceDataType(grandChild.firstChild.data);
                        }
                        else if ('v' === grandChild.nodeName) {
                            // Value might be object, string, number, boolean... even null or undefined
                            value = xajax.tools._nodeToObject(grandChild.firstChild);
                        }
                        grandChild = grandChild.nextSibling;
                    }
                    // Allow the value to be null or undefined (or a value)
                    if (null != key) { // && null != value) {
                        data[key] = value;
                        key = value = null;
                    }
                }
                child = child.nextSibling;
            }
            return data;
        }
    }
    throw {code: 10001, data: node.nodeName};
};
/*
	Function: xajax.tools.getRequestObject
	
	Construct an XMLHttpRequest object dependent on the capabilities
	of the browser.
	
	Returns:
	
	object - Javascript XHR object.
*/
xajax.tools.getRequestObject = function () {
    if ('undefined' !== typeof XMLHttpRequest) {
        xajax.tools.getRequestObject = function () {
            return new XMLHttpRequest();
        };
    } else if ('undefined' !== typeof ActiveXObject) {
        xajax.tools.getRequestObject = function () {
            try {
                return new ActiveXObject('Msxml2.XMLHTTP.4.0');
            } catch (e) {
                xajax.tools.getRequestObject = function () {
                    try {
                        return new ActiveXObject('Msxml2.XMLHTTP');
                    } catch (e2) {
                        xajax.tools.getRequestObject = function () {
                            return new ActiveXObject('Microsoft.XMLHTTP');
                        };
                        return xajax.tools.getRequestObject();
                    }
                };
                return xajax.tools.getRequestObject();
            }
        };
    } else if (window.createRequest) {
        xajax.tools.getRequestObject = function () {
            return window.createRequest();
        };
    } else {
        xajax.tools.getRequestObject = function () {
            throw {code: 10002};
        };
    }
    // this would seem to cause an infinite loop, however, the function should
    // be reassigned by now and therefore, it will not loop.
    return xajax.tools.getRequestObject();
};
/*
	Function: xajax.tools.getBrowserHTML
	
	Insert the specified string of HTML into the document, then
	extract it.  This gives the browser the ability to validate
	the code and to apply any transformations it deems appropriate.
	
	Parameters:
	
	sValue - (string):
		A block of html code or text to be inserted into the
		browser's document.
		
	Returns:
	
	The (potentially modified) html code or text.
*/
xajax.tools.getBrowserHTML = function (sValue) {
    var oDoc = xajax.config.baseDocument;
    if (!oDoc.body)
        return '';
    var elWorkspace = xajax.$('xajax_temp_workspace');
    if (!elWorkspace) {
        elWorkspace = oDoc.createElement('div');
        elWorkspace.setAttribute('id', 'xajax_temp_workspace');
        elWorkspace.style.display = 'none';
        elWorkspace.style.visibility = 'hidden';
        oDoc.body.appendChild(elWorkspace);
    }
    elWorkspace.innerHTML = sValue;
    var browserHTML = elWorkspace.innerHTML;
    elWorkspace.innerHTML = '';
    return browserHTML;
};
/*
	Function: xajax.tools.willChange
	
	Tests to see if the specified data is the same as the current
	value of the element's attribute.
	
	Parameters:
	element - (string or object):
		The element or it's unique name (specified by the ID attribute)
		
	attribute - (string):
		The name of the attribute.
		
	newData - (string):
		The value to be compared with the current value of the specified
		element.
		
	Returns:
	
	true - The specified value differs from the current attribute value.
	false - The specified value is the same as the current value.
*/
xajax.tools.willChange = function (element, attribute, newData) {
    element = xajax.$(element);
    if (element) {
        var oldData;
        try {
            // @since 0.7.1
            oldData = element.attribute;
        } catch (error) {
            // old-way deprecated
            eval('oldData=element.' + attribute);
        }
        return (newData !== oldData);
    }
    return false;
};
/*
	Function: xajax.tools.getFormValues
	
	Build an associative array of form elements and their values from
	the specified form.
	
	Parameters:
	
	element - (string): The unique name (id) of the form to be processed.
	disabled - (boolean, optional): Include form elements which are currently disabled.
	prefix - (string, optional): A prefix used for selecting form elements.

	Returns:
	
	An associative array of form element id and value.
*/
xajax.tools.getFormValues = function (parent) {
    var submitDisabledElements = false;
    if (arguments.length > 1 && arguments[1] == true)
        submitDisabledElements = true;
    var prefix = '';
    if (arguments.length > 2)
        prefix = arguments[2];
    if ('string' === typeof parent)
        parent = xajax.$(parent);
    var aFormValues = {};
//		JW: Removing these tests so that form values can be retrieved from a specified
//		container element like a DIV, regardless of whether they exist in a form or not.
//
//		if (parent.tagName)
//			if ('FORM' == parent.tagName.toUpperCase())
    if (parent)
        if (parent.childNodes)
            xajax.tools._getFormValues(aFormValues, parent.childNodes, submitDisabledElements, prefix);
    return aFormValues;
};
/*
	Function: xajax.tools._getFormValues
	
	Used internally by <xajax.tools.getFormValues> to recursively get the value
	of form elements.  This function will extract all form element values
	regardless of the depth of the element within the form.
*/
xajax.tools._getFormValues = function (aFormValues, children, submitDisabledElements, prefix) {
    var iLen = children.length;
    for (var i = 0; i < iLen; ++i) {
        var child = children[i];
        if (('undefined' !== typeof child.childNodes) && (child.type !== 'select-one') && (child.type !== 'select-multiple'))
            xajax.tools._getFormValues(aFormValues, child.childNodes, submitDisabledElements, prefix);
        xajax.tools._getFormValue(aFormValues, child, submitDisabledElements, prefix);
    }
};
/*
	Function: xajax.tools._getFormValue
	
	Used internally by <xajax.tools._getFormValues> to extract a single form value.
	This will detect the type of element (radio, checkbox, multi-select) and
	add it's value(s) to the form values array.

	Modified version for multidimensional arrays
*/
xajax.tools._getFormValue = function (aFormValues, child, submitDisabledElements, prefix) {
    if (!child.name)
        return;
    if ('PARAM' === child.tagName) return;
    if (child.disabled)
        if (true == child.disabled)
            if (false == submitDisabledElements)
                return;
    if (prefix !== child.name.substring(0, prefix.length))
        return;
    if (child.type)
        if (child.type === 'radio' || child.type === 'checkbox')
            if (false == child.checked)
                return;
    var name = child.name;
    var values = [];
    if ('select-multiple' === child.type) {
        var jLen = child.length;
        for (var j = 0; j < jLen; ++j) {
            var option = child.options[j];
            if (true == option.selected)
                values.push(option.value);
        }
    } else {
        values = child.value;
    }
    var keyBegin = name.indexOf('[');
    /* exists name/object before the Bracket?*/
    if (0 <= keyBegin) {
        var n = name;
        var k = n.substr(0, n.indexOf('['));
        var a = n.substr(n.indexOf('['));
        if (typeof aFormValues[k] === 'undefined')
            aFormValues[k] = {};
        var p = aFormValues; // pointer reset
        while (a.length !== 0) {
            var sa = a.substr(0, a.indexOf(']') + 1);
            var lk = k; //save last key
            var lp = p; //save last pointer
            a = a.substr(a.indexOf(']') + 1);
            p = p[k];
            k = sa.substr(1, sa.length - 2);
            if (k === '') {
                if ('select-multiple' === child.type) {
                    k = lk; //restore last key
                    p = lp;
                } else {
                    k = p.length;
                }
            }
            if (typeof k === 'undefined') {
                /*check against the global aFormValues Stack wich is the next(last) usable index */
                k = 0;
                for (var i in lp[lk]) k++;
            }
            if (typeof p[k] === 'undefined') {
                p[k] = {};
            }
        }
        p[k] = values;
    } else {
        aFormValues[name] = values;
    }
};
/*
	Class: xajax.tools.queue
	
	This contains the code and variables for building, populating
	and processing First In Last Out (FILO) buffers.
*/
/*
	Class: xajax.responseProcessor
*/
/*
	Function: xajax.responseProcessor.json
	
	Parse the JSON response into a series of commands.  The commands
	are constructed by calling <xajax.tools.xml.parseAttributes> and
	<xajax.tools.xml.parseChildren>.
	
	Parameters:
	
	oRequest - (object):  The request context object.
*/
xajax.tools.json = {};
xajax.tools.json.processFragment = function (nodes, seq, oRet, oRequest) {
    var xx = xajax;
    var xt = xx.tools;
    for (var nodeName in nodes) {
        if ('xjxobj' === nodeName) {
            for (var a in nodes[nodeName]) {
                
                /*
                prevents from using not numbered indexes of 'xjxobj'
                nodes[nodeName][a]= "0" is an valid xajax response stack item
                nodes[nodeName][a]= "pop" is an method from somewhere but not from xjxobj
                */
                if (parseInt(a) != a) continue;
                var obj = nodes[nodeName][a];
                obj.fullName = '*unknown*';
                obj.sequence = seq;
                obj.request = oRequest;
                obj.context = oRequest.context;
                xt.queue.push(xx.response, obj);
                ++seq;
            }
        } else if ('xjxrv' === nodeName) {
            oRet = nodes[nodeName];
        } else if ('debugmsg' === nodeName) {
            txt = nodes[nodeName];
        } else
            throw {code: 10004, data: obj.fullName};
    }
    return oRet;
};
/*
Function: xajax.responseProcessor.json
 */
/*
	Function: xajax.responseProcessor.xml

*/
// xajax js
// xajax dom
// xajax domResponse
/*
	Class: xajax.css
*/
/*
	Class: xajax.forms
*/
/*
	Class: xajax.events
*/
/*
    Class: xajax.callback
*/
/*
	Class: xajax
*/
/*
	Object: xajax.response
	
	The response queue that holds response commands, once received
	from the server, until they are processed.
*/
xajax.response = xajax.tools.queue.create(xajax.config.responseQueueSize);
/*
	Object: responseSuccessCodes
	
	This array contains a list of codes which will be returned from the
	server upon successful completion of the server portion of the
	request.
	
	These values should match those specified in the HTTP standard.
*/
xajax.responseSuccessCodes = ['0', '200'];
// 10.4.1 400 Bad Request
// 10.4.2 401 Unauthorized
// 10.4.3 402 Payment Required
// 10.4.4 403 Forbidden
// 10.4.5 404 Not Found
// 10.4.6 405 Method Not Allowed
// 10.4.7 406 Not Acceptable
// 10.4.8 407 Proxy Authentication Required
// 10.4.9 408 Request Timeout
// 10.4.10 409 Conflict
// 10.4.11 410 Gone
// 10.4.12 411 Length Required
// 10.4.13 412 Precondition Failed
// 10.4.14 413 Request Entity Too Large
// 10.4.15 414 Request-URI Too Long
// 10.4.16 415 Unsupported Media Type
// 10.4.17 416 Requested Range Not Satisfiable
// 10.4.18 417 Expectation Failed
// 10.5 Server Error 5xx
// 10.5.1 500 Internal Server Error
// 10.5.2 501 Not Implemented
// 10.5.3 502 Bad Gateway
// 10.5.4 503 Service Unavailable
// 10.5.5 504 Gateway Timeout
// 10.5.6 505 HTTP Version Not Supported
/*
	Object: responseErrorsForAlert
	
	This array contains a list of status codes returned by
	the server to indicate that the request failed for some
	reason.
*/
xajax.responseErrorsForAlert = [
    '400',
    '401',
    '402',
    '403',
    '404',
    '500',
    '501',
    '502',
    '503'];
// 10.3.1 300 Multiple Choices
// 10.3.2 301 Moved Permanently
// 10.3.3 302 Found
// 10.3.4 303 See Other
// 10.3.5 304 Not Modified
// 10.3.6 305 Use Proxy
// 10.3.7 306 (Unused)
// 10.3.8 307 Temporary Redirect
/*
	Object: responseRedirectCodes
	
	An array of status codes returned from the server to
	indicate a request for redirect to another URL.
	
	Typically, this is used by the server to send the browser
	to another URL.  This does not typically indicate that
	the xajax request should be sent to another URL.
*/
xajax.responseRedirectCodes = ['301', '302', '307'];
/*
	Class: xajax.command
	
	The object that manages commands and command handlers.
*/



/**
 *  @since 0.7.1
 * */
/*
	Function: xajax.initializeRequest
	
	Initialize a request object, populating default settings, where
	call specific settings are not already provided.
	
	Parameters:
	
	oRequest - (object):  An object that specifies call specific settings
		that will, in addition, be used to store all request related
		values.  This includes temporary values used internally by xajax.
*/
xajax.initializeRequest = function (oRequest) {
    var xx = xajax;
    var xc = xx.config;
    oRequest.append = function (opt, def) {
        if ('undefined' !== typeof this[opt]) {
            for (var itmName in def)
                if ('undefined' === typeof this[opt][itmName])
                    this[opt][itmName] = def[itmName];
        } else this[opt] = def;
    };
    oRequest.append('commonHeaders', xc.getOption('commonHeaders'));
    oRequest.append('postHeaders', xc.getOption('postHeaders'));
    oRequest.append('getHeaders', xc.getOption('getHeaders'));
    oRequest.set = function (option, defaultValue) {
        if ('undefined' === typeof this[option])
            this[option] = defaultValue;
    };
    oRequest.set('statusMessages', xc.getOption('statusMessages'));
    oRequest.set('waitCursor', xc.getOption('waitCursor'));
    oRequest.set('mode', xc.getOption('defaultMode'));
    oRequest.set('method', xc.getOption('defaultMethod'));
    oRequest.set('URI', xc.getOption('requestURI'));
    oRequest.set('httpVersion', xc.getOption('defaultHttpVersion'));
    oRequest.set('contentType', xc.getOption('defaultContentType'));
    oRequest.set('retry', xc.getOption('defaultRetry'));
    oRequest.set('returnValue', xc.getOption('defaultReturnValue'));
    oRequest.set('maxObjectDepth', xc.getOption('maxObjectDepth'));
    oRequest.set('maxObjectSize', xc.getOption('maxObjectSize'));
    oRequest.set('context', window);
    var xcb = xx.callback;
    var gcb = xcb.global;
    var lcb = xcb.create();
    lcb.take = function (frm, opt) {
        if ('undefined' !== typeof frm[opt]) {
            lcb[opt] = frm[opt];
            lcb.hasEvents = true;
        }
        delete frm[opt];
    };
    lcb.take(oRequest, 'onRequest');
    lcb.take(oRequest, 'onResponseDelay');
    lcb.take(oRequest, 'onExpiration');
    lcb.take(oRequest, 'beforeResponseProcessing');
    lcb.take(oRequest, 'onFailure');
    lcb.take(oRequest, 'onRedirect');
    lcb.take(oRequest, 'onSuccess');
    lcb.take(oRequest, 'onComplete');
    if ('undefined' !== typeof oRequest.callback) {
        if (lcb.hasEvents)
            oRequest.callback = [oRequest.callback, lcb];
    } else
        oRequest.callback = lcb;
    oRequest.status = (oRequest.statusMessages) ?
      xc.status.update() :
      xc.status.dontUpdate();
    oRequest.cursor = (oRequest.waitCursor) ?
      xc.cursor.update() :
      xc.cursor.dontUpdate();
    oRequest.method = oRequest.method.toUpperCase();
    if ('GET' !== oRequest.method)
        oRequest.method = 'POST';	// W3C: Method is case sensitive
    oRequest.requestRetry = oRequest.retry;
    oRequest.append('postHeaders', {
        'content-type': oRequest.contentType
    });
    delete oRequest['append'];
    delete oRequest['set'];
    delete oRequest['take'];
    if ('undefined' === typeof oRequest.URI)
        throw {code: 10005};
};
/*
	Function: xajax.processParameters
	
	Processes request specific parameters and generates the temporary
	variables needed by xajax to initiate and process the request.
	
	Parameters:
	
	oRequest - A request object, created initially by a call to
		<xajax.initializeRequest>
	
	Note:
	This is called once per request; upon a request failure, this
	will not be called for additional retries.
*/
xajax.processParameters = function (oRequest) {
    var xx = xajax;
    var xt = xx.tools;
    var rd = [];
    var separator = '';
    for (var sCommand in oRequest.functionName) {
        if ('constructor' !== sCommand) {
            rd.push(separator);
            rd.push(sCommand);
            rd.push('=');
            rd.push(encodeURIComponent(oRequest.functionName[sCommand]));
            separator = '&';
        }
    }
    var dNow = new Date();
    rd.push('&xjxr=');
    rd.push(dNow.getTime());
    delete dNow;
    if (oRequest.parameters) {
        var i = 0;
        var iLen = oRequest.parameters.length;
        while (i < iLen) {
            var oVal = oRequest.parameters[i];
            if ('object' === typeof oVal && null !== oVal) {
                try {
//					var oGuard = {};
//					oGuard.depth = 0;
//					oGuard.maxDepth = oRequest.maxObjectDepth;
//					oGuard.size = 0;
//					oGuard.maxSize = oRequest.maxObjectSize;
                    //oVal = xt._objectToXML(oVal, oGuard);
                    oVal = JSON.stringify(oVal);
                } catch (e) {
                    oVal = '';
                    // do nothing, if the debug module is installed
                    // it will catch the exception and handle it
                }
                rd.push('&xjxargs[]=');
                oVal = encodeURIComponent(oVal);
                rd.push(oVal);
                ++i;
            } else {
                rd.push('&xjxargs[]=');
                if ('undefined' === typeof oVal || null == oVal) {
                    rd.push('*');
                } else {
                    var sType = typeof oVal;
                    if ('string' === sType)
                        rd.push('S');
                    else if ('boolean' === sType)
                        rd.push('B');
                    else if ('number' === sType)
                        rd.push('N');
                    oVal = encodeURIComponent(oVal);
                    rd.push(oVal);
                }
                ++i;
            }
        }
    }
    oRequest.requestURI = oRequest.URI;
    if ('GET' === oRequest.method) {
        oRequest.requestURI += oRequest.requestURI.indexOf('?') == -1 ?
          '?' :
          '&';
        oRequest.requestURI += rd.join('');
        rd = [];
    }
    oRequest.requestData = rd.join('');
};
/*
	Function: xajax.prepareRequest
	
	Prepares the XMLHttpRequest object for this xajax request.
	
	Parameters:
	
	oRequest - (object):  An object created by a call to <xajax.initializeRequest>
		which already contains the necessary parameters and temporary variables
		needed to initiate and process a xajax request.
	
	Note:
	This is called each time a request object is being prepared for a
	call to the server.  If the request is retried, the request must be
	prepared again.
*/
xajax.prepareRequest = function (oRequest) {
    var xx = xajax;
    var xt = xx.tools;
    oRequest.request = xt.getRequestObject();
    oRequest.setRequestHeaders = function (headers) {
        if ('object' == typeof headers) {
            for (var optionName in headers)
                this.request.setRequestHeader(optionName, headers[optionName]);
        }
    };
    oRequest.setCommonRequestHeaders = function () {
        this.setRequestHeaders(this.commonHeaders);
        if (this.challengeResponse)
            this.request.setRequestHeader('challenge-response', this.challengeResponse);
    };
    oRequest.setPostRequestHeaders = function () {
        this.setRequestHeaders(this.postHeaders);
    };
    oRequest.setGetRequestHeaders = function () {
        this.setRequestHeaders(this.getHeaders);
    };
    if ('asynchronous' === oRequest.mode) {
        // references inside this function should be expanded
        // IOW, don't use shorthand references like xx for xajax
        oRequest.request.onreadystatechange = function () {
            if (4 !== oRequest.request.readyState)
                return;
            xajax.responseReceived(oRequest);
        };
        oRequest.finishRequest = function () {
            return this.returnValue;
        };
    } else {
        oRequest.finishRequest = function () {
            return xajax.responseReceived(oRequest);
        };
    }
    if ('undefined' !== typeof oRequest.userName && 'undefined' !== typeof oRequest.password) {
        oRequest.open = function () {
            this.request.open(this.method, this.requestURI, 'asynchronous' === this.mode,
              oRequest.userName,
              oRequest.password);
        };
    } else {
        oRequest.open = function () {
            this.request.open(
              this.method,
              this.requestURI,
              'asynchronous' == this.mode);
        };
    }
    if ('POST' === oRequest.method) {	// W3C: Method is case sensitive
        oRequest.applyRequestHeaders = function () {
            this.setCommonRequestHeaders();
            try {
                this.setPostRequestHeaders();
            } catch (e) {
                this.method = 'GET';
                this.requestURI += this.requestURI.indexOf('?') == -1 ?
                  '?' :
                  '&';
                this.requestURI += this.requestData;
                this.requestData = '';
                if (0 === this.requestRetry) this.requestRetry = 1;
                throw e;
            }
        };
    } else {
        oRequest.applyRequestHeaders = function () {
            this.setCommonRequestHeaders();
            this.setGetRequestHeaders();
        };
    }
};
/*
	Function: xajax.request
	
	Initiates a request to the server.

	Parameters:
	
	functionName - (object):  An object containing the name of the function to execute
	on the server. The standard request is: {xjxfun:'function_name'}
	
	oRequest - (object, optional):  A request object which
		may contain call specific parameters.  This object will be
		used by xajax to store all the request parameters as well
		as temporary variables needed during the processing of the
		request.
	
*/
xajax.request = function () {
    var numArgs = arguments.length;
    if (0 === numArgs)
        return false;
    var oRequest = {};
    if (1 < numArgs)
        oRequest = arguments[1];
    oRequest.functionName = arguments[0];
    var xx = xajax;
    xx.initializeRequest(oRequest);
    xx.processParameters(oRequest);
    while (0 < oRequest.requestRetry) {
        try {
            --oRequest.requestRetry;
            xx.prepareRequest(oRequest);
            return xx.submitRequest(oRequest);
        } catch (e) {
            xajax.callback.execute(
              [xajax.callback.global, oRequest.callback],
              'onFailure',
              oRequest
            );
            if (0 === oRequest.requestRetry)
                throw e;
        }
    }
};
/*
	Function: xajax.submitRequest
	
	Create a request object and submit the request using the specified
	request type; all request parameters should be finalized by this
	point.  Upon failure of a POST, this function will fall back to a
	GET request.
	
	Parameters:
	
	oRequest - (object):  The request context object.
*/
xajax.submitRequest = function (oRequest) {
    oRequest.status.onRequest();
    var xcb = xajax.callback;
    var gcb = xcb.global;
    var lcb = oRequest.callback;
    xcb.execute([gcb, lcb], 'onResponseDelay', oRequest);
    xcb.execute([gcb, lcb], 'onExpiration', oRequest);
    xcb.execute([gcb, lcb], 'onRequest', oRequest);
    oRequest.open();
    oRequest.applyRequestHeaders();
    // todo work with emiters
    oRequest.cursor.onWaiting();
    oRequest.status.onWaiting();
    xajax._internalSend(oRequest);
    // synchronous mode causes response to be processed immediately here
    return oRequest.finishRequest();
};
/*
	Function: xajax._internalSend
	
	This function is used internally by xajax to initiate a request to the
	server.
	
	Parameters:
	
	oRequest - (object):  The request context object.
*/
xajax._internalSend = function (oRequest) {
    // this may block if synchronous mode is selected
    oRequest.request.send(oRequest.requestData);
};
/*
	Function: xajax.abortRequest
	
	Abort the request.
	
	Parameters:
	
	oRequest - (object):  The request context object.
*/
xajax.abortRequest = function (oRequest) {
    oRequest.aborted = true;
    oRequest.request.abort();
    xajax.completeResponse(oRequest);
};
/*
	Function: xajax.responseReceived
	
	Process the response.
	
	Parameters:
	
	oRequest - (object):  The request context object.
*/
xajax.responseReceived = function (oRequest) {
    var xx = xajax;
    var xcb = xx.callback;
    var gcb = xcb.global;
    var lcb = oRequest.callback;
    // sometimes the responseReceived gets called when the
    // request is aborted
    if (oRequest.aborted)
        return;
    xcb.clearTimer([gcb, lcb], 'onExpiration');
    xcb.clearTimer([gcb, lcb], 'onResponseDelay');
    xcb.execute([gcb, lcb], 'beforeResponseProcessing', oRequest);
    var challenge = oRequest.request.getResponseHeader('challenge');
    if (challenge) {
        oRequest.challengeResponse = challenge;
        xx.prepareRequest(oRequest);
        return xx.submitRequest(oRequest);
    }
    var fProc = xx.getResponseProcessor(oRequest);
    if ('undefined' === typeof fProc) {
        xcb.execute([gcb, lcb], 'onFailure', oRequest);
        xx.completeResponse(oRequest);
        return;
    }
    return fProc(oRequest);
};
/*
	Function: xajax.getResponseProcessor
	
	This function attempts to determine, based on the content type of the
	reponse, what processor should be used for handling the response data.
	
	The default xajax response will be text/xml which will invoke the
	xajax xml response processor.  Other response processors may be added
	in the future.  The user can specify their own response processor on
	a call by call basis.
	
	Parameters:
	
	oRequest - (object):  The request context object.
*/
xajax.getResponseProcessor = function (oRequest) {
    var fProc;
    if ('undefined' === typeof oRequest.responseProcessor) {
        var cTyp = oRequest.request.getResponseHeader('content-type');
        if (cTyp) {
            if (0 <= cTyp.indexOf('text/xml')) {
                fProc = xajax.responseProcessor.xml;
            } else if (0 <= cTyp.indexOf('application/json')) {
                fProc = xajax.responseProcessor.json;
            }
        }
    } else fProc = oRequest.responseProcessor;
    return fProc;
};
/*
	Function: xajax.executeCommand
	
	Perform a lookup on the command specified by the response command
	object passed in the first parameter.  If the command exists, the
	function checks to see if the command references a DOM object by
	ID; if so, the object is located within the DOM and added to the
	command data.  The command handler is then called.
	
	If the command handler returns true, it is assumed that the command
	completed successfully.  If the command handler returns false, then the
	command is considered pending; xajax enters a wait state.  It is up
	to the command handler to set an interval, timeout or event handler
	which will restart the xajax response processing.
	
	Parameters:
	
	obj - (object):  The response command to be executed.
	
	Returns:
	
	true - The command completed successfully.
	false - The command signalled that it needs to pause processing.
*/
xajax.executeCommand = function (command) {
    if (xajax.command.isRegistered(command)) {
        // it is important to grab the element here as the previous command
        // might have just created the element
        if (command.id)
            command.target = xajax.$(command.id);
        // process the command
        if (false === xajax.command.call(command)) {
            xajax.tools.queue.pushFront(xajax.response, command);
            return false;
        }
    }
    return true;
};
/*
	Function: xajax.completeResponse
	
	Called by the response command queue processor when all commands have
	been processed.
	
	Parameters:
	
	oRequest - (object):  The request context object.
*/
xajax.completeResponse = function (oRequest) {
    xajax.callback.execute(
      [xajax.callback.global, oRequest.callback], 'onComplete', oRequest);
    oRequest.cursor.onComplete();
    oRequest.status.onComplete();
    // clean up -- these items are restored when the request is initiated
    var resets = [
        'functionName',
        'requestURI',
        'requestData',
        'requestRetry',
        'request',
        'set',
        'open',
        'setRequestHeaders',
        'setCommonRequestHeaders',
        'setPostRequestHeaders',
        'setGetRequestHeaders',
        'applyRequestHeaders',
        'finishRequest',
        'status',
        'cursor',
        'challengeResponse'
    ];
    resets.forEach(function (value) {
        delete oRequest[value];
    });
};
/*
	Function: xajax.$
	
	Shortcut to <xajax.tools.$>.
*/
xajax.$ = xajax.tools.$;
/*
	Function: xajax.getFormValues
	
	Shortcut to <xajax.tools.getFormValues>.
*/
xajax.getFormValues = xajax.tools.getFormValues;
/*
	Boolean: xajax.isLoaded
	
	true - xajax module is loaded.
*/
xajax.isLoaded = true;
/*
	Class: xjx
	
	Contains shortcut's to frequently used functions.
*/
xjx = {};
/*
	Function: xjx.$
	
	Shortcut to <xajax.tools.$>.
*/
xjx.$ = xajax.tools.$;
/*
	Function: xjx.getFormValues
	
	Shortcut to <xajax.tools.getFormValues>.
*/
xjx.getFormValues = xajax.tools.getFormValues;
xjx.request = xajax.request;
