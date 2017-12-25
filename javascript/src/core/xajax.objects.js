/**
 * Diverse Core-Helper Helpers
 * **/
(function (xjx) {
    /**
     * Short tester to save a lot of typeof's
     * @param {*} val
     * @return {boolean}
     * */
    xjx.isStr = function (val) {
        return 'string' === typeof val;
    };
    /**
     * Short tester to save a lot of typeof's
     * @param {*} val
     * @return {boolean}
     * */
    xjx.isNum = function (val) {
        return 'number' === typeof val;
    };
    /**
     * Check the value is valid as attribute value
     *
     * @param {*} val
     * @return {boolean}
     * */
    xjx.isAttribValue = function (val) {
        return xjx.isNum(val) || xjx.isStr(val);
    };
    /**
     * Safe getting of an Object element
     * @since 0.7.1
     *
     * @param {object} obj
     * @param {object} key
     *
     * @return {*} content of the object
     * */
    xjx.getObjEle = function (obj, key) {
        if (('object' === typeof obj) && 'string' === typeof key && key in obj) {
            return obj[key];
        }
        return void 0;
    };
}(xajax));