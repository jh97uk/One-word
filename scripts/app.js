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
					if(!scope.canSend) return;
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
	
	apiFactory.hostOrClient = function(session){
		return $http({
			method: "POST",
			url: "api/sessionhostclient.php",
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
		if(session == null){
			return;
		}

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
		console.log("test");
		apiFactory.startNewSession().success(function(reply){
			console.log(reply);
			if(reply.status == "complete"){
				$state.goto('session', {session:reply.sessionid});
			}
		});
	};
});

app.controller("sessionCtrl", function($http, $scope, $routeParams, $state, $interval, $location, apiFactory){
		sessionCtrl = this;	
		var session = $routeParams.session;
		
		$scope.messages = [];
		$scope.uid;
		$scope.canSend = false;

		sessionCtrl.isEmpty = function(json){
			for(item in json){
				return false;
			}
			return true;
		};

		sessionCtrl.start = function(){
			apiFactory.initUser().success(function(reply){
				$scope.uid = reply.uid;
				sessionCtrl.setHostOrClient();
			});	
		};
		
		sessionCtrl.setHostOrClient = function(){
			apiFactory.hostOrClient(session).success(function(reply){
				
				if(reply.user_status == "host"){
					console.log("You are the host!");

					$scope.isHost = true;
					sessionCtrl.playerCheck = $interval(function(){ sessionCtrl.checkForPlayerJoin(); }, 1000, 0);	
					sessionCtrl.messageCheck = $interval(function(){ sessionCtrl.getLatestMessages(); }, 300, 0);
				} else {
					
					console.log("You are a player");

					$scope.isHost = false;
					$scope.startedMessage = "You have joined!";
					
					sessionCtrl.messageCheck2 = $interval(function(){ sessionCtrl.getLatestMessages(); }, 300, 0);
				}
			});	
		};

		sessionCtrl.checkForPlayerJoin = function(){
			apiFactory.hasPlayerJoined(session).success(function(reply){
				if(reply.status == 1){
					$interval.cancel(sessionCtrl.playerCheck);
					$scope.startedMessage = "Played has joined!";
					$scope.canSend = true;
				} else {
					$scope.startedMessage = "Waiting for player...";
				}
			}); 
		};

		sessionCtrl.getMessageCount = function(){
			var messageCount = $scope.messages.length - 1;
			return messageCount;
		};

		sessionCtrl.getLatestMessages = function(){
			if(sessionCtrl.isEmpty($scope.messages)){
				
				apiFactory.getAllMessages(session).success(function(reply){
					if(!sessionCtrl.isEmpty(reply)){
						for(var i = 0; i < reply.length; i++){
							
							if(reply[i].senderuid == $scope.uid){
								reply[i].user = "You";
								$scope.canSend = false;
							} else{
								reply[i].user = "Other";
								$scope.canSend = true;
							}

							$scope.messages.push(reply[i]);
						}
					}
				});

			} else {
				var messageIndex = $scope.messages.length -1;
				var lastMessage = $scope.messages[messageIndex].id;

				apiFactory.getMessagesFrom(session, lastMessage).success(function(reply){	
					if(!sessionCtrl.isEmpty(reply)){
						
						if(reply[0].id > lastMessage){
							
							if(reply[0].senderuid == $scope.uid){
								reply[0].user = "You";
								$scope.canSend = false;
							} else{
								reply[0].user = "Other";
								$scope.canSend = true;
							}

							$scope.messages.push(reply[0]);
						}
					}
				});
			}
		};

		$scope.sendMessage = function(message){
			if(message == ""){
				alert("Message needs atleast 3 characters!");
				return;
			}

			apiFactory.sendMessage(session, message).success(function(reply){
			});
		};

		sessionCtrl.start(); 

		$scope.$on('$destroy', function(){
			if(sessionCtrl.messageCheck){
				$interval.cancel(sessionCtrl.messageCheck);
			} else if(sessionCtrl.messageCheck2){
				$interval.cancel(sessionCtrl.messageCheck2);
			} else if(sessionCtrl.playerCheck){
				$interval.cancel(sessionCtrl.playerCheck);
			}
			
			$scope.canSend = false;
		});
});
