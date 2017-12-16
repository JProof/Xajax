/**
 * Diverse Core-Helper Helpers
 * **/
(function (xjx) {
    /**
     * Short tester to save a lot of typeof's
     * */
    xjx.isStr = function (ele) {
        return typeof ele === 'string';
    };
    /**
     * Short tester to save a lot of typeof's
     * */
    xjx.isNum = function (ele) {
        return typeof ele === 'number';
    };
    /**
     * Check the value is valid as attribute value
     * @property ele string or number
     * @return bool
     * */
    xjx.isAttribValue = function (ele) {
        return xjx.isNum(ele) || xjx.isStr(ele);
    };
    /**
     * Safe getting of an Object element
     * @since 0.7.1
     * */
    xjx.getObjEle = function (obj, ident) {
        if (('object' === typeof obj) && 'string' === typeof ident && ident in obj) {
            return obj[ident];
        }
        return void 0;
    };
}(xajax));