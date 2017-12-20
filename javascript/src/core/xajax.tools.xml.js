(function (xjx) {
    
    /*
        Class: xajax.tools.xml
        
        An object that contains utility function for processing
        xml response packets.
    */
    xjx.tools.xml = {};
    /*
        Function: xajax.tools.xml.parseAttributes
        
        Take the parameters passed in the command of the XML response
        and convert them to parameters of the args object.  This will
        serve as the command object which will be stored in the
        response command queue.
        
        Parameters:
        
        child - (object):  The xml child node which contains the
            attributes for the current response command.
            
        obj - (object):  The current response command that will have the
            attributes applied.
    */
    xjx.tools.xml.parseAttributes = function (child, obj) {
        var iLen = child.attributes.length;
        for (var i = 0; i < iLen; ++i) {
            var attr = child.attributes[i];
            obj[attr.name] = attr.value;
        }
    };
    /*
        Function: xajax.tools.xml.parseChildren
        
        Parses the child nodes of the command of the response XML.  Generally,
        the child nodes contain the data element of the command; this member
        may be an object, which will be deserialized by <xajax._nodeToObject>
        
        Parameters:
        
        child - (object):   The xml node that contains the child (data) for
            the current response command object.
            
        obj - (object):  The response command object.
    */
    xjx.tools.xml.parseChildren = function (child, obj) {
        obj.data = '';
        if (0 < child.childNodes.length) {
            if (1 < child.childNodes.length) {
                var grandChild = child.firstChild;
                do {
                    if ('#cdata-section' == grandChild.nodeName || '#text' == grandChild.nodeName) {
                        obj.data += grandChild.data;
                    }
                } while (grandChild = grandChild.nextSibling);
            } else {
                var grandChild = child.firstChild;
                if ('xjxobj' == grandChild.nodeName) {
                    obj.data = xajax.tools._nodeToObject(grandChild);
                    return;
                } else if ('#cdata-section' == grandChild.nodeName || '#text' == grandChild.nodeName) {
                    obj.data = grandChild.data;
                }
            }
        } else if ('undefined' != typeof child.data) {
            obj.data = child.data;
        }
        obj.data = xajax.tools._enforceDataType(obj.data);
    };
    /*
        Function: xajax.tools.xml.processFragment
        
        Parameters:
        
        xmlNode - (object):  The first xml node in the xml fragment.
        seq - (number):  A counter used to keep track of the sequence
            of this command in the response.
        oRet - (object):  A variable that is used to return the request
            "return value" for use with synchronous requests.
    */
    xjx.tools.xml.processFragment = function (xmlNode, seq, oRet, oRequest) {
        var xx = xajax;
        var xt = xx.tools;
        while (xmlNode) {
            if ('cmd' == xmlNode.nodeName) {
                var obj = {};
                obj.fullName = '*unknown*';
                obj.sequence = seq;
                obj.request = oRequest;
                obj.context = oRequest.context;
                xt.xml.parseAttributes(xmlNode, obj);
                xt.xml.parseChildren(xmlNode, obj);
                xjx.queue.push(xx.response, obj);
            } else if ('xjxrv' == xmlNode.nodeName) {
                oRet = xt._nodeToObject(xmlNode.firstChild);
            } else if ('debugmsg' == xmlNode.nodeName) {
                // txt = xt._nodeToObject(xmlNode.firstChild);
            } else
                throw {code: 10004, data: xmlNode.nodeName};
            ++seq;
            xmlNode = xmlNode.nextSibling;
        }
        return oRet;
    };
}(xajax));