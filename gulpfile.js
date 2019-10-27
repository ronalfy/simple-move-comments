const gulp = require( 'gulp' );
const del = require( 'del' );
const run = require( 'gulp-run' );
const zip = require( 'gulp-zip' );

gulp.task( 'bundle', function() {
	return gulp.src( [
		'**/*',
		'!bin/**/*',
		'!node_modules/**/*',
		'!vendor/**/*',
		'!composer.*',
		'!release/**/*',
		'!src/**/*',
		'!src',
		'!tests/**/*',
		'!phpcs.xml'
	] )
		.pipe( gulp.dest( 'release/simple-move-comments' ) );
} );

gulp.task( 'remove:bundle', function() {
	return del( [
		'release/simple-move-comments',
	] );
} );

gulp.task( 'wporg:prepare', function() {
	return run( 'mkdir -p release' ).exec();
} );

gulp.task( 'release:copy-for-zip', function() {
	return gulp.src('release/simple-move-comments/**')
		.pipe(gulp.dest('simple-move-comments'));
} );

gulp.task( 'release:zip', function() {
	return gulp.src('simple-move-comments/**/*', { base: "." })
	.pipe(zip('simple-move-comments.zip'))
	.pipe(gulp.dest('.'));
} );

gulp.task( 'cleanup', function() {
	return del( [
		'release',
		'simple-move-comments'
	] );
} );

gulp.task( 'clean:bundle', function() {
	return del( [
		'release/simple-move-comments/bin',
		'release/simple-move-comments/node_modules',
		'release/simple-move-comments/vendor',
		'release/simple-move-comments/tests',
		'release/simple-move-comments/trunk',
		'release/simple-move-comments/gulpfile.js',
		'release/simple-move-comments/Makefile',
		'release/simple-move-comments/package*.json',
		'release/simple-move-comments/phpunit.xml.dist',
		'release/simple-move-comments/README.md',
		'release/simple-move-comments/CHANGELOG.md',
		'release/simple-move-comments/webpack.config.js',
		'release/simple-move-comments/.editorconfig',
		'release/simple-move-comments/.eslistignore',
		'release/simple-move-comments/.eslistrcjson',
		'release/simple-move-comments/.git',
		'release/simple-move-comments/.gitignore',
		'release/simple-move-comments/src/block',
		'package/prepare',
	] );
} );

gulp.task( 'default', gulp.series(
	'remove:bundle',
	'bundle',
	'wporg:prepare',
	'clean:bundle',
	'release:copy-for-zip',
	'release:zip',
	'cleanup'
) );
