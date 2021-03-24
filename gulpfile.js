'use strict';

const gulp = require('gulp');
const sass = require('gulp-sass');
const sourcemaps = require('gulp-sourcemaps');
const autoprefixer = require('gulp-autoprefixer');
const concat = require('gulp-concat');
const uglify = require('gulp-uglify');
const babel = require('gulp-babel');
const plumber = require('gulp-plumber');
const image = require('gulp-image');
const browserSync = require('browser-sync').create();

// File Sources
const stylesSrc = 'assets/styles/**/*.scss';
const scriptsSrc = 'assets/scripts/**/*.js';
const phpSrc = './**/*.php';
const imagesSrc = 'assets/images/*';

// File Destinations
const stylesDist = 'dist/styles';
const scriptsDist = 'dist/scripts';
const imagesDist = 'dist/images';

sass.compiler = require('node-sass');

gulp.task('styles', function () {
  return gulp.src(stylesSrc)
    .pipe(sourcemaps.init())
    .pipe(sass({
      outputStyle: 'compressed',
    }).on('error', sass.logError))
    .pipe(autoprefixer({
      browsers: ['last 2 versions'],
      cascade: false
    }))
    .pipe(sourcemaps.write('.'))
    .pipe(gulp.dest(stylesDist))
    .pipe(browserSync.stream());
});

gulp.task('scripts', function () {
  return gulp.src(scriptsSrc)
    .pipe(plumber())
    .pipe(babel({
      presets: [
        ['@babel/env', {
          modules: false
        }]
      ]
    }))
    .pipe(concat('main.js'))
    .pipe(uglify())
    .pipe(gulp.dest(scriptsDist))
    .pipe(browserSync.stream());
});

gulp.task('images', function () {
  return gulp.src(imagesSrc)
    .pipe(image())
    .pipe(gulp.dest(imagesDist));
});

gulp.task('watch', function () {
  browserSync.init({
    proxy: 'LOCALHOST',
  });
  gulp.watch(phpSrc).on('change', browserSync.reload);
  gulp.watch(stylesSrc, gulp.series('styles')).on('change', browserSync.reload);
  gulp.watch(scriptsSrc, gulp.series('scripts')).on('change', browserSync.reload);
  gulp.watch(imagesSrc, gulp.series('images')).on('change', browserSync.reload);
});

gulp.task('default', gulp.series('styles','scripts','images'));
