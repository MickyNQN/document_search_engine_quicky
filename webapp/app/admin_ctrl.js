/**
 * Created by heet on 09/04/2016.
 */
angular.module('homeApp')
	.controller('admin_ctrl', function ($http,$scope,$location) {
	    console.log("welcome admin");

	    $scope.doLogin=function(username, password){
			//$location.path("/document_master");
            console.log("in login client side: ");
            var postObject=new Object();
            postObject.username=username;
            postObject.password=password;
	        console.log(postObject);

	        $http({
	            method: "POST",
	            dataType: 'json',
	            url: "./userLogin",
	            data: postObject,
	            headers: {'Content-Type': 'application/json; charset=utf-8'}
	        }).then(function mySucces(response) {
		        $scope.data = response.data;
		        //console.log($scope.data.message);
		        if(response.data.data==1){
		        	$location.path("/document_master");
		        }
		        else{
		        	//$location.path("/document_master");
		        }
		    }, function myError(response) {
		        console.log(response.message);
		    });
	    }
	});