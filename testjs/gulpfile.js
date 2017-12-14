var gulp = require('gulp');
//var sass = require("gulp-sass");
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var rename = require('gulp-rename');
var minify = require('gulp-clean-css');
var scriptStackDir = 'src/core/';
var scripts = [
    scriptStackDir + 'xajax.init.js',
    scriptStackDir + 'xajax.config.js',
    scriptStackDir + 'xajax.config.status.js',
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
    scriptStackDir + 'xajax.events.js',
    scriptStackDir + 'xajax.callback.js',
    scriptStackDir + 'xajax.attr.js',
    //"src/core/xajax.event.js"
    scriptStackDir + 'xajax_core_old.js'
];
gulp.task('scripts', function () {
    return gulp.src(scripts).pipe(concat('xajax_core.js')).pipe(gulp.dest('dist/js')).pipe(rename('xajax_core.min.js')).pipe(uglify()).pipe(gulp.dest('dist/js'));
});
gulp.task('watch', function () {
    gulp.watch(scripts, ['scripts']);
    //gulp.watch(['src/_scss/*.scss', 'src/_scss/components/*.scss', 'src/_scss/mixins/*.scss'], ['sass', 'combine']);
});
gulp.task('default', ['scripts', 'watch']);