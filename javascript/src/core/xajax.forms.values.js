// xajax form values handler
(function (xjx) {
    
    /*
           Function: xajax.tools._getFormValues
           
           Used internally by <xajax.tools.getFormValues> to recursively get the value
           of form elements.  This function will extract all form element values
           regardless of the depth of the element within the form.
       */
    var _getFormValues = function (aFormValues, children, submitDisabledElements, prefix) {
        var iLen = children.length;
        for (var i = 0; i < iLen; ++i) {
            var child = children[i];
            if (('undefined' !== typeof child.childNodes) && (child.type !== 'select-one') && (child.type !== 'select-multiple'))
                _getFormValues(aFormValues, child.childNodes, submitDisabledElements, prefix);
            _getFormValue(aFormValues, child, submitDisabledElements, prefix);
        }
    };
    /*
            Function: xajax.tools._getFormValue
            
            Used internally by <xajax.tools._getFormValues> to extract a single form value.
            This will detect the type of element (radio, checkbox, multi-select) and
            add it's value(s) to the form values array.
        
            Modified version for multidimensional arrays
        */
    var _getFormValue = function (aFormValues, child, submitDisabledElements, prefix) {
        if (!child.name)
            return;
        if ('PARAM' === child.tagName) return;
        if (child.disabled)
            if (true == child.disabled)
                if (false == submitDisabledElements)
                    return;
        if (prefix !== child.name.substring(0, prefix.length))
            return;
        if (child.type)
            if (child.type === 'radio' || child.type === 'checkbox')
                if (false == child.checked)
                    return;
        var name = child.name;
        var values = [];
        if ('select-multiple' === child.type) {
            var jLen = child.length;
            for (var j = 0; j < jLen; ++j) {
                var option = child.options[j];
                if (true == option.selected)
                    values.push(option.value);
            }
        } else {
            values = child.value;
        }
        var keyBegin = name.indexOf('[');
        /* exists name/object before the Bracket?*/
        if (0 <= keyBegin) {
            var n = name;
            var k = n.substr(0, n.indexOf('['));
            var a = n.substr(n.indexOf('['));
            if (typeof aFormValues[k] === 'undefined')
                aFormValues[k] = {};
            var p = aFormValues; // pointer reset
            while (a.length !== 0) {
                var sa = a.substr(0, a.indexOf(']') + 1);
                var lk = k; //save last key
                var lp = p; //save last pointer
                a = a.substr(a.indexOf(']') + 1);
                p = p[k];
                k = sa.substr(1, sa.length - 2);
                if (k === '') {
                    if ('select-multiple' === child.type) {
                        k = lk; //restore last key
                        p = lp;
                    } else {
                        k = p.length;
                    }
                }
                if (typeof k === 'undefined') {
                    /*check against the global aFormValues Stack which is the next(last) usable index */
                    k = 0;
                    for (var i in lp[lk]) k++;
                }
                if (typeof p[k] === 'undefined') {
                    p[k] = {};
                }
            }
            p[k] = values;
        } else {
            aFormValues[name] = values;
        }
    };
    /*
	Function: xajax.tools.getFormValues
	
	Build an associative array of form elements and their values from
	the specified form.
	
	Parameters:
	
	element - (string): The unique name (id) of the form to be processed.
	disabled - (boolean, optional): Include form elements which are currently disabled.
	prefix - (string, optional): A prefix used for selecting form elements.

	Returns:
	
	An associative array of form element id and value.
*/
    xjx.forms = {
        getFormValues: function (parent) {
            var submitDisabledElements = false;
            if (arguments.length > 1 && arguments[1] == true)
                submitDisabledElements = true;
            var prefix = '';
            if (arguments.length > 2)
                prefix = arguments[2];
            // todo check parent is type?!
            if ('string' === typeof parent)
                parent = xjx.$(parent);
            var aFormValues = {};
//		JW: Removing these tests so that form values can be retrieved from a specified
//		container element like a DIV, regardless of whether they exist in a form or not.
//
//		if (parent.tagName)
//			if ('FORM' == parent.tagName.toUpperCase())
            if (parent)
                if (parent.childNodes)
                    _getFormValues(aFormValues, parent.childNodes, submitDisabledElements, prefix);
            return aFormValues;
        }
    };
}(xajax));