// Project configuration.
var scriptStackDir = 'src/core/';
module.exports = function (grunt) {
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        mochaTest: {
            test: {
                options: {
                    reporter: 'list',
                    timeout: 15000
                },
                src: ['test/unit/*.js']
            }
        },
        concat: {
            options: {
                separator: '\n'
            },
            basic: {
                src: [
                    scriptStackDir + 'xajax.init.js',
                    scriptStackDir + 'xajax.html.js',
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
                    scriptStackDir + 'xajax_core_old.js'],
                dest: 'dist/xajax_core.js'
            }
        },
        uglify: {
            options: {
                mangle: true
            },
            build: {
                src: 'dist/xajax_core.js',
                dest: 'dist/xajax_core.min.js'
            }
        }
    });
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-mocha');
    grunt.loadNpmTasks('grunt-mocha-test');
    grunt.registerTask('compressTask', ['concat', 'uglify']);
    grunt.registerTask('mochaTest', ['mochaTest']);
};