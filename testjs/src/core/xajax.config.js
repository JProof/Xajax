/** Core Configuration Module **/
(function (xjx) {
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
    'use strict';
    xjx.config = {};
    var options = [];
    xjx.config.getOption = function (optName) {
        return options.hasOwnProperty(optName) ? options[optName] : null;
    };
    xjx.config.setOption = function (key, value) {
        options[key] = value;
    };
    xjx.config.setOptions = function (arr) {
        for (var obj in arr) {
            xjx.config.setOption(obj, arr[obj]);
        }
    };
    xjx.config.defaults = {
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
        'baseDocument': document,
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
    xjx.config.setOptions(xjx.config.defaults);
}(xajax));
console.log(xajax);