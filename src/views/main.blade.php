<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Elemental CMS</title>

    
    <!--<link href="/css/app.css" rel="stylesheet">-->
    <link href="/js/elemental/ui/semantic-ui/semantic.css" rel='stylesheet' type='text/css'>
    <!-- Fonts -->
    <link href='//fonts.googleapis.com/css?family=Roboto:400,300' rel='stylesheet' type='text/css'>

    <link rel="stylesheet" href="/js/elemental/ui/redactor/redactor.css" />

    <link rel="stylesheet" href="/js/bower_components/ng-sortable/dist/ng-sortable.min.css" />
    <link rel="stylesheet" type="text/css" href="/js/bower_components/ng-sortable/dist/ng-sortable.style.min.css">
    <!-- <link rel="stylesheet" href="/js/bower_components/jquery.fileapi/jcrop/jquery.Jcrop.min.css" /> -->
    <link rel="stylesheet" href="/js/bower_components/selectize/dist/css/selectize.default.css">
    <link rel="stylesheet" href="/js/bower_components/angularjs-datepicker/dist/angular-datepicker.min.css" />

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
    <div class="pusher">
        <nav class="ui main menu">
            <a class="view-ui right item sidebar-toggle">
              <i class="sidebar icon"></i> Menu
            </a>
        
        
            <div class="ui dropdown item">
                <% Auth::user()->name %> <i class="dropdown icon"></i>
                <div class="menu">
                    <a class="item" href="/auth/logout">Logout</a>
                </div>
                
                
            </div>
               
        </nav>
        
        @yield('content')
    </div>

    
    <!-- build:js(.) scripts/vendor.js -->
    <!-- bower:js -->
    <script src="/js/bower_components/jquery/dist/jquery.min.js"></script>
    <script src="/js/elemental/ui/semantic-ui/semantic.js"></script>
    
    <script src="/js/elemental/ui/redactor/redactor.js"></script>
    <script src="/js/elemental/ui/redactor/plugins/video.js"></script>
    <script src="/js/elemental/ui/redactor/plugins/definedLinks.js"></script>
    <script src="/js/elemental/ui/redactor/plugins/table.js"></script>
    <script src="/js/elemental/ui/redactor/plugins/imagemanager.js"></script>
    <script src="/js/elemental/ui/redactor/plugins/fullscreen.js"></script>
    <script src="/js/bower_components/underscore/underscore-min.js"></script>
    <script src="/js/bower_components/angular/angular.js"></script>
    <script src="/js/bower_components/angular-animate/angular-animate.js"></script>
    <script src="/js/bower_components/angular-cookies/angular-cookies.js"></script>
    <script src="/js/bower_components/angular-resource/angular-resource.js"></script>
    <script src="/js/bower_components/angular-route/angular-route.js"></script>
    <script src="/js/bower_components/angular-sanitize/angular-sanitize.js"></script>
    <script src="/js/bower_components/angular-touch/angular-touch.js"></script>
    <script src="/js/bower_components/ng-sortable/dist/ng-sortable.min.js"></script>
    <script src="/js/bower_components/ng-sortable/dist/ng-sortable.min.js"></script>
    <script src="/js/bower_components/selectize/dist/js/standalone/selectize.min.js"></script>
    <script src="/js/bower_components/angular-selectize2/dist/selectize.js"></script>
    <script src="/js/bower_components/angularjs-datepicker/dist/angular-datepicker.min.js"></script>
    <script src="/js/bower_components/angular-redactor/angular-redactor.js"></script>
    <script src="/js/bower_components/flow.js/dist/flow.min.js"></script>
    <script src="/js/bower_components/ng-flow/dist/ng-flow.min.js"></script>
    <!-- endbower -->
    <!-- endbuild -->

    <!-- build:js({.tmp,app}) scripts/scripts.js -->
    <script src="/js/elemental/scripts/app.js"></script>
    <script src="/js/elemental/scripts/controllers/dashCtrl.js"></script>
    <script src="/js/elemental/scripts/controllers/collectionCtrl.js"></script>
    <script src="/js/elemental/scripts/controllers/userCtrl.js"></script>
    <script src="/js/elemental/scripts/services/collection.js"></script>
    <script src="/js/elemental/scripts/services/element.js"></script>
    <script src="/js/elemental/scripts/services/component.js"></script>
    <script src="/js/elemental/scripts/services/user.js"></script>
    <script src="/js/elemental/scripts/directives/routeloader.js"></script>
    <script src="/js/elemental/scripts/directives/cmsCollection.js"></script>
    <script src="/js/elemental/scripts/directives/cmsElement.js"></script>
    <script src="/js/elemental/scripts/directives/cmsStatus.js"></script>
    <script src="/js/elemental/scripts/directives/cmsAdd.js"></script>
    <script src="/js/elemental/scripts/directives/cmsField.js"></script>
    <script src="/js/elemental/scripts/directives/cmsImage.js"></script>
    <script src="/js/elemental/scripts/directives/cmsSidebar.js"></script>
    <script src="/js/elemental/scripts/directives/cmsSelect.js"></script>
    <script src="/js/elemental/scripts/directives/cmsCheckbox.js"></script>
    <script src="/js/elemental/scripts/directives/cmsModal.js"></script>
    <script src="/js/elemental/scripts/directives/cmsCollapsable.js"></script>
    <script src="/js/elemental/scripts/directives/passwordConfirm.js"></script>
    <!-- endbuild -->

     <script>
        $('.ui.dropdown').dropdown();
     </script>

</body>
</html>
