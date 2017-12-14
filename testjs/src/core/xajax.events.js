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