@auth()
    @include('tester.layouts.navbars.navs.auth')
@endauth
    
@guest()
    @include('tester.layouts.navbars.navs.guest')
@endguest