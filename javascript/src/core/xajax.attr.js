/** xajax attr **/
(function (xjx) {
    /**
     * remove all useless stuff
     *
     * @param {string} str
     *
     * @return {string}
     * **/
    var remS = function (str) {
        return (xjx.isStr(str)) ? str.replace(/\s\s+/g, ' ') : '';
    };
    /**
     * internal proxy to remove the old xajax.tools.$ class
     * @param {string|Element} elem Element or id=""
     *
     * @return {null|Element}
     * */
    var getEle = function (elem) {
        return xjx.tools.$(elem);
    };
    /**
     * Adds an class string
     *
     * @param {string|Element} elem Element or id=""
     * @param {string} val class to add
     */
    xjx.addClass = function (elem, val) {
        var ident = 'class';
        if (xjx.isAttribValue(val) && null !== (elem = getEle(elem))) {
            if (xjx.hasAttrib(elem, ident)) {
                elem.setAttribute(ident, elem.getAttribute(ident) + ' ' + val);
            } else {
                elem.setAttribute(ident, val);
            }
        }
    };
    /**
     * Removes an class from element
     *
     * @param {string|Element} elem Element or id=""
     * @param {string} val class to remove
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
     * @param {string|Element} elem Element or id=""
     * @param {string} val class to check
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
    /**
     * Checks an Class exists
     *
     * @param {string|Element} elem Element or id=""
     * @param {string} prop attribute to check
     *
     * @return {boolean}
     */
    xjx.hasAttrib = function (elem, prop) {
        if (xjx.isStr(prop) && (elem = getEle(elem))) {
            return elem.hasAttribute(prop);
        }
        return false;
    };
    /**
     * Adding an Attribute if it not exists
     *
     * @param {string|Element} elem Element or id=""
     * @param {string} prop attribute to check
     * @param {string} val content to add in Attribute
     **/
    xjx.addAttrib = function (elem, prop, val) {
        elem = getEle(elem);
        if (xjx.isAttribValue(val) && xjx.isStr(prop))
            if (!xjx.hasAttrib(elem, prop))
                elem.setAttribute(prop, val);
            else
                elem.setAttribute(prop, elem.getAttribute(prop) + val);
    };
    /**
     * Remove an Attribute if exists
     *
     * @param {string|Element} elem Element or id=""
     * @param {string} prop attribute to remove
     */
    xjx.removeAttr = function (elem, prop) {
        if (xjx.isStr(prop) && true === xjx.hasAttrib(elem, prop))
            elem.removeAttribute(prop);
    };
}(xajax));