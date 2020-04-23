'use strict';

var gulp = require("gulp");
var watch = require("gulp-watch");
var sass = require('gulp-sass'),
    cssnano = require('gulp-cssnano'),
    autoprefixer = require('gulp-autoprefixer'),
    concat = require('gulp-concat');

var path = 'bitrix/components/dlay/main.masters.list/templates/.default/';

gulp.task('sass', function () {console.log('sass');
    return gulp.src(path + '/**/*.scss')
        .pipe(sass())
        .pipe(autoprefixer("last 2 version", "> 1%", "ie 8", "ie 7"))
        .pipe(gulp.dest(function(file) {
            return file.base;
        }));
});


gulp.task('watch', function() {
    console.log('watch');
    watch(path + '/**/*.scss', gulp.parallel('sass'));
});

//gulp.task("default", ["sass", "watch"]);