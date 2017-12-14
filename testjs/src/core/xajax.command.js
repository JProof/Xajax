/*
	Class: xajax.command
	
	The object that manages commands and command handlers.
*/
(function (xjx) {
    if ('undefined' === typeof xjx.command)
        xjx.command = {};
    /*
        Function: xajax.command.create
        
        Creates a new command (object) that will be populated with
        command parameters and eventually passed to the command handler.
    */
    xjx.command.create = function (sequence, request, context) {
        var newCmd = {};
        newCmd.cmd = '*';
        newCmd.fullName = '* unknown command name *';
        newCmd.sequence = sequence;
        newCmd.request = request;
        newCmd.context = context;
        return newCmd;
    };
    /*
        Class: xajax.command.handler
        
        The object that manages command handlers.
    */
    if ('undefined' === typeof xjx.command.handler)
        xjx.command.handler = {};
    /*
        Object: handlers
        
        An array that is used internally in the xajax.command.handler object
        to keep track of command handlers that have been registered.
    */
    if ('undefined' === typeof xjx.command.handler.handlers)
        xjx.command.handler.handlers = [];
    /*
        Function: xajax.command.handler.register
        
        Registers a new command handler.
    */
    xjx.command.handler.register = function (shortName, func) {
        xjx.command.handler.handlers[shortName] = func;
    };
    /*
        Function: xajax.command.handler.unregister
        
        Unregisters and returns a command handler.
        
        Parameters:
            shortName - (string): The name of the command handler.
            
        Returns:
            func - (function): The unregistered function.
    */
    xjx.command.handler.unregister = function (shortName) {
        var func = xjx.command.handler.handlers[shortName];
        delete xjx.command.handler.handlers[shortName];
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
    xjx.command.handler.isRegistered = function (command) {
        var shortName = command.cmd;
        return (xjx.command.handler.handlers[shortName]);
    };
}(xajax));