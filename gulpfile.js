var gulp = require('gulp');
    sass = require('gulp-sass');
    watch = require('gulp-watch');

//gulp task to compile all .scss files in directory to the destination directory that contains the css file
gulp.task('sass', function () {
    gulp.src('./assets/sass/**/*.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(gulp.dest('./build/css/'));
});

//first runs the sass compiler and than it watches all the .scss files and whenever one of these are changed and saved
//the gulp sass task is ran again
gulp.task('default',['sass'], function(){
    return gulp.watch('./assets/sass/**/*.scss',['sass']);
});