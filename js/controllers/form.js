'use strict';

/* Controllers */

  // Form controller
app.controller('FormDemoCtrl', ['$scope','$http','toaster', function($scope,$http,toaster) {
    $scope.notBlackListed = function(value) {
      var blacklist = ['bad@domain.com','verybad@domain.com'];
      return blacklist.indexOf(value) === -1;
    }
   
    $scope.submitform = function(tenant){
       var defaultenant = {
              name : "",
              address : "",
              phone: "",
              entry_date :""
          };
console.log(tenant);
   $http.post('api/homigo.php/tenants',tenant).
        success(function(data, status) {
            toaster.pop('success', 'Add Tenant', 'Successfully added New Tenant');
          $scope.status = status;
          $scope.data = data;
          console.log($scope.data);
        }).
        error(function(data, status) {
          $scope.data = data || "Request failed";
          $scope.status = status;
      });
    
    $scope.addTenant.$setPristine();
    $scope.tenant = defaultenant;

    };
    $scope.submithouse = function(house,owner){
   var p = $('#entry_date').val();
      var d = $('#dth_date').val();
       var e= $('#power_date').val();
       house["entry_date"] = p;
       house['due_date']=p;
               house["dthbilldate"] = d;
         house["powerbilldate"] = e;
       console.log(house);
       var defaulthouse = {
              name : "",
              address : "",
              phone: "",
              entry_date :""
          };
 
   $http.post('api/homigo.php/owners',owner).
        success(function(data, status) {
           
          $scope.status = status;
          $scope.data = data;
          house['owners_id'] = $scope.data;
           $http.post('api/homigo.php/houses',house).
        success(function(data, status) {
            toaster.pop('success', 'Add House', 'Successfully added New House');
          $scope.status = status;
          $scope.data = data;
          console.log(data);
          
          
        });

          
        });
    
    $scope.addHouse.$setPristine();
    $scope.house = defaulthouse;

    };
    $scope.val = 15;
    var updateModel = function(val){
      $scope.$apply(function(){
        $scope.val = val;
      });
    };
    angular.element("#slider").on('slideStop', function(data){
      updateModel(data.value);
    });

    $scope.select2Number = [
      {text:'First',  value:'One'},
      {text:'Second', value:'Two'},
      {text:'Third',  value:'Three'}
    ];

    $scope.list_of_string = ['tag1', 'tag2']
    $scope.select2Options = {
        'multiple': true,
        'simple_tags': true,
        'tags': ['tag1', 'tag2', 'tag3', 'tag4']  // Can be empty list.
    };

    angular.element("#LinkInput").bind('click', function (event) {
      event.stopPropagation();
    });

  }])
 ;