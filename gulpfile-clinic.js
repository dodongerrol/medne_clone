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
			'public/assets/userWeb/process/controllers/mainCtrl.js',
			'public/assets/userWeb/process/services/authService.js',
			'public/assets/userWeb/process/services/benefitService.js',
			'public/assets/userWeb/process/services/favouriteService.js',
			'public/assets/userWeb/process/services/appointmentService.js',
			'public/assets/userWeb/process/services/walletService.js',
			'public/assets/userWeb/process/services/profileService.js',
			'public/assets/userWeb/process/services/clinicService.js',
			'public/assets/userWeb/process/app.js',
			'public/assets/userWeb/process/directives/calendar.js',
			'public/assets/userWeb/process/directives/home.js',
			'public/assets/userWeb/process/directives/benefits.js',
			'public/assets/userWeb/process/directives/appointments.js',
			'public/assets/userWeb/process/directives/favourites.js',
			'public/assets/userWeb/process/directives/profile.js',
			'public/assets/userWeb/process/directives/wallet.js',
			'public/assets/userWeb/process/directives/clinic-maps.js',
			'public/assets/userWeb/process/directives/appointment-create.js',
			'public/assets/userWeb/process/directives/ecommerce.js',
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
			'public/assets/userWeb/js/jquery.min.js',
			'public/assets/userWeb/js/moment.min.js',
			'public/assets/userWeb/js/moment-range.min.js',
			'public/assets/userWeb/js/fullcalendar.js',
			'public/assets/userWeb/js/bootstrap.min.js',
			'public/assets/userWeb/js/angular.min.js',
			'public/assets/userWeb/js/unsavedChanges.js',
			'public/assets/userWeb/js/bootstrap-material-datetimepicker.js',
			'public/assets/userWeb/js/angular-animate.min.js',
			'public/assets/userWeb/js/ng-file-upload-shim.js',
			'public/assets/userWeb/js/ng-file-upload.min.js',
			'public/assets/userWeb/js/angular-ui-router.min.js',
			'public/assets/userWeb/js/main.js',
			'public/assets/userWeb/js/ng-image-appear.js',
			'public/assets/userWeb/js/loading-bar.min.js',
			'public/assets/userWeb/js/jquery-confirm.min.js',
			'public/assets/userWeb/js/intlTelInput.min.js',
			'public/assets/userWeb/js/utils.js'
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
			'public/assets/userWeb/css/style.css',
			'public/assets/userWeb/css/responsive.css'
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
