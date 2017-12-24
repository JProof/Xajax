'use strict';
if ('undefined' === typeof window) {
    // bridge to handle mocha-tests
    require('./loader.js');
    var assert = require('assert');
    require('util');
    
}
// otherwise it handles karma
/*
 * xajax Form Values test
 */
describe('FormValues from html string to sendable xajax object', function () {
    describe('Extract FormFieldNames', function () {
        // todo make error
        it('right way: Regular name myTestFieldName', function () {
            var str = 'myTestFieldName';
            var result = xajax.forms.valueHandler.extractFieldName(str);
            assert.equal(result, 'myTestFieldName');
        });
        
        it('Trim name="&nbsp; myTestFieldName" ', function () {
            var str = ' myTestFieldName';
            var result = xajax.forms.valueHandler.extractFieldName(str);
            assert.equal(result, 'myTestFieldName');
        });
        it('right way: test[subname][nextSub] ', function () {
            var str = 'test[subname][nextSub]';
            var result = xajax.forms.valueHandler.extractFieldName(str);
            assert.deepEqual(result, ['test', 'subname', 'nextSub']);
        });
        
        // todo make error
        it('Html Error Nested test[subname][nextSub[subError]]', function () {
            var str = 'test[subname][nextSub[subError]]';
            var result = xajax.forms.valueHandler.extractFieldName(str);
            assert.deepEqual(result, [
                'test',
                'subname',
                'nextSub',
                'subError']);
        });
        
        // todo make error
        it('Html Error Nested test[subname][nextSub[subError]]', function () {
            var str = 'test[subname[subError]][nextSub]';
            var result = xajax.forms.valueHandler.extractFieldName(str);
            assert.deepEqual(result, [
                'test',
                'subname',
                'subError',
                '',
                'nextSub']);
        });
        // todo make error
        it('Html List incremented test[subname][]', function () {
            var str = ' test[subname][]';
            var result = xajax.forms.valueHandler.extractFieldName(str);
            assert.deepEqual(result, ['test', 'subname', '']);
        });
    });
    describe('Make js-Objects from FormFieldName', function () {
        
        it('Do not Handle Null values', function () {
            var str = ['myTestFieldName'];
            var result = xajax.forms.valueHandler.listAsObject(str);
            assert.equal(result, null);
        });
        
        it('Handle empty String ""', function () {
            var str = ['myTestFieldName'];
            var result = xajax.forms.valueHandler.listAsObject(str, '');
            assert.deepEqual(result, {myTestFieldName: ''});
        });
        
        it('int -> DeepNestedFormName mainContent[categories][category][item][pieceOfItem]', function () {
            var str = [
                'mainContent',
                'categories',
                'category',
                'item',
                'pieceOfItem'];
            var result = xajax.forms.valueHandler.listAsObject(str, 122);
            console.log('json Parse: ', JSON.stringify(result));
            console.log('parsed reparsed ', JSON.parse(JSON.stringify(result)));
            assert.deepEqual(result, {mainContent: {categories: {category: {item: {pieceOfItem: 122}}}}});
        });
        it('array -> simply Nested mainContent[categories][122,2294]', function () {
            var str = [
                'mainContent',
                'categories'
            ];
            var result = xajax.forms.valueHandler.listAsObject(str, [
                122,
                2294]);
            console.log('json Parse: ', JSON.stringify(result));
            console.log('parsed reparsed ', JSON.parse(JSON.stringify(result)));
            assert.deepEqual(result, {mainContent: {categories: [122, 2294]}});
        });
    });
    describe('testing Merge Recursive', function () {
        
        it('Simple Merge', function () {
            var obj1 = {test1: {mySuper: 1}};
            var obj2 = {test1: {mySuper2: 2}};
            var result = xajax.forms.valueHandler.merge(obj1, obj2);
            
            assert.deepEqual(result, {test1: {mySuper: 1, mySuper2: 2}});
        });
        
        it('Simple Merge Override Value', function () {
            var obj1 = {test1: {mySuper: 1}};
            var obj2 = {test1: {mySuper: 2}};
            var result = xajax.forms.valueHandler.merge(obj1, obj2);
            
            assert.deepEqual(result, {test1: {mySuper: 2}});
        });
        
        it('Simple Merge Append Array Value', function () {
            var obj1 = {test1: {mySuper: [1]}};
            var obj2 = {test1: {mySuper: [2]}};
            var result = xajax.forms.valueHandler.merge(obj1, obj2);
            
            assert.deepEqual(result, {test1: {mySuper: [1, 2]}});
        });
        
        it('Simple Merge Override integer Value with an Array ', function () {
            var obj1 = {test1: {mySuper: 1}};
            var obj2 = {test1: {mySuper: [2]}};
            var result = xajax.forms.valueHandler.merge(obj1, obj2);
            
            assert.deepEqual(result, {test1: {mySuper: [2]}});
        });
        
        it('Complex Merge with Array values', function () {
            
            var obj1 = {test1: {mySuper: 1, checkboxes: [1], deeperText: 'myText'}};
            var obj2 = {test1: {mySuper: [2], checkboxes: [2], deeperText2: 'yetAnOtherText'}};
            var result = xajax.forms.valueHandler.merge(obj1, obj2);
            
            assert.deepEqual(result, {
                test1: {
                    mySuper: [2], checkboxes: [
                        1,
                        2], deeperText: 'myText', deeperText2: 'yetAnOtherText'
                }
            });
        });
        
    });
});