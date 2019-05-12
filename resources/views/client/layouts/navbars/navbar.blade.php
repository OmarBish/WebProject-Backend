@auth()
    @include('client.layouts.navbars.navs.auth')
@endauth
    
@guest()
    @include('client.layouts.navbars.navs.guest')
@endguest