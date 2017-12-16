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
            var oDoc = xajax.config.baseDocument;
            var loadedScripts = oDoc.getElementsByTagName('script');
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
        includeScript: function (command) {
            command.fullName = 'includeScript';
            var oDoc = xajax.config.baseDocument;
            var objHead = oDoc.getElementsByTagName('head');
            var objScript = oDoc.createElement('script');
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
        removeScript: function (command) {
            command.fullName = 'removeScript';
            var fileName = command.data;
            var unload = command.unld;
            var oDoc = xajax.config.baseDocument;
            var loadedScripts = oDoc.getElementsByTagName('script');
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
            if (xajax.tools.queue.retry(command, command.prop)) {
                xajax.tools.queue.setWakeup(xajax.response, 100);
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
                    xajax.tools.queue.pop(xajax.response);
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
                if (xajax.tools.queue.retry(args, args.prop)) {
                    xajax.tools.queue.setWakeup(xajax.response, 100);
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