app.controller('houseCtrl', ['$scope', '$http','$stateParams', function($scope, $http, $stateParams) {
    
    
   $http.get('api/homigo.php/houses').then(function (resp) {

  $scope.house = resp.data.aaData;
  



   });
   
 
}]);