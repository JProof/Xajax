'use strict';
/*
 * xajax attributes
 */
describe('Attribute and ClassName Tests', function () {
    
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
    it('adding an new  ClassName', function () {
        xajax.addClass('classNameTest', 'myTestClass');
        controls.currentClass.should.equal('myTestClass');
    });
    it('adding an Additional ClassName', function () {
        xajax.addClass('classNameTest', 'myTestClass');
        xajax.addClass('classNameTest', 'extraClass');
        controls.currentClass.should.equal('myTestClass extraClass');
    });
    it('adding more Classes in one string', function () {
        xajax.addClass('classNameTest', 'myTestClass with extra');
        controls.currentClass.should.equal('myTestClass with extra');
    });
    it('remove non existing Class', function () {
        xajax.addClass('classNameTest', 'myTestClass');
        xajax.removeClass('classNameTest', 'extraClass');
        controls.currentClass.should.equal('myTestClass');
    });
    it('Checks an Class Exists in ClassName', function () {
        xajax.addClass('classNameTest', 'myTestClass');
        var hasClass = xajax.hasClass('classNameTest', 'myTestClass');
        assert.equal(hasClass, true);
    });
    it('Checks an Class does not Exists in ClassName', function () {
        xajax.addClass('classNameTest', 'myTestClass');
        var hasClass = xajax.hasClass('classNameTest', 'myTestClasssa');
        assert.equal(hasClass, false);
    });
    it('add Attribute to existing Html Element', function () {
        xajax.addAttrib('attribTester', 'myTestAttrib', 'myAttribValue');
        controls.currentAttrib.should.equal('myAttribValue');
    });
    it('adding 2 AttributeValues on an existing attribute Html Element', function () {
        xajax.addAttrib('attribTester', 'myTestAttrib', 'myOld');
        xajax.addAttrib('attribTester', 'myTestAttrib', ' myAttribValue');
        controls.currentAttrib.should.equal('myOld myAttribValue');
    });
});