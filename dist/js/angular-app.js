/*
TODO:
	> set up authentication
	> set up py files for download
	> set up custom lists and connect them to views
	> intgrate text files for movies
	> generate tree

*/


app = angular.module("app", ['ui.router','angularModalService','ngCookies']);


app.config(['$stateProvider', '$urlRouterProvider',
  function($stateProvider, $urlRouterProvider) {
    $stateProvider.
      state('main', {
      	url: '/main',
        templateUrl: 'main.html',
        controller: 'mainController'
      }).
      state('login', {
      	url: '/login',
        templateUrl: 'login.html',
        controller: 'loginController'
      }).
      state('main.dashboard', {
      	url: '/dashboard',
        templateUrl: 'files/dashboard.html',
        controller: 'dashboardController'
      }).
      state('main.directory', {
      	url: '/directory',
        templateUrl: 'files/directory.html',
        controller: 'directoryController'
      }).
      state('main.movies-top-rated', {
      	url: '/movies-new',
        templateUrl: 'files/thumbnails.html',
        controller: 'topRatedMoviesController'
      }).
      state('main.movies-popular', {
      	url: '/movies-popular',
        templateUrl: 'files/thumbnails.html',
        controller: 'popularMoviesController'
      }).
      state('main.movies-upcomming', {
      	url: '/movies-upcomming',
        templateUrl: 'files/thumbnails.html',
        controller: 'upcommingMoviesController'
      }).
      state('main.movies-watched', {
      	url: '/movies-watched',
        templateUrl: 'files/thumbnails.html',
        controller: 'watchedMoviesController'
      }).
      state('main.movies-not-watched', {
      	url: '/movies-not-watched',
        templateUrl: 'files/thumbnails.html',
        controller: 'notWatchedMoviesController'
      }).
      state('main.tv-top-rated', {
      	url: '/tv-new',
        templateUrl: 'files/thumbnails.html',
        controller: 'topRatedTVController'
      }).
      state('main.tv-popular', {
      	url: '/tv-popular',
        templateUrl: 'files/thumbnails.html',
        controller: 'popularTVController'
      }).
      state('main.tv-watched', {
      	url: '/tv-watched',
        templateUrl: 'files/thumbnails.html',
        controller: 'watchedTVController'
      }).
      state('main.tv-not-watched', {
      	url: '/tv-not-watched',
        templateUrl: 'files/thumbnails.html',
        controller: 'notWatchedTVController'
      }).
      state('main.lists', {
      	url: '/lists',
        templateUrl: 'files/lists.html',
        controller: 'listsController'
      });
      $urlRouterProvider.otherwise('/login')
  }]);

// app.config(function($sceDelegateProvider) {
//   $sceDelegateProvider.resourceUrlWhitelist([
//     // Allow same origin resource loads.
//     'self',
//     // Allow loading from Youtube.
//     'https://www.youtube.com/**'
//   ]);

// });

app.run(['$rootScope','$http', function ($rootScope, $http) {

	//get genre mappings on app start
	//alert("here")
    $http({
	        url: 'extras/genre.json',
    		method: "GET"
	    }).then(function successCallback(response) {
	        $rootScope.genre_mapping=response.data;

	    }, function errorCallback(response) {
	    	console.log("Failed to obtain data.")
	});

	$rootScope.getGenreMapping = function (genre_ids, category) {
        var genre_names = [];
        angular.forEach(genre_ids, function(genre_id){
        	angular.forEach($rootScope.genre_mapping[category], function (data) {
	            if (data.id == genre_id){
	            	genre_names.push(data.name)
	            }
	        });
        });
        return genre_names.join();

    }

}]);

app.factory("userData",['$http','$location', function($http, $location){

	var userData = {};
	userData.loggedIn = false;

	userData.isLoggedIn = function(){
		return userData.loggedIn;
	}

	userData.getUserData = function(){
		return $http({
	        url: 'dist/php/generic/account_details.php',
    		method: "GET",
	    }).then(function successCallback(response) {
			if(response.data!=-1)
			{
				userData.loggedIn = true;
		    	userData.user = response.data;
			}
			else{
				userData.loggedIn = false;
			}
	    
	    }, function errorCallback(response) {
	    	console.log("Failed to obtain data.")
	    });
	}

	return userData;

}]);

app.factory("authLogin",['$rootScope','$http','$location','userData', function($rootScope, $http, $location, userData){

	var authLogin = {};
	authLogin.loginError = false;
	authLogin.loading = false;

	authLogin.login = function(username, password){
		
		authLogin.loginError = false;
		authLogin.loading = true;

		return $http({
	        url: 'dist/php/generic/authentication.php',
			method: "POST",
			data: { 
				'username' : username ,
				'password' : password
			}
	    }).then(function successCallback(response) {
	    	
	    	//alert(response.data)
	        if(response.data!=-1){
	        	authLogin.loginError = false
	        	sessionStorage['session_id'] = response.data; 

	        	userData.getUserData().then(function(){
					sessionStorage['user'] = JSON.stringify(userData.user);
					authLogin.loading = false;
					$location.path('/main/dashboard')
				});

	        	//redirect
	        	
	        }
	        else{
	        	authLogin.loginError = true;
	        	authLogin.loading = false;
	        }
	    
	    }, function errorCallback(response) {

	    });
	}

	authLogin.logout = function(){

		//alert("here")
		$http({
	        url: 'dist/php/generic/logout.php',
    		method: "GET",
	    }).then(function successCallback(response) {
	        sessionStorage.clear();
	        $location.path('/login')

	    
	    }, function errorCallback(response) {
	    	console.log("Failed to obtain data.")
	    });

	}


	return authLogin;

}]);

app.factory("addToList",['$http','userData','ModalService','$q', function($http, userData, ModalService, $q){

	var addToList = {};

	addToList.addToFavourites = function(media_type,media_id){

		if(media_type == 'Movies'){
			media_type = 'movie';
		}
		else if(media_type == 'TV Shows'){
			media_type = 'tv';
		}

		$http({
	        url: 'dist/php/lists/add_fav.php?media_type='+ media_type +'&media_id='+ media_id,
    		method: "GET",
	    }).then(function successCallback(response) {
	        if(response.data != -1){

	        	userData.getUserData().then(function(){
					sessionStorage['user'] = JSON.stringify(userData.user);
				});

	        	alert("Successfully added to Favourites.")
	        }

	    
	    }, function errorCallback(response) {
	    	console.log("Failed to obtain data.")
	    });
	}

	addToList.addToWatchList = function(media_type,media_id){

		if(media_type == 'Movies'){
			media_type = 'movie';
		}
		else if(media_type == 'TV Shows'){
			media_type = 'tv';
		}

		$http({
	        url: 'dist/php/lists/add_watchlist.php?media_type='+ media_type +'&media_id='+ media_id,
    		method: "GET",
	    }).then(function successCallback(response) {
	        
	        if(response.data != -1){

	        	userData.getUserData().then(function(){
					sessionStorage['user'] = JSON.stringify(userData.user);
				});

	        	alert("Successfully added to Watch List.")
	        }

	    
	    }, function errorCallback(response) {
	    	console.log("Failed to obtain data.")
	    });
	}

	addToList.addToCustomList = function(media_id, list_id_array){

		promises = [];
		angular.forEach(list_id_array, function(list_id){
			p = $http({
		        url: 'dist/php/lists/add_movie.php?list_id='+ list_id +'&media_id='+ media_id,
	    		method: "GET",
		    })

		    promises.push(p);
			
		});

		$q.all(promises).then(function success(response){

        	userData.getUserData().then(function(){
				sessionStorage['user'] = JSON.stringify(userData.user);
			});

	    }, function failure(err){
	      	console.log("Failed to obtain data.")
	    });

	};

	addToList.openListModal = function(id, category){
		// alert(id)
		// $scope.modalService.setId(id);
		ModalService.showModal({
		    templateUrl: "files/listModal.html",
		    controller: "ListModalController",
		    inputs: {
                id: id
            }
		  }).then(function(modal) {
		    //it's a bootstrap element, use 'modal' to show it
		    modal.element.modal();
		  });

        // modalService.showModal({}, modalOptions);
	}

	return addToList;

}]);

app.factory("galleryFactory",['$http', 'ModalService', function($http, ModalService){

	var galleryFactory = {};

	galleryFactory.model = {
		heading: null,
		subheading: null,
		category: null,
		icon: null,
		poster_url: null,
	   	list: null
	 };

	galleryFactory.init = function(model,php_file){
		galleryFactory.model.heading = model.heading;
		galleryFactory.model.subheading = model.subheading;
		galleryFactory.model.category = model.category;
		galleryFactory.model.icon = model.icon;
		galleryFactory.model.poster_url = model.poster_url;
		

		return galleryFactory.populate(php_file)
	}

	galleryFactory.populate = function(php_file){

		return $http({
	        url: php_file,
    		method: "GET"
	    }).then(function successCallback(response) {
	        galleryFactory.model.list=response.data.results;
	    	
	    }, function errorCallback(response) {
	    	console.log("Failed to obtain data.")
	    });

	};

	galleryFactory.openModal = function(id, category){
		// alert(id)
		// $scope.modalService.setId(id);
		ModalService.showModal({
		    templateUrl: "files/modal.html",
		    controller: "ModalController",
		    inputs: {
                id: id, 
                category: category
            }
		  }).then(function(modal) {
		    //it's a bootstrap element, use 'modal' to show it
		    modal.element.modal();
		  });

        // modalService.showModal({}, modalOptions);
	}


	return galleryFactory;

}]);

app.controller("mainController", ['$rootScope', '$scope', '$http', function($rootScope, $scope, $http) {
	$rootScope.$on('$stateChangeStart', function(event, toState) {
	  	if ((toState.name !== 'login') && (!sessionStorage['session_id'])) {
	    	event.preventDefault();
	    	$state.go('login');
	  	}

	});

}]);

app.controller("headerController", ['$rootScope', '$scope', '$http', 'authLogin', function($rootScope, $scope, $http, authLogin) {

	$scope.user = JSON.parse(sessionStorage['user'])	

	$scope.logout = authLogin.logout;

}]);

app.controller("dashboardController", ['$rootScope', '$scope', '$http', 'galleryFactory', function($rootScope, $scope, $http, galleryFactory) {

	$scope.user = JSON.parse(sessionStorage['user'])	

	$scope.label_color = ['label-danger', 'label-success','label-info', 'label-warning','label-primary']

	var model = {
		heading: "Top Rated TV Shows",
		subheading: "Latest Releases and the Works",
		category: "TV Shows",
		icon: "fa-television",
		poster_url: 'https://image.tmdb.org/t/p/w300'
	}

	galleryFactory.init(model, 'dist/php/tv/top_rated.php').then(function(){
		$scope.tv = galleryFactory.model;
		//alert(JSON.stringify($scope.tv))
	});

	var model = {
		heading: "Top Rated Movies",
		subheading: "Latest Releases and the Works",
		category: "Movies",
		icon: "fa-film",
		poster_url: 'https://image.tmdb.org/t/p/w300'
	}

	galleryFactory.init(model, 'dist/php/movies/top_rated.php').then(function(){
		$scope.movie = galleryFactory.model;
		//alert(JSON.stringify($scope.tv))
	});
	//alert(JSON.stringify($rootScope.user))
	
}]);

app.controller("directoryController", ['$rootScope', '$scope', '$http','addToList', function($rootScope, $scope, $http, addToList) {

	$scope.user = JSON.parse(sessionStorage['user'])

	$scope.myFileUpload = null;	

	$scope.fileUpload = function(){

		var file = $scope.myFile;

        var data = new FormData();
        data.append('file', file);

        $http.post('dist/php/core/read_json.php',data,{
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
            
        }).then(function successCallback(response) {
	        if(response.data !=-1){
	        	alert("File Upload Successful")
	        	$scope.displayResults();
	        }
	        else{
	        	alert("File Upload Unsuccessful")
	        }
	    	
	    }, function errorCallback(response) {
	    	console.log("Failed to obtain data.")
	    });

		
		
	}

	$scope.displayResults = function(){

		$http({
			//chaneg this to appropriate name
            url: 'dist/php/core/output.json?rnd='+new Date().getTime(),
            method: "GET",
        }).then(function successCallback(response) {
	        $scope.result = response.data;
	    	
	    }, function errorCallback(response) {
	    	console.log("Failed to obtain data.")
	    });

	}

	$scope.openListModal = addToList.openListModal;

	$scope.displayResults();

}]);

app.directive('fileModel', ['$parse', function ($parse) {
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



// controllers for movies and tv shows

// movie controllers
app.controller("topRatedMoviesController", ['$rootScope', '$scope', 'galleryFactory','addToList', function($rootScope, $scope, galleryFactory, addToList) {

	var model = {
		heading: "Top Rated Movies",
		subheading: "Latest Releases and the Works",
		category: "Movies",
		icon: "fa-film",
		poster_url: 'https://image.tmdb.org/t/p/w300'
	}

	galleryFactory.init(model,'dist/php/movies/top_rated.php').then(function(){
		$scope.model = galleryFactory.model;
	});
	
	$scope.openModal = galleryFactory.openModal;

	$scope.addToFavourites = addToList.addToFavourites;

	$scope.addToWatchList = addToList.addToWatchList;

	$scope.openListModal = addToList.openListModal;
	//alert("here")
}]);

app.controller("popularMoviesController", ['$rootScope', '$scope', 'galleryFactory','addToList', function($rootScope, $scope, galleryFactory, addToList) {

	var model = {
		heading: "Popular Movies",
		subheading: "What everyone is talking about",
		category: "Movies",
		icon: "fa-film",
		poster_url: 'https://image.tmdb.org/t/p/w300'
	}

	galleryFactory.init(model,'dist/php/movies/popular.php').then(function(){
		$scope.model = galleryFactory.model;
		console.log($scope.model)
	});
	
	$scope.openModal = galleryFactory.openModal;

	$scope.addToFavourites = addToList.addToFavourites;

	$scope.addToWatchList = addToList.addToWatchList;

	$scope.openListModal = addToList.openListModal;
	//alert("here")
}]);

app.controller("upcommingMoviesController", ['$rootScope', '$scope', 'galleryFactory','addToList', function($rootScope, $scope, galleryFactory, addToList) {

	var model = {
		heading: "Uocomming Movies",
		subheading: "Stay Tuned for what's next",
		category: "Movies",
		icon: "fa-film",
		poster_url: 'https://image.tmdb.org/t/p/w300'
	}

	galleryFactory.init(model,'dist/php/movies/upcoming.php').then(function(){
		$scope.model = galleryFactory.model;
		console.log($scope.model)
	});
	
	$scope.openModal = galleryFactory.openModal;

	$scope.addToFavourites = addToList.addToFavourites;

	$scope.addToWatchList = addToList.addToWatchList;

	$scope.openListModal = addToList.openListModal;
	//alert("here")
}]);

app.controller("watchedMoviesController", ['$rootScope', '$scope', 'galleryFactory','addToList', function($rootScope, $scope, galleryFactory, addToList) {

	$scope.user = JSON.parse(sessionStorage['user'])	

	var model = {
		heading: "Watched Movies",
		subheading: "Keep track of whats already seen",
		category: "Movies",
		icon: "fa-film",
		poster_url: 'https://image.tmdb.org/t/p/w300'
	}

	galleryFactory.init(model, 'dist/php/new-movies.php').then(function(){
		$scope.model = galleryFactory.model;
		console.log($scope.model)
	});
	
	$scope.openModal = galleryFactory.openModal;

	$scope.addToFavourites = addToList.addToFavourites;
	//alert("here")
}]);

app.controller("notWatchedMoviesController", ['$rootScope', '$scope', 'galleryFactory','addToList', function($rootScope, $scope, galleryFactory, addToList) {

	$scope.user = JSON.parse(sessionStorage['user'])	

	var model = {
		heading: "Movies Not Yet Watched",
		subheading: "Better start watching!",
		category: "Movies",
		icon: "fa-film",
		poster_url: 'https://image.tmdb.org/t/p/w300'
	}

	galleryFactory.init(model, 'dist/php/new-movies.php').then(function(){
		$scope.model = galleryFactory.model;
		console.log($scope.model)
	});
	
	$scope.openModal = galleryFactory.openModal;

	$scope.addToFavourites = addToList.addToFavourites;

	$scope.openListModal = addToList.openListModal;
	//alert("here")
}]);

// movie controllers
app.controller("topRatedTVController", ['$rootScope', '$scope', 'galleryFactory','addToList', function($rootScope, $scope, galleryFactory, addToList) {

	var model = {
		heading: "Top Rated TV Shows",
		subheading: "Latest Releases and the Works",
		category: "TV Shows",
		icon: "fa-television",
		poster_url: 'https://image.tmdb.org/t/p/w300'
	}

	galleryFactory.init(model, 'dist/php/tv/top_rated.php').then(function(){
		$scope.model = galleryFactory.model;
		console.log($scope.model)
	});
	
	$scope.openModal = galleryFactory.openModal;

	$scope.addToFavourites = addToList.addToFavourites;

	$scope.addToWatchList = addToList.addToWatchList;
	//alert("here")
}]);

app.controller("popularTVController", ['$rootScope', '$scope', 'galleryFactory','addToList', function($rootScope, $scope, galleryFactory, addToList) {

	var model = {
		heading: "Popular TV Shows",
		subheading: "What everyone is talking about",
		category: "TV Shows",
		icon: "fa-television",
		poster_url: 'https://image.tmdb.org/t/p/w300'
	}

	galleryFactory.init(model,'dist/php/tv/popular.php').then(function(){
		$scope.model = galleryFactory.model;
		console.log($scope.model)
	});
	
	$scope.openModal = galleryFactory.openModal;

	$scope.addToFavourites = addToList.addToFavourites;

	$scope.addToWatchList = addToList.addToWatchList;
	//alert("here")
}]);

app.controller("watchedTVController", ['$rootScope', '$scope', 'galleryFactory','addToList', function($rootScope, $scope, galleryFactory, addToList) {

	$scope.user = JSON.parse(sessionStorage['user'])	

	var model = {
		heading: "Watched TV Shows",
		subheading: "Keep track of whats already seen",
		category: "TV Shows",
		icon: "fa-television",
		poster_url: 'https://image.tmdb.org/t/p/w300'
	}

	galleryFactory.init(model,'dist/php/new-movies.php').then(function(){
		$scope.model = galleryFactory.model;
		console.log($scope.model)
	});
	
	$scope.openModal = galleryFactory.openModal;

	$scope.addToFavourites = addToList.addToFavourites;
	//alert("here")
}]);

app.controller("notWatchedTVController", ['$rootScope', '$scope', 'galleryFactory','addToList', function($rootScope, $scope, galleryFactory, addToList) {

	$scope.user = JSON.parse(sessionStorage['user'])	

	var model = {
		heading: "TV Shows Not Yet Watched",
		subheading: "Better start watching!",
		category: "TV Shows",
		icon: "fa-television",
		poster_url: 'https://image.tmdb.org/t/p/w300'
	}

	galleryFactory.init(model,'dist/php/new-movies.php').then(function(){
		$scope.model = galleryFactory.model;
		console.log($scope.model)
	});
	
	$scope.openModal = galleryFactory.openModal;

	$scope.addToFavourites = addToList.addToFavourites;
	//alert("here")
}]);

app.filter('youtubeEmbedUrl', function ($sce) {
    return function(videoid,height,width) {
      return $sce.trustAsResourceUrl('https://www.youtube.com/embed/'+videoid+'?autoplay=0&amp;html5=1&amp;theme=light&amp;modesbranding=0&amp;' +
    'color=white&amp;iv_load_policy=3&amp;showinfo=1&amp;controls=1&amp;enablejsapi=1&amp;widgetid=1');
    };
  });

app.directive('youtube', ['$window',function($window) {
  
  return {
    restrict: "E",

    scope: {
      height:   "@",
      width:    "@",
      videoid:  "@"  
    },

    template: '<iframe allowfullscreen="1" title="YouTube video player"' +
    'ng-src="{{videoid | youtubeEmbedUrl:height:width}}" id="widget2" width="{{width}}" height="{{height}}" frameborder="0"></iframe>' 
 
  }
}]);


app.controller('ModalController', ['$scope', '$http', 'close', 'id','category', function($scope, $http, close, id, category) {

	$scope.id = id;
	$scope.category = category;
	$scope.poster_url = 'https://image.tmdb.org/t/p/w500'
	$scope.result = null;
	$scope.label_color = ['label-danger', 'label-success','label-info', 'label-warning','label-primary']

	$scope.populate = function(){
		
		if($scope.category == "Movies"){
			$http({
		        url: 'dist/php/movies/movie_details.php?movie_id='+$scope.id,
	    		method: "GET"
		    }).then(function successCallback(response) {
		        $scope.result=response.data;
		        console.log($scope.result)
		    	
		    }, function errorCallback(response) {
		    	console.log("Failed to obtain data.")
		    });
		}
		else if($scope.category == "TV Shows"){
			$http({
		        url: 'dist/php/tv/tv_details.php?tv_id='+$scope.id,
	    		method: "GET"
		    }).then(function successCallback(response) {
		        $scope.result=response.data;
		        console.log($scope.result)
		    	
		    }, function errorCallback(response) {
		    	console.log("Failed to obtain data.")
		    });
		}
	    
	}

  	// when you need to close the modal, call close
   	$scope.close = function(result) {
	 	close(result, 500); // close, but give 500ms for bootstrap to animate
	};
	$scope.populate();	
	
}]);

app.controller('ListModalController', ['$scope', '$http', 'close', 'id','addToList', function($scope, $http, close, id, addToList) {	

	$scope.user = JSON.parse(sessionStorage['user'])

	$scope.id = id;

	$scope.selection = [];

	$scope.toggleSelection = function toggleSelection(list_id) {
	    var idx = $scope.selection.indexOf(list_id);

	    // is currently selected
	    if (idx > -1) {
	      $scope.selection.splice(idx, 1);
	    }

	    // is newly selected
	    else {
	      $scope.selection.push(list_id);
	    }
	}

	$scope.addToCustomList = addToList.addToCustomList;

	// when you need to close the modal, call close
   	$scope.close = function(result) {
	 	close(result, 500); // close, but give 500ms for bootstrap to animate
	};
}]);

app.controller("listsController",['$rootScope', '$scope', '$http', 'userData',function($rootScope, $scope, $http, userData){

	$scope.user = JSON.parse(sessionStorage['user'])	

	$scope.name = "";
	$scope.description ="";
	$scope.addError = false;
	$scope.listItems = null;

	$scope.showItems = function(id){
		$http({
		        url: 'dist/php/lists/get_list.php?list_id='+id,
				method: "GET"
		    }).then(function successCallback(response) {
		    	
		    	//alert(response.data)
		        if(response.data!=-1){
		        	$scope.listItems = response.data;
		        }
		    
		    }, function errorCallback(response) {

		    });
	}

	$scope.addList = function(){
		
		if($scope.name==""){
			$scope.addError = true
		}
		else if($scope.description==""){
			$scope.addError = true
		}
		else{

			$http({
		        url: 'dist/php/lists/create_list.php?name='+$scope.name+'&description='+$scope.description,
				method: "GET"
		    }).then(function successCallback(response) {
		    	
		    	//alert(response.data)
		        if(response.data!=-1){
		        	$scope.addError = false

		        	$scope.name = "";
					$scope.description ="";

					userData.getUserData().then(function(){
						sessionStorage['user'] = JSON.stringify(userData.user);
						$scope.user = JSON.parse(sessionStorage['user'])
					});
		        	
		        }
		        else{
		        	$scope.addError = true
		        }
		    
		    }, function errorCallback(response) {

		    });
		}

 //        // modalService.showModal({}, modalOptions);
	}

}]);

app.controller("loginController", ['$rootScope', '$scope', '$cookieStore','authLogin', function($rootScope, $scope, $cookieStore, authLogin) {

	$scope.username = "";
	$scope.password= "";
	$scope.loginError = authLogin.loading;
	// $rootscope.logginIn = false;
	

	$scope.validate = function(){
		//validates the data from the front end
		if($scope.username==""){
			$scope.loginError = true
		}
		else if($scope.password==""){
			$scope.loginError = true
		}
		else{

			$scope.loading = true
			authLogin.login($scope.username, $scope.password).then(function(){
				$scope.loginError = authLogin.loginError;
				$scope.loading = authLogin.loading;
			});


		}
	}
}]);