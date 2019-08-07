var gulp        = require('gulp');
var sass        = require('gulp-sass');
var cleanCSS    = require('gulp-clean-css');
var merge       = require('merge-stream');
var concat      = require('gulp-concat');
var del         = require('del');
var uglify      = require('gulp-uglify');
var replace     = require('gulp-replace');

var version = '2.8.1.1';

gulp.task('default', [
    'main-css',
    'assets-css',
    'print-css',
    'backend-css',
    'clean:ds_stores',
    'compress',
    'dashboard-js'
]);

// Style.css
gulp.task('main-css', function () {
    return gulp.src('./scss/style.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(cleanCSS({ advanced : true }))
        .pipe(replace('2.8.1.1', version, {skipBinary : true}))
        .pipe(gulp.dest('./'));
});

// Assets.css
gulp.task('assets-css', function () {

    // Bootstrap
    var scssStream = gulp.src(['./scss/assets.scss'])
    .pipe(sass({
            outputStyle: 'nested',
            precison: 3,
            includePaths: [
                './node_modules/bootstrap/scss',
                './node_modules/@fortawesome/fontawesome-free/scss'
            ]
        }))
        .pipe(cleanCSS({ advanced : true }))
        .pipe(concat('scss-asssets.scss'));

    // Animate CSS & Bootstrap
    var cssStream = gulp.src([
        './../node_modules/animate.css/animate.min.css'
    ])
    .pipe(concat('css-asssets.css'));

    return merge(scssStream, cssStream)
        .pipe(concat('assets.min.css'))
        .pipe(gulp.dest('./css'));

});

// Print.css
gulp.task('print-css', function () {

    return gulp.src(['./scss/print.scss'])
        .pipe(sass())
        .pipe(cleanCSS({ advanced : true }))
        .pipe(concat('print.min.css'))
        .pipe(gulp.dest('./css'));

});

// Backend.css
gulp.task('backend-css', function () {

    return gulp.src(['./scss/backend.scss'])
        .pipe(sass())
        .pipe(cleanCSS({ advanced : true }))
        .pipe(concat('backend.min.css'))
        .pipe(gulp.dest('./css'));

});

// Removing all ds_stores files
gulp.task('clean:ds_stores', function () {
    return del([
        '.DS_store',
        '*/.DS_store',
        '*/**/.DS_store'
    ]);
});

// Scripts.js
gulp.task('compress', function () {
    // returns a Node.js stream, but no handling of error messages
    return gulp.src([
        'js/vue.min.js',
        'js/plugins.js',
        'js/scripts.js',
        'js/addableItems.vue.js'
    ])
        .pipe(uglify())
        .pipe(concat('woffice.min.js'))
        .pipe(gulp.dest('js/'));
});

// Dashboard.js
gulp.task('dashboard-js', function () {
    // returns a Node.js stream, but no handling of error messages
    return gulp.src([
            './../node_modules/draggabilly/dist/draggabilly.pkgd.min.js',
            './../node_modules/packery/dist/packery.pkgd.min.js'
        ])
        .pipe(uglify())
        .pipe(concat('dashboard.min.js'))
        .pipe(gulp.dest('js/'));
});

// Build command
gulp.task('deploy', ['default'], function() {
    require('fs').writeFileSync('../dist/version.txt', version);
    return gulp.src([
        './!(node_modules|plugins|dist)/**/*',
        '!./node_modules/',
        '!./plugins/',
        '!./dist/',
        '!.idea',
        '!.DS_store',
        '!*/.DS_store',
        '!*/**/.DS_store',
        '!.git',
        '!.gitignore',
        '!/.gitignore',
        '!/**/.gitignore',
        '!.gitmodules',
        '!*/.gitmodules',
        './!*/**/.gitmodules',
        './!(*.log)'
    ], { base : "." })
        .pipe(replace('2.8.1.1', version, {skipBinary : true}))
        .pipe(gulp.dest('../dist/'+version+'/woffice'))
});

// Watch task
gulp.task('watch', function () {
    gulp.watch('./scss/**/*.scss', ['main-css', 'assets-css'] );
    gulp.watch('./js/scripts.js', ['compress'] );
});