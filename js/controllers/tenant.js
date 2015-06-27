app.controller('tenantCtrl', ['$scope', '$http','$modal','$stateParams','$log','$filter', function($scope, $http, $modal, $stateParams,$log,$filter) {
    var id = $stateParams.tenant_id;
    $scope.url = id;
   $http.get('api/homigo.php/tenants').then(function (resp) {

  $scope.tenant = resp.data.aaData[0];
  $scope.Days = function (date) {

       var _date = $filter('date')(new Date(), 'yyyy-MM-dd');
       console.log(_date);
       if(_date === undefined || date === undefined){
                    
                }else{

         var dt1 = _date.split('-'),
            dt2 = date.split('-');

           var  one = new Date(dt1[0], dt1[1], dt1[2]),
            two = new Date(dt2[0], dt2[1], dt2[2]);

        
        var millisecondsPerDay = 1000 * 60 * 60 * 24;
        var millisBetween = two.getTime() - one.getTime();
        var days = millisBetween / millisecondsPerDay;
    
        return Math.floor(days); }     
    };
console.log($scope.Days('26-8-2015'));
$scope.totaldepositpaid = function (data) {
    if(data != undefined){
     var total = 0;
    for(var i = 0; i < data.length; i++){
        var product = data[i];
        if(product.status == '1'){
        total += parseInt(product.rent);}
    }    }
    return total;
    };

    

  


 
    $scope.open = function (size) {
      var modalInstance = $modal.open({
        templateUrl: 'myModalContent.html',
        controller: 'ModalInstanceCtrl',
        size: size,
        resolve: {
          items: function () {
            return $scope.tenant;
          }
        }
      });

      modalInstance.result.then(function (selectedItem) {
        $scope.selected = selectedItem;
      }, function () {
        $log.info('Modal dismissed at: ' + new Date());
      });
    };
 
   });
}]);
app.controller('ModalInstanceCtrl', ['$scope', '$modalInstance','$http', 'items', function($scope, $modalInstance,$http, items) {
    $scope.items = items;
    $scope.selected = {
      item: $scope.items[0]
    };

    $scope.ok = function () {
       var currentdate =  $scope.items.entry_date;
       var test =  currentdate.split("-");
        var newmonth=test[1];
        var newyear = test[2];
       if(newmonth<12){
        newmonth++;
        
     ;}else{
        var newmonth= 1;
        newyear++;
      }
       var newdate = test[0] +"-"+newmonth+"-"+newyear;
       $scope.items.entry_date = newdate;
      
       //$scope.items['_METHOD'] = "PUT";
       $http.put('api/homigo.php/tenants/'+items.id,items).
        success(function(data, status) {
           $modalInstance.close($scope.selected.item);

            //toaster.pop('success', 'Add Tenant', 'Successfully added New Tenant');
          $scope.status = status;
          $scope.data = data;
          console.log($scope.data);
        }).
        error(function(data, status) {
          $scope.data = data || "Request failed";
          $scope.status = status;
      });
     
    };

    $scope.cancel = function () {
      $modalInstance.dismiss('cancel');
    };
  }])
  ; 