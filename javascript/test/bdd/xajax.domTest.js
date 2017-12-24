'use strict';
/*
 * xajax attributes
 */
describe('Xajax Dom Tests', function () {
    
    // API for interacting with the page.
    var controls = {
        get assignedContent() {
            return document.getElementById('target').innerHTML;
        }
        
    };
    beforeEach(function () {
        fixture.load('dom_assign.html');
    });
    
    // remove the html fixture from the DOM
    afterEach(function () {
        fixture.cleanup();
    });
    describe('Xajax Html', function () {
        
        it('set innerHTML naked text', function () {
            var exp = '<span>myText</span>';
            xajax.html('target', exp);
            
            controls.assignedContent.should.equal(exp);
        });
        it('set innerHTML empty text', function () {
            var exp = '';
            xajax.html('target', exp);
            
            controls.assignedContent.should.equal(exp);
        });
        it('set innerHTML Override Html', function () {
            
            var exp = '<a>a lot of content</a>';
            var exp2 = '<a>a lot of content has overridden</a>';
            xajax.html('target', exp);
            xajax.html('target', exp2);
            controls.assignedContent.should.equal(exp2);
        });
        it('set innerHTML Null Content', function () {
            
            var exp = '<a>a lot of content</a>';
            
            xajax.html('target', exp);
            xajax.html('target', null);
            controls.assignedContent.should.equal('');
        });
        it('get innerHTML', function () {
            
            var exp = '<a>a lot of content</a>';
            xajax.html('target', exp);
            
            assert.equal(xajax.html('target'), exp);
        });
        it('get innerHTML empty', function () {
            var exp = '';
            xajax.html('target');
            assert.equal(xajax.html('target'), exp);
        });
    });
    describe('Dom Assign', function () {
        
        it('Assign naked text', function () {
            xajax.dom.assign('target', 'innerHTML', 'myText');
            
            controls.assignedContent.should.equal('myText');
        });
        it('Assign empty text', function () {
            xajax.dom.assign('target', 'innerHTML', '');
            
            controls.assignedContent.should.equal('');
        });
        it('Assign Override Text', function () {
            
            xajax.dom.assign('target', 'innerHTML', '<div>wrong </div>');
            xajax.dom.assign('target', 'innerHTML', 'clear');
            
            controls.assignedContent.should.equal('clear');
        });
        it('Assign on not existing object', function () {
            xajax.dom.assign('notarget', 'innerHTML', '<div>wrong </div>');
            controls.assignedContent.should.equal('');
        });
    });
});