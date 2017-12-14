(function (xjx) {
    xjx.responseProcessor.json = function (oRequest) {
        var xx = xajax;
        var xt = xx.tools;
        var xcb = xx.callback;
        var gcb = xcb.global;
        var lcb = oRequest.callback;
        var oRet = oRequest.returnValue;
        if (xt.in_array(xx.responseSuccessCodes, oRequest.request.status)) {
            xcb.execute([gcb, lcb], 'onSuccess', oRequest);
            var seq = 0;
            if (oRequest.request.responseText) {
                try {
                    var responseJSON = eval('(' + oRequest.request.responseText + ')');
                } catch (ex) {
                    throw(ex);
                }
                if (('object' === typeof responseJSON) && ('object' === typeof responseJSON.xjxobj)) {
                    oRequest.status.onProcessing();
                    oRet = xt.json.processFragment(responseJSON, seq, oRet, oRequest);
                } else {
                }
            }
            var obj = {};
            obj.fullName = 'Response Complete';
            obj.sequence = seq;
            obj.request = oRequest;
            obj.context = oRequest.context;
            obj.cmd = 'rcmplt';
            xt.queue.push(xx.response, obj);
            // do not re-start the queue if a timeout is set
            if (null == xx.response.timeout)
                xt.queue.process(xx.response);
        } else if (xt.in_array(xx.responseRedirectCodes, oRequest.request.status)) {
            xcb.execute([gcb, lcb], 'onRedirect', oRequest);
            window.location = oRequest.request.getResponseHeader('location');
            xx.completeResponse(oRequest);
        } else if (xt.in_array(xx.responseErrorsForAlert, oRequest.request.status)) {
            xcb.execute([gcb, lcb], 'onFailure', oRequest);
            xx.completeResponse(oRequest);
        }
        return oRet;
    };
}(xajax));