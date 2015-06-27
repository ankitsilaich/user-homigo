'use strict';

/**
 * Config for the router
 */
angular.module('app')
    .run(
        ['$rootScope', '$state', '$stateParams', '$location', 'LoginService',
            function($rootScope, $state, $stateParams, $location, LoginService) {
                $rootScope.$state = $state;

                $rootScope.$stateParams = $stateParams;
                 var routesPermission = ['/app/tenant/details','/app/update/tenants'];   // routes that requires login
                $rootScope.isLoggedIn = false;
                $rootScope.processGoingOn = false;
                 $rootScope.authError = null;
              

                $rootScope.$on('$stateChangeStart', function(event, toState, toParams, fromState, fromParams) {
                    // check if the user is logged in or not and the root scope variable according to that
                    $state.current = toState;
                    console.log($state.current);
                  //  console.log($state.current);
                    var connected = LoginService.isLoggedIn();
                    connected.then(function(msg) {

console.log(msg);
                        if (msg.data) {
                            //  user is logged in and it will also handle the page refresh
                             $rootScope.isLoggedIn = true;
                            // $location.path('/app/tenant/details');

                              console.log("this is true so");
                        } else {

                            // user is not logged in and now check is the user is on login required page or not
                            // if not, redirect him to the login page
                           console.log(routesPermission.indexOf($state.current.name));
                            if ((routesPermission.indexOf($location.path()) != -1)) {

                               $location.path('/access/signin');
                            console.log("not logged in");

                               
                            }

                        }
                    })

                });
                $rootScope.$on('$stateChangeSuccess', function() {

                });
            }
        ]
    )
    .config(
        ['$stateProvider', '$urlRouterProvider',
            function($stateProvider, $urlRouterProvider) {

                $urlRouterProvider
                    .otherwise('/app/tenant/details');
                $stateProvider
                    .state('app', {
                        abstract: true,
                        url: '/app',
                        templateUrl: 'tpl/app.html'
                    })
                    
                    

                .state('app.tenant', {
                        url: '/tenant',
                        template: '<div ui-view class="fade-in-up"></div>'
                    })
                    .state('app.tenant.details', {
                        url: '/details',
                        templateUrl: 'tpl/tenant_profile.html',
                        resolve: {
                            deps: ['uiLoad',
                                function(uiLoad) {
                                    return uiLoad.load('js/controllers/tenant.js');
                                }
                            ]
                        }
                    })




                // table

                .state('app.all', {
                    url: '/all',
                    template: '<div ui-view></div>'
                })

                .state('app.all.houses', {
                        url: '/houses',
                        templateUrl: 'tpl/all_houses.html',
                        resolve: {
                            deps: ['$ocLazyLoad',
                                function($ocLazyLoad) {
                                    return $ocLazyLoad.load(['js/controllers/allhouses.js']);
                                }
                            ]
                        }
                    })
                    .state('app.all.tenants', {
                        url: '/tenants',
                        templateUrl: 'tpl/all_tenants.html',
                        resolve: {
                            deps: ['$ocLazyLoad',
                                function($ocLazyLoad) {
                                    return $ocLazyLoad.load(['js/controllers/alltenants.js']);
                                }
                            ]
                        }
                    })

                .state('app.add', {
                        url: '/add',
                        template: '<div ui-view class="fade-in"></div>',

                        resolve: {
                            deps: ['$ocLazyLoad', 'uiLoad',
                                function($ocLazyLoad, uiLoad) {
                                    return $ocLazyLoad.load('toaster').then(
                                        function() {
                                            return uiLoad.load(
                                                'js/controllers/form.js')
                                        }
                                    );
                                }
                            ]
                        }

                    })
                    .state('app.update', {
                        url: '/update',
                        template: '<div ui-view class="fade-in"></div>',



                    })
                    .state('app.update.tenants', {
                        url: '/tenants',
                        templateUrl: 'tpl/update_tenants.html',
                        resolve: {
                            deps: ['$ocLazyLoad', 'uiLoad',
                                function($ocLazyLoad, uiLoad) {
                                    return $ocLazyLoad.load('toaster').then(
                                        function() {
                                            return uiLoad.load(
                                                'js/controllers/updatetenant.js')
                                        }
                                    );
                                }
                            ]
                        }
                    })
 .state('app.make', {
                        url: '/make',
                        template: '<div ui-view class="fade-in"></div>',



                    })
                    .state('app.make.complaint', {
                        url: '/complaint',
                        templateUrl: 'tpl/make_complaint.html',
                        resolve: {
                            deps: ['$ocLazyLoad', 'uiLoad',
                                function($ocLazyLoad, uiLoad) {
                                    return $ocLazyLoad.load('toaster').then(
                                        function() {
                                            return uiLoad.load(
                                                'js/controllers/makecomplaint.js')
                                        }
                                    );
                                }
                            ]
                        }
                    })
                    .state('app.update.houses', {
                        url: '/houses/:house_id',
                        templateUrl: 'tpl/update_houses.html',
                        resolve: {
                            deps: ['$ocLazyLoad', 'uiLoad',
                                function($ocLazyLoad, uiLoad) {
                                    return $ocLazyLoad.load('toaster').then(
                                        function() {
                                            return uiLoad.load(
                                                'js/controllers/updatehouse.js')
                                        }
                                    );
                                }
                            ]
                        }
                    })
                    .state('app.add.tenants', {
                        url: '/tenants',
                        templateUrl: 'tpl/add_tenants.html'
                    })
                    .state('app.add.houses', {
                        url: '/houses',
                        templateUrl: 'tpl/add_houses.html'
                    })
                    // form
                    .state('app.form', {
                        url: '/form',
                        template: '<div ui-view class="fade-in"></div>',
                        resolve: {
                            deps: ['uiLoad',
                                function(uiLoad) {
                                    return uiLoad.load('js/controllers/form.js');
                                }
                            ]
                        }
                    })


                .state('app.house', {
                        url: '/house',
                        template: '<div ui-view class="fade-in-down"></div>'
                    })
                    .state('app.house.detail', {
                        url: '/details/:house_id',
                        templateUrl: 'tpl/page_profile.html',
                        resolve: {
                            deps: ['$ocLazyLoad',
                                function($ocLazyLoad) {
                                    return $ocLazyLoad.load('toaster').then(
                                        function() {
                                            return $ocLazyLoad.load('js/controllers/houses.js');
                                        }
                                    );
                                }
                            ]
                        }
                    })
                    .state('app.deadlines', {
                        url: '/deadlines',
                        template: '<div ui-view></div>'
                    })

                .state('app.deadlines.houses', {
                        url: '/houses',
                        templateUrl: 'tpl/deadline_houses.html',
                        resolve: {
                            deps: ['uiLoad',
                                function(uiLoad) {
                                    return uiLoad.load(['js/controllers/table.js']);
                                }
                            ]
                        }
                    })
                    .state('app.deadlines.tenants', {
                        url: '/tenants',
                        templateUrl: 'tpl/deadline_tenants.html',
                        resolve: {
                            deps: ['uiLoad',
                                function(uiLoad) {
                                    return uiLoad.load(['js/controllers/deadlines.js']);
                                }
                            ]
                        }
                    })



                // others

                .state('lockme', {
                        url: '/lockme',
                        templateUrl: 'tpl/page_lockme.html',
                        resolve: {
                            deps: ['uiLoad',
                                function(uiLoad) {
                                    return uiLoad.load(['js/controllers/login.js']);
                                }
                            ]
                        }
                    })
                    .state('access', {
                        url: '/access',
                        template: '<div ui-view class="fade-in-right-big smooth"></div>'
                    })
                    .state('access.signin', {
                        url: '/signin',
                        templateUrl: 'tpl/page_signin.html',
                        resolve: {
                            deps: ['uiLoad',
                                function(uiLoad) {
                                    return uiLoad.load(['js/controllers/signin.js']);
                                }
                            ]
                        }
                    })
                    .state('access.signup', {
                        url: '/signup',
                        templateUrl: 'tpl/page_signup.html',
                        resolve: {
                      deps: ['$ocLazyLoad',
                        function( $ocLazyLoad){
                          return $ocLazyLoad.load('angularFileUpload').then(
                              function(){
                                 return $ocLazyLoad.load('js/controllers/signup.js');
                              }
                          );
                      }]
                  }
                       
                    })
                    .state('access.forgotpwd', {
                        url: '/forgotpwd',
                        templateUrl: 'tpl/page_forgotpwd.html'
                    })
                    .state('access.404', {
                        url: '/404',
                        templateUrl: 'tpl/page_404.html'
                    })

                // fullCalendar




            }
        ]
    );