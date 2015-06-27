'use strict';

/* Controllers */

  // Form controller
app.controller('FormDemoCtrl', ['$scope','$http','toaster','$stateParams', function($scope,$http,toaster,$stateParams) {
    var id = $stateParams.tenant_id;
  
   $http.get('api/homigo.php/tenants').then(function (resp) {

  $scope.tenant = resp.data.aaData[0];
  $scope.ds = $scope.tenant.entry_date;
  console.log($scope.tenant);
  
});
 
 $scope.update = function(tenant,ds){
tenant['entrydate'] = ds;
  $http.put('api/homigo.php/updatetenants/'+tenant.id,tenant).
        success(function(data, status) {
            toaster.pop('success', 'Update Tenant', 'Successfully updated Tenant');
          $scope.status = status;
          $scope.data = data;
          console.log($scope.data);
        }).
        error(function(data, status) {
          $scope.data = data || "Request failed";
          $scope.status = status;
      });

console.log(tenant);

 }

  }]);
 