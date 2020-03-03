'use strict';

var gulp = require("gulp");
var watch = require("gulp-watch");
var sass = require('gulp-sass'),
    cssnano = require('gulp-cssnano'),
    autoprefixer = require('gulp-autoprefixer'),
    concat = require('gulp-concat');

gulp.task('sass', function () {
    return gulp.src('bitrix/components/dlay/main.masters.list/templates/.default/**/*.scss')
        .pipe(sass())
        .pipe(autoprefixer("last 2 version", "> 1%", "ie 8", "ie 7"))
        .pipe(gulp.dest('bitrix/components/dlay/main.masters.list/templates/.default/'));
});


gulp.task('watch', function() {
    watch('bitrix/components/dlay/main.masters.list/templates/.default/**/*.scss', gulp.parallel('sass'));
});

//gulp.task("default", ["sass", "watch"]);