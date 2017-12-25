/*
	Function: xajax.tools._escape
	
	Determine if the specified value contains special characters and
	create a CDATA section so the value can be safely transmitted.
	
	Parameters:
	
	data - (string or other):
		The source string value to be evaluated or an object of unknown
		type.
		
	Returns:
	
	string - The string value, escaped if necessary or the object provided
		if it is not a string.
		
	Note:
		When the specified object is NOT a string, the value is returned
		as is.
*/
/*
xajax.tools._escape = function(data) {
	if ('undefined' == typeof data)
		return data;
	// 'object' is handled elsewhere,
	// 'string' handled below,
	// 'number' will be returned here
	// 'boolean' will be returned here
	if ('string' != typeof data)
		return data;
	
	var needCDATA = false;
	
	if (encodeURIComponent(data) != data) {
		needCDATA = true;
		
		var segments = data.split('<![CDATA[');
		var segLen = segments.length;
		data = [];
		for (var i = 0; i < segLen; ++i) {
			var segment = segments[i];
			var fragments = segment.split(']]>');
			var fragLen = fragments.length;
			segment = '';
			for (var j = 0; j < fragLen; ++j) {
				if (0 != j)
					segment += ']]]]><![CDATA[>';
				segment += fragments[j];
			}
			if (0 != i)
				data.push('<![]]><![CDATA[CDATA[');
			data.push(segment);
		}
		data = data.join('');
	}
	
	if (needCDATA)
		data = '<![CDATA[' + data + ']]>';
	
	return data;
}
*/
/*
	Function: xajax.tools._objectToXML
	
	Convert a javascript object or array into XML suitable for
	transmission to the server.
	
	Parameters:
	
	obj - The object or array to convert.
	
	guard - An object used to track the level of recursion
		when encoding javascript objects.  When an object
		contains a reference to it's parent and the parent
		contains a reference to the child, an infinite
		recursion will cause some browsers to crash.
		
	Returns:
	
	string - the xml representation of the object or array.
	
	See also:
	
	<xajax.config.maxObjectDepth> and <xajax.config.maxObjectSize>
*/
/*
xajax.tools._objectToXML = function(obj, guard) {
	var aXml = [];
	aXml.push('<xjxobj>');
	for (var key in obj) {
		++guard.size;
		if (guard.maxSize < guard.size)
			return aXml.join('');
		if ('undefined' != typeof obj[key]) {
			if ('constructor' == key)
				continue;
			if ('function' == typeof obj[key])
				continue;
			aXml.push('<e><k>');
			var val = xajax.tools._escape(key);
			aXml.push(val);
			aXml.push('</k><v>');
			if ('object' == typeof obj[key]) {
				++guard.depth;
				if (guard.maxDepth > guard.depth) {
					try {
						aXml.push(xajax.tools._objectToXML(obj[key], guard));
					} catch (e) {
						// do nothing, if the debug module is installed
						// it will catch the exception and handle it
					}
				}
				--guard.depth;
			} else {
				var val = xajax.tools._escape(obj[key]);
				if ('undefined' == typeof val || null == val) {
					aXml.push('*');
				} else {
					var sType = typeof val;
					if ('string' == sType)
						aXml.push('S');
					else if ('boolean' == sType)
						aXml.push('B');
					else if ('number' == sType)
						aXml.push('N');
					aXml.push(val);
				}
			}
			
			aXml.push('</v></e>');
		}
	}
	aXml.push('</xjxobj>');
	
	return aXml.join('');
}
*/
/*
	Function: xajax.tools._enforceDataType
	
	Ensure that the javascript variable created is of the correct data type.
	
	Parameters:
		value (string)

	Returns:
		
		(unknown) - The value provided converted to the correct data type.
*/
xajax.tools._enforceDataType = function (value) {
    value = String(value);
    var type = value.substr(0, 1);
    value = value.substr(1);
    if ('*' == type)
        value = null;
    else if ('N' == type)
        value = value - 0;
    else if ('B' == type)
        value = !!value;
//	else if ('S' == type)
//		value = new String(value);
    return value;
};
/*
	Function: xajax.tools._nodeToObject
	
	Deserialize a javascript object from an XML node.
	
	Parameters:
	
	node - A node, likely from the xml returned by the server.
	
	Returns:
	
		object - The object extracted from the xml node.
*/
xajax.tools._nodeToObject = function (node) {
    if (null == node)
        return '';
    if ('undefined' !== typeof node.nodeName) {
        var data;
        if ('#cdata-section' === node.nodeName || '#text' === node.nodeName) {
            data = '';
            do if (node.data) data += node.data; while (node = node.nextSibling);
            return xajax.tools._enforceDataType(data);
        } else if ('xjxobj' === node.nodeName) {
            var key = null;
            var value = null;
            data = [];
            var child = node.firstChild;
            while (child) {
                if ('e' === child.nodeName) {
                    var grandChild = child.firstChild;
                    while (grandChild) {
                        if ('k' === grandChild.nodeName) {
                            // Don't support objects here, only number, string, etc...
                            key = xajax.tools._enforceDataType(grandChild.firstChild.data);
                        }
                        else if ('v' === grandChild.nodeName) {
                            // Value might be object, string, number, boolean... even null or undefined
                            value = xajax.tools._nodeToObject(grandChild.firstChild);
                        }
                        grandChild = grandChild.nextSibling;
                    }
                    // Allow the value to be null or undefined (or a value)
                    if (null != key) { // && null != value) {
                        data[key] = value;
                        key = value = null;
                    }
                }
                child = child.nextSibling;
            }
            return data;
        }
    }
    throw {code: 10001, data: node.nodeName};
};