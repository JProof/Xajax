'use strict';
/*
 * xajax Form Values test
 */
describe('Testing Form-Values Select', function () {
    
    // API for interacting with the page.
    var controls = {
        formValues: function (formId) {
            return xajax.forms.getFormValues(formId);
        }
    }; // inject the HTML fixture for the tests
    beforeEach(function () {
        fixture.load('form_multiple_select.html');
    });
    // remove the html fixture from the DOM
    afterEach(function () {
        fixture.cleanup();
    });
    // todo check zero Values
    it('No Form Select', function () {
        var str = '[subname][nextSub]';
        xajax.extractName(str);
    });
    
});