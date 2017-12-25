// push xajax finished distribution file into xajax main repo
var gulp = require('gulp');

gulp.task('copy', function () {
    gulp.src('./javascript/dist/xajax_core.js').pipe(gulp.dest('./src/assets/js/'));
    gulp.src('./javascript/dist/xajax_core.min.js').pipe(gulp.dest('./src/assets/js/'));
  
});

