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
xajax.tools.getBrowserHTML = function (sValue, context) {
    // todo check object command
    var baseDoc = xjx.getContext(context);
    if (!baseDoc.body)
        return '';
    var elWorkspace = xajax.$('xajax_temp_workspace');
    if (!elWorkspace) {
        elWorkspace = baseDoc.createElement('div');
        elWorkspace.setAttribute('id', 'xajax_temp_workspace');
        elWorkspace.style.display = 'none';
        elWorkspace.style.visibility = 'hidden';
        baseDoc.body.appendChild(elWorkspace);
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
	
	@deprecated not used anymore because of wild implementation
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
	Class: xajax.queue
	
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
                xx.queue.push(xx.response, obj);
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
xajax.response = xajax.queue.create(xajax.config.responseQueueSize);
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

function objectToParamsString(obj, sVarName, flat) {
    
    var tOf = typeof obj;
    flat = flat ? flat : [];
    
    if ('object' === tOf) {
        // recurse
        var baseVarName = sVarName ? sVarName : null;
        for (var key in obj) {
            if (obj.hasOwnProperty(key)) {
                
                flat = objectToParamsString(obj[key], !baseVarName ?
                  key :
                  baseVarName + '[' + key + ']', flat);
            }
        }
        
    } else {
        flat[sVarName] = obj;
        
        //   if ('string' === tOf || 'float' === tOf || 'int' === tOf || 'bool' === tOf) {
        // todo check name
        
    }
    return flat;
}
/**
 * Create from array {formFieldName:1} 'formFieldName=1' which is need for Post/Get directly
 *
 * @param {array|object} arr
 * @return {Array}
 */
function stringifyKeyValuePairs(arr) {
    var retArray = [];
    for (var key in arr) {
        if (arr.hasOwnProperty(key)) {
            retArray.push(key + '=' + encodeURI(arr[key]));
        }
    }
    return retArray;
    
}
/**
 * Merging the found Parameters in one Object
 *
 * @param {array} baseArr
 * @param {array} pArray
 * @return {array}
 */
function mergeParams(baseArr, pArray) {
    
    for (var k in pArray) {
        if (pArray.hasOwnProperty(k))
            baseArr[k] = pArray[k];
    }
    
    return baseArr;
}
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
    
    var commandParam = [];
    var clearParams = [];
    
    for (var sCommand in oRequest.functionName) {
        if (oRequest.functionName.hasOwnProperty(sCommand))
            if ('constructor' !== sCommand) {
                commandParam[sCommand] = encodeURIComponent(oRequest.functionName[sCommand]);
            }
    }
    var d = new Date();
    commandParam['xjxr'] = d.getTime();
    
    if (oRequest.parameters) {
        var i = 0;
        var iLen = oRequest.parameters.length;
        while (i < iLen) {
            var oVal = oRequest.parameters[i];
            if ('object' === typeof oVal && null !== oVal) {
                try {
                    // merge params if nee if there are same fields twice
                    clearParams = mergeParams(clearParams, objectToParamsString(oVal));
                    
                } catch (e) {
                    //   oVal = '';
                    // do nothing, if the debug module is installed
                    // it will catch the exception and handle it
                }
    
            } else {
                throw new Error('You can not use the old way to handle parameters @see');
    
            }
            ++i;
        }
    }
    
    commandParam = mergeParams(commandParam, clearParams);
    commandParam = stringifyKeyValuePairs(commandParam);
    var cmdParamString = commandParam.join('&');
    
    oRequest.requestURI = oRequest.URI;
    if ('GET' === oRequest.method) {
        oRequest.requestURI += oRequest.requestURI.indexOf('?') === -1 ?
          '?' :
          '&' + cmdParamString;
    
        cmdParamString = '';
    }
    oRequest.requestData = cmdParamString;
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
        if ('object' === typeof headers) {
            for (var optionName in headers)
                if (headers.hasOwnProperty(optionName))
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
              'asynchronous' === this.mode);
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
    //  oRequest.status.onRequest();
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
    //   oRequest.status.onWaiting();
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
            xajax.queue.pushFront(xajax.response, command);
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
    //oRequest.status.onComplete();
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
	@since 0.7.3 not anymore on tools!
	Shortcut to <xajax.tools.getFormValues>.
*/
xajax.getFormValues = xajax.forms.getFormValues;

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
// not used anymore listed in  xjx.forms
//xjx.getFormValues = xajax.tools.getFormValues;
// new class where the formHandler is located

xjx.getFormValues = xajax.forms.getFormValues;
xjx.request = xajax.request;

/*
	Boolean: xajax.isLoaded
	
	true - xajax module is loaded.
*/
xajax.isLoaded = true;