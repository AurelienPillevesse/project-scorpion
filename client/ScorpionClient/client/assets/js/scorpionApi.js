(function(window, angular, undefined) {'use strict';
	var urlBase = "http://private-9fe3f9-scorpapi.apiary-mock.com/api";

	var module = angular.module("scorpApi", ['ngResource']);


        module.factory(
                "Media",
                ['scorpResource', 'ScorpAuth', '$injector', function(Resource, ScorpAuth, $injector) {
                        var R = Resource(
                                urlBase + "/media/:id",
                                { 'id': '@id',
																	'type': '@type'},
                                {
                                	"myMedias":{
                                		url: urlBase + "/media/me",
                                		method: "GET",
																		isArray: true
                                	},
																	"countCurrentUserMedias":{
																		url: urlBase + "/media/me/count",
																		method: "GET"
																	},
                                	"search":{
                                		method: "GET",
                                		url: urlBase + "/media/search",
																		isArray: true
                                	},
																	"delete":{
																		method: "DELETE",
																		url: urlBase + "/media/:type/:id"
																	},
																	"like":{
																		method: "POST",
																		url: urlBase + "/likes/:id/:type"
																	},
																	"randMedias":{
																		method: "GET",
																		url: urlBase + "/media/random/:nb",
																		isArray: true
																	},
																	"countComments":{
																		method: "GET",
																		url: urlBase + "/media/:type/:id/comment/count"
																	},
																	"getComments":{
																		method: "GET",
																		url: urlBase + "/media/:type/:id/comment",
																		isArray: true
																	},
																	"sendComment":{
																		method: "POST",
																		url: urlBase + "/media/:type/:id/comment"
																	},
																	"getLikes":{
																		method: "GET",
																		url: urlBase + "/media/me/likes",
																		isArray: true
																	}
                                });

                        R.getUploadUrl = function(){
                            return urlBase + "/media/upload?key="+ScorpAuth.accessTokenHash;
                        };

												R.getStreamUrl = function(stream){
													return stream;
												};

                        R.modelName = "Media";

                        return R;
                }]);

	module.factory(
		"User",
		['scorpResource', 'ScorpAuth','$injector', function(Resource, ScorpAuth, $injector) {
			var R = Resource(
				urlBase + "/users/:id",
				{ 'id': '@id' },
				{
					"create": {
						url: urlBase + "/users",
						method: "POST"
					},
					"edit": {
						method: "PATCH",
						url: urlBase + "/user"
					},
					"login": {
						interceptor: {
							response: function(response) {
								var accessToken = response.data;
								ScorpAuth.setUser(accessToken.hash, accessToken.user.id, accessToken.user);
								ScorpAuth.save();
								return response.resource;
							}
						},
						url: urlBase + "/users/login",
						method: "POST"
					},
					"logout": {
						interceptor: {
							response: function(response) {
								ScorpAuth.clearUser();
								ScorpAuth.clearStorage();
								return response.resource;
							}
						},
						url: urlBase + "/users/logout",
						method: "POST"
					},
					"getCurrentUser": {
						url: urlBase + "/user",
						method: "GET",
						params: {
							id: function() {
								var id = ScorpAuth.currentUserId;
								if(id == null) id = '__anonymous__';
								return id;
							},
						},
						interceptor: {
							response: function(response) {
								ScorpAuth.currentUserData = response.data;
								return response.resource;
							}
						},
						__isGetCurrentUser__ : true
					}
				});

			R.isAuthenticated = function() {
				return this.getCurrentId() != null;
			};

			R.getCurrentId = function() {
				return ScorpAuth.currentUserId;
			};

			R.getBaseUrl = function() {
				return urlBase;
			};

			R.modelName = "User";

			return R;
		}]);

	module.factory("ScorpAuth", function(){
		var props = ['accessTokenHash', 'currentUserId'];
		var prefix = "$scorp$";

		function ScorpAuth() {
			var self = this;
			props.forEach(function(name){
				self[name] = load(name);
			});
			this.currentUserData = null;
		}

		ScorpAuth.prototype.save = function() {
			var self = this;
			var storage = localStorage;
			props.forEach(function(name){
				save(storage, name, self[name]);
			});
		};

		ScorpAuth.prototype.setUser = function(accessTokenHash, userId, userData) {
			this.accessTokenHash = accessTokenHash;
			this.currentUserId = userId;
			this.currentUserData = userData;
		};

		ScorpAuth.prototype.clearUser = function() {
			this.accessTokenHash = null;
			this.currentUserId   = null;
			this.currentUserData = null;
		};

		ScorpAuth.prototype.clearStorage = function() {
			props.forEach(function(name){
				save(localStorage, name, null);
			});
		};

		return new ScorpAuth();

		function save (storage, name, value) {
			var key = prefix + name;
			if(value == null) value = '';
			storage[key] = value;
		}

		function load (name) {
			var key = prefix + name;
			return localStorage[key] || null;
		}


	})
	.config(['$httpProvider', function($httpProvider) {
		$httpProvider.interceptors.push('ScorpAuthRequestInterceptor');
	}])
	.factory('ScorpAuthRequestInterceptor', ['$q', 'ScorpAuth',
		function($q, ScorpAuth) {
			return {
				'request': function(config) {
					if(config.url.substr(0, urlBase.length) !== urlBase) {
						return config;
					}

					if(ScorpAuth.accessTokenHash) {
						if(config.params === undefined){
							config.params = {};
						}
						config.params.key = ScorpAuth.accessTokenHash;
					}

					return config || $q.when(config);
				}
			}
		}])
	.provider('scorpResource', function scorpResourceProvider() {
		this.$get = ['$resource', function($resource) {
      		return function(url, params, actions) {
        	var resource = $resource(url, params, actions);

	        // Angular always calls POST on $save()
	        // This hack is based on
	        // http://kirkbushell.me/angular-js-using-ng-resource-in-a-more-restful-manner/
	        resource.prototype.$save = function(success, error) {
	          // Fortunately, LoopBack provides a convenient `upsert` method
	          // that exactly fits our needs.
	          var result = resource.upsert.call(this, {}, this, success, error);
	          return result.$promise || result;
	        };
	        	return resource;
        	};
    	}];
	});

})(window, window.angular);
