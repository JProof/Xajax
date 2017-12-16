/*
	Class: xajax.dom
*/
(function (xjx) {
    xjx.dom = {
        /*
            Function: xajax.dom.assign
            
            Assign an element's attribute to the specified value.
            
            Parameters:
            
            element - (object):  The HTML element to effect.
            property - (string):  The name of the attribute to set.
            data - (string):  The new value to be applied.
            
            Returns:
            
            true - The operation completed successfully.
        */
        assign: function (element, property, data) {
            element = xjx.$(element);
            switch (property) {
                case 'innerHTML':
                    element.innerHTML = data;
                    break;
                case 'outerHTML':
                    if ('undefined' === typeof element.outerHTML) {
                        var r = xajax.config.baseDocument.createRange();
                        r.setStartBefore(element);
                        var df = r.createContextualFragment(data);
                        element.parentNode.replaceChild(df, element);
                    } else element.outerHTML = data;
                    break;
                default:
                    if (xajax.tools.willChange(element, property, data))
                        eval('element.' + property + ' = data;');
                    break;
            }
            return true;
        },
        /*
            Function: xajax.dom.append
            
            Append the specified value to an element's attribute.
            
            Parameters:
            
            element - (object):  The HTML element to effect.
            property - (string):  The name of the attribute to append to.
            data - (string):  The new value to be appended.
            
            Returns:
            
            true - The operation completed successfully.
        */
        append: function (element, property, data) {
            if ('string' === typeof element)
                element = xajax.$(element);
            eval('element.' + property + ' += data;');
            return true;
        },
        /*
            Function: xajax.dom.prepend
            
            Prepend the specified value to an element's attribute.
            
            Parameters:
            
            element - (object):  The HTML element to effect.
            property - (string):  The name of the attribute.
            data - (string):  The new value to be prepended.
            
            Returns:
            
            true - The operation completed successfully.
        */
        prepend: function (element, property, data) {
            if ('string' === typeof element)
                element = xajax.$(element);
            eval('element.' + property + ' = data + element.' + property);
            return true;
        },
        /*
            Function: xajax.dom.replace
            
            Search and replace the specified text.
            
            Parameters:
            
            element - (string or object):  The name of, or the element itself which is
                to be modified.
            sAttribute - (string):  The name of the attribute to be set.
            aData - (array):  The search text and replacement text.
            
            Returns:
            
            true - The operation completed successfully.
        */
        replace: function (element, sAttribute, aData) {
            var sSearch = aData['s'];
            var sReplace = aData['r'];
            if (sAttribute === 'innerHTML')
                sSearch = xajax.tools.getBrowserHTML(sSearch);
            if ('string' === typeof element)
                element = xajax.$(element);
            eval('var txt = element.' + sAttribute);
            var bFunction = false;
            if ('function' === typeof txt) {
                txt = txt.join('');
                bFunction = true;
            }
            var start = txt.indexOf(sSearch);
            if (start > -1) {
                var newTxt = [];
                while (start > -1) {
                    var end = start + sSearch.length;
                    newTxt.push(txt.substr(0, start));
                    newTxt.push(sReplace);
                    txt = txt.substr(end, txt.length - end);
                    start = txt.indexOf(sSearch);
                }
                newTxt.push(txt);
                newTxt = newTxt.join('');
                if (bFunction) {
                    eval('element.' + sAttribute + '=newTxt;');
                } else if (xajax.tools.willChange(element, sAttribute, newTxt)) {
                    eval('element.' + sAttribute + '=newTxt;');
                }
            }
            return true;
        },
        /*
            Function: xajax.dom.remove
            
            Delete an element.
            
            Parameters:
            
            element - (string or object):  The name of, or the element itself which
                will be deleted.
                
            Returns:
            
            true - The operation completed successfully.
        */
        remove: function (element) {
            if ('string' === typeof element)
                element = xajax.$(element);
            if (element && element.parentNode && element.parentNode.removeChild)
                element.parentNode.removeChild(element);
            return true;
        },
        /*
            Function: xajax.dom.create
            
            Create a new element and append it to the specified parent element.
            
            Parameters:
            
            objParent - (string or object):  The name of, or the element itself
                which will contain the new element.
            sTag - (string):  The tag name for the new element.
            sId - (string):  The value to be assigned to the id attribute of
                the new element.
                
            Returns:
            
            true - The operation completed successfully.
        */
        create: function (objParent, sTag, sId) {
            if ('string' === typeof objParent)
                objParent = xajax.$(objParent);
            var target = xajax.config.baseDocument.createElement(sTag);
            target.setAttribute('id', sId);
            if (objParent)
                objParent.appendChild(target);
            return true;
        },
        /*
            Function: xajax.dom.insert
            
            Insert a new element before the specified element.
        
            Parameters:
            
            objSibling - (string or object):  The name of, or the element itself
                that will be used as the reference point for insertion.
            sTag - (string):  The tag name for the new element.
            sId - (string):  The value that will be assigned to the new element's
                id attribute.
            
            Returns:
            
            true - The operation completed successfully.
        */
        insert: function (objSibling, sTag, sId) {
            if ('string' === typeof objSibling)
                objSibling = xajax.$(objSibling);
            var target = xajax.config.baseDocument.createElement(sTag);
            target.setAttribute('id', sId);
            objSibling.parentNode.insertBefore(target, objSibling);
            return true;
        },
        /*
            Function: xajax.dom.insertAfter
            
            Insert a new element after the specified element.
        
            Parameters:
            
            objSibling - (string or object):  The name of, or the element itself
                that will be used as the reference point for insertion.
            sTag - (string):  The tag name for the new element.
            sId - (string):  The value that will be assigned to the new element's
                id attribute.
            
            Returns:
            
            true - The operation completed successfully.
        */
        insertAfter: function (objSibling, sTag, sId) {
            if ('string' === typeof objSibling)
                objSibling = xajax.$(objSibling);
            var target = xajax.config.baseDocument.createElement(sTag);
            target.setAttribute('id', sId);
            objSibling.parentNode.insertBefore(target, objSibling.nextSibling);
            return true;
        },
        /*
            Function: xajax.dom.contextAssign
            
            Assign a value to a named member of the current script context object.
            
            Parameters:
            
            args - (object):  The response command object which will contain the
                following:
                
                - args.prop: (string):  The name of the member to assign.
                - args.data: (string or object):  The value to assign to the member.
                - args.context: (object):  The current script context object which
                    is accessible via the 'this' keyword.
            
            Returns:
            
            true - The operation completed successfully.
        */
        contextAssign: function (args) {
            args.fullName = 'context assign';
            var code = [];
            code.push('this.');
            code.push(args.prop);
            code.push(' = data;');
            code = code.join('');
            args.context.xajaxDelegateCall = function (data) {
                eval(code);
            };
            args.context.xajaxDelegateCall(args.data);
            return true;
        },
        /*
            Function: xajax.dom.contextAppend
            
            Appends a value to a named member of the current script context object.
            
            Parameters:
            
            args - (object):  The response command object which will contain the
                following:
                
                - args.prop: (string):  The name of the member to append to.
                - args.data: (string or object):  The value to append to the member.
                - args.context: (object):  The current script context object which
                    is accessible via the 'this' keyword.
            
            Returns:
            
            true - The operation completed successfully.
        */
        contextAppend: function (args) {
            args.fullName = 'context append';
            var code = [];
            code.push('this.');
            code.push(args.prop);
            code.push(' += data;');
            code = code.join('');
            args.context.xajaxDelegateCall = function (data) {
                eval(code);
            };
            args.context.xajaxDelegateCall(args.data);
            return true;
        },
        /*
            Function: xajax.dom.contextPrepend
            
            Prepend a value to a named member of the current script context object.
            
            Parameters:
            
            args - (object):  The response command object which will contain the
                following:
                
                - args.prop: (string):  The name of the member to prepend to.
                - args.data: (string or object):  The value to prepend to the member.
                - args.context: (object):  The current script context object which
                    is accessible via the 'this' keyword.
            
            Returns:
            
            true - The operation completed successfully.
        */
        contextPrepend: function (args) {
            args.fullName = 'context prepend';
            var code = [];
            code.push('this.');
            code.push(args.prop);
            code.push(' = data + this.');
            code.push(args.prop);
            code.push(';');
            code = code.join('');
            args.context.xajaxDelegateCall = function (data) {
                eval(code);
            };
            args.context.xajaxDelegateCall(args.data);
            return true;
        }
    };
}(xajax));