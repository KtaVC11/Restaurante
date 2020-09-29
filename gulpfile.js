const path = "wp-content/themes/baum-restaurante-child/",
  gulp = require("gulp"),
  del = require("del"),
  iife = require("gulp-iife"),
  sourcemaps = require("gulp-sourcemaps"),
  sass = require("gulp-sass"),
  util = require("gulp-util"),
  rename = require("gulp-rename"),
  uglify = require("gulp-uglify"),
  uglifycss = require("gulp-uglifycss"),
  connect = require("browser-sync").create(),
  concat = require("gulp-concat"),
  sass_src = [path + "source/sass/*.scss", path + "source/sass/**/*.scss"],
  plugin_js_src = [
    // path + "source/plugins/sticky-kit/sticky-kit.js",
  ],
  js_src = [
    path + "source/js/*.js",
    path + "source/js/**/*.js",
    path + "source/js/**/**/*.js",
  ];

function PluginJs() {
  if (plugin_js_src.length == 0) {
    return del(path + "assets/js/plugin*");
  }
  return gulp
    .src(plugin_js_src, { allowEmpty: true })
    .pipe(concat("plugin.js"))
    .pipe(gulp.dest(path + "assets/js"))
    .pipe(rename("plugin.min.js"))
    .pipe(uglify().on("error", util.log))
    .pipe(gulp.dest(path + "assets/js"));
}

function Concat() {
  return gulp
    .src(js_src)
    .pipe(concat("all.js"))
    .pipe(gulp.dest(path + "assets/js"))
    .pipe(
      iife({
        useStrict: false,
        trimCode: false,
        prependSemicolon: false,
        params: ["$"],
        args: ["jQuery"],
      })
    )
    .pipe(gulp.dest(path + "assets/js"))

    .pipe(rename("all.min.js"))
    .pipe(uglify().on("error", util.log))
    .pipe(gulp.dest(path + "assets/js"));
}

function Sass() {
  return gulp
    .src(path + "source/sass/*.scss")
    .pipe(sourcemaps.init())
    .pipe(sass({ outputStyle: "expanded" }).on("error", sass.logError))
    .pipe(sourcemaps.write("."))
    .pipe(gulp.dest(path + "assets/css"))
    .pipe(rename("theme.min.css"))
    .pipe(
      uglifycss({
        maxLineLen: 500,
        uglyComments: true,
      })
    )
    .pipe(gulp.dest(path + "assets/css"));
}

function clearCache() {
  return del(["wp-content/cache/*", "!wp-content/cache/index.html"]);
}

function Serve() {
  gulp.watch("**/*.php").on("change", connect.reload);
  gulp.watch(sass_src, gulp.parallel(Sass)).on("change", connect.reload);
}

const concatjs = gulp.series(PluginJs, Concat);
const sasscss = gulp.series(Sass, clearCache);
const build = gulp.parallel(concatjs, clearCache, Sass);
const watch = gulp.parallel(Serve);

gulp.task("default", build);
gulp.task("watch", watch);
gulp.task("Concat", concatjs);
gulp.task("Sass", sasscss);
