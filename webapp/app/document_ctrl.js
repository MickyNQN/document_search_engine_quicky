/**
 * Created by heet on 09/04/2016.
 */
angular.module('homeApp')
    .controller('document_master_ctrl', function ($http,$scope,Data,$location,$route,$rootScope) {
        console.log("welcome admin in document_master_ctrl");

        $scope.add_new_document=function(){
            $location.path("/add_document");
        }

        $scope.logout=function(){
            //destroySession();
            $location.path("");
        }

        $scope.stats=function(){
            //destroySession();
            $location.path("/stats");
        }

        $scope.deleteDocument=function(document_id,document_thumbnail,document_url){
            //$location.path("/add_document");
            console.log("In deleteDocument function: ");
            var postObject=new Object();
            postObject.id=document_id;
            postObject.thumbnail=document_thumbnail;
            postObject.url=document_url;
            console.log(postObject);

            $http({
                method: "DELETE",
                dataType: 'json',
                url: "./deleteDocument",
                data: postObject,
                headers: {'Content-Type': 'application/json; charset=utf-8'}
            }).then(function mySucces(response) {
                //$scope.search_result = response.data;
                console.log(response.data + "deleted successfully");
                console.log(response.message);
                //$location.path("/document_master");
                $route.reload();
            }, function myError(response) {
                console.log(response.message);
            });
        }


        // Directly API called from front-end
        /*$scope.downloadDocument=function(document_url){
            //$location.path("/add_document");
            console.log("In downloadDocument function: ");
            var postObject=new Object();
            postObject.url=document_url;
            console.log(postObject);

            $http({
                method: "POST",
                dataType: 'json',
                url: "./download",
                data: postObject,
                headers: {'Content-Type': 'application/json; charset=utf-8'}
            }).then(function mySucces(response) {
                //$scope.search_result = response.data;
                //console.log(response.data + "deleted successfully");
                console.log(response);
                //$location.path("/document_master");
                $route.reload();
            }, function myError(response) {
                console.log(response.message);
            });
        }*/

        $scope.editDocument=function(document_id){
            $location.path("/edit_document");
            console.log("In downloadDocument function: ");
            var postObject = new Object();
            postObject.id=document_id;
            console.log(postObject);

            $http({
                method: "POST",
                dataType: 'json',
                url: "./getDocument",
                data: postObject,
                headers: {'Content-Type': 'application/json; charset=utf-8'}
            }).then(function mySucces(response) {
                $rootScope.data = response.data;
                console.log($rootScope.data);
            }, function myError(response) {
                console.log(response.message);
            });
        }


        Data.post('getAllDocuments').then(function (results) {
            if (results.status == 200) {
                $scope.document_master=results.data;
                console.log($scope.document_master);
            }else{
                console.log(results.message);
            }
        });
    });

angular.module('homeApp')
    .controller('search_result_ctrl', function ($scope,Data,$location) {
        console.log("welcome user in search_result_ctrl");
    });




angular.module('homeApp')
    .directive('fileModel', ['$parse', function ($parse) {
        return {
            restrict: 'A',
            link: function(scope, element, attrs) {
                var model = $parse(attrs.fileModel);
                var modelSetter = model.assign;

                element.bind('change', function(){
                    scope.$apply(function(){
                        modelSetter(scope, element[0].files[0]);
                    });
                });
            }
        };
    }]);

angular.module('homeApp')
    .service('fileUpload', ['$http',  function ($http) {
        this.uploadFileToUrl = function(file, uploadUrl){
            var fd = new FormData();
            fd.append('file', file);
            //console.log(file,uploadUrl);

            $http.post(uploadUrl, fd, {
                    transformRequest: angular.identity,
                    headers: {'Content-Type': undefined}
                })

                .success(function(){
                    //console.log("File successfully uploaded...!!");
                })

                .error(function(){
                });
        }
    }]);



angular.module('homeApp')
    .controller('add_document_ctrl', ['$scope', 'fileUpload', '$http', '$location', function($scope, fileUpload, $http, $location){
        console.log("welcome admin in add_document_ctrl");

        $scope.go_to_home=function(){
            $location.path("/document_master");
        }

        $scope.addDocument = function(){
            var file = $scope.files; //thumbnail
            var file1 = $scope.files1; //document

            console.dir(file);
            console.dir(file1);

            var uploadDocumentUrl = "./uploadDocument";
            var uploadThumbnailUrl="./uploadThumbnail";
            fileUpload.uploadFileToUrl(file, uploadThumbnailUrl);
            fileUpload.uploadFileToUrl(file1, uploadDocumentUrl);

            var postObject = new Object();
            postObject.thumbnailName=file.name;
            postObject.documentName=file1.name;
            postObject.documentType=file1.type;
            postObject.title = $scope.title;
            postObject.caption = $scope.caption;
            postObject.keywords = $scope.keywords;

            console.log(postObject);
            
            $http
            ({
                method: "POST",
                dataType: 'json',
                url: "./addDocument",
                data: postObject,
                headers: {'Content-Type': 'application/json'}
            }).then(function mySucces(response) {
                $scope.result=response.data.message;
                $scope.id=response.data.response.id;
                console.log($scope.result, $scope.id);
            }, function myError(response) {
                $scope.result=response.data.message;
                console.log($scope.result);
            });
        };
    }]);


angular.module('homeApp')
    .controller('edit_document_ctrl', ['$scope', 'fileUpload', '$http', '$rootScope', '$location', function($scope, fileUpload, $http, $rootScope, $location){
        console.log("welcome admin in edit_document_ctrl");

        //$scope.data=$rootScope.data;
        console.log($rootScope.data);

        $scope.go_to_home=function(){
            $location.path("/document_master");
        }


        $scope.updateDocument = function(data){
            //var file = $scope.thumbnail; //thumbnail
            //var file1 = $scope.document; //document

            console.log(data);
            var postObject = new Object();

            /*if(file)
            {
                console.dir(file);
                var uploadThumbnailUrl="./uploadThumbnail";
                fileUpload.uploadFileToUrl(file, uploadThumbnailUrl);
                postObject.thumbnailName=file.name;
            }

            if(file1)
            {
                console.dir(file1);
                var uploadDocumentUrl = "./uploadDocument";
                fileUpload.uploadFileToUrl(file1, uploadDocumentUrl);
                postObject.documentName=file1.name;
                postObject.documentType=file1.type;
            }*/

            postObject.id = data.id;
            postObject.title = data.title;
            postObject.caption = data.caption;
            postObject.keywords = data.keywords;

            console.log(postObject);
            
            $http
            ({
                method: "POST",
                dataType: 'json',
                url: "./updateDocument",
                data: postObject,
                headers: {'Content-Type': 'application/json'}
            }).then(function mySucces(response) {
                $scope.result=response.data;
                //$scope.id=response.data.response.id;
                console.log($scope.result, $scope.result.data);
            }, function myError(response) {
                $scope.result=response.data.message;
                console.log($scope.result);
            });
        };

    }]);




angular.module('homeApp')
    .controller('stats_ctrl', ['$scope', 'fileUpload', '$http', '$rootScope', '$location', function($scope, fileUpload, $http, $rootScope, $location){
        console.log("welcome admin in edit_document_ctrl");

        $scope.go_to_home=function(){
            $location.path("/document_master");
        }

        $http({
            method: "GET",
            url: "./getStats"
        }).then(function mySucces(response) {
            $scope.data = response.data;
            console.log(response.data);
            //$location.path("/search_result");
        }, function myError(response) {
            console.log(response.message);
        });


        console.log("Fetching trends: ");
        $http({
            method: "GET",
            url: "./getTrends"
        }).then(function mySucces(response) {
            $scope.trends = response.data;
            console.log(response.data);
            //$location.path("/search_result");
        }, function myError(response) {
            console.log(response.message);
        });

    }]);