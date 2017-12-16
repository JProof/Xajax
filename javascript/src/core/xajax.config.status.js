/*
    Class: xajax.config.status
    
    Provides support for updating the browser's status bar during
    the request process.  By splitting the status bar functionality
    into an object, the xajax developer has the opportunity to
    customize the status bar messages prior to sending xajax requests.
*/
(function (xjx) {
    xjx.config.status = {
        /*
            Function: update
            
            Constructs and returns a set of event handlers that will be
            called by the xajax framework to set the status bar messages.
        */
        update: function () {
            return {
                onRequest: function () {
                    window.status = 'Sending Request...';
                },
                onWaiting: function () {
                    window.status = 'Waiting for Response...';
                },
                onProcessing: function () {
                    window.status = 'Processing...';
                },
                onComplete: function () {
                    window.status = 'Done.';
                }
            };
        },
        /*
            Function: dontUpdate
            
            Constructs and returns a set of event handlers that will be
            called by the xajax framework where status bar updates
            would normally occur.
        */
        dontUpdate: function () {
            return {
                onRequest: function () {
                },
                onWaiting: function () {
                },
                onProcessing: function () {
                },
                onComplete: function () {
                }
            };
        }
    };
}(xajax));