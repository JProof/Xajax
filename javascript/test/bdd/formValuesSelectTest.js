'use strict';
/*
 * xajax Form Values test
 */
describe('Testing Form-Values Select', function () {
    
    // API for interacting with the page.
    var controls = {
         formValues:function(formId) {
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
        var vars = controls.formValues;
        var chckObject = {hiddenOne: '12', textOne: '124', dateOne: '2017-12-30', textareaOne: '<div>teststring</div>', radioOne: 'valOne', chckbx: {caseOne: 'valOne', caseTwo: 'valTwo'}};
        assert.equal(JSON.stringify(vars), JSON.stringify(chckObject));
    });
    // todo check zero Values
    it('Nameless will be ignored', function () {
        var vars = controls.formValues;
        var chckObject = {hiddenOne: '12', textOne: '124', dateOne: '2017-12-30', textareaOne: '<div>teststring</div>', radioOne: 'valOne', chckbx: {caseOne: 'valOne', caseTwo: 'valTwo'}};
        assert.equal(JSON.stringify(vars), JSON.stringify(chckObject));
    });
    it('Multiple Select', function () {
        fixture.load('form_multiple_select.html');
        var vars = controls.formValues;
        var chckObject = {hiddenOne: '12', textOne: '124', dateOne: '2017-12-30', textareaOne: '<div>teststring</div>', radioOne: 'valOne', chckbx: {caseOne: 'valOne', caseTwo: 'valTwo'}};
        assert.equal(JSON.stringify(vars), JSON.stringify(chckObject));
    });
});