/*
	File: xajax_core.js
	
	This file contains the definition of the main xajax javascript core.
	
	This is the client side code which runs on the web browser or similar
	web enabled application.  Include this in the HEAD of each page for
	which you wish to use xajax.
	
	Title: xajax core javascript library
	
	Please see <copyright.inc.php> for a detailed description, copyright
	and license information.
*/
/*
	@package xajax
	@version $Id: xajax_core_uncompressed.js 327 2007-02-28 16:55:26Z calltoconstruct $
	@copyright Copyright (c) 2005-2007 by Jared White & J. Max Wilson
	@copyright Copyright (c) 2008-2010 by Joseph Woolley, Steffen Konerow, Jared White  & J. Max Wilson
	@license http://www.xajaxproject.org/bsd_license.txt BSD License
*/
/*
	Class: xajax.config
	
	This class contains all the default configuration settings.  These
	are application level settings; however, they can be overridden
	by including a xajax.config definition prior to including the
	<xajax_core.js> file, or by specifying the appropriate configuration
	options on a per call basis.
*/
if ('undefined' === typeof xajax) {
    var xajax = {};
}

/**
 * Diverse Core-Helper Helpers
 * **/
(function (xjx) {
    /**
     * Short tester to save a lot of typeof's
     * @param {*} val
     * @return {boolean}
     * */
    xjx.isStr = function (val) {
        return 'string' === typeof val;
    };
    /**
     * Short tester to save a lot of typeof's
     * @param {*} val
     * @return {boolean}
     * */
    xjx.isNum = function (val) {
        return 'number' === typeof val;
    };
    /**
     * Check the value is valid as attribute value
     *
     * @param {*} val
     * @return {boolean}
     * */
    xjx.isAttribValue = function (val) {
        return xjx.isNum(val) || xjx.isStr(val);
    };
    /**
     * Safe getting of an Object element
     * @since 0.7.1
     *
     * @param {object} obj
     * @param {object} key
     *
     * @return {*} content of the object
     * */
    xjx.getObjEle = function (obj, key) {
        if (('object' === typeof obj) && 'string' === typeof key && key in obj) {
            return obj[key];
        }
        return void 0;
    };
}(xajax));
/** Core Configuration Module **/
(function (xjx) {
    'use strict';
    /**
     * @example to get Access to config Properties use xajax.config('parameterName');
     **/
    xjx.config = function (key) {
        return xjx.config.getOption(key);
    };
    /*
	Function: xajax.config.setDefault
	
	This function will set a default configuration option if it is
	not already set.
	
	Parameters:
	option - (string):
		The name of the option that will be set.
		
	defaultValue - (unknown):
		The value to use if a value was not already set.
*/
    var options = [];
    /**
     * @public
     *
     * Getting an option if set, default return is null so you can check against null
     * @return mixed
     * **/
    xjx.config.getOption = function (optName) {
        return options.hasOwnProperty(optName) ? options[optName] : null;
    };
    /**
     *  @public
     * **/
    xjx.config.setOption = function (key, value) {
        options[key] = value;
    };
    /**
     * @private
     * */
    var setOptions = function (objs) {
        for (var key in objs) {
            xjx.config.setOption(key, objs[key]);
        }
    };
    var defaults = {
        /*
	    Object: commonHeaders
	
    	An array of header entries where the array key is the header
	    option name and the associated value is the value that will
	    set when the request object is initialized.
	
	    These headers will be set for both POST and GET requests.
        */
        'commonHeaders': {
            'If-Modified-Since': 'Sat, 1 Jan 2000 00:00:00 GMT'
        },
        /*
	Object: postHeaders
	
	An array of header entries where the array key is the header
	option name and the associated value is the value that will
	set when the request object is initialized.
*/
        'postHeaders': {},
        /*
	Object: getHeaders
	
	An array of header entries where the array key is the header
	option name and the associated value is the value that will
	set when the request object is initialized.
*/
        'getHeaders': {},
        /*
	Boolean: waitCursor
	
	true - xajax should display a wait cursor when making a request
	false - xajax should not show a wait cursor during a request
*/
        'waitCursor': false,
        /*
	Boolean: statusMessages
	
	true - xajax should update the status bar during a request
	false - xajax should not display the status of the request
*/
        'statusMessages': false,
        /*
	Object: baseDocument
	
	The base document that will be used throughout the code for
	locating elements by ID.
	@todo more explanations
*/
        'baseDocument': window.document,
        /*
            String: requestURI
            
            The URI that requests will be sent to.
            @todo buggy internal loop
        */
        'requestURI': '',
        /*
	String: defaultMode
	
	The request mode.
	
	'asynchronous' - The request will immediately return, the
		response will be processed when (and if) it is received.
		
	'synchronous' - The request will block, waiting for the
		response.  This option allows the server to return
		a value directly to the caller.
*/
        'defaultMode': 'asynchronous',
        /*
	String: defaultHttpVersion
	
	The Hyper Text Transport Protocol version designated in the
	header of the request.
*/
        'defaultHttpVersion': 'HTTP/1.1',
        /*
	String: defaultContentType
	
	The content type designated in the header of the request.
	@todo extra headers for upload
*/
        'defaultContentType': 'application/x-www-form-urlencoded',
        /*
            Integer: defaultResponseDelayTime
            
            The delay time, in milliseconds, associated with the
            <xajax.callback.global.onRequestDelay> event.
        */
        'defaultResponseDelayTime': 1000,
        /*

	Integer: defaultExpirationTime
	
	The amount of time to wait, in milliseconds, before a request
	is considered expired.  This is used to trigger the
	<xajax.callback.global.onExpiration event.
*/
        'defaultExpirationTime': 10000,
        /*
	String: defaultMethod
	
	The method used to send requests to the server.
	
	'POST' - Generate a form POST request
	'GET' - Generate a GET request; parameters are appended
		to the <xajax.config.requestURI> to form a URL.
		 W3C: Method is case sensitive
*/
        'defaultMethod': 'POST',
        /*
	Integer: defaultRetry
	
	The number of times a request should be retried
	if it expires.
*/
        'defaultRetry': 5,
        /*
	Object: defaultReturnValue
	
	The value returned by <xajax.request> when in asynchronous
	mode, or when a synchronous call does not specify the
	return value.
*/
        'defaultReturnValue': false,
        /*
	Integer: maxObjectDepth
	
	The maximum depth of recursion allowed when serializing
	objects to be sent to the server in a request.
*/
        'maxObjectDepth': 20,
        /*
	Integer: maxObjectSize
	
	The maximum number of members allowed when serializing
	objects to be sent to the server in a request.
*/
        'maxObjectSize': 2000,
        /**
         *
         * @desc How many items can be hold in queue. It is an prevention from overload the queue(on errors or buggy loops)
         * **/
        'responseQueueSize': 1000
    };
    setOptions(defaults);
}(xajax));
// config end
/*
    Class: xajax.config.cursor
    
    Provides the base functionality for updating the browser's cursor
    during requests.  By splitting this functionality into an object
    of it's own, xajax developers can now customize the functionality
    prior to submitting requests.
*/
(function (xjx) {
    xjx.config.cursor = {
        /*
            Function: update
            
            Constructs and returns a set of event handlers that will be
            called by the xajax framework to effect the status of the
            cursor during requests.
        */
        update: function () {
            return {
                onWaiting: function () {
                    if (xajax.config.baseDocument.body)
                        xajax.config.baseDocument.body.style.cursor = 'wait';
                },
                onComplete: function () {
                    xajax.config.baseDocument.body.style.cursor = 'auto';
                }
            };
        },
        /*
            Function: dontUpdate
            
            Constructs and returns a set of event handlers that will
            be called by the xajax framework where cursor status changes
            would typically be made during the handling of requests.
        */
        dontUpdate: function () {
            return {
                onWaiting: function () {
                },
                onComplete: function () {
                }
            };
        }
    };
}(xajax));
/*
  Class: xajax.tools
  
  This contains utility functions which are used throughout
  the xajax core.
*/
(function (xjx) {
    'use strict';
    // checks the given element s an HTML Element
    xjx.isElement = function (element) {
        // until IE7
        return element instanceof Element;
    };
    /**
     * Getting the "document" as context so you are able to use iframe support
     *
     * @since 0.7.0
     * **/
    xjx.getContext = function (con) {
        if (con) return con;
        if ('object' === typeof (con = xjx.config('baseDocument'))) return con;
        return window.document;
    };
    /**
     * Getting straight id element
     * **/
    function getEle(sId, baseDoc) {
        
        var tOf = typeof sId;
        // nothing
        if (null === sId || 'undefined' === tOf) return null;
        // is already node
        if (xjx.isElement(sId)) return sId;
        // todo check against "undefined"
        if ('object' === typeof sId && 'undefined' !== sId.id)
            return baseDoc.getElementById(sId.id);
        //sId not an string so return it maybe its an object.
        if (!xjx.isStr(sId))
            return null;
        return baseDoc.getElementById(sId);
    }
    /**
     * QuerySelector @todo make more complex
     *
     * @property eleS
     * @property baseDoc
     * @private Search
     * **/
    /**
     * Xajax Document Tools
     * */
    if (undefined === xjx.tools) {xjx.tools = {};}
    xjx.tools = {
        /*
           Function: xajax.tools.$
       
           Shorthand for finding a uniquely named element within
           the document.
       
           Parameters:
           sId - (string||object):
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
        
        $: function (eleS, context) {
            return getEle(eleS, xjx.getContext(context));
        },
        /**
         * query selector
         *
         * @return null|Element
         * **/
        qs: function (eleS, context) {
            return xjx.getContext(context).querySelector(eleS);
        },
        /**
         * query selector all
         *
         * @return null|NodeList
         * **/
        qsa: function (eleS, context) {
            return xjx.getContext(context).querySelectorAll(eleS);
        }
    };
}(xajax));
/*
	Class: xajax.queue
	
	This contains the code and variables for building, populating
	and processing First In Last Out (FILO) buffers.
*/
(function (xjx) {
    xjx.queue = {
        /*
            Function: create
            
            Construct and return a new queue object.
            
            Parameters:
            
            size - (integer):
                The number of entries the queue will be able to hold.
        */
        create: function (size) {
            return {
                start: 0,
                size: size,
                end: 0,
                commands: [],
                timeout: null
            };
        },
        /*
            Function: xajax.queue.retry
            
            Maintains a retry counter for the given object.
            
            Parameters:
            
            obj - (object):
                The object to track the retry count for.
                
            count - (integer):
                The number of times the operation should be attempted
                before a failure is indicated.
                
            Returns:
            
            true - The object has not exhausted all the retries.
            false - The object has exhausted the retry count specified.
        */
        retry: function (obj, count) {
            var retries = obj.retries;
            if (retries) {
                --retries;
                if (1 > retries)
                    return false;
            } else retries = count;
            obj.retries = retries;
            return true;
        },
        /*
            Function: xajax.queue.rewind
            
            Rewind the buffer head pointer, effectively reinserting the
            last retrieved object into the buffer.
            
            Parameters:
            
            theQ - (object):
                The queue to be rewound.
        */
        rewind: function (theQ) {
            if (0 < theQ.start)
                --theQ.start;
            else
                theQ.start = theQ.size;
        },
        /*
            Function: xajax.queue.setWakeup
            
            Set or reset a timeout that is used to restart processing
            of the queue.  This allows the queue to asynchronously wait
            for an event to occur (giving the browser time to process
            pending events, like loading files)
            
            Parameters:
            
            theQ - (object):
                The queue to process upon timeout.
                
            when - (integer):
                The number of milliseconds to wait before starting/
                restarting the processing of the queue.
        */
        setWakeup: function (theQ, when) {
            if (null != theQ.timeout) {
                clearTimeout(theQ.timeout);
                theQ.timeout = null;
            }
            theQ.timout = setTimeout(function () {
                xajax.queue.process(theQ);
            }, when);
        },
        /*
            Function: xajax.queue.process
            
            While entries exist in the queue, pull and entry out and
            process it's command.  When a command returns false, the
            processing is halted.
            
            Parameters:
            
            theQ - (object): The queue object to process.  This should
                have been crated by calling <xajax.queue.create>.
            
            Returns:
        
            true - The queue was fully processed and is now empty.
            false - The queue processing was halted before the
                queue was fully processed.
                
            Note:
            
            - Use <xajax.queue.setWakeup> or call this function to
            cause the queue processing to continue.
        
            - This will clear the associated timeout, this function is not
            designed to be reentrant.
            
            - When an exception is caught, do nothing; if the debug module
            is installed, it will catch the exception and handle it.
        */
        process: function (theQ) {
            if (null != theQ.timeout) {
                clearTimeout(theQ.timeout);
                theQ.timeout = null;
            }
            var obj = xajax.queue.pop(theQ);
            while (null != obj) {
                try {
                    if (false === xajax.executeCommand(obj))
                        return false;
                } catch (e) {
                }
                delete obj;
                obj = xajax.queue.pop(theQ);
            }
            return true;
        },
        /*
            Function: xajax.queue.push
            
            Push a new object into the tail of the buffer maintained by the
            specified queue object.
            
            Parameters:
            
            theQ - (object):
                The queue in which you would like the object stored.
                
            obj - (object):
                The object you would like stored in the queue.
        */
        push: function (theQ, obj) {
            var next = theQ.end + 1;
            if (next > theQ.size)
                next = 0;
            if (next !== theQ.start) {
                theQ.commands[theQ.end] = obj;
                theQ.end = next;
            } else
                throw {code: 10003};
        },
        /*
            Function: xajax.queue.pushFront
            
            Push a new object into the head of the buffer maintained by
            the specified queue object.  This effectively pushes an object
            to the front of the queue... it will be processed first.
            
            Parameters:
            
            theQ - (object):
                The queue in which you would like the object stored.
                
            obj - (object):
                The object you would like stored in the queue.
        */
        pushFront: function (theQ, obj) {
            xjx.queue.rewind(theQ);
            theQ.commands[theQ.start] = obj;
        },
        /*
            Function: xajax.queue.pop
            
            Attempt to pop an object off the head of the queue.
            
            Parameters:
            
            theQ - (object):
                The queue object you would like to modify.
                
            Returns:
            
            object - The object that was at the head of the queue or
                null if the queue was empty.
        */
        pop: function (theQ) {
            var next = theQ.start;
            if (next === theQ.end)
                return null;
            next++;
            if (next > theQ.size)
                next = 0;
            var obj = theQ.commands[theQ.start];
            delete theQ.commands[theQ.start];
            theQ.start = next;
            return obj;
        }
    }
    ;
}(xajax));
/*
	Class: xajax.responseProcessor
*/
(function (xjx) {
    xjx.responseProcessor = {};
}(xajax));
(function (xjx) {
    xjx.responseProcessor.json = function (oRequest) {
        var xx = xajax;
        var xt = xx.tools;
        var xcb = xx.callback;
        var gcb = xcb.global;
        var lcb = oRequest.callback;
        var oRet = oRequest.returnValue;
        if (xt.in_array(xx.responseSuccessCodes, oRequest.request.status)) {
            xcb.execute([gcb, lcb], 'onSuccess', oRequest);
            var seq = 0;
            if (oRequest.request.responseText) {
                try {
                    var responseJSON = eval('(' + oRequest.request.responseText + ')');
                } catch (ex) {
                    throw(ex);
                }
                if (('object' === typeof responseJSON) && ('object' === typeof responseJSON.xjxobj)) {
                    // oRequest.status.onProcessing();
                    oRet = xt.json.processFragment(responseJSON, seq, oRet, oRequest);
                } else {
                }
            }
            var obj = {};
            obj.fullName = 'Response Complete';
            obj.sequence = seq;
            obj.request = oRequest;
            obj.context = oRequest.context;
            obj.cmd = 'rcmplt';
            xjx.queue.push(xx.response, obj);
            // do not re-start the queue if a timeout is set
            if (null == xx.response.timeout)
                xjx.queue.process(xx.response);
        } else if (xt.in_array(xx.responseRedirectCodes, oRequest.request.status)) {
            xcb.execute([gcb, lcb], 'onRedirect', oRequest);
            window.location = oRequest.request.getResponseHeader('location');
            xx.completeResponse(oRequest);
        } else if (xt.in_array(xx.responseErrorsForAlert, oRequest.request.status)) {
            xcb.execute([gcb, lcb], 'onFailure', oRequest);
            xx.completeResponse(oRequest);
        }
        return oRet;
    };
}(xajax));
/*
	Function: xajax.responseProcessor.xml
	
	Parse the response XML into a series of commands.  The commands
	are constructed by calling <xajax.tools.xml.parseAttributes> and
	<xajax.tools.xml.parseChildren>.
	
	Parameters:
	
	oRequest - (object):  The request context object.
*/
(function (xjx) {
    xjx.responseProcessor.xml = function (oRequest) {
        var xx = xajax;
        var xt = xx.tools;
        var xcb = xx.callback;
        var gcb = xcb.global;
        var lcb = oRequest.callback;
        var oRet = oRequest.returnValue;
        if (xt.in_array(xx.responseSuccessCodes, oRequest.request.status)) {
            xcb.execute([gcb, lcb], 'onSuccess', oRequest);
            var seq = 0;
            if (oRequest.request.responseXML) {
                var responseXML = oRequest.request.responseXML;
                if (responseXML.documentElement) {
                    //   oRequest.status.onProcessing();
                    var child = responseXML.documentElement.firstChild;
                    oRet = xt.xml.processFragment(child, seq, oRet, oRequest);
                }
            }
            var obj = {};
            obj.fullName = 'Response Complete';
            obj.sequence = seq;
            obj.request = oRequest;
            obj.context = oRequest.context;
            obj.cmd = 'rcmplt';
            xjx.queue.push(xx.response, obj);
            // do not re-start the queue if a timeout is set
            if (null == xx.response.timeout)
                xjx.queue.process(xx.response);
        } else if (xt.in_array(xx.responseRedirectCodes, oRequest.request.status)) {
            xcb.execute([gcb, lcb], 'onRedirect', oRequest);
            window.location = oRequest.request.getResponseHeader('location');
            xx.completeResponse(oRequest);
        } else if (xt.in_array(xx.responseErrorsForAlert, oRequest.request.status)) {
            xcb.execute([gcb, lcb], 'onFailure', oRequest);
            xx.completeResponse(oRequest);
        }
        return oRet;
    };
}(xajax));
/*
	Class: xajax.js
	
	Contains the functions for javascript file and function
	manipulation.
*/
(function (xjx) {
    xjx.js = {
        /*
	Function: xajax.js.includeScriptOnce
	
	Add a reference to the specified script file if one does not
	already exist in the HEAD of the current document.
	
	This will effectively cause the script file to be loaded in
	the browser.

	Parameters:
	
	fileName - (string):  The URI of the file.
	
	Returns:
	
	true - The reference exists or was added.
*/
        includeScriptOnce: function (command) {
            command.fullName = 'includeScriptOnce';
            var fileName = command.data;
            // Check for existing script tag for this file.
            var baseDoc = xjx.getContext(context);
            var loadedScripts = baseDoc.getElementsByTagName('script');
            var iLen = loadedScripts.length;
            for (var i = 0; i < iLen; ++i) {
                var script = loadedScripts[i];
                if (script.src) {
                    if (0 <= script.src.indexOf(fileName))
                        return true;
                }
            }
            return xajax.js.includeScript(command);
        },
        /*
	Function: xajax.js.includeScript
	
	Adds a SCRIPT tag referencing the specified file.  This
	effectively causes the script to be loaded in the browser.
	
	Parameters:
	
	command (object) - Xajax response object
	
	Returns:
	
	true - The reference was added.
*/
        includeScript: function (command, context) {
            // todo check object command
            command.fullName = 'includeScript';
            var baseDoc = xjx.getContext(context || command.context);
            var objHead = baseDoc.getElementsByTagName('head');
            var objScript = baseDoc.createElement('script');
            objScript.src = command.data;
            if ('undefined' === typeof command.type) objScript.type = 'text/javascript';
            else objScript.type = command.type;
            if ('undefined' !== typeof command.type) objScript.setAttribute('id', command.elm_id);
            objHead[0].appendChild(objScript);
            return true;
        },
        /*
            Function: xajax.js.removeScript
            
            Locates a SCRIPT tag in the HEAD of the document which references
            the specified file and removes it.
            
            Parameters:
            
            command (object) - Xajax response object
            
            Returns:
            
            true - The script was not found or was removed.
        */
        removeScript: function (command, context) {
            command.fullName = 'removeScript';
            // todo check object command
            var fileName = command.data;
            var unload = command.unld;
            var baseDoc = xjx.getContext(context || command.context);
            var loadedScripts = baseDoc.getElementsByTagName('script');
            var iLen = loadedScripts.length;
            for (var i = 0; i < iLen; ++i) {
                var script = loadedScripts[i];
                if (script.src) {
                    if (0 <= script.src.indexOf(fileName)) {
                        if ('undefined' !== typeof unload) {
                            var args = {};
                            args.data = unload;
                            args.context = window;
                            xajax.js.execute(args);
                        }
                        var parent = script.parentNode;
                        parent.removeChild(script);
                    }
                }
            }
            return true;
        },
        /*
	Function: xajax.js.sleep
	
	Causes the processing of items in the queue to be delayed
	for the specified amount of time.  This is an asynchronous
	operation, therefore, other operations will be given an
	opportunity to execute during this delay.
	
	Parameters:
	
	args - (object):  The response command containing the following
		parameters.
		- args.prop: The number of 10ths of a second to sleep.
	
	Returns:
	
	true - The sleep operation completed.
	false - The sleep time has not yet expired, continue sleeping.
*/
        sleep: function (command) {
            command.fullName = 'sleep';
            // inject a delay in the queue processing
            // handle retry counter
            if (xajax.queue.retry(command, command.prop)) {
                xajax.queue.setWakeup(xajax.response, 100);
                return false;
            }
            // wake up, continue processing queue
            return true;
        },
        /*
            Function: xajax.js.confirmCommands
            
            Prompt the user with the specified text, if the user responds by clicking
            cancel, then skip the specified number of commands in the response command
            queue.  If the user clicks Ok, the command processing resumes normal
            operation.
            
            Parameters:
            
             command (object) - xajax response object
             
            Returns:
            
            true - The operation completed successfully.
        */
        confirmCommands: function (command) {
            command.fullName = 'confirmCommands';
            var msg = command.data;
            var numberOfCommands = command.id;
            if (false === confirm(msg)) {
                while (0 < numberOfCommands) {
                    xajax.queue.pop(xajax.response);
                    --numberOfCommands;
                }
            }
            return true;
        },
        /*
            Function: xajax.js.execute
            
            Execute the specified string of javascript code, using the current
            script context.
            
            Parameters:
            
            args - The response command object containing the following:
                - args.data: (string):  The javascript to be evaluated.
                - args.context: (object):  The javascript object that to be
                    referenced as 'this' in the script.
                    
            Returns:
            
            unknown - A value set by the script using 'returnValue = '
            true - If the script does not set a returnValue.
        */
        execute: function (args) {
            args.fullName = 'execute Javascript';
            var returnValue = true;
            args.context = args.context ? args.context : {};
            args.context.xajaxDelegateCall = function () {
                eval(args.data);
            };
            args.context.xajaxDelegateCall();
            return returnValue;
        },
        /*
            Function: xajax.js.waitFor
            
            Test for the specified condition, using the current script
            context; if the result is false, sleep for 1/10th of a
            second and try again.
            
            Parameters:
            
            args - The response command object containing the following:
            
                - args.data: (string):  The javascript to evaluate.
                - args.prop: (integer):  The number of 1/10ths of a
                    second to wait before giving up.
                - args.context: (object):  The current script context object
                    which is accessible in the javascript being evaluated
                    via the 'this' keyword.
            
            Returns:
            
            false - The condition evaluates to false and the sleep time
                has not expired.
            true - The condition evaluates to true or the sleep time has
                expired.
        */
        waitFor: function (args) {
            args.fullName = 'waitFor';
            var bResult = false;
            var cmdToEval = 'bResult = (';
            cmdToEval += args.data;
            cmdToEval += ');';
            try {
                args.context.xajaxDelegateCall = function () {
                    eval(cmdToEval);
                };
                args.context.xajaxDelegateCall();
            } catch (e) {
            }
            if (false === bResult) {
                // inject a delay in the queue processing
                // handle retry counter
                if (xajax.queue.retry(args, args.prop)) {
                    xajax.queue.setWakeup(xajax.response, 100);
                    return false;
                }
                // give up, continue processing queue
            }
            return true;
        },
        /*
            Function: xajax.js.call
            
            Call a javascript function with a series of parameters using
            the current script context.
            
            Parameters:
            
            args - The response command object containing the following:
                - args.data: (array):  The parameters to pass to the function.
                - args.func: (string):  The name of the function to call.
                - args.context: (object):  The current script context object
                    which is accessible in the function name via the 'this'
                    keyword.
                    
            Returns:
            
            true - The call completed successfully.
        */
        call: function (args) {
            args.fullName = 'call js function';
            var parameters = args.data;
            var scr = [];
            scr.push(args.func);
            scr.push('(');
            if ('undefined' !== typeof parameters) {
                if ('object' === typeof parameters) {
                    var iLen = parameters.length;
                    if (0 < iLen) {
                        scr.push('parameters[0]');
                        for (var i = 1; i < iLen; ++i)
                            scr.push(', parameters[' + i + ']');
                    }
                }
            }
            scr.push(');');
            args.context.xajaxDelegateCall = function () {
                eval(scr.join(''));
            };
            args.context.xajaxDelegateCall();
            return true;
        },
        /*
            Function: xajax.js.setFunction
        
            Constructs the specified function using the specified javascript
            as the body of the function.
            
            Parameters:
            
            args - The response command object which contains the following:
            
                - args.func: (string):  The name of the function to construct.
                - args.data: (string):  The script that will be the function body.
                - args.context: (object):  The current script context object
                    which is accessible in the script name via the 'this' keyword.
                    
            Returns:
            
            true - The function was constructed successfully.
        */
        setFunction: function (args) {
            args.fullName = 'setFunction';
            var code = [];
            code.push(args.func);
            code.push(' = function(');
            if ('object' === typeof args.prop) {
                var separator = '';
                for (var m in args.prop) {
                    code.push(separator);
                    code.push(args.prop[m]);
                    separator = ',';
                }
            } else code.push(args.prop);
            code.push(') { ');
            code.push(args.data);
            code.push(' }');
            args.context.xajaxDelegateCall = function () {
                eval(code.join(''));
            };
            args.context.xajaxDelegateCall();
            return true;
        },
        /*
            Function: xajax.js.wrapFunction
            
            Construct a javascript function which will call the original function with
            the same name, potentially executing code before and after the call to the
            original function.
            
            Parameters:
            
            args - (object):  The response command object which will contain
                the following:
                
                - args.func: (string):  The name of the function to be wrapped.
                - args.prop: (string):  List of parameters used when calling the function.
                - args.data: (array):  The portions of code to be called before, after
                    or even between calls to the original function.
                - args.context: (object):  The current script context object which is
                    accessible in the function name and body via the 'this' keyword.
                    
            Returns:
            
            true - The wrapper function was constructed successfully.
        */
        wrapFunction: function (args) {
            args.fullName = 'wrapFunction';
            var code = [];
            code.push(args.func);
            code.push(' = xajax.js.makeWrapper(');
            code.push(args.func);
            code.push(', args.prop, args.data, args.type, args.context);');
            args.context.xajaxDelegateCall = function () {
                eval(code.join(''));
            };
            args.context.xajaxDelegateCall();
            return true;
        },
        /*
            Function: xajax.js.makeWrapper
            
        
            Helper function used in the wrapping of an existing javascript function.
        
            Parameters:
            
            origFun - (string):  The name of the original function.
            args - (string):  The list of parameters used when calling the function.
            codeBlocks - (array):  Array of strings of javascript code to be executed
                before, after and perhaps between calls to the original function.
            returnVariable - (string):  The name of the variable used to retain the
                return value from the call to the original function.
            context - (object):  The current script context object which is accessible
                in the function name and body via the 'this' keyword.
                
            Returns:
            
            object - The complete wrapper function.
        */
        makeWrapper: function (origFun, args, codeBlocks, returnVariable, context) {
            var originalCall = '';
            if (0 < returnVariable.length) {
                originalCall += returnVariable;
                originalCall += ' = ';
            }
            var originalCall = 'origFun(';
            originalCall += args;
            originalCall += '); ';
            var code = 'wrapper = function(';
            code += args;
            code += ') { ';
            if (0 < returnVariable.length) {
                code += ' var ';
                code += returnVariable;
                code += ' = null;';
            }
            var separator = '';
            var bLen = codeBlocks.length;
            for (var b = 0; b < bLen; ++b) {
                code += separator;
                code += codeBlocks[b];
                separator = originalCall;
            }
            if (0 < returnVariable.length) {
                code += ' return ';
                code += returnVariable;
                code += ';';
            }
            code += ' } ';
            var wrapper = null;
            context.xajaxDelegateCall = function () {
                eval(code);
            };
            context.xajaxDelegateCall();
            return wrapper;
        }
    };
}(xajax));
/*
	Class: xajax.dom
*/
(function (xjx) {
    
    xjx.dom = {
        /*
            Function: xajax.dom.assign
            
            Assign an element's attribute to the specified value.
            
            Parameters:
            
            element - (object):  The HTML element to effect.
            property - (string):  The name of the attribute to set.
            data - (string):  The new value to be applied.
            
            Returns:
            
            true - The operation completed successfully.
            @deprecated use xajax.html(); or xajax.wrapHtml()
        */
        assign: function (element, property, data) {
            if (null === (element = xjx.$(element))) return null;
            switch (property) {
                case 'innerHTML':
                    // switched to his own mechanism @since 0.7.3
                    xjx.html(element, data);
                    break;
                case 'outerHTML':
                    if ('undefined' === typeof element.outerHTML) {
                        var r = xjx.config('baseDocument').createRange();
                        r.setStartBefore(element);
                        var df = r.createContextualFragment(data);
                        element.parentNode.replaceChild(df, element);
                    } else element.outerHTML = data;
                    break;
                default:
                    if (xajax.tools.willChange(element, property, data))
                        eval('element.' + property + ' = data;');
                    break;
            }
            return true;
        },
        /*
            Function: xajax.dom.append
            
            Append the specified value to an element's attribute.
            
            Parameters:
            
            element - (object):  The HTML element to effect.
            property - (string):  The name of the attribute to append to.
            data - (string):  The new value to be appended.
            
            Returns:
            
            true - The operation completed successfully.
        */
        append: function (element, property, data) {
            if ('string' === typeof element)
                element = xajax.$(element);
            eval('element.' + property + ' += data;');
            return true;
        },
        /*
            Function: xajax.dom.prepend
            
            Prepend the specified value to an element's attribute.
            
            Parameters:
            
            element - (object):  The HTML element to effect.
            property - (string):  The name of the attribute.
            data - (string):  The new value to be prepended.
            
            Returns:
            
            true - The operation completed successfully.
        */
        prepend: function (element, property, data) {
            if ('string' === typeof element)
                element = xajax.$(element);
            eval('element.' + property + ' = data + element.' + property);
            return true;
        },
        /*
            Function: xajax.dom.replace
            
            Search and replace the specified text.
            
            Parameters:
            
            element - (string or object):  The name of, or the element itself which is
                to be modified.
            sAttribute - (string):  The name of the attribute to be set.
            aData - (array):  The search text and replacement text.
            
            Returns:
            
            true - The operation completed successfully.
        */
        replace: function (element, sAttribute, aData) {
            var sSearch = aData['s'];
            var sReplace = aData['r'];
            if (sAttribute === 'innerHTML')
                sSearch = xajax.tools.getBrowserHTML(sSearch);
            if ('string' === typeof element)
                element = xajax.$(element);
            eval('var txt = element.' + sAttribute);
            var bFunction = false;
            if ('function' === typeof txt) {
                txt = txt.join('');
                bFunction = true;
            }
            var start = txt.indexOf(sSearch);
            if (start > -1) {
                var newTxt = [];
                while (start > -1) {
                    var end = start + sSearch.length;
                    newTxt.push(txt.substr(0, start));
                    newTxt.push(sReplace);
                    txt = txt.substr(end, txt.length - end);
                    start = txt.indexOf(sSearch);
                }
                newTxt.push(txt);
                newTxt = newTxt.join('');
                if (bFunction) {
                    eval('element.' + sAttribute + '=newTxt;');
                } else if (xajax.tools.willChange(element, sAttribute, newTxt)) {
                    eval('element.' + sAttribute + '=newTxt;');
                }
            }
            return true;
        },
        /*
            Function: xajax.dom.remove
            
            Delete an element.
            
            Parameters:
            
            element - (string or object):  The name of, or the element itself which
                will be deleted.
                
            Returns:
            
            true - The operation completed successfully.
        */
        remove: function (element) {
            if ('string' === typeof element)
                element = xajax.$(element);
            if (element && element.parentNode && element.parentNode.removeChild)
                element.parentNode.removeChild(element);
            return true;
        },
        /*
            Function: xajax.dom.create
            
            Create a new element and append it to the specified parent element.
            
            Parameters:
            
            objParent - (string or object):  The name of, or the element itself
                which will contain the new element.
            sTag - (string):  The tag name for the new element.
            sId - (string):  The value to be assigned to the id attribute of
                the new element.
                
            Returns:
            
            true - The operation completed successfully.
        */
        create: function (objParent, sTag, sId) {
            if ('string' === typeof objParent)
                objParent = xajax.$(objParent);
            var target = xajax.config.baseDocument.createElement(sTag);
            target.setAttribute('id', sId);
            if (objParent)
                objParent.appendChild(target);
            return true;
        },
        /*
            Function: xajax.dom.insert
            
            Insert a new element before the specified element.
        
            Parameters:
            
            objSibling - (string or object):  The name of, or the element itself
                that will be used as the reference point for insertion.
            sTag - (string):  The tag name for the new element.
            sId - (string):  The value that will be assigned to the new element's
                id attribute.
            
            Returns:
            
            true - The operation completed successfully.
        */
        insert: function (objSibling, sTag, sId) {
            if ('string' === typeof objSibling)
                objSibling = xajax.$(objSibling);
            var target = xajax.config.baseDocument.createElement(sTag);
            target.setAttribute('id', sId);
            objSibling.parentNode.insertBefore(target, objSibling);
            return true;
        },
        /*
            Function: xajax.dom.insertAfter
            
            Insert a new element after the specified element.
        
            Parameters:
            
            objSibling - (string or object):  The name of, or the element itself
                that will be used as the reference point for insertion.
            sTag - (string):  The tag name for the new element.
            sId - (string):  The value that will be assigned to the new element's
                id attribute.
            
            Returns:
            
            true - The operation completed successfully.
        */
        insertAfter: function (objSibling, sTag, sId) {
            if ('string' === typeof objSibling)
                objSibling = xajax.$(objSibling);
            var target = xajax.config.baseDocument.createElement(sTag);
            target.setAttribute('id', sId);
            objSibling.parentNode.insertBefore(target, objSibling.nextSibling);
            return true;
        },
        /*
            Function: xajax.dom.contextAssign
            
            Assign a value to a named member of the current script context object.
            
            Parameters:
            
            args - (object):  The response command object which will contain the
                following:
                
                - args.prop: (string):  The name of the member to assign.
                - args.data: (string or object):  The value to assign to the member.
                - args.context: (object):  The current script context object which
                    is accessible via the 'this' keyword.
            
            Returns:
            
            true - The operation completed successfully.
        */
        contextAssign: function (args) {
            args.fullName = 'context assign';
            var code = [];
            code.push('this.');
            code.push(args.prop);
            code.push(' = data;');
            code = code.join('');
            args.context.xajaxDelegateCall = function (data) {
                eval(code);
            };
            args.context.xajaxDelegateCall(args.data);
            return true;
        },
        /*
            Function: xajax.dom.contextAppend
            
            Appends a value to a named member of the current script context object.
            
            Parameters:
            
            args - (object):  The response command object which will contain the
                following:
                
                - args.prop: (string):  The name of the member to append to.
                - args.data: (string or object):  The value to append to the member.
                - args.context: (object):  The current script context object which
                    is accessible via the 'this' keyword.
            
            Returns:
            
            true - The operation completed successfully.
        */
        contextAppend: function (args) {
            args.fullName = 'context append';
            var code = [];
            code.push('this.');
            code.push(args.prop);
            code.push(' += data;');
            code = code.join('');
            args.context.xajaxDelegateCall = function (data) {
                eval(code);
            };
            args.context.xajaxDelegateCall(args.data);
            return true;
        },
        /*
            Function: xajax.dom.contextPrepend
            
            Prepend a value to a named member of the current script context object.
            
            Parameters:
            
            args - (object):  The response command object which will contain the
                following:
                
                - args.prop: (string):  The name of the member to prepend to.
                - args.data: (string or object):  The value to prepend to the member.
                - args.context: (object):  The current script context object which
                    is accessible via the 'this' keyword.
            
            Returns:
            
            true - The operation completed successfully.
        */
        contextPrepend: function (args) {
            args.fullName = 'context prepend';
            var code = [];
            code.push('this.');
            code.push(args.prop);
            code.push(' = data + this.');
            code.push(args.prop);
            code.push(';');
            code = code.join('');
            args.context.xajaxDelegateCall = function (data) {
                eval(code);
            };
            args.context.xajaxDelegateCall(args.data);
            return true;
        }
        
    };
    /**
     * Simplify dom assign
     *
     * @param {string|Element} ele id of the element or an Element
     * @param {string|undefined} content string to set the string | Leave empty content to get the content of the current Html-Element
     *
     * @return {string|null} returns the Element-Content or null if ele not found
     * @since 0.7.3
     *
     * @todo adding context for xjx.$()
     * **/
    xjx.html = function (ele, content) {
        if (null === (ele = xjx.$(ele))) return null;
        if ('undefined' !== typeof content)
            return ele.innerHTML = content;
        else
            return ele.innerHTML;
    };
    
}(xajax));
/*
	Class: xajax.domResponse
*/
(function (xjx) {
    
    /*
        Class: xajax.domResponse
    */
    xajax.domResponse = {
        startResponse: function (args) {
            xjxElm = [];
        },
        createElement: function (args) {
            eval(
              [args.tgt, ' = document.createElement(args.data)'].join('')
            );
        },
        setAttribute: function (args) {
            args.context.xajaxDelegateCall = function () {
                eval(
                  [args.tgt, '.setAttribute(args.key, args.data)'].join('')
                );
            };
            args.context.xajaxDelegateCall();
        },
        appendChild: function (args) {
            args.context.xajaxDelegateCall = function () {
                eval(
                  [args.par, '.appendChild(', args.data, ')'].join('')
                );
            };
            args.context.xajaxDelegateCall();
        },
        insertBefore: function (args) {
            args.context.xajaxDelegateCall = function () {
                eval(
                  [
                      args.tgt, '.parentNode.insertBefore(', args.data, ', ',
                      args.tgt, ')'].join('')
                );
            };
            args.context.xajaxDelegateCall();
        },
        insertAfter: function (args) {
            args.context.xajaxDelegateCall = function () {
                eval(
                  [
                      args.tgt, 'parentNode.insertBefore(', args.data, ', ',
                      args.tgt, '.nextSibling)'].join('')
                );
            };
            args.context.xajaxDelegateCall();
        },
        appendText: function (args) {
            args.context.xajaxDelegateCall = function () {
                eval(
                  [
                      args.par,
                      '.appendChild(document.createTextNode(args.data))'].join('')
                );
            };
            args.context.xajaxDelegateCall();
        },
        removeChildren: function (args) {
            var skip = args.skip || 0;
            var remove = args.remove || -1;
            var element = null;
            args.context.xajaxDelegateCall = function () {
                eval(['element = ', args.data].join(''));
            };
            args.context.xajaxDelegateCall();
            var children = element.childNodes;
            for (var i in children) {
                if (isNaN(i) === false && children[i].nodeType === 1) {
                    if (skip > 0) skip = skip - 1;
                    else if (remove !== 0) {
                        if (remove > 0)
                            remove = remove - 1;
                        element.removeChild(children[i]);
                    }
                }
            }
        },
        endResponse: function (args) {
            xjxElm = [];
        }
    };
}(xajax));
/*
	Class: xajax.css
*/
(function (xjx) {
    xjx.css = {
        /*
            Function: xajax.css.add
            
            Add a LINK reference to the specified .css file if it does not
            already exist in the HEAD of the current document.
            
            Parameters:
            
            filename - (string):  The URI of the .css file to reference.
        
            media - (string):  The media type of the css file (print/screen/handheld,..)
            
            Returns:
            
            true - The operation completed successfully.
        */
        add: function (fileName, media, context) {
            var oDoc = xjx.getContext(context);
            var oHeads = oDoc.getElementsByTagName('head');
            var oHead = oHeads[0];
            var oLinks = oHead.getElementsByTagName('link');
            var found = false;
            var iLen = oLinks.length;
            for (var i = 0; i < iLen && false === found; ++i)
                if (0 <= oLinks[i].href.indexOf(fileName) && oLinks[i].media === media)
                    found = true;
            if (true === found) return true;
            var oCSS = oDoc.createElement('link');
            oCSS.rel = 'stylesheet';
            oCSS.type = 'text/css';
            oCSS.href = fileName;
            oCSS.media = media;
            oHead.appendChild(oCSS);
            return true;
        },
        /*
            Function: xajax.css.remove
            
            Locate and remove a LINK reference from the current document's
            HEAD.
            
            Parameters:
            
            filename - (string):  The URI of the .css file.
            
            Returns:
            
            true - The operation completed successfully.
        */
        remove: function (fileName, media, context) {
            var oDoc = xjx.getContext(context);
            var oHeads = oDoc.getElementsByTagName('head');
            var oHead = oHeads[0];
            var oLinks = oHead.getElementsByTagName('link');
            var i = 0;
            while (i < oLinks.length)
                if (0 <= oLinks[i].href.indexOf(fileName) && oLinks[i].media === media)
                    oHead.removeChild(oLinks[i]);
                else ++i;
            return true;
        },
        /*
            Function: xajax.css.waitForCSS
            
            Attempt to detect when all .css files have been loaded once
            they are referenced by a LINK tag in the HEAD of the current
            document.
            
            Parameters:
            
            args - (object):  The response command object which will contain
                the following:
                
                - args.prop - (integer):  The number of 1/10ths of a second
                    to wait before giving up.
            
            Returns:
            
            true - The .css files appear to be loaded.
            false - The .css files do not appear to be loaded and the timeout
                has not expired.
        */
        waitForCSS: function (args, context) {
            var oDoc = xjx.getContext(context);
            // todo property styleSheets exists
            // todo test
            var oDocSS = oDoc.styleSheets;
            var ssEnabled = [];
            var iLen = oDocSS.length;
            for (var i = 0; i < iLen; ++i) {
                ssEnabled[i] = 0;
                try {
                    ssEnabled[i] = oDocSS[i].cssRules.length;
                } catch (e) {
                    try {
                        ssEnabled[i] = oDocSS[i].rules.length;
                    } catch (e) {
                    }
                }
            }
            var ssLoaded = true;
            var ssEnL = ssEnabled.length;
            for (var t = 0; t < ssEnL; ++t)
                if (0 === ssEnabled[t])
                    ssLoaded = false;
            if (false === ssLoaded) {
                // inject a delay in the queue processing
                // handle retry counter
                if (xajax.queue.retry(args, args.prop)) {
                    xajax.queue.setWakeup(xajax.response, 10);
                    return false;
                }
                // give up, continue processing queue
            }
            return true;
        }
    };
}(xajax));
/*
	Class: xajax.forms
*/
(function (xjx) {
    xjx.forms = {
        /*
            Function: xajax.forms.getInput
            
            Create and return a form input element with the specified parameters.
            
            Parameters:
            
            type - (string):  The type of input element desired.
            name - (string):  The value to be assigned to the name attribute.
            id - (string):  The value to be assigned to the id attribute.
            
            Returns:
            
            object - The new input element.
        */
        getInput: function (type, name, id, context) {
            var baseDoc = xjx.getContext(context);
            if ('undefined' === typeof window.addEventListener) {
                xajax.forms.getInput = function (type, name, id) {
                    return baseDoc.createElement('<input type="' + type + '" name="' + name + '" id="' + id + '">');
                };
            } else {
                xjx.forms.getInput = function (type, name, id) {
                    var Obj = baseDoc.createElement('input');
                    Obj.setAttribute('type', type);
                    Obj.setAttribute('name', name);
                    Obj.setAttribute('id', id);
                    return Obj;
                };
            }
            return xjx.forms.getInput(type, name, id);
        },
        /*
            Function: xajax.forms.createInput
            
            Create a new input element under the specified parent.
            
            Parameters:
            
            objParent - (string or object):  The name of, or the element itself
                that will be used as the reference for the insertion.
            sType - (string):  The value to be assigned to the type attribute.
            sName - (string):  The value to be assigned to the name attribute.
            sId - (string):  The value to be assigned to the id attribute.
            
            Returns:
            
            true - The operation completed successfully.
        */
        createInput: function (command) {
            command.fullName = 'createInput';
            var objParent = command.id;
            var sType = command.type;
            var sName = command.data;
            var sId = command.prop;
            if ('string' === typeof objParent)
                objParent = xjx.$(objParent);
            var target = xjx.forms.getInput(sType, sName, sId);
            if (objParent && target) {
                objParent.appendChild(target);
            }
            return true;
        },
        /*
            Function: xajax.forms.insertInput
            
            Insert a new input element before the specified element.
            
            Parameters:
            
            objSibling - (string or object):  The name of, or the element itself
                that will be used as the reference for the insertion.
            sType - (string):  The value to be assigned to the type attribute.
            sName - (string):  The value to be assigned to the name attribute.
            sId - (string):  The value to be assigned to the id attribute.
            
            Returns:
            
            true - The operation completed successfully.
        */
        insertInput: function (command) {
            command.fullName = 'insertInput';
            var objSibling = command.id;
            var sType = command.type;
            var sName = command.data;
            var sId = command.prop;
            if ('string' === typeof objSibling)
                objSibling = xjx.$(objSibling);
            var target = xjx.forms.getInput(sType, sName, sId);
            if (target && objSibling && objSibling.parentNode)
                objSibling.parentNode.insertBefore(target, objSibling);
            return true;
        },
        /*
            Function: xajax.forms.insertInputAfter
        
            Insert a new input element after the specified element.
            
            Parameters:
            
            objSibling - (string or object):  The name of, or the element itself
                that will be used as the reference for the insertion.
            sType - (string):  The value to be assigned to the type attribute.
            sName - (string):  The value to be assigned to the name attribute.
            sId - (string):  The value to be assigned to the id attribute.
            
            Returns:
            
            true - The operation completed successfully.
        */
        insertInputAfter: function (command) {
            command.fullName = 'insertInputAfter';
            var objSibling = command.id;
            var sType = command.type;
            var sName = command.data;
            var sId = command.prop;
            if ('string' === typeof objSibling)
                objSibling = xjx.$(objSibling);
            var target = xjx.forms.getInput(sType, sName, sId);
            if (target && objSibling && objSibling.parentNode)
                objSibling.parentNode.insertBefore(target, objSibling.nextSibling);
            return true;
        }
    };
}(xajax));
// xajax form values handler
(function (xjx) {
    
    /*
       Function: xajax.tools._getFormValues
       
       Used internally by <xajax.tools.getFormValues> to recursively get the value
       of form elements.  This function will extract all form element values
       regardless of the depth of the element within the form.
       */
    var _getFormValues = function (aFormValues, children, submitDisabledElements, prefix) {
        var iLen = children.length;
        for (var i = 0; i < iLen; ++i) {
            var child = children[i];
            if (('undefined' !== typeof child.childNodes) && (child.type !== 'select-one') && (child.type !== 'select-multiple'))
                _getFormValues(aFormValues, child.childNodes, submitDisabledElements, prefix);
            _getFormValue(aFormValues, child, submitDisabledElements, prefix);
        }
    };
    /**
     * Try to readout the field Attribute name
     *
     * @property Element nEle
     * @return string | null
     * */
    var getFieldName = function (nEle) {
        var sName;
        if (null !== (nEle = xjx.tools.$(nEle)))
            if (null !== (sName = nEle.getAttribute('name'))) {
                return sName;
            }
        return null;
    };
    
    /**
     * Extract the FormField Name from string
     *
     * @property sName string
     * @return object
     * **/
    var extractFieldName = function (sName) {
        if (xjx.isStr(sName)) {
            // todo check against valid name (is not allowed to give back bracket)
            if (sName.indexOf('[') === -1 || sName.indexOf(']') === -1) return new Array(sName.trim());
            
            var parts = sName.split(/[[\]]{1,2}/);
            parts.length--;
            var ret = [];
            parts.forEach(function (value) {
                ret.push(value.trim());
            });
            return ret;
        }
    };
    /*
    * Method to create an Object from skeleton form field name
    *
    * @property {string} aLst
    * @property {string|int|float|array} val
    *
    * @return object
    * **/
    var listAsObject = function (aLst, val) {
        // if no value we do not have to transport it@todo check html specs
        if ('undefined' === typeof val || null === val) return null;
        
        var tmpObject = null, p = null, oldP = null;
        
        var lng = aLst.length;
        for (lng; lng > 0; --lng) {
            oldP = p;
            p = {};
            p[aLst[lng - 1]] = (null === oldP) ? val : oldP;
            tmpObject = p;
        }
        return tmpObject;
    };
    var merge = function (obj1, obj2) {
        
        for (var p in obj2) {
            if (obj2.hasOwnProperty(p)) {
                if ('object' === typeof obj1[p]) {
                    // recursive merge
                    if ('object' === typeof obj2[p])
                        obj1[p] = merge(obj1[p], obj2[p]);
                    else
                    // obj2 is not deeper
                        obj1[p] = obj2[p];
                } else {
                    if ('function' === typeof obj1.push)
                    // numbered like checkboxes
                        obj1.push(obj2[p]);
                    else
                    // regular push
                        obj1[p] = obj2[p];
                }
            }
        }
        
        return obj1;
    };
    
    /**
     * Function: xajax.tools._getFormValue
     *
     * Used internally by <xajax.tools._getFormValues> to extract a single form value.
     * This will detect the type of element (radio, checkbox, multi-select) and
     * add it's value(s) to the form values array.
     *
     * Modified version for multidimensional arrays
     **/
    var _getFormValue = function (aFormValues, child, submitDisabledElements, prefix) {
        if (!child.name)
            return;
        // todo check whats with param
        if ('PARAM' === child.tagName) return;
        
        // getting the html name-Attribute
        var sFieldName = getFieldName(child);
        if (null === sFieldName) return null;
        
        // check against disabled
        if (child.disabled)
            if (true === child.disabled)
                if (false === submitDisabledElements)
                    return;
        
        if (prefix !== child.name.substring(0, prefix.length))
            return;
        var cT = child.type;
        if (cT) {
            // kick down on null value
            if (cT === 'radio' || cT === 'checkbox') {
                if (!child.checked)
                    return;
            }
            
            if (cT === 'select-one') {
                if (child.selectedIndex)
                    values = child.options[child.selectedIndex].value;
                else
                // nothing selected
                    return;
            }
            
            else if ('select-multiple' === child.type) {
                var values = [];
                var jLen = child.length;
                for (var j = 0; j < jLen; ++j) {
                    var option = child.options[j];
                    if (option.selected)
                        values.push(option.value);
                }
            } else {
                values = child.value;
            }
        }
        // new Method
        var fieldParts = extractFieldName(child.name);
        var field = listAsObject(fieldParts, values);
        
        return merge(aFormValues, field);
        
    };
    
    /**
     * Function: xajax.tools.getFormValues
     *
     * Build an associative array of form elements and their values from
     * the specified form.
     *
     * Parameters:
     *
     * element - (string): The unique name (id) of the form to be processed.
     * disabled - (boolean, optional): Include form elements which are currently disabled.
     * prefix - (string, optional): A prefix used for selecting form elements.
     *
     * @return null|object  Null on not found Parent form  An associative array of form element id and value.
     */
    
    var getFormValues = function (parent) {
        
        if (null === (parent = xjx.tools.$(parent))) return null;
        
        var submitDisabledElements = false;
        if (arguments.length > 1 && arguments[1] === true)
            submitDisabledElements = true;
        var prefix = '';
        if (arguments.length > 2)
            prefix = arguments[2];
        parent = xjx.$(parent);
        var aFormValues = {};
//		JW: Removing these tests so that form values can be retrieved from a specified
//		container element like a DIV, regardless of whether they exist in a form or not.
//
        if (parent)
            if (parent.childNodes)
                _getFormValues(aFormValues, parent.childNodes, submitDisabledElements, prefix);
        return aFormValues;
    };
    // old Hook
    
    xjx.forms = {
        getFormValues: getFormValues,
        // currently for unitTesting
        valueHandler: {merge: merge, extractFieldName: extractFieldName, listAsObject: listAsObject}
    };
    
}(xajax));
/*
	Class: xajax.events
*/
(function (xjx) {
    
    
    /*
        Function: xajax.tools.stripOnPrefix
        
        Detect, and if found, remove the prefix 'on' from the specified
        string.  This is used while working with event handlers.
        
        Parameters:
        
        sEventName - (string): The string to be modified.
        
        Returns:
        
        string - The modified string.
    */
    xjx.tools.stripOnPrefix = function (sEventName) {
        sEventName = sEventName.toLowerCase();
        if (0 === sEventName.indexOf('on'))
            sEventName = sEventName.replace(/on/, '');
        return sEventName;
    };
    /*
        Function: xajax.tools.addOnPrefix
        
        Detect, and add if not found, the prefix 'on' from the specified
        string.  This is used while working with event handlers.
        
        Parameters:
        
        sEventName - (string): The string to be modified.
        
        Returns:
        
        string - The modified string.
    */
    xjx.tools.addOnPrefix = function (sEventName) {
        sEventName = sEventName.toLowerCase();
        if (0 !== sEventName.indexOf('on'))
            sEventName = 'on' + sEventName;
        return sEventName;
    };
    xjx.events = {
        /*
            Function: xajax.events.setEvent
            
            Set an event handler.
            
            Parameters:
            
            command - (object): Response command object.
            - id: Element ID
            - prop: Event
            - data: Code
        
            Returns:
            
            true - The operation completed successfully.
        */
        setEvent: function (command) {
            command.fullName = 'setEvent';
            var element = command.id;
            var sEvent = command.prop;
            var code = command.data;
            //force to get the element
            element = xajax.$(element);
            sEvent = xajax.tools.addOnPrefix(sEvent);
            code = xajax.tools.doubleQuotes(code);
            eval('element.' + sEvent + ' = function(e) { ' + code + '; }');
            return true;
        },
        /*
            Function: xajax.events.addHandler
            
            Add an event handler to the specified element.
            
            Parameters:
            
            element - (string or object):  The name of, or the element itself
                which will have the event handler assigned.
            sEvent - (string):  The name of the event.
            fun - (string):  The function to be called.
            
            Returns:
            
            true - The operation completed successfully.
        */
        addHandler: function (element, sEvent, fun) {
            if (window.addEventListener) {
                xajax.events.addHandler = function (command) {
                    command.fullName = 'addHandler';
                    var element = command.id;
                    var sEvent = command.prop;
                    var fun = command.data;
                    if ('string' === typeof element)
                        element = xajax.$(element);
                    sEvent = xajax.tools.stripOnPrefix(sEvent);
                    eval('element.addEventListener("' + sEvent + '", ' + fun + ', false);');
                    return true;
                };
            } else {
                xajax.events.addHandler = function (command) {
                    command.fullName = 'addHandler';
                    var element = command.id;
                    var sEvent = command.prop;
                    var fun = command.data;
                    if ('string' === typeof element)
                        element = xajax.$(element);
                    sEvent = xajax.tools.addOnPrefix(sEvent);
                    eval('element.attachEvent("' + sEvent + '", ' + fun + ', false);');
                    return true;
                };
            }
            return xajax.events.addHandler(element, sEvent, fun);
        },
        /*
            Function: xajax.events.removeHandler
            
            Remove an event handler from an element.
            
            Parameters:
            
            element - (string or object):  The name of, or the element itself which
                will have the event handler removed.
            event - (string):  The name of the event for which this handler is
                associated.
            fun - The function to be removed.
            
            Returns:
            
            true - The operation completed successfully.
        */
        removeHandler: function (element, sEvent, fun) {
            if (window.removeEventListener) {
                xajax.events.removeHandler = function (command) {
                    command.fullName = 'removeHandler';
                    var element = command.id;
                    var sEvent = command.prop;
                    var fun = command.data;
                    if ('string' === typeof element)
                        element = xajax.$(element);
                    sEvent = xajax.tools.stripOnPrefix(sEvent);
                    eval('element.removeEventListener("' + sEvent + '", ' + fun + ', false);');
                    return true;
                };
            } else {
                xajax.events.removeHandler = function (command) {
                    command.fullName = 'removeHandler';
                    var element = command.id;
                    var sEvent = command.prop;
                    var fun = command.data;
                    if ('string' === typeof element)
                        element = xajax.$(element);
                    sEvent = xajax.tools.addOnPrefix(sEvent);
                    eval('element.detachEvent("' + sEvent + '", ' + fun + ', false);');
                    return true;
                };
            }
            return xajax.events.removeHandler(element, sEvent, fun);
        }
    };
}(xajax));
/*
    Class: xajax.callback
*/
(function (xjx) {
    xjx.callback = {
        /*
            Function: xajax.callback.create
            
            Create a blank callback object.  Two optional arguments let you
            set the delay time for the onResponseDelay and onExpiration events.
            
            Returns:
            
            object - The callback object.
        */
        create: function () {
            var xx = xajax;
            var xc = xx.config;
            var xcb = xx.callback;
            var oCB = {};
            oCB.timers = {};
            oCB.timers.onResponseDelay = xcb.setupTimer(
              (arguments.length > 0)
                ? arguments[0]
                : xc.getOption('defaultResponseDelayTime'));
            oCB.timers.onExpiration = xcb.setupTimer(
              (arguments.length > 1)
                ? arguments[1]
                : xc.getOption('defaultExpirationTime'));
            oCB.onRequest = null;
            oCB.onResponseDelay = null;
            oCB.onExpiration = null;
            oCB.beforeResponseProcessing = null;
            oCB.onFailure = null;
            oCB.onRedirect = null;
            oCB.onSuccess = null;
            oCB.onComplete = null;
            return oCB;
        },
        /*
            Function: xajax.callback.setupTimer
            
            Create a timer to fire an event in the future.  This will
            be used fire the onRequestDelay and onExpiration events.
            
            Parameters:
            
            iDelay - (integer):  The amount of time in milliseconds to delay.
            
            Returns:
            
            object - A callback timer object.
        */
        setupTimer: function (iDelay) {
            return {timer: null, delay: iDelay};
        },
        /*
            Function: xajax.callback.clearTimer
            
            Clear a callback timer for the specified function.
            
            Parameters:
            
            oCallback - (object):  The callback object (or objects) that
                contain the specified function timer to be cleared.
            sFunction - (string):  The name of the function associated
                with the timer to be cleared.
        */
        clearTimer: function (oCallback, sFunction) {
            if ('undefined' !== typeof oCallback.timers) {
                if ('undefined' !== typeof oCallback.timers[sFunction]) {
                    clearTimeout(oCallback.timers[sFunction].timer);
                }
            } else if ('object' === typeof oCallback) {
                var iLen = oCallback.length;
                for (var i = 0; i < iLen; ++i)
                    xajax.callback.clearTimer(oCallback[i], sFunction);
            }
        },
        /*
            Function: xajax.callback.execute
            
            Execute a callback event.
            
            Parameters:
            
            oCallback - (object):  The callback object (or objects) which
                contain the event handlers to be executed.
            sFunction - (string):  The name of the event to be triggered.
            args - (object):  The request object for this request.
        */
        execute: function (oCallback, sFunction, args) {
            if ('undefined' !== typeof oCallback[sFunction]) {
                var func = oCallback[sFunction];
                if ('function' === typeof func) {
                    if ('undefined' !== typeof oCallback.timers[sFunction]) {
                        oCallback.timers[sFunction].timer = setTimeout(function () {
                            func(args);
                        }, oCallback.timers[sFunction].delay);
                    }
                    else {
                        func(args);
                    }
                }
            } else if ('object' === typeof oCallback) {
                var iLen = oCallback.length;
                for (var i = 0; i < iLen; ++i)
                    xajax.callback.execute(oCallback[i], sFunction, args);
            }
        }
        /*
            Class: xajax.callback.global
            
            The global callback object which is active for every request.
        */
    };
    xajax.callback.global = xajax.callback.create();
}(xajax));
/** xajax attr **/
(function (xjx) {
    /**
     * remove all useless stuff
     *
     * @param {string} str
     *
     * @return {string}
     * **/
    var remS = function (str) {
        return (xjx.isStr(str)) ? str.replace(/\s\s+/g, ' ') : '';
    };
    /**
     * internal proxy to remove the old xajax.tools.$ class
     * @param {string|Element} elem Element or id=""
     *
     * @return {null|Element}
     * */
    var getEle = function (elem) {
        return xjx.tools.$(elem);
    };
    /**
     * Adds an class string
     *
     * @param {string|Element} elem Element or id=""
     * @param {string} val class to add
     */
    xjx.addClass = function (elem, val) {
        if (xjx.isAttribValue(val) && (elem = getEle(elem))) {
            val = val.trim();
            if (!xjx.hasClass(elem, val)) {
                var nC = elem.className + ' ' + val;
                elem.className = nC.trim();
            }
            
        }
    };
    /**
     * Removes an class from element
     *
     * @param {string|Element} elem Element or id=""
     * @param {string} val class to remove
     */
    xjx.removeClass = function (elem, val) {
        
        if (xjx.isAttribValue(val) && (elem = getEle(elem)) && elem.className) {
            val = val.trim();
            try {
                
                var nS = remS(elem.className.replace(val, ' '));
                elem.className = nS.trim();
            } catch (error) {
                throw error;
            }
            return true;
        }
    };
    /**
     * Checks an Class exists
     *
     * @param {string|Element} elem Element or id=""
     * @param {string} val class to check
     */
    xjx.hasClass = function (elem, val) {
        
        if (xjx.isAttribValue(val) && (elem = getEle(elem))) {
            val = val.trim();
            return elem.className && new RegExp('(^|\\s)' + val + '(\\s|$)').test(elem.className);
        }
        return false;
    };
    /**
     * Checks an Class exists
     *
     * @param {string|Element} elem Element or id=""
     * @param {string} prop attribute to check
     *
     * @return {boolean}
     */
    xjx.hasAttrib = function (elem, prop) {
        if (xjx.isStr(prop) && (elem = getEle(elem))) {
            return elem.hasAttribute(prop);
        }
        return false;
    };
    /**
     * Adding an Attribute if it not exists
     *
     * @param {string|Element} elem Element or id=""
     * @param {string} prop attribute to check
     * @param {string} val content to add in Attribute
     **/
    xjx.addAttrib = function (elem, prop, val) {
        elem = getEle(elem);
        if (xjx.isAttribValue(val) && xjx.isStr(prop))
            if (!xjx.hasAttrib(elem, prop))
                elem.setAttribute(prop, val);
            else
                elem.setAttribute(prop, elem.getAttribute(prop) + val);
    };
    /**
     * Remove an Attribute if exists
     *
     * @param {string|Element} elem Element or id=""
     * @param {string} prop attribute to remove
     */
    xjx.removeAttr = function (elem, prop) {
        if (xjx.isStr(prop) && true === xjx.hasAttrib(elem, prop))
            elem.removeAttribute(prop);
    };
}(xajax));
/*
	Class: xajax.command
	
	The object that manages commands and command handlers.
*/
(function (xjx) {
    'use strict';
    xjx.command = {};
    /**
     *   Class: xajax.command.handler
     *
     * The object that manages command handlers.
     * @private Handler not public anymore. use setter and getter
     * **/
    var handlers =
      {
          'rcmplt': function (args) {
              xajax.completeResponse(args.request);
              return true;
          }, 'css': function (args) {
              args.fullName = 'includeCSS';
              if ('undefined' === typeof args.media)
                  args.media = 'screen';
              return xajax.css.add(args.data, args.media);
          }, 'rcss': function (args) {
              args.fullName = 'removeCSS';
              if ('undefined' === typeof args.media)
                  args.media = 'screen';
              return xajax.css.remove(args.data, args.media);
          },
          'wcss': function (args) {
              args.fullName = 'waitForCSS';
              return xajax.css.waitForCSS(args);
          },
          'as': function (args) {
              // @deprecated
              args.fullName = 'assign/clear';
              try {
                  return xajax.dom.assign(args.target, args.prop, args.data);
              } catch (e) {
                  // do nothing, if the debug module is installed it will
                  // catch and handle the exception
              }
              return true;
          },
          'ap': function (args) {
              args.fullName = 'append';
              return xajax.dom.append(args.target, args.prop, args.data);
          },
          'pp': function (args) {
              args.fullName = 'prepend';
              return xajax.dom.prepend(args.target, args.prop, args.data);
          },
          'rp': function (args) {
              args.fullName = 'replace';
              return xajax.dom.replace(args.id, args.prop, args.data);
          },
          'rm': function (args) {
              args.fullName = 'remove';
              return xajax.dom.remove(args.id);
          },
          'ce': function (args) {
              args.fullName = 'create';
              return xajax.dom.create(args.id, args.data, args.prop);
          },
          'ie': function (args) {
              args.fullName = 'insert';
              return xajax.dom.insert(args.id, args.data, args.prop);
          },
          'ia': function (args) {
              args.fullName = 'insertAfter';
              return xajax.dom.insertAfter(args.id, args.data, args.prop);
          },
          'DSR': xjx.domResponse.startResponse,
          'DCE': xjx.domResponse.createElement,
          'DSA': xjx.domResponse.setAttribute,
          'DAC': xjx.domResponse.appendChild,
          'DIB': xjx.domResponse.insertBefore,
          'DIA': xjx.domResponse.insertAfter,
          'DAT': xjx.domResponse.appendText,
          'DRC': xjx.domResponse.removeChildren,
          'DER': xjx.domResponse.endResponse,
          'attr:ad': xjx.addAttrib,
          'attr:re': xjx.removeAttr,
          'cls:add': function (args) {
              xjx.addClass(args.id, args.data);
          }, 'cls:rem': function (args) {
              xjx.removeClass(args.id, args.data);
          },
          'c:as': xjx.dom.contextAssign,
          'c:ap': xjx.dom.contextAppend,
          'c:pp': xjx.dom.contextPrepend,
          's': xjx.js.sleep,
          'ino': xjx.js.includeScriptOnce,
          'in': xjx.js.includeScript,
          'rjs': xjx.js.removeScript,
          'wf': xjx.js.waitFor,
          'js': xjx.js.execute,
          'jc': xjx.js.call,
          'sf': xjx.js.setFunction,
          'wpf': xjx.js.wrapFunction,
          'al': function (args) {
              args.fullName = 'alert';
              alert(args.data);
              return true;
          },
          'cc': xjx.js.confirmCommands,
          'ci': xjx.forms.createInput,
          'ii': xjx.forms.insertInput,
          'iia': xjx.forms.insertInputAfter,
          'ev': xjx.events.setEvent,
          'ah': xjx.events.addHandler,
          'rh': xjx.events.removeHandler,
          'html': function (args) {
              try {
                  return xajax.html(args.id, args.data);
              } catch (e) {
                  // do nothing, if the debug module is installed it will
                  // catch and handle the exception
              }
              return true;
          },
          'dbg': function (args) {
              args.fullName = 'debug message';
              return true;
          }
      };
    /*
        Function: xajax.command.create
        
        Creates a new command (object) that will be populated with
        command parameters and eventually passed to the command handler.
    */
    xjx.command.create = function (sequence, request, context) {
        return {cmd: '*', fullName: '* unknown command name *', sequence: sequence, request: request, context: context};
    };
    /*
        Object: handlers
        
        An array that is used internally in the xajax.command.handler object
        to keep track of command handlers that have been registered.
    */
    /*
        Function: xajax.command.handler.register
        
        Registers a new command handler.
    */
    xjx.command.register = function (shortName, func) {
        handlers[shortName] = func;
    };
    /*
        Function: xajax.command.handler.unregister
        
        Unregisters and returns a command handler.
        
        Parameters:
            shortName - (string): The name of the command handler.
            
        Returns:
            func - (function): The unregistered function.
    */
    xjx.command.unregister = function (shortName) {
        var func = handlers[shortName];
        delete handlers[shortName];
        return func;
    };
    /*
        Function: xajax.command.handler.isRegistered
        
        
        Parameters:
            command - (object):
                - cmd: The Name of the function.
    
        Returns:
    
        boolean - (true or false): depending on whether a command handler has
        been created for the specified command (object).
        
    */
    xjx.command.isRegistered = function (command) {
        var shortName = command.cmd;
        return (handlers[shortName]);
    };
    /*
	Function: xajax.command.handler.call
	
	Calls the registered command handler for the specified command
	(you should always check isRegistered before calling this function)

	Parameters:
		command - (object):
			- cmd: The Name of the function.

	Returns:
		true - (boolean) :
*/
    xajax.command.call = function (command) {
        var shortName = command.cmd;
        return handlers[shortName](command);
    };
}(xajax));
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

function recurser() {
    
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