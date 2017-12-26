'use strict';
/*
 * xajax Form Values test
 */
describe('Testing Form-Values', function () {
    
    // API for interacting with the page.
    // inject the HTML fixture for the tests
    beforeEach(function () {
    
    });
    // remove the html fixture from the DOM
    afterEach(function () {
        fixture.cleanup();
    });
    describe('Testing new Params for Transportation', function () {
        it('Simple Formcheck that vars will be collected to object', function () {
            var chckObject = {hiddenOne: 12, textOne: '124', dateOne: '2017-12-30', textareaOne: '<div>teststring</div>', radioOne: 'valOne', chckbx: {caseOne: 'valOne', caseTwo: 'valTwo'}};
            
            var res = objectToParamsString(chckObject);
            var exp = ['hiddenOne', 'textOne'];
            assert.deepEqual(res, exp);
        });
    });
    describe('Getting Form', function () {
        it('No Form-Node Found by Null', function () {
            xajax.forms.getFormValues(null);
            assert.equal(xajax.forms.getFormValues(null), null);
        });
        it('No Form-Node Found by none existing id', function () {
            assert.equal(xajax.forms.getFormValues('iamNotExists'), null);
        });
        // todo check zero Values
        it('Simple Formcheck that vars will be collected to object', function () {
            fixture.load('form_regular.html');
            var vars = xajax.forms.getFormValues('concreteId');
            var chckObject = {hiddenOne: '12', textOne: '124', dateOne: '2017-12-30', textareaOne: '<div>teststring</div>', radioOne: 'valOne', chckbx: {caseOne: 'valOne', caseTwo: 'valTwo'}};
            assert.deepEqual(JSON.stringify(chckObject), JSON.stringify(vars));
        });
    });
    describe('Html Select Test', function () {
        beforeEach(function () {
            fixture.load('form_multiple_select.html');
        });
        // remove the html fixture from the DOM
        afterEach(function () {
            fixture.cleanup();
        });
        it('Nothing selected  ', function () {
            var vars = xajax.forms.getFormValues('regular');
            assert.deepEqual({}, vars);
        });
        it('Single-Select with 2 selected Option, last will use  ', function () {
            var vars = xajax.forms.getFormValues('single-two-checked');
            assert.deepEqual({selectOne: 'testOptionTwoValue'}, vars);
        });
        it('Multiple-Select with 2 selected Option ', function () {
            var vars = xajax.forms.getFormValues('multiple-multiple-attr');
            assert.deepEqual({
                selectOne: [
                    'testOptionTwoValue',
                    'testOptionThreeValue']
            }, vars);
        });
    });
});