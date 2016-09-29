/**
 * Created by heet on 09/04/2016.
 */
angular.module('homeApp')
	.controller('home_ctrl', function ($http,$scope,$location) {

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

        $scope.doSearch=function(searchkey, searchQuery){
			//$location.path("/add_document");
            console.log("postObject at client side: ");
            var postObject=new Object();
            postObject.searchkey=searchkey;
            postObject.searchQuery="";
            postObject.searchDate="";

            if(searchQuery.keywords){
                postObject.searchQuery=postObject.searchQuery+"keywords,";
            }

            if(searchQuery.title){
            	postObject.searchQuery=postObject.searchQuery+"title,";
            }

            if(searchQuery.caption){
           		postObject.searchQuery=postObject.searchQuery+"caption,";
            }

            /*if(searchQuery.DoC){
           		postObject.searchQuery=postObject.searchQuery+"DoC,";
                postObject.searchDate=searchQuery.year;
            }*/

	        console.log(postObject);

	        $http({
	            method: "POST",
	            dataType: 'json',
	            url: "./search",
	            data: postObject,
	            headers: {'Content-Type': 'application/json; charset=utf-8'}
	        }).then(function mySucces(response) {
		        $scope.search_result = response.data;
		        console.log(response.data);
		        //$location.path("/search_result");
                
                //update the visitors of particualr keyword after successful retreival
                console.log(postObject.searchkey);
                $http({
                    method: "POST",
                    dataType: 'json',
                    url: "./updateVisits",
                    data: postObject,
                    headers: {'Content-Type': 'application/json; charset=utf-8'}
                }).then(function mySucces(response) {
                        //$scope.search_result = response.data;
                        console.log(response.data);
                        //$location.path("/search_result");
                }, function myError(response) {
                        console.log(response.message);
                });


		    }, function myError(response) {
		        console.log(response.message);
		    });
        }
        

	});
