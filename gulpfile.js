var gulp = require('gulp');
var concat = require('gulp-concat');
var ngAnnotate = require('gulp-ng-annotate');
var plumber = require('gulp-plumber');
var uglify = require('gulp-uglify');
var bytediff = require('gulp-bytediff');
var rename = require('gulp-rename');
var minifyCSS = require('gulp-minify-css');
var autoprefixer = require('gulp-autoprefixer');

gulp.task('app', function() {
	return gulp.src([
			'public/user_platform/process/controllers/mainCtrl.js',
			'public/user_platform/process/services/authService.js',
			'public/user_platform/process/services/benefitService.js',
			'public/user_platform/process/services/favouriteService.js',
			'public/user_platform/process/services/appointmentService.js',
			'public/user_platform/process/services/walletService.js',
			'public/user_platform/process/services/profileService.js',
			'public/user_platform/process/services/clinicService.js',
			'public/user_platform/process/app.js',
			'public/user_platform/process/directives/calendar.js',
			'public/user_platform/process/directives/home.js',
			'public/user_platform/process/directives/benefits.js',
			'public/user_platform/process/directives/appointments.js',
			'public/user_platform/process/directives/favourites.js',
			'public/user_platform/process/directives/profile.js',
			'public/user_platform/process/directives/wallet.js',
			'public/user_platform/process/directives/clinic-maps.js',
			'public/user_platform/process/directives/appointment-create.js',
			'public/user_platform/process/directives/ecommerce.js',
		])
		.pipe(plumber())
		.pipe(concat('app.js', {newLine: ';'}))
		.pipe(bytediff.start())
		.pipe(ngAnnotate({add: true}))
		.pipe(uglify({mangle: true}))
		.pipe(bytediff.stop())
		.pipe(rename('app.min.js'))
		.pipe(plumber.stop())
		.pipe(gulp.dest('public/production/'));
});


gulp.task('vendor', function() {
	return gulp.src([
			'public/user_platform/js/jquery.min.js',
			'public/user_platform/js/moment.min.js',
			'public/user_platform/js/moment-range.min.js',
			'public/user_platform/js/fullcalendar.js',
			'public/user_platform/js/bootstrap.min.js',
			'public/user_platform/js/angular.min.js',
			'public/user_platform/js/unsavedChanges.js',
			'public/user_platform/js/bootstrap-material-datetimepicker.js',
			'public/user_platform/js/angular-animate.min.js',
			'public/user_platform/js/ng-file-upload-shim.js',
			'public/user_platform/js/ng-file-upload.min.js',
			'public/user_platform/js/angular-ui-router.min.js',
			'public/user_platform/js/main.js',
			'public/user_platform/js/ng-image-appear.js',
			'public/user_platform/js/loading-bar.min.js',
			'public/user_platform/js/jquery-confirm.min.js',
			'public/user_platform/js/intlTelInput.min.js',
			'public/user_platform/js/utils.js'
		])
		.pipe(plumber())
		.pipe(concat('vendor.js', {newLine: ';'}))
		.pipe(bytediff.start())
		.pipe(ngAnnotate({add: true}))
		.pipe(uglify({mangle: true}))
		.pipe(bytediff.stop())
		.pipe(rename('vendor.min.js'))
		.pipe(plumber.stop())
		.pipe(gulp.dest('public/production/'));
});

gulp.task('style', function() {
	return gulp.src([
			'public/user_platform/css/style.css',
			'public/user_platform/css/responsive.css'
		])
		.pipe(minifyCSS())
		.pipe(autoprefixer('last 2 version', 'safari 5', 'ie 8', 'ie 9'))
		.pipe(bytediff.start())
		.pipe(concat('style.min.css'))
		.pipe(rename('style.min.css'))
		.pipe(bytediff.stop())
		.pipe(gulp.dest('public/production/'));
});

gulp.task('default', ['app', 'vendor', 'style']);
