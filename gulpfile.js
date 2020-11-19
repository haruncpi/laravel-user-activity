var gulp = require('gulp');
var inject = require('gulp-inject');
var print = require('gulp-print').default;

gulp.task('inject-style', function () {
    gulp.watch(['./assets/style.css'], function (file) {
        return gulp.src('./views/index.blade.php')
            .pipe(inject(gulp.src(['./assets/style.css']), {
                starttag: '<!-- inject:style:css -->',
                transform: function (filepath, file) {
                    return `<style>${file.contents.toString()}</style>`;
                }
            }))
            .pipe(print(function (file) {
                return "Processing " + file;
            }))
            .pipe(gulp.dest('./views'));
    });
});

gulp.task('default', gulp.series('inject-style'));