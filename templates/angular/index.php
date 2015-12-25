<!DOCTYPE html>
<html data-ng-app="admin" ng-controller="LayoutController as layout">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1.0" />
    <title>Admin</title>    
    <link rel="stylesheet" href="/bower_components/semantic-ui/dist/semantic.css" />
    <link rel="stylesheet" href="/bower_components/jquery/css/jquery-ui.css" />    
    <link rel="stylesheet" href="/css/admin.css" />    
        
    <script src="/bower_components/jquery/jquery-1.11.2.min.js"></script>
    <script src="/bower_components/jquery/jquery-ui.js"></script>    
    <script src="/bower_components/semantic-ui/dist/semantic.js"></script>   
    <script src="/bower_components/angular/angular.js"></script>
    <script src="/bower_components/angular/angular-local-storage.js"></script>
    <script src="/bower_components/angular/angular-ui-router.js"></script>       
    
    <script src="/app/admin/admin.module.js"></script>   
    <script src="/app/admin/layout/layout.module.js"></script>
    <script src="/app/admin/layout/config.js"></script>
    <script src="/app/admin/layout/layout.js"></script>
    <script src="/app/admin/layout/layout.service.js"></script>
    <script src="/app/admin/layout/connection.service.js"></script>
    
    <script src="/app/admin/user/user.module.js"></script>   
    <script src="/app/admin/user/user.js"></script>
    <script src="/app/admin/user/user.service.js"></script>
</head>
<body>
    <div class="login-form-container" ng-if="!layout.getIsUserLoggedIn()" ng-include="'/app/admin/user/login-form.html'"></div>
    <div class="admin-wrapper" ng-if="layout.getIsUserLoggedIn()">
        <div ng-include="'/app/admin/user/menu.html'"></div>
        <div class="ui vertical feature segment admin-content">
            <div class="ui centered page grid">
                <div class="fourteen wide column">
                    <div ui-view></div>
                    <div class="back-button-wrapper" ng-show="layout.showBackButton">                                   
                        <a ng-click="layout.returnBack()">
                            <div class="ui blue button add-btn">
                                <i class="arrow left icon"></i> Back
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>        
        <div ng-include="'/app/admin/layout/footer.html'"></div>
    </div>
</body>
</html>
