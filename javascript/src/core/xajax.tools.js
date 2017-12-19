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
     * @since 0.7.3
     *
     * **/
    xjx.getContext = function (con) {
        if (con) return con;
        if ('object' === typeof (con = xjx.config('baseDocument'))) return con;
        return window.document;
    };
    /**
     * Xajax Document Tools
     * */
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
        $: function (sId, context) {
            // nothing
            if ('undefined' === typeof sId) { return null; }
            // is already node
            if (xjx.isElement(sId)) { return sId;}
            var baseDoc = xjx.getContext(context);
            if ('object' === typeof sId && undefined !== sId.id) {
                return baseDoc.getElementById(sId.id);
            }
            //sId not an string so return it maybe its an object.
            if (!xjx.isStr(sId)) {
                return null;
            }
            return baseDoc.getElementById(sId);
        },
        /**
         * Searching by something
         * **/
        $$: function (eleS, context) {
            var ele;
            if (ele = xjx.tools.$(eleS, context)) return ele;
        }
    };
}(xajax));