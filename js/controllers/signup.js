'use strict';

// signup controller
app.controller('SignupFormController', ['$scope', '$http', '$state','$filter','FileUploader', function($scope, $http, $state,$filter,FileUploader) {
var uploader = $scope.uploader = new FileUploader({
        url: 'api/homigo.php/doupload'
    });
 $scope.imageDiv = 0;
uploader.filters.push({
            name: 'imageFilter',
            fn: function(item /*{File|FileLikeObject}*/, options) {
                var type = '|' + item.type.slice(item.type.lastIndexOf('/') + 1) + '|';
                return '|jpg|jpeg|'.indexOf(type) !== -1;
            }
        });
 uploader.onWhenAddingFileFailed = function(item /*{File|FileLikeObject}*/, filter, options) {
        console.info('onWhenAddingFileFailed', item, filter, options);
    };
    uploader.onAfterAddingFile = function(fileItem) {
        uploader.uploadAll();
        $scope.progressBar = 1;
        console.info('onAfterAddingFile', fileItem);
    };
    uploader.onAfterAddingAll = function(addedFileItems) {
        console.info('onAfterAddingAll', addedFileItems);
    };
    uploader.onBeforeUploadItem = function(item) {
        console.info('onBeforeUploadItem', item);
    };
    uploader.onProgressItem = function(fileItem, progress) {
        console.info('onProgressItem', fileItem, progress);
       $scope.text = "uploading";
       $scope.dynamic = progress;
       
    };
    uploader.onProgressAll = function(progress) {
       
        console.info('onProgressAll', progress);
    };
    uploader.onSuccessItem = function(fileItem, response,headers, status) {
        console.log("dfsf");
        console.info('onSuccessItem', fileItem, response, status);
    };
    uploader.onErrorItem = function(fileItem, response, status) {
        console.info('onErrorItem', fileItem, response, status);
    };
    uploader.onCancelItem = function(fileItem, response, status) {
        console.info('onCancelItem', fileItem, response, status);
    };
    uploader.onCompleteItem = function(fileItem, response, status) {
          $scope.imageDiv = 1;
          $scope.progressBar = 0;
          $scope.isUploaded = 1;
          $scope.text = "uploaded";
        $scope.filename = response.filename;
        console.info('onCompleteItem', fileItem, response, status);
    };
    uploader.onCompleteAll = function() {
        console.info('onCompleteAll');
    };

    console.info('uploader', uploader);
 $scope.user = {};
    $scope.authError = null;
    $scope.signup = function(user) {
       user['entry_date'] = $filter('date')(new Date(), 'yyyy-MM-dd');
      $scope.authError = null;
      var defaultdata = {
              name : "",
              address : "",
              phone: "",
              company :"",
              email :"",
              password :"",
              entry_date:""
          };
      // Try to create
      $http.post('api/homigo.php/newtenant', user)
      .then(function(response) {
       
          
          $scope.authError = response.data.message;
          if(response.data.status != 'false'){
          $scope.form.$setPristine();
    $scope.user = defaultdata;
$scope.imageDiv = 0;
          }
       
      }, function(x) {
        $scope.authError = 'Server Error';
      });
    };
  }])
 ;