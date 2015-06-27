// config

var app =  
angular.module('app')
  .config(
    [        '$controllerProvider', '$compileProvider', '$filterProvider', '$provide',
    function ($controllerProvider,   $compileProvider,   $filterProvider,   $provide) {
        
        // lazy controller, directive and service
        app.controller = $controllerProvider.register;
        app.directive  = $compileProvider.directive;
        app.filter     = $filterProvider.register;
        app.factory    = $provide.factory;
        app.service    = $provide.service;
        app.constant   = $provide.constant;
        app.value      = $provide.value;
    }
  ])
  .config(['$translateProvider', function($translateProvider){
    // Register a loader for the static files
    // So, the module will search missing translation tables under the specified urls.
    // Those urls are [prefix][langKey][suffix].
    $translateProvider.useStaticFilesLoader({
      prefix: 'l10n/',
      suffix: '.js'
    });
    // Tell the module what language to use by default
    $translateProvider.preferredLanguage('en');
    // Tell the module to store the language in the local storage
    $translateProvider.useLocalStorage();
  }])
  .factory('Subject', ['$resource', function($resource) {
return $resource('api/index.php/subject/:subject_id', null,
    {
        'update': { method:'PUT' }
    });
}])
.factory('LoginService', ['$http', '$location', '$rootScope','$state', function($http,$location, $rootScope,$state){
  return {
    login: function(user, scope){
      //console.log(user);
      $rootScope.processGoingOn = true;
      var $promise = $http.post('api/homigo.php/auth/process/user', user); 
      return $promise;
       // send data to server to user.php
      // $promise.then(function(msg){
      //   var responseData = msg.data;
      
      //   if(responseData['login_success'] == 'true'){
          
      //     $rootScope.isLoggedIn = true;
      //     $rootScope.processGoingOn = false;
      //     return "success"
      //     // $('#login-modal').modal('hide');
      //   //  $state.go('app.tenant.details');
      //   }
      //   else{
      //       console.log(responseData);
      //     $rootScope.isLoggedIn = false;
      //     $rootScope.processGoingOn = false;
      //     return responseData['message'];
      //     scope.user = "";
      //   }
        
      // })
    },
    logout: function(){
    //  var $promise = SessionService.destroy('userId');
      //$promise.then(function(){
        //$rootScope.isLoggedIn = false;
      //});
    },
    isLoggedIn: function(){
     var $checkSessionServer = $http.get('api/homigo.php/auth/process/user');
      return $checkSessionServer;
    }
  }
}])
