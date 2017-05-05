/**
* This is the main build script
*/
var gulp 	 = require('gulp');
var rimraf   = require('rimraf');
var sequence = require('run-sequence');
var replace = require('gulp-replace');
var rename = require('gulp-rename');


var buildDir = './build';

var paths = {
	client: {
		root: "./client/ScorpionClient"
	},
	server: [                
		"./server/ScorpionServer/**/*",
                "!./server/ScorpionServer/**/",
                "./server/ScorpionServer/**.htaccess"
	    ]
}

gulp.task('clean', function(cb) {
	rimraf(buildDir, cb);
});

gulp.task('copy:symfony', ['clean'], function() {
	return gulp.src("./server/ScorpionServer/**/*").pipe(gulp.dest(buildDir));
});

gulp.task('clean:symfony', ['copy:symfony'], function(cb) {
	rimraf(buildDir+"/app/cache", function() {
		rimraf(buildDir+"/app/logs", cb);
	});
});

gulp.task('copy:client', ['clean:symfony'], function(cb) {
	gulp.src(['./client/ScorpionClient/build/**/*.*', '!./client/ScorpionClient/build/index.html'])
	.pipe(replace(/http:\/\/private-9fe3f9-scorpapi\.apiary-mock\.com/, "http://localhost:8000"))
	.pipe(gulp.dest(buildDir+'/web'));
        rimraf(buildDir+"/app/Resources//views/client/index.html.twig", function() {
            gulp.src(['./client/ScorpionClient/build/index.html'])
            .pipe(rename('index.html.twig'))
            .pipe(replace(/^(.+<link href=")(.+)(" rel.+)$/gm, "$1{{ asset('$2') }}$3"))
            .pipe(replace(/^(.+<script src=")(.+)(">.+)$/gm, "$1{{ asset('$2') }}$3"))            
            .pipe(gulp.dest('./build/app/Resources/views/client/'));
            cb();
        });
    });

gulp.task('build', function(cb) {
	sequence('copy:client', cb);
});

gulp.task('default', function() {
	//STUB
});