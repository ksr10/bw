(function() {
    'use strict';
    
    angular
        .module('admin.layout')
        .service('connectionService', connectionService);
        
    connectionService.$inject = ['$http'];
        
    function connectionService($http) {
        var connectionService = {
            apiUrl: 'https://tisp-sprocket.c9.io/',
            //apiUrl: 'http://insurance-api.local/',
                                                
            makeRequest: makeRequest,
            showLoading: showLoading,
            hideLoading: hideLoading
        };
        
        return connectionService;
                
        function makeRequest(type, url, callback, params, callbackParams, exclude) {
            if (type == 'get') {
                
                $http.get(url).
                success(function(data, status, headers, config) {
                   callback(data, callbackParams);
                }).
                error(function(data, status, headers, config) {
                   
                });
            } else if (type == 'put') {
                $http.put(url).
                success(function(data, status, headers, config) {
                   callback(data, callbackParams);
                }).
                error(function(data, status, headers, config) {
                   
                });
            } else if (type == 'delete') {
                $http.delete(url).
                success(function(data, status, headers, config) {
                   callback(data, callbackParams);
                }).
                error(function(data, status, headers, config) {
                   
                });
            } else if (type == 'post') {
                if (!exclude) {
                    exclude = [];
                }
                
                $http({
                    method: 'POST',
                    url: url,
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    transformRequest: function(obj) {
                        var str = [];
                        for (var p in obj) {         
                            if (exclude.indexOf(p) != '-1') {
                                continue;
                            }
                            str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));                        
                        }                    

                        return str.join("&");
                    },
                    data: params
                }).success(function (data) {
                    callback(data, callbackParams);
                });
            }
        }       
        
        function showLoading(iconClass, wrapperClass) {
            var obj = $('.btn-wrapper .icon');
            
            if (wrapperClass) {
                obj = $(wrapperClass);
            }
            
            obj.removeClass(iconClass);
            obj.addClass('spinner');
            obj.addClass('loading');            
        }
        
        function hideLoading(iconClass, wrapperClass) {
            var obj = $('.btn-wrapper .icon');
            
            if (wrapperClass) {
                obj = $(wrapperClass);
            }
            
            obj.removeClass('spinner');
            obj.removeClass('loading');
            obj.addClass(iconClass);
        }
    }   
})();