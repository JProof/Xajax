/*  browser */
/*  global window */
/**
 * Cross-browser function to add an event listener.
 * @param {!HTMLElement} elem  The DOM element to attach event to.
 * @param {string} eventName  The name of the event.
 * @param {!Function} fnHandler  The function to server as one of the specified
 *     event handlers.
 * @return {Function}  Returns the function that was added as an event handler.
 */


(function (xjx) {
    'use strict';
    var readyList = [];
    var readyFired = false;
    var readyEventHandlersInstalled = false;
    var browserEvents = ['click'];
    // call this when the document is ready
    // this function protects itself against being called more than once
    function ready() {
        if (!readyFired) {
            // this must be set to true before we start calling callbacks
            readyFired = true;
            // if a callback here happens to add new ready handlers,
            // the docReady() function will see that it already fired
            // and will schedule the callback to run right after
            // this event loop finishes so all handlers will still execute
            // in order and no new ones will be added to the readyList
            // while we are processing the list
            readyList.forEach(function (item) {
                item.fn.call(window, item.ctx);
            });
            // allow any closures held by these functions to free
            readyList = [];
        }
    }
    function readyStateChange() {
        if (document.readyState === 'complete') {
            ready();
        }
    }
    xjx.on = function (evtName) {
        return true;
    };
    // This is the one public interface
    // docReady(fn, context);
    // the context argument is optional - if present, it will be passed
    // as an argument to the callback
    xjx.onDocReady = function (callback, context) {
        if (typeof callback !== 'function') {
            throw new TypeError('callback for docReady(fn) must be a function');
        }
        // if ready has already fired, then just schedule the callback
        // to fire asynchronously, but right away
        if (readyFired) {
            setTimeout(function () {
                callback(context);
            }, 1);
            return;
        }
        // add the function and context to the list
        readyList.push({fn: callback, ctx: context});
        // if document already ready to go, schedule the ready function to run
        // IE only safe when readyState is "complete", others safe when readyState is "interactive"
        if ('complete' === document.readyState || (!document.attachEvent && 'interactive' === document.readyState)) {
            setTimeout(ready, 1);
        } else if (!readyEventHandlersInstalled) {
            // otherwise if we don't have event handlers installed, install them
            if (document.addEventListener) {
                // first choice is DOMContentLoaded event
                document.addEventListener('DOMContentLoaded', ready, false);
                // backup is window load event
                window.addEventListener('load', ready, false);
            } else {
                // must be IE
                document.attachEvent('onreadystatechange', readyStateChange);
                window.attachEvent('onload', ready);
            }
            readyEventHandlersInstalled = true;
        }
    };
}(xajax, window));
// modify this previous line to pass in your own method name
// and object for the method to be attached to