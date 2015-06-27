'use strict';

/* Controllers */
  // signin controller
app.controller('SigninFormController', ['$rootScope', '$scope', '$http', '$state','LoginService', function($rootScope,$scope, $http, $state, LoginService) {
    $scope.user = {};
    $scope.authError = null;
 // console.log($rootScope.isLoggedIn);
    $scope.login = function(user){
    
   
      var test = LoginService.login($scope.user, $scope);
      test.then(function(response){

        console.log(response.data);
        if(response.data.login_success == 'true'){

        $state.go('app.tenant.details');

        }else{

          $scope.authError = response.data.message;

        }

        
      });
      
     
     
    
    // else $state.go('app.tenant.details');
  };
    //console.log($scope.authError);
  $scope.logout = function(){
    LoginService.logout();
  }
   /* $scope.login = function() {
      $scope.authError = null;
      // Try to login
      $http.post('api/login', {email: $scope.user.email, password: $scope.user.password})
      .then(function(response) {
        if ( !response.data.user ) {
          $scope.authError = 'Email or Password not right';
        }else{
          $state.go('app.dashboard-v1');
        }
      }, function(x) {
        $scope.authError = 'Server Error';
      });
    };*/
  }])
;