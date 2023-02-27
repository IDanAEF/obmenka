const gulp        = require('gulp');
const browserSync = require('browser-sync');
const sass        = require('gulp-sass');
const cleanCSS = require('gulp-clean-css');
const autoprefixer = require('gulp-autoprefixer');
const rename = require("gulp-rename");
const webpack = require("webpack-stream");
//const imagemin = require('gulp-imagemin');
//const htmlmin = require('gulp-htmlmin');

gulp.task('server', function() {
    browserSync({
        server: {
            baseDir: "/"
        }
    });

    gulp.watch("assets/sass/**/*.+(scss|sass)").on('change', browserSync.reload);
});

const dist = "assets/js/";

gulp.task("build-js", () => {
    return gulp.src("assets/es6/main.js")
        .pipe(webpack({
            mode: 'development',
            output: {
                filename: 'script.js'
            },
            watch: false,
            devtool: "source-map",
            module: {
                rules: [
                    {
                    test: /\.m?js$/,
                    exclude: /(node_modules|bower_components)/,
                    use: {
                        loader: 'babel-loader',
                        options: {
                        presets: [['@babel/preset-env', {
                            corejs: 3,
                            useBuiltIns: 'usage'
                        }]]
                        }
                    }
                    }
                ]
                }
        }))
        .pipe(gulp.dest(dist));
});

gulp.task("build-prod-js", () => {
    return gulp.src("assets/es6/main.js")
        .pipe(webpack({
            mode: 'production',
            output: {
                filename: 'script.js'
            },
            module: {
                rules: [
                    {
                    test: /\.m?js$/,
                    exclude: /(node_modules|bower_components)/,
                    use: {
                        loader: 'babel-loader',
                        options: {
                        presets: [['@babel/preset-env', {
                            corejs: 3,
                            useBuiltIns: 'usage'
                        }]]
                        }
                    }
                    }
                ]
                }
        }))
        .pipe(gulp.dest(dist));
});

gulp.task('styles', function() {
    return gulp.src("assets/sass/**/*.+(scss|sass)")
        .pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
        .pipe(rename({suffix: '.min', prefix: ''}))
        .pipe(autoprefixer())
        .pipe(cleanCSS({compatibility: 'ie8'}))
        .pipe(gulp.dest("assets/css"))
        .pipe(browserSync.stream());
});

gulp.task('watch', function() {
    gulp.watch("assets/sass/**/*.+(scss|sass|css)", gulp.parallel('styles'));
    gulp.watch("assets/es6/**/*.js").on('change', gulp.parallel('build-js'));
});

gulp.task('default', gulp.parallel('watch', 'server', 'styles', 'build-js'));