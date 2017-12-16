/** xajax attr **/
(function (xjx) {
    /**
     * internal proxy to remove the old xajax.tools.$ class
     * */
    var getEle = function (elem) {
        return xjx.tools.$(elem);
    };
    /**
     * Removes an class from element
     *
     * @property ele element or element-id
     * @property val value to removes from
     */
    xjx.removeClass = function (elem, val) {
        var ident = 'class';
        if (xjx.isAttribValue(val) && true === xjx.attr.has(ident)) {
            try {
                var nS = xjx.attr.removeSpaces(elem.getAttribute(ident).replace(val, ''));
                elem.setAttribute(nS.trim());
            } catch (error) {
                throw error;
            }
            return true;
        }
    };
    /**
     * Adds an class string
     *
     * @property ele element or element-id
     * @property val class to add
     */
    xjx.addClass = function (elem, val) {
        var ident = 'class';
        if (xjx.isAttribValue(val)) {
            if (true === xajax.attr.has(ident)) {
                elem.setAttribute(ident, elem.getAttribute(ident) + ' ' + val);
            } else {
                elem.setAttribute(ident, val);
            }
            return true;
        }
        return false;
    };
    /**
     * Attribute Helper to set get remove
     * **/
    xjx.attr = {
        /*
        * remove all useless stuff
        * @property str string
        * @return string
        * **/
        removeSpaces: function (str) {
            return (xjx.isStr(str)) ? str.replace(/\s\s+/g, ' ') : '';
        },
        /**
         * Adding an Attribute if it not exists
         *
         * @property ele element or element-id
         * @property prop attribute to check
         **/
        has: function (elem, prop) {
            if (xjx.isStr(prop) && (elem = getEle(elem))) {
                prop = prop === 'class' ? 'className' : prop;
                return elem.hasAttribute(prop);
            }
            return false;
        },
        /**
         * Adding an Attribute if it not exists
         *
         * @property ele element or element-id
         * @property prop attribute to set
         * @property val value to set
         **/
        add: function (elem, prop, val) {
            if (xjx.isAttribValue(val) && xjx.isStr(prop) && (null !== (elem = getEle(elem)) && false === xajax.attr.has(elem))) {
                elem.setAttribute(prop, val);
                return true;
            }
            return false;
        },
        /**
         * Remove an Attribute if exists
         *
         * @property ele element or element-id
         * @property prop attribute to remove
         */
        remove: function (elem, prop) {
            if (xjx.isStr(prop) && true === xajax.attr.has(prop)) {
                elem.removeAttribute(prop);
                return true;
            }
            return false;
        }
    };
}(xajax));