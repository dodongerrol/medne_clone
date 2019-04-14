var app = angular.module('app', ['ui.router']);

app.config(function($stateProvider, $urlRouterProvider){
	$stateProvider
    .state('home', {
      url: '/home',
      data : { pageTitle: 'Home' },
      views: {
        'header': {
          templateUrl: '../assets/quote/templates/header.html'
        },
        'main': {
          templateUrl: '../assets/quote/templates/home.html'
        },
      },
    })

    $urlRouterProvider.otherwise('/home');
});


app.directive("regexInput", function(){
"use strict";
return {
    restrict: "A",
    // require: "?regEx",
    scope: {},
    replace: false,
    link: function(scope, element, attrs, ctrl){
      element.bind('keypress', function (event) {
        // var regex = new RegExp("^[a-zA-Z0-9]+$");
        var regex = /[0-9]|\./;
        var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
        var input = $('#first_input').val();
        if (!regex.test(key)) {
           event.preventDefault();
           return false;
        }

        if( input.length == 0 ){
          // $( "#alert-stat" ).text('Zip code is required to get a quote');
        }

        if( input.length > 4 ){
          event.preventDefault();
           return false;
        }

        if( input.length < 4 ){
          // $( "#alert-stat" ).text('Zip code must be 5 digits');
        }

        if( input.length == 4 ){
          // $( "#alert-stat" ).text('Unfortunately we aren’t offering plans in your area for 2017. Contact our partner, GoHealth, at 855-786-2825 or shop at gohealth.com/oscar for plans offered in your area.');
        }
      });
    }
};
});