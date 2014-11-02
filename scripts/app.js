var app = angular.module("oneword", ['dotjem.routing', "luegg.directives"]);
var availPages = [
{
	path: "/home",
	name: "home",
	page: "page/home.html"
},
{
	path: "/session",
	name: "session",
	page: "page/session.html"
},
{
	path: "/start",
	name:"start",
	page: "page/start.html"
}

];

app.directive("ngSend", function(){
	return function(scope, element, attr){
			element.bind("keyup", function(event){
				if(event.which === 13){
					if(!scope.player.canSend) return;
					scope.sendMessage(scope.messageContent);
					element.val('');
				}
		});
	}
});

app.factory('apiFactory', ['$http', function($http){
	var apiFactory = {};
	
	apiFactory.startNewSession = function(){
		return $http({
			method: "POST",
			url: "api/newsession.php",
			headers: {"Content-Type": "application/x-www-form-urlencoded"}
		});
	}

	apiFactory.initUser = function(){
		return	$http({
			method: "POST",
			url: "api/new_user.php",
			headers: {"Content-Type": "application/x-www-form-urlencoded"}
		});
	};
	
	apiFactory.joinSession = function(session){
		return $http({
			method: "POST",
			url: "api/join_session.php",
			data: $.param({"session":session}),
			headers:{"Content-Type": "application/x-www-form-urlencoded"}
		});
	};

	apiFactory.hasPlayerJoined = function(session){
		return $http({
			method: "POST",
			url: "api/sessionstatus.php",
			data: $.param({"session":session}),
			headers: {"Content-Type": "application/x-www-form-urlencoded"}
		});
	};
	
	apiFactory.sendMessage = function(session, message){
		return $http({
			method: "POST",
			url: "api/addmessage.php",
			data: $.param({"message":message, "session":session}),
			headers: {"Content-Type": "application/x-www-form-urlencoded"}
		});
	};
	
	apiFactory.checkMessages = function(session, id){
		return $http({
			method: "POST",
			url: "api/getmessages.php",
			data: $.param({"session":session, "id":id}),
			headers: {"Content-Type": "application/x-www-form-urlencoded"}
		});
	};

	apiFactory.getAllMessages = function(session) {
		if(session == null) return;

		return $http({
			method:"POST",
			url:"api/getallmessages.php",
			data: $.param({"session":session}),
			headers:{"Content-Type": "application/x-www-form-urlencoded"}
		});
	} ;

	apiFactory.getMessagesFrom = function(session, id){
		return $http({
			method: "POST",
			url: "api/getmessages.php",
			data: $.param({"session":session, "id":id}),
			headers: {"Content-Type": "application/x-www-form-urlencoded"}
		});
	};

	apiFactory.getShortURL = function(){
		return $http({
			method: "POST",
			url:"https://www.googleapis.com/urlshortener/v1/url",
			data:{"longUrl":document.URL},
			headers:{"Content-Type":"application/json"}
		});
	}

	return apiFactory;
}]);

app.config(function($locationProvider, $routeProvider, $stateProvider){
	$routeProvider.otherwise({redirectTo: '/home'});

	for(state in availPages){
		$stateProvider.state(availPages[state].name, {
			route: availPages[state].path,
			views: {
				'main': {
					template: availPages[state].page
				}
			}
		});
	}
	
});

app.controller("mainController", function($scope, $http, $routeParams){

});

app.controller("startSess", function($scope, $http, $state, apiFactory){
	var self = this;
	var thisScope = $scope;
	self.state = $state;

	$scope.startNewSession = function(){
		apiFactory.startNewSession().success(function(reply){
			if(reply.status == "complete") $state.goto('session', {session:reply.sessionid});
		});
	};
});

app.controller("sessionCtrl", function($http, $scope, $routeParams, $state, $interval, $location, apiFactory){
	var ctrl = this;
	var session = $routeParams.session;
	
	$scope.messages = [];
	
	$scope.player = {};
	$scope.player.rank = "";
	$scope.player.uid;
	$scope.player.canSend = false;
	
	this.isEmpty = function(json){
		for(item in json){
			return false;
		}
		return true;
	};

	this.onPlayerJoin = function(callback){
			ctrl.playerJoin = $interval(function(){
				apiFactory.hasPlayerJoined(session).success(function(reply){

				if(reply.status == 1){
					callback(reply);
					$interval.cancel(ctrl.playerJoin);
				}
			});
		}, 600, 0);
	};

	this.getMessages = function(callback){
		ctrl.messageCheck = $interval(function(){
			if(ctrl.isEmpty($scope.messages)){
				apiFactory.getAllMessages(session).success(function(reply){
					if(!ctrl.isEmpty(reply)){
						callback(reply);		
					}
				});
			} else {
				var messageIndex = $scope.messages.length -1;
				var lastMessage = $scope.messages[messageIndex].id;

				apiFactory.getMessagesFrom(session, lastMessage).success(function(reply){
					callback(reply);
				}); 
			}
		}, 300, 0);
	};
	
	this.start = function(){
		apiFactory.initUser().success(function(reply){
			$scope.uid = reply.uid;
			ctrl.joinSession(session);
		});	
	};

	this.joinSession = function(session){
		apiFactory.joinSession(session).success(function(reply){
			if(reply.user_status == "host"){
				$scope.player.rank = reply.user_status;
				$scope.startedMessage = "Waiting for player...";

				ctrl.onPlayerJoin(function(reply){
					$scope.startedMessage = "Player has joined!";
					if(ctrl.isEmpty($scope.messages)) $scope.player.canSend = true;
			
				});

			} else if(reply.user_status == "player"){
				$scope.player.rank = reply.user_status;
				$scope.startedMessage = "You have joined!";
			} else if(reply.user_status == "spectator"){
				$scope.player.rank = reply.user_status;
				$scope.startedMessage = "You are a spectator!";
			} else if(reply.error == "needs_session"){
				$scope.startedMessage = "You need a valid session!";
			}
		});

		ctrl.getMessages(function(reply){
			for(var i = 0; i < reply.length; i++){				
				if(reply[i].senderuid == $scope.uid){
					reply[i].user = "You";
					$scope.player.canSend = false;
				} else if(reply[i].senderuid != $scope.uid){
					reply[i].user = "Other";
					$scope.player.canSend = true;
				}

				$scope.messages.push(reply[i]);
			}
		});
	};

	$scope.sendMessage = function(message){
		if(message.length < 1){
			alert("Message needs atleast 1 characters!");
			return;
		}

		$scope.messageContent = "";

		apiFactory.sendMessage(session, message);
	};
	
	$scope.shortenLink = function(){
		apiFactory.getShortURL().success(function(reply){
			console.log(reply);
			window.prompt("Heres a shortened link:", reply.id);
		});
	}

	this.start();

	$scope.$on('$destroy', function(){
			if(ctrl.playerJoin){
				$interval.cancel(ctrl.playerJoin);
			} else if(ctrl.messageCheck){
				$interval.cancel(ctrl.messageCheck);
				}
			
			$scope.canSend = false;
		});

});