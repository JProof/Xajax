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