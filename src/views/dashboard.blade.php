
@extends('elemental.main')

@section('content')

<main ng-app="elementalApp">
     
    <div ng-controller="DashCtrl"> 
        <div class="ui active inverted dimmer" route-loader>
            <div class="ui text loader">Loading…</div>
        </div>
         <div ng-view></div>
    </div>

    <div class="ui right vertical red labeled icon wide sidebar menu uncover cms-main" cms-sidebar></div>
    

</main>
@stop