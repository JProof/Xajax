'use strict';
/*
 * Unit tests for lib/calculator.js
 */
describe('Element Test', function () {
    
    // API for interacting with the page.
    var controls = {
        get domElement() {
            return document.getElementById('existingElement');
        }
    };
    beforeEach(function () {
        var fixture = '<div id="fixture">' +
          '<span id="existingElement"></span>' +
          '</div>';
        document.body.insertAdjacentHTML(
          'afterbegin',
          fixture);
    });
    // remove the html fixture from the DOM
    afterEach(function () {
        document.body.removeChild(document.getElementById('fixture'));
    });
    // remove the html fixture from the DOM
    afterEach(function () {
        fixture.cleanup();
    });
    it('getHtml Element by id which exists', function () {
        controls.domElement.should.equal(xajax.tools.$('existingElement'));
    });
    it('returns the same dom element (already is an element)', function () {
        controls.domElement.should.equal(xajax.tools.$(controls.domElement));
    });
    it('get element by xajax typical obj.id', function () {
        controls.domElement.should.equal(xajax.tools.$({id: 'existingElement'}));
    });
});