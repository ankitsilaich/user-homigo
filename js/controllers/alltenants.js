app.controller('houseCtrl', ['$scope', '$http','$stateParams', function($scope, $http, $stateParams) {
    
    
   $http.get('api/homigo.php/tenants').then(function (resp) {

  $scope.tenant = resp.data.aaData;
  
  console.log($scope.tenant);



   });
   $scope.test = "ankit";
}]);