// Karma configuration
// Generated on Fri Dec 15 2017 13:48:16 GMT+0100 (Mitteleuropäische Zeit)
module.exports = function (config) {
    var scriptStackDir = 'src/core/';
    config.set({
        // base path that will be used to resolve all patterns (eg. files, exclude)
        basePath: '',
        // frameworks to use
        // available frameworks: https://npmjs.org/browse/keyword/karma-adapter
        frameworks: ['mocha', 'fixture', 'chai'],
        // list of files / patterns to load in the browser
        files: [
            // scripts
            scriptStackDir + 'xajax.init.js',
            scriptStackDir + 'xajax.objects.js',
            scriptStackDir + 'xajax.config.js',
            scriptStackDir + 'xajax.config.cursor.js',
            scriptStackDir + 'xajax.tools.js',
            scriptStackDir + 'xajax.tools.queue.js',
            scriptStackDir + 'xajax.responseProcessor.js',
            scriptStackDir + 'xajax.responseProcessor.json.js',
            scriptStackDir + 'xajax.responseProcessor.xml.js',
            scriptStackDir + 'xajax.js.js',
            scriptStackDir + 'xajax.dom.js',
            scriptStackDir + 'xajax.domResponse.js',
            scriptStackDir + 'xajax.css.js',
            scriptStackDir + 'xajax.forms.js',
            scriptStackDir + 'xajax.forms.values.js',
            scriptStackDir + 'xajax.events.js',
            scriptStackDir + 'xajax.callback.js',
            // some extra tools
            scriptStackDir + 'xajax.attr.js',
            scriptStackDir + 'xajax.command.js',
            //"src/core/xajax.event.js"
            scriptStackDir + 'xajax_core_old.js',
            
            'test/bdd/formValuesTest.js',
            'test/bdd/formFieldNameObjectTest.js',
            'test/bdd/elementTest.js',
            'test/bdd/attributeTest.js',
            {pattern: 'spec/fixtures/**/*.html'}
        ],
        
        // list of files to exclude
        exclude: [],
        // preprocess matching files before serving them to the browser
        // available preprocessors: https://npmjs.org/browse/keyword/karma-preprocessor
        // https://www.npmjs.com/package/karma-fixture
        preprocessors: {
            'spec/fixtures/**/*.html': ['html2js'],
            'spec/fixtures/**/*.json': ['json_fixtures']
        },
        // test results reporter to use
        // possible values: 'dots', 'progress'
        // available reporters: https://npmjs.org/browse/keyword/karma-reporter
        reporters: ['progress'],
        // web server port
        port: 9876,
        // enable / disable colors in the output (reporters and logs)
        colors: true,
        // level of logging
        // possible values: config.LOG_DISABLE || config.LOG_ERROR || config.LOG_WARN || config.LOG_INFO || config.LOG_DEBUG
        logLevel: config.LOG_INFO,
        // enable / disable watching file and executing tests whenever any file changes
        autoWatch: false,
        // start these browsers
        // available browser launchers: https://npmjs.org/browse/keyword/karma-launcher
        browsers: ['Chrome', 'Firefox'],
        // Continuous Integration mode
        // if true, Karma captures browsers, runs the tests and exits
        singleRun: false,
        // Concurrency level
        // how many browser should be started simultaneous
        concurrency: 1,
        jsonFixturesPreprocessor: {
            variableName: '__json__'
        }
    });
};
