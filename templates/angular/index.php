<!DOCTYPE html>
<html data-ng-app="app">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1.0" />
    <title>Admin</title>    
    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="/bower_components/jquery/css/jquery-ui.css" />
    <link rel="stylesheet" href="/assets/css/main.css">  
    <link rel="stylesheet" href="/assets/css/override.css">    
</head>
<body>
<div class="wrapper">
  <header class="header" id="header">
    <div class="container">
      <div class="logo header__logo">        
      </div>
      <div class="header__auth">
        <div class="header__actions">
          
        </div>        
        <nav class="menu">
          <ul class="menu__list">
            <li class="menu__item">              
            </li>
            <li class="menu__item">              
            </li>
            <li class="menu__item">              
            </li>
          </ul>
        </nav>
      </div>
    </div>
  </header><!-- .header-->

  <main class="content">
    <div class="container">
      <div class="container-inner">
        <div id="baseContent" ui-view></div>
        </section>
      </div>
    </div>
  </main><!-- .content -->

</div><!-- .wrapper -->

<footer class="footer">
  <div class="container">
    <div class="fl-l">
      <div class="logo footer__logo">
        
      </div>
     
    </div>
    <div class="fl-r">      
    </div>
  </div>
</footer><!-- .footer -->

<script src="/bower_components/jquery/jquery-1.11.2.min.js"></script>
<script src="/bower_components/jquery/jquery-ui.js"></script>
<script src="/bower_components/angular/angular.js"></script>
<script src="/bower_components/angular/angular-ui-router.js"></script>
<script src="/bower_components/angular/angular-local-storage.js"></script>

<script src="/app/app.module.js"></script>
<script src="/app/bet/bet.module.js"></script>
<script src="/app/bet/config.js"></script>
<script src="/app/bet/controllers/bet.js"></script>
<script src="/app/bet/services/bet.service.js"></script>

</body>
</html>
