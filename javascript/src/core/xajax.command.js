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