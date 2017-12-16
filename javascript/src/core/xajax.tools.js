/*
  Class: xajax.tools
  
  This contains utility functions which are used throughout
  the xajax core.
*/
(function ($xa) {
    'use strict';
    // checks the given element s an HTML Element
    $xa.isElement = function (element) {
        // works on major browsers back to IE7
        return element instanceof Element;
    };
    $xa.tools = {
        /*
            Function: xajax.tools.$
        
            Shorthand for finding a uniquely named element within
            the document.
        
            Parameters:
            sId - (string):
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
                @deprecated an other method such as in jquery is need
        */
        $: function (sId) {
            if (!sId) { return null; }
            if ($xa.isElement(sId)) { return sId;}
            var obj;
            var oDoc = $xa.config('baseDocument');//xajax.config.baseDocument;
            if ('object' === typeof sId) {
                if (undefined !== sId.id) {
                    obj = oDoc.getElementById(sId.id);
                    if (obj)
                        return obj;
                }
            }
            //sId not an string so return it maybe its an object.
            if (!$xa.isStr(sId)) {
                return sId;
            }
            obj = oDoc.getElementById(sId);
            if (obj)
                return obj;
            if (oDoc.all)
                return oDoc.all[sId];
            return null;
        }
    }
    ;
}(xajax));