/** xajax attr **/
(function (xjx) {
    /*
   * remove all useless stuff
   * @property str string
   * @return string
   * **/
    var remS = function (str) {
        return (xjx.isStr(str)) ? str.replace(/\s\s+/g, ' ') : '';
    };
    /**
     * internal proxy to remove the old xajax.tools.$ class
     * */
    var getEle = function (elem) {
        return xjx.tools.$(elem);
    };
    /**
     * Adds an class string
     *
     * @property ele element or element-id
     * @property val class to add
     */
    xjx.addClass = function (elem, val) {
        var ident = 'class';
        if (xjx.isAttribValue(val) && null !== (elem = getEle(elem))) {
            if (true === xajax.hasAttrib(elem, ident)) {
                elem.setAttribute(ident, elem.getAttribute(ident) + ' ' + val);
            } else {
                elem.setAttribute(ident, val);
            }
        }
    };
    /**
     * Removes an class from element
     *
     * @property ele element or element-id
     * @property val value to removes from
     */
    xjx.removeClass = function (elem, val) {
        var ident = 'class';
        elem = getEle(elem);
        if (xjx.isAttribValue(val) && true === xjx.hasAttrib(elem, ident)) {
            try {
                var nS = remS(elem.getAttribute(ident).replace(val, ''));
                elem.setAttribute(ident, nS.trim());
            } catch (error) {
                throw error;
            }
            return true;
        }
    };
    /**
     * Checks an Class exists
     *
     * @property ele element or element-id
     * @property val class to add
     */
    xjx.hasClass = function (elem, val) {
        var res = false;
        var ident = 'class';
        if (xjx.isAttribValue(val) && null !== (elem = getEle(elem))) {
            if (true === xajax.hasAttrib(elem, ident)) {
                var parts = elem.getAttribute(ident).split(' ');
                parts.forEach(function (value) {
                    if (value === val) {
                        return res = true;
                    }
                });
            }
        }
        return res;
    };
    xjx.hasAttrib = function (elem, prop) {
        if (xjx.isStr(prop) && (elem = getEle(elem))) {
            return elem.hasAttribute(prop);
        }
        return false;
    };
    /**
     * Adding an Attribute if it not exists
     *
     * @property ele element or element-id
     * @property prop attribute to set
     * @property val value to set
     **/
    xjx.addAttrib = function (elem, prop, val) {
        elem = getEle(elem);
        if (xjx.isAttribValue(val) && xjx.isStr(prop))
            if (false === xajax.hasAttrib(elem, prop))
                elem.setAttribute(prop, val);
            else
                elem.setAttribute(prop, elem.getAttribute(prop) + val);
    };
    /**
     * Remove an Attribute if exists
     *
     * @property ele element or element-id
     * @property prop attribute to remove
     */
    xjx.removeAttr = function (elem, prop) {
        if (xjx.isStr(prop) && true === xajax.attr.has(prop))
            elem.removeAttribute(prop);
    };
}(xajax));