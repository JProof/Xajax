/*
	Class: xajax.forms
*/
(function (xjx) {
    xjx.forms = {
        /*
            Function: xajax.forms.getInput
            
            Create and return a form input element with the specified parameters.
            
            Parameters:
            
            type - (string):  The type of input element desired.
            name - (string):  The value to be assigned to the name attribute.
            id - (string):  The value to be assigned to the id attribute.
            
            Returns:
            
            object - The new input element.
        */
        getInput: function (type, name, id) {
            if ('undefined' === typeof window.addEventListener) {
                xajax.forms.getInput = function (type, name, id) {
                    return xajax.config.baseDocument.createElement('<input type="' + type + '" name="' + name + '" id="' + id + '">');
                };
            } else {
                xajax.forms.getInput = function (type, name, id) {
                    var oDoc = xajax.config.baseDocument;
                    var Obj = oDoc.createElement('input');
                    Obj.setAttribute('type', type);
                    Obj.setAttribute('name', name);
                    Obj.setAttribute('id', id);
                    return Obj;
                };
            }
            return xajax.forms.getInput(type, name, id);
        },
        /*
            Function: xajax.forms.createInput
            
            Create a new input element under the specified parent.
            
            Parameters:
            
            objParent - (string or object):  The name of, or the element itself
                that will be used as the reference for the insertion.
            sType - (string):  The value to be assigned to the type attribute.
            sName - (string):  The value to be assigned to the name attribute.
            sId - (string):  The value to be assigned to the id attribute.
            
            Returns:
            
            true - The operation completed successfully.
        */
        createInput: function (command) {
            command.fullName = 'createInput';
            var objParent = command.id;
            var sType = command.type;
            var sName = command.data;
            var sId = command.prop;
            if ('string' === typeof objParent)
                objParent = xajax.$(objParent);
            var target = xajax.forms.getInput(sType, sName, sId);
            if (objParent && target) {
                objParent.appendChild(target);
            }
            return true;
        },
        /*
            Function: xajax.forms.insertInput
            
            Insert a new input element before the specified element.
            
            Parameters:
            
            objSibling - (string or object):  The name of, or the element itself
                that will be used as the reference for the insertion.
            sType - (string):  The value to be assigned to the type attribute.
            sName - (string):  The value to be assigned to the name attribute.
            sId - (string):  The value to be assigned to the id attribute.
            
            Returns:
            
            true - The operation completed successfully.
        */
        insertInput: function (command) {
            command.fullName = 'insertInput';
            var objSibling = command.id;
            var sType = command.type;
            var sName = command.data;
            var sId = command.prop;
            if ('string' === typeof objSibling)
                objSibling = xajax.$(objSibling);
            var target = xajax.forms.getInput(sType, sName, sId);
            if (target && objSibling && objSibling.parentNode)
                objSibling.parentNode.insertBefore(target, objSibling);
            return true;
        },
        /*
            Function: xajax.forms.insertInputAfter
        
            Insert a new input element after the specified element.
            
            Parameters:
            
            objSibling - (string or object):  The name of, or the element itself
                that will be used as the reference for the insertion.
            sType - (string):  The value to be assigned to the type attribute.
            sName - (string):  The value to be assigned to the name attribute.
            sId - (string):  The value to be assigned to the id attribute.
            
            Returns:
            
            true - The operation completed successfully.
        */
        insertInputAfter: function (command) {
            command.fullName = 'insertInputAfter';
            var objSibling = command.id;
            var sType = command.type;
            var sName = command.data;
            var sId = command.prop;
            if ('string' === typeof objSibling)
                objSibling = xajax.$(objSibling);
            var target = xajax.forms.getInput(sType, sName, sId);
            if (target && objSibling && objSibling.parentNode)
                objSibling.parentNode.insertBefore(target, objSibling.nextSibling);
            return true;
        }
    };
}(xajax));