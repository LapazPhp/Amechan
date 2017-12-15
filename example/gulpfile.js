const gulp = require('gulp');
const pump = require('pump');
const del = require('del');
const concat = require('gulp-concat');
// const urlAdjuster = require('gulp-css-url-adjuster');
const uglifycss = require('gulp-uglifycss');
const uglify = require('gulp-uglify');
const rename = require("gulp-rename");
const sourcemaps = require('gulp-sourcemaps');
const rev = require('gulp-rev');
const revReplace = require('gulp-rev-replace');

const basedir = './public/assets';

gulp.task('default', ['vendor']);

gulp.task('build', ['fonts', 'css', 'js']);
gulp.task('dist', ['rev-replace']);

gulp.task('clean', () => {
    return del([
        basedir + '/dist',
        basedir + '/vendor'
    ]);
});

gulp.task('vendor', ['clean'], () => {
    return pump([
        gulp.src('./node_modules/+(jquery|bootstrap)/dist/**/*', { base: './node_modules/' }),
        gulp.dest(basedir + '/vendor')
    ]);
});

gulp.task('fonts', ['vendor'], () => {
    return pump([
        gulp.src(basedir + '/vendor/bootstrap/dist/fonts/*'),
        gulp.dest(basedir + '/dist/fonts')
    ]);
});

gulp.task('css', ['vendor'], () => {
    return pump([
        gulp.src([
            basedir + '/vendor/bootstrap/dist/css/bootstrap.css',
            basedir + '/vendor/bootstrap/dist/css/bootstrap-theme.css'
        ], { base: basedir }),
        sourcemaps.init(),

        concat('all.css'),
        // urlAdjuster({
        //     replace:  ['../', '../vendor/bootstrap/dist/']
        // }),
        // gulp.dest(basedir + '/dist/css'),

        uglifycss(),
        rename({ extname: '.min.css'}),
        //gulp.dest(basedir + '/dist/css'),
        sourcemaps.write('.', {includeContent: false, sourceRoot: '../../'}),
        gulp.dest(basedir + '/dist/css')
    ]);
});

gulp.task('js', ['vendor'], () => {
    return pump([
        gulp.src([
            basedir + '/vendor/jquery/dist/jquery.js',
            basedir + '/vendor/bootstrap/dist/js/bootstrap.js'
        ], { base: basedir }),
        sourcemaps.init(),

        concat('all.js'),
        // gulp.dest(basedir + '/dist/js'),

        uglify(),
        rename({ extname: '.min.js'}),
        sourcemaps.write('.', {includeContent: false, sourceRoot: '../../'}),
        gulp.dest(basedir + '/dist/js')
    ]);
});

gulp.task('rev', ['build'], () => {
    return pump([
        // gulp.src(basedir + '/dist/**/*.+(js|css|png|gif|jpg|jpeg|svg|woff|woff2|ttf|eot|ico)'),
        gulp.src(basedir + '/dist/**/*'),

        rev(),
        gulp.dest(basedir + '/dist'),

        rev.manifest(),
        gulp.dest(basedir + '/dist')
    ]);
});

gulp.task('rev-replace', ['rev'], () => {
    return pump([
        gulp.src(basedir + '/dist/**/*.+(js|css)'),
        revReplace({manifest: gulp.src(basedir + '/dist/rev-manifest.json')}),
        gulp.dest(basedir + '/dist')
    ]);
});
