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
    /**
     * Try to readout the field Attribute name
     *
     * @property Element nEle
     * @return string | null
     * */
    var getFieldName = function (nEle) {
        var sName;
        if (null !== (nEle = xjx.tools.$(nEle)))
            if (null !== (sName = nEle.getAttribute('name'))) {
                return sName;
            }
        return null;
    };
    
    /**
     * Extract the FormField Name from string
     *
     * @property sName string
     * @return object
     * **/
    var extractFieldName = function (sName) {
        if (xjx.isStr(sName)) {
            // todo check against valid name (is not allowed to give back bracket)
            if (sName.indexOf('[') === -1 || sName.indexOf(']') === -1) return new Array(sName.trim());
            
            var parts = sName.split(/[[\]]{1,2}/);
            parts.length--;
            var ret = [];
            parts.forEach(function (value) {
                ret.push(value.trim());
            });
            return ret;
        }
    };
    /*
    * Method to create an Object from skeleton form field name
    *
    * @property {string} aLst
    * @property {string|int|float|array} val
    *
    * @return object
    * **/
    var listAsObject = function (aLst, val) {
        // if no value we do not have to transport it@todo check html specs
        if ('undefined' === typeof val || null === val) return null;
        
        var tmpObject = null, p = null, oldP = null;
        
        var lng = aLst.length;
        for (lng; lng > 0; --lng) {
            oldP = p;
            p = {};
            p[aLst[lng - 1]] = (null === oldP) ? val : oldP;
            tmpObject = p;
        }
        return tmpObject;
    };
    var merge = function (obj1, obj2) {
        
        for (var p in obj2) {
            if (obj2.hasOwnProperty(p)) {
                if ('object' === typeof obj1[p]) {
                    // recursive merge
                    if ('object' === typeof obj2[p])
                        obj1[p] = merge(obj1[p], obj2[p]);
                    else
                    // obj2 is not deeper
                        obj1[p] = obj2[p];
                } else {
                    if ('function' === typeof obj1.push)
                    // numbered like checkboxes
                        obj1.push(obj2[p]);
                    else
                    // regular push
                        obj1[p] = obj2[p];
                }
            }
        }
        
        return obj1;
    };
    
    /**
     * Function: xajax.tools._getFormValue
     *
     * Used internally by <xajax.tools._getFormValues> to extract a single form value.
     * This will detect the type of element (radio, checkbox, multi-select) and
     * add it's value(s) to the form values array.
     *
     * Modified version for multidimensional arrays
     **/
    var _getFormValue = function (aFormValues, child, submitDisabledElements, prefix) {
        if (!child.name)
            return;
        // todo check whats with param
        if ('PARAM' === child.tagName) return;
        
        // getting the html name-Attribute
        var sFieldName = getFieldName(child);
        if (null === sFieldName) return null;
        
        // check against disabled
        if (child.disabled)
            if (true === child.disabled)
                if (false === submitDisabledElements)
                    return;
        
        if (prefix !== child.name.substring(0, prefix.length))
            return;
        var cT = child.type;
        if (cT) {
            // kick down on null value
            if (cT === 'radio' || cT === 'checkbox') {
                if (!child.checked)
                    return;
            }
            
            if (cT === 'select-one') {
                if (child.selectedIndex)
                    values = child.options[child.selectedIndex].value;
                else
                // nothing selected
                    return;
            }
            
            else if ('select-multiple' === child.type) {
                var values = [];
                var jLen = child.length;
                for (var j = 0; j < jLen; ++j) {
                    var option = child.options[j];
                    if (option.selected)
                        values.push(option.value);
                }
            } else {
                values = child.value;
            }
        }
        // new Method
        var fieldParts = extractFieldName(child.name);
        var field = listAsObject(fieldParts, values);
        
        return merge(aFormValues, field);
        
    };
    
    /**
     * Function: xajax.tools.getFormValues
     *
     * Build an associative array of form elements and their values from
     * the specified form.
     *
     * Parameters:
     *
     * element - (string): The unique name (id) of the form to be processed.
     * disabled - (boolean, optional): Include form elements which are currently disabled.
     * prefix - (string, optional): A prefix used for selecting form elements.
     *
     * @return null|object  Null on not found Parent form  An associative array of form element id and value.
     */
    
    var getFormValues = function (parent) {
        
        if (null === (parent = xjx.tools.$(parent))) return null;
        
        var submitDisabledElements = false;
        if (arguments.length > 1 && arguments[1] === true)
            submitDisabledElements = true;
        var prefix = '';
        if (arguments.length > 2)
            prefix = arguments[2];
        parent = xjx.$(parent);
        var aFormValues = {};
//		JW: Removing these tests so that form values can be retrieved from a specified
//		container element like a DIV, regardless of whether they exist in a form or not.
//
        if (parent)
            if (parent.childNodes)
                _getFormValues(aFormValues, parent.childNodes, submitDisabledElements, prefix);
        return aFormValues;
    };
    // old Hook
    
    xjx.forms = {
        getFormValues: getFormValues,
        // currently for unitTesting
        valueHandler: {merge: merge, extractFieldName: extractFieldName, listAsObject: listAsObject}
    };
    
}(xajax));