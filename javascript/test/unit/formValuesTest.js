'use strict';
/*
 * xajax Form Values test
 */
describe('Testing Form-Values getter from Html', function () {
    
    // API for interacting with the page.
    var controls = {
        get currentClass() {
            return document.getElementById('classNameTest').className;
        },
        get currentAttrib() {
            return document.getElementById('attribTester').getAttribute('myTestAttrib');
        }
    };
    beforeEach(function () {
        var fixture = '<div id="fixture">' +
          '<span id="classNameTest"></span>' +
          '<span id="attribTester"></span>' +
          '</div>';
        document.body.insertAdjacentHTML(
          'afterbegin',
          fixture);
    });
    // remove the html fixture from the DOM
    afterEach(function () {
        document.body.removeChild(document.getElementById('fixture'));
    });
    // inject the HTML fixture for the tests
    beforeEach(function () {
        
        //       fixture.load('attribute.html');
        ///  window.calculator.init();
    });
    // remove the html fixture from the DOM
    afterEach(function () {
        fixture.cleanup();
    });
    it('Check Fixture', function () {
        fixture.load('attribute.html');
    });
});