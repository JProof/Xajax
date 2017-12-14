/** xajax attr **/
(function (xjx) {
    xjx.attr = {
        that: this,
        has: function (ele) {
            var hasAttrib = false;
            try {
                hasAttrib = xajax.tools.$(xajax.get(ele, 'id')).hasAttribute(xajax.get(ele, 'prop'));
            } catch (error) {
                throw error;
            }
            return hasAttrib;
        },
        /**
         * ele={'id',prop,data[value]};
         * ***/
        add: function (ele) {
            if (!xajax.attr.has(ele)) {
                var elem = xajax.tools.$(xajax.get(ele, 'id'));
                try {
                    var data = xajax.get(ele, 'data');
                    var value = xajax.get(data, 'value');
                    value = (typeof value === 'string') ? value : '';
                    elem.setAttribute(xajax.get(ele, 'prop'), value);
                } catch (error) {
                    throw error;
                }
                return true;
            }
        }, /**
         * ele={'id',prop};
         */
        remove: function (ele) {
            if (xajax.attr.has(ele)) {
                var elem = xajax.tools.$(xajax.get(ele, 'id'));
                try {
                    elem.removeAttribute(xajax.get(ele, 'prop'));
                } catch (error) {
                    throw error;
                }
                return true;
            }
        },
        /**
         * ele={'id',prop,data[value,new]};
         * ***/
        replace: function (ele) {
            if (xajax.attr.has(ele)) {
                xajax.attr.remove(ele);
            }
            var data = xajax.get(ele, 'data');
            ele.prop = xajax.get(data, 'new');
            return xajax.attr.add(ele);
        }
    };
}(xajax));