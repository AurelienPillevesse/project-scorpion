(function() {
  'use strict';

  angular.module('application', [
    'ui.router',
    'ngAnimate',

    //foundation
    'foundation',
    'foundation.dynamicRouting',
    'foundation.dynamicRouting.animations',
    //Scorpion
    'scorpApi',
    'angularFileUpload',
    'ngVideo',
    'ngAudio',
    'cfp.hotkeys',
    'angularSpinner'

  ])
    .config(config)
    .run(run)
    .directive('playable', ['$document', '$rootScope', function($document, $rootScope){
      return {
        restrict: 'A',
        scope: {media: '=playable'},
        link: function(scope, element, attr){
          element.on('click', function(event) {
            if(!scope.media)
              return;
            $rootScope.$broadcast('media-play', scope.media);
          });
        }
      };
    }])
    .directive('likeable', ['$document', 'Media', function($document, Media){
      return {
        restrict: 'A',
        scope: {media: '=likeable'},
        link: function(scope, element, attr){
          element.on('click', function(event) {
            if(!scope.media)
              return;
            if(!element.hasClass('sx-liked'))
            {
              element.addClass('sx-liked');
              element.children().addClass('iconic-color-secondary');
            }else{
              element.removeClass('sx-liked');
              element.children().removeClass('iconic-color-secondary');
            }
            Media.like(scope.media);
          });
        }
      };
    }])
       .directive('ngThumb', ['$window', function($window) {
        var helper = {
            support: !!($window.FileReader && $window.CanvasRenderingContext2D),
            isFile: function(item) {
                return angular.isObject(item) && item instanceof $window.File;
            },
            isImage: function(file) {
                var type =  '|' + file.type.slice(file.type.lastIndexOf('/') + 1) + '|';
                return '|jpg|png|jpeg|bmp|gif|'.indexOf(type) !== -1;
            }
        };

        return {
            restrict: 'A',
            template: '<canvas/>',
            link: function(scope, element, attributes) {
                if (!helper.support) return;

                var params = scope.$eval(attributes.ngThumb);

                if (!helper.isFile(params.file)) return;
                if (!helper.isImage(params.file)) return;

                var canvas = element.find('canvas');
                var reader = new FileReader();

                reader.onload = onLoadFile;
                reader.readAsDataURL(params.file);

                function onLoadFile(event) {
                    var img = new Image();
                    img.onload = onLoadImage;
                    img.src = event.target.result;
                }

                function onLoadImage() {
                    var width = params.width || this.width / this.height * params.height;
                    var height = params.height || this.height / this.width * params.width;
                    canvas.attr({ width: width, height: height });
                    canvas[0].getContext('2d').drawImage(this, 0, 0, width, height);
                }
            }
        };
    }])
    .controller('logInCtrl', logInCtrl)
    .controller('signInCtrl', signInCtrl)
    .controller('profileCtrl', profileCtrl)
    .controller('profileModifCtrl',profileModifCtrl)
    .controller('myMediaCtrl',myMediaCtrl)
    .controller('uploadCtrl',uploadCtrl)
    .controller('TopBarConnectCtrl',TopBarConnectCtrl)
    .controller('TopBarCtrl',TopBarCtrl)
    .controller('videoPlayerCtrl',videoPlayerCtrl)
    .controller('SoundPlayerCtrl',SoundPlayerCtrl)
    .controller('PlayerZoneCtrl', PlayerZoneCtrl)
    .controller('searchResultController',searchResultController)
    .controller('SearchCtrl',SearchCtrl)
    .controller('boardCtrl', boardCtrl)
    .controller('commentCtrl', commentCtrl)
    .controller('soundCardCtrl', soundCardCtrl)
    .controller('activityCtrl', activityCtrl)
    .controller('likesCtrl', likesCtrl)

    .service('searchService', function(Media, $rootScope){
      var currentSearch = '';
      var searching = false;
      var results = [];

      var setCurrentSearch = function(newSearch){
        currentSearch = newSearch;
      };

      var getCurrentSearch = function(){
        return currentSearch;
      };

      var doSearch = function(){
        if(currentSearch.length > 0){
          results = [];
          searching = true;
          Media.search({search: currentSearch}, function(data){
            searching = false;
            results = data;
          }, function(data, err){
            searching = false;
          });
        }
      };

      var isSearching = function(){
        return searching;
      };

      var getResults = function(){
        return results;
      }

      return {
        setCurrentSearch: setCurrentSearch,
        getCurrentSearch: getCurrentSearch,
        currentSearch: currentSearch,
        searching: searching,
        doSearch: doSearch,
        isSearching: isSearching,
        results: results,
        getResults: getResults
      };
    })

    .service('AudioPlayer', function(ngAudio){
      this.currentMedia = null;
      this.isPlaying = false;
      this.isLoaded = false;
      this.audio = null;

      this.load = function (media) {
        this.currentMedia = media;
        this.audio = ngAudio.load(this.currentMedia.stream_url);
        this.isLoaded = true;
      };

      this.stop = function () {
        this.isPlaying = false;
        this.audio.stop();
      };

      this.pause = function() {
        this.isPlaying = false;
        this.audio.pause();
      };

      this.flush = function () {
        console.log("flush");
        this.stop();
        this.currentMedia = null;
        this.isLoaded = false;
      //  audio = null;
      };


      /*return {
        load: load,
        stop: stop,
        pause: pause,
        flush: flush,
        isPlaying: isPlaying,
        audio: audio,
        currentMedia: currentMedia
      };*/
    })
    .service('VideoPlayer', function(){

    })
  ;

  config.$inject = ['$urlRouterProvider', '$locationProvider'];

  function config($urlProvider, $locationProvider) {
    $urlProvider.otherwise('/');

    $locationProvider.html5Mode({
      enabled:false,
      requireBase: false
    });

    $locationProvider.hashPrefix('!');
  }

  function run() {
    FastClick.attach(document.body);
  }

signInCtrl.$inject = ['$scope', '$stateParams', '$state', '$controller', 'User', '$location', '$rootScope', 'FoundationApi'];
  function signInCtrl($scope, $stateParams, $state, $controller, User, $location, $rootScope, FoundationApi) {
    angular.extend(this, $controller('DefaultController', {$scope: $scope, $stateParams: $stateParams, $state: $state}));

    if(User.isAuthenticated())
    {
      $state.go('board');
    }

    $scope.signIn=function(credential) {
      var p = { user:null };
      p.user=credential;
      var result = User.create(p, function(){
        FoundationApi.publish('main-notifications', { title: 'Sign in Successfully', content: 'Your profile as been successfully created.' });
        $state.go('connect');
      });
      $scope.p = p;
    };
  }

  profileCtrl.$inject = ['$scope', '$stateParams', '$state', '$controller', 'User', '$location', '$rootScope', 'FoundationApi'];
  function profileCtrl($scope, $stateParams, $state, $controller, User, $location, $rootScope, FoundationApi) {
    angular.extend(this, $controller('DefaultController', {$scope: $scope, $stateParams: $stateParams, $state: $state}));

    $scope.tabButton={
      'mymedia': {style: {}, path: 'partials/mymedia.html'},
      'aboutme': {style: {}, path: 'partials/profilemodif.html'},
      'activity': {style: {'background-color': '#BBBBBB'}, path: 'partials/activity.html'},
      'like': {style: {}, path: 'partials/likes.html'}
    };

    $scope.contenu='partials/activity.html';

    $scope.changerPage=function (idButton){
      $scope.contenu = $scope.tabButton[idButton].path;
      Object.keys($scope.tabButton).forEach(function(v,k){
        if(v==idButton){

          $scope.tabButton[v].style = {'background-color' : '#BBBBBB'};
        }
        else{
          $scope.tabButton[v].style = {};
        }
      });
    };

    $scope.credential=User.getCurrentUser(function(){}, function(){ $location.path("/connect")});
  }

  logInCtrl.$inject = ['$scope', '$stateParams', '$state', '$controller', 'User', '$location', '$rootScope', 'FoundationApi'];
  function logInCtrl($scope, $stateParams, $state, $controller, User, $location, $rootScope, FoundationApi) {
    angular.extend(this, $controller('DefaultController', {$scope: $scope, $stateParams: $stateParams, $state: $state}));

    if(User.isAuthenticated())
    {
      $state.go('board');
    }

    $scope.erreur=false;

    $scope.logIn=function(credential){
      User.login(credential, function(data){
        $rootScope.$broadcast('login');
        FoundationApi.publish('main-notifications', { title: 'Log in successfully', content: 'Welcome back onto Mediaz!' });
        $state.go('board');

      }, function(err) {
        $scope.erreur = true;
      });
    }

  }

  profileModifCtrl.$inject = ['$scope', '$stateParams', '$state', '$controller', 'User', '$location', '$rootScope', 'FoundationApi'];
  function profileModifCtrl($scope, $stateParams, $state, $controller, User, $location, $rootScope, FoundationApi) {
    $scope.credential = User.getCurrentUser();

    $scope.profileModif = function(user) {
      var credential = angular.copy(user);
      credential.firstName = credential.first_name;
      credential.lastName = credential.last_name;
      delete credential.first_name;
      delete credential.last_name;
      delete credential.login;
      delete credential.id;
      User.edit({user: credential}, function(data){
        console.log(data);
      });
    };
  }

  commentCtrl.$inject = ['$scope', '$stateParams', '$state', '$controller', 'User', '$location', '$rootScope', 'FoundationApi', 'ngAudio', 'Media'];
  function commentCtrl($scope, $stateParams, $state, $controller, User, $location, $rootScope, FoundationApi, ngAudio, Media) {

    $scope.init = function (media) {
      $scope.media = media;
    };
    $scope.showComm = false;

    $scope.commentCount = Media.countComments({id: $scope.media.id, type: $scope.media.type });

    $scope.showComments = function () {
      $scope.showComm = !$scope.showComm;
      $scope.comments = $scope.comments || Media.getComments({id: $scope.media.id, type: $scope.media.type });
    };

    $scope.sendComment = function () {
      var com = angular.copy($scope.com);
      var to_send = angular.copy(com);
      $scope.com.content = "";
      com.owner = User.getCurrentUser();
      com.created_at = new Date();
      $scope.comments.push(com);
      Media.sendComment({id: $scope.media.id, type: $scope.media.type, comment: to_send});
    }
  }

  likesCtrl.$inject = ['$scope', '$stateParams', '$state', '$controller', 'User', '$location', '$rootScope', 'FoundationApi', 'ngAudio', 'Media'];
  function likesCtrl($scope, $stateParams, $state, $controller, User, $location, $rootScope, FoundationApi, ngAudio, Media) {
    $scope.medias = Media.getLikes();
  }

  activityCtrl.$inject = ['$scope', '$stateParams', '$state', '$controller', 'User', '$location', '$rootScope', 'FoundationApi', 'ngAudio', 'Media'];
  function activityCtrl($scope, $stateParams, $state, $controller, User, $location, $rootScope, FoundationApi, ngAudio, Media) {
    $scope.medias = Media.myMedias();
    $scope.userstat = {
      mediaz: Media.countCurrentUserMedias()
    };
    $scope.likes = Media.getLikes();
  }

  myMediaCtrl.$inject = ['$scope', '$stateParams', '$state', '$controller', 'User', '$location', '$rootScope', 'FoundationApi', 'ngAudio', 'Media'];
  function myMediaCtrl($scope, $stateParams, $state, $controller, User, $location, $rootScope, FoundationApi, ngAudio, Media) {
    $scope.medias = Media.myMedias();

    $scope.deleteMedia=function(media){
      $scope.medias.splice($scope.medias.indexOf(media), 1);
      Media.delete(media);
    };
  }

  boardCtrl.$inject = ['$scope', '$stateParams', '$state', '$controller', 'User', '$location', '$rootScope', 'FoundationApi', 'ngAudio', 'Media'];
  function boardCtrl($scope, $stateParams, $state, $controller, User, $location, $rootScope, FoundationApi, ngAudio, Media) {
    if(!User.isAuthenticated()){
      $state.go('home');
    }
    $scope.medias = Media.randMedias({nb: 20});
    $scope.likes = Media.getLikes();
  }


  soundCardCtrl.$inject = ['$scope', '$stateParams', '$state', '$controller', 'User', '$location', '$rootScope', 'FoundationApi', 'ngAudio', 'AudioPlayer'];
  function soundCardCtrl($scope, $stateParams, $state, $controller, User, $location, $rootScope, FoundationApi, ngAudio, AudioPlayer) {

    $scope.init = function(sound){
        $scope.sound = sound;
        $scope.audio = ngAudio.load($scope.sound.stream_url);
    };

    $scope.play = "media-play";
    $scope.isPlaying = false;

    $scope.$watch(function () {
      return $scope.audio.paused;
    }, function (paused) {
      paused ? $scope.play = "media-play" : $scope.play = "media-pause"
    });


    $scope.$watch(function () {
      return AudioPlayer.currentMedia;
    }, function () {
      if($scope.sound == AudioPlayer.currentMedia){
        $scope.audio = AudioPlayer.audio;
        $scope.audio.play();
        $scope.play = "media-pause";
        //$scope.play = "media-pause";
        $scope.isPlaying = true;
        $scope.changerPlay = function () {
          $scope.audio.paused ? $scope.audio.play() : $scope.audio.pause()

        };
      }
      else {
        if($scope.isPlaying){
          $scope.audio = ngAudio.load($scope.sound.stream_url);
          $scope.play = "media-play";
          $scope.isPlaying = false;
          $scope.changerPlay = function () {
            return;
          };
        }
      }
    });
  }


SoundPlayerCtrl.$inject = ['$scope', 'FoundationApi', 'ngAudio', 'hotkeys', 'Media', 'AudioPlayer'];
  function SoundPlayerCtrl($scope, FoundationApi, ngAudio, hotkeys, Media, AudioPlayer) {

    $scope.player = AudioPlayer;
    //$scope.player = AudioPlayer;
    console.log(AudioPlayer);
    console.log($scope.player);
    console.log($scope.player.audio);
    $scope.audio = $scope.player.audio;
    $scope.play = "media-play";
    $scope.vol = "volume-high";
    $scope.Volume = new Volume($scope.audio);

    $scope.$watch(function () {
      return $scope.audio.paused;
    }, function (paused) {
      paused ? $scope.play = "media-play" : $scope.play = "media-pause"
    });

    $scope.$on('play-music', function(event, music){
      $scope.audio.play(music.stream_url);
      $scope.$apply();
    });

    $scope.$on('$destroy', function(){
      $scope.player.flush();
    });

     $scope.changerPlay = function() {
        $scope.audio.paused ? $scope.audio.play() : $scope.audio.pause()

      //  $scope.audio.paused ? $scope.play = "media-pause" : $scope.play = "media-pause"
     };

     $scope.mute = function() {
        $scope.audio.muting = !$scope.audio.muting;
        $scope.audio.muting ? $scope.audio.volume = 0 : $scope.audio.volume = 1

        $scope.audio.muting ? $scope.Volume.icon = "volume-off" : $scope.Volume.icon = "volume-high"
     };

     hotkeys.bindTo($scope)
    .add({
      combo: 'space',
      description: 'Press to play/pause',
      callback: $scope.changerPlay
    });
  }

  function Volume(audio){

    var icon = "volume-high";
    var volume = audio;

    this.__defineSetter__("volume", function(val){
      icon = "volume-high";
      volume.volume = val;
      console.log(val);
      if (val == 0){
        icon = "volume-off";
      }
      else if (val < 0.5){
        icon = "volume-low";
      }
      else {
        icon = "volume-high";
      }
    });

    this.__defineSetter__("icon", function(val){
      icon = val;
    });

    this.__defineGetter__("volume", function(){
      return volume.volume;
    });

    this.__defineGetter__("icon", function(){
      return icon;
    });
  }


  uploadCtrl.$inject = ['$scope', '$stateParams', '$state', '$controller', 'Media', '$location', '$rootScope', 'FoundationApi', 'FileUploader'];
  function uploadCtrl($scope, $stateParams, $state, $controller, Media, $location, $rootScope, FoundationApi, FileUploader) {

    $scope.uploader = new FileUploader({
      url:Media.getUploadUrl()
    });

    $scope.uploader.onProgressAll = function(progress) {
            $scope.progress = progress;
        };

    $scope.changeVis = function(item, newVisibility){
      item.formData.length = 0;
      item.formData.push({
        'visibility': newVisibility
      });
      console.log(item.formData);
    };
  }


  videoPlayerCtrl.$inject = ['$scope', '$stateParams', '$state', '$controller', 'User', '$location', '$rootScope', 'FoundationApi', 'ngVideoOptions', 'video'];
  function videoPlayerCtrl($scope, $stateParams, $state, $controller, User, $location, $rootScope, FoundationApi, ngVideoOptions, video) {

    video.addSource('mp4', $scope.videourl);

    $scope.$on('$destroy', function(){
      video.stop();
    });

        /**
         * @property playlistOpen
         * @type {Boolean}
         * @default false
         */
        $scope.playlistOpen = false;

        /**
         * @method playVideo
         * @param sourceUrl {String}
         * @return {void}
         */
        $scope.playVideo = function playVideo(sourceUrl) {
            video.addSource('mp4', sourceUrl, true);
        };

        /**
         * @method getVideoName
         * @param videoModel {Object}
         * @return {String}
         */
        $scope.getVideoName = function getVideoName(videoModel) {

            switch (videoModel.src) {
                case ($scope.videos.first): return "Big Buck Bunny";
                case ($scope.videos.second): return "The Bear";
                default: return "Unknown Video";
            }

        };

    //$scope.credential=User.getCurrentUser(function(){}, function(){ $location.path("/videoplayer")});
  }

  TopBarConnectCtrl.$inject = ['$scope', 'User', '$rootScope'];
  function TopBarConnectCtrl($scope, User, $rootScope) {
      $scope.connected = User.isAuthenticated();

    $rootScope.$on('logout', function(event){
      $scope.connected = false;
    });
    $rootScope.$on('login', function(event){
      $scope.connected = true;
    });
  }

  TopBarCtrl.$inject = ['$scope', 'User', '$location', '$rootScope', 'FoundationApi'];
  function TopBarCtrl($scope, User, $location, $rootScope, FoundationApi) {
      $scope.accueil = false;
      if ($location.path() === '/')
        $scope.accueil=true;

    $scope.$on('$locationChangeSuccess', function(event, next, current){
        if ($location.path() === '/')
        {
          $scope.accueil=true;
        }
        else
        {
          $scope.accueil=false;
        }
      });

      $scope.logOut = function(){
          User.logout();
          FoundationApi.publish('main-notifications', { title: 'Log out successfully', content: 'See you soon on Mediaz!' });
          $rootScope.$broadcast('logout');
      };
  }

  SearchCtrl.$inject = ['$scope', 'Media', '$location', '$rootScope', '$state', 'searchService'];
  function SearchCtrl($scope, Media, $location, $rootScope, $state, searchService) {

    $scope.doSearch = function(){
        if ($scope.searchContent.length > 0){
         searchService.setCurrentSearch($scope.searchContent);
         searchService.doSearch();
         console.log(searchService);
         $state.go('searchresult');
        }
    };
  }

  PlayerZoneCtrl.$inject = ['$scope', '$rootScope', 'ModalFactory', 'Media', 'AudioPlayer'];
  function PlayerZoneCtrl($scope, $rootScope, ModalFactory, Media, AudioPlayer){
    $scope.type = 'none';

    $rootScope.$on('media-play', function(event, toplay){
      console.log(toplay);
      $scope.media = toplay;
      $scope.type = toplay.type;

      if($scope.type === 'image') {
        var modal = new ModalFactory({
          animationIn: "slideInUp",
          animationOut: "hingeOutFromMiddleY",
          class: "large",
        overlay: true,
        overlayClose: true,
        templateUrl: 'partials/imgplayer.html',
        contentScope: {
          close: function() {
            modal.deactivate();
            $timeout(function() {
              modal.destroy();
              }, 1000);
            },
            media: toplay,
            comments: Media.getComments({id: toplay.id, type: toplay.type})
          }
        });
        modal.activate();
      }

      if($scope.type === 'musique'){
        AudioPlayer.load(toplay);
        $scope.playMusic = true;
        $scope.playVideo = false;

      }
      if($scope.type === 'video'){
        $scope.playMusic = false;
        $scope.playVideo = true;
      //  Player.load(toplay);
        var modal = new ModalFactory({
        class: 'collapse',
        overlay: true,
        overlayClose: true,
        templateUrl: 'partials/videoplayer.html',
        contentScope: {
          close: function() {
            modal.deactivate();
            },
            videourl: toplay.stream_url
          }
        });
        modal.activate();
      }

      $scope.$apply();
    });
  }

  searchResultController.$inject = ['$scope', '$stateParams', '$state', '$controller', 'User', '$location', '$rootScope', 'FoundationApi', 'ngAudio', 'Media', 'searchService'];
  function searchResultController($scope, $stateParams, $state, $controller, User, $location, $rootScope, FoundationApi, ngAudio, Media, searchService) {
    angular.extend(this, $controller('DefaultController', {$scope: $scope, $stateParams: $stateParams, $state: $state}));

    $scope.likes = Media.getLikes();

    $scope.$watch(function () {
      return searchService.getCurrentSearch();
    },function () {
      $scope.searchContent = searchService.getCurrentSearch();
    });

    $scope.$watch(function () {
      return searchService.isSearching();
    },function () {
      $scope.showLoad = searchService.isSearching();
    });

    $scope.$watch(function () {
      return searchService.getResults();
    },function () {
        $scope.medias = searchService.getResults();
    });
  }

})();
