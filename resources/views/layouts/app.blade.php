<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title ?? 'App' }} | InstaApps</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Pacifico&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://unpkg.com/cropperjs@1.6.2/dist/cropper.min.css" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
</head>

<body>

    <div class="container-fluid">
        <div class="row">
            <div class="d-md-none px-3 sticky-top shadow bg-header py-4">
                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 font-pacifico ps-3">InstaApp</h4>
                    <button class="btn btn-outline-light" type="button" data-bs-toggle="offcanvas"
                        data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
                        <i class="fa fa-bars"></i>
                    </button>
                </div>
            </div>

            <div
                class="col-md-3 col-lg-2 futuristic vh-100 p-3 position-fixed d-none d-md-flex flex-column shadow sidebar">
                <div>
                    <h4 class="mb-4 font-pacifico ps-3 text-white">InstaApp</h4>
                    <ul class="nav flex-column">
                        <li class="nav-item mb-3 {{ request()->routeIs('home') ? 'active' : '' }}"><a
                                class="nav-link text-white" href="{{ route('home') }}"><i
                                    class="fa fa-home text-center nav-icon"></i>Home</a></li>
                        <li class="nav-item mb-3 {{ request()->routeIs('profile') ? 'active' : '' }}"><a
                                class="nav-link text-white" href="{{ route('profile') }}"><img src=""
                                    class="profile-sidebar" alt="">
                                <div class="avatar-profile-sidebar"></div>Profile
                            </a></li>
                    </ul>
                </div>
                <div class="mt-auto ps-3">
                    <a class="nav-logout text-danger" href="#" onclick="logout()"><i
                            class="fa fa-sign-out-alt me-1 nav-icon"></i>Logout</a>
                </div>

                <div class="bubble b1"></div>
                <div class="bubble b2"></div>
                <div class="bubble b3"></div>
                <div class="bubble b4"></div>
            </div>


            <div class="offcanvas offcanvas-end p-0 sidebar-mobile" tabindex="-1" id="mobileSidebar"
                aria-labelledby="mobileSidebarLabel">
                <div class="offcanvas-header custom">
                    <h5 class="offcanvas-title font-pacifico" id="mobileSidebarLabel">InstaApp</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="d-flex flex-column offcanvas-body futuristic">
                    <div>
                        <ul class="nav flex-column">
                            <li class="nav-item mb-3 {{ request()->routeIs('home') ? 'active' : '' }}"><a
                                    class="nav-link text-white" href="{{ route('home') }}"><i
                                        class="fa fa-home text-center nav-icon"></i>Home</a></li>
                            <li class="nav-item mb-3 {{ request()->routeIs('profile') ? 'active' : '' }}"><a
                                    class="nav-link text-white" href="{{ route('profile') }}"><img src=""
                                        class="profile-sidebar" alt="">Profile</a></li>
                        </ul>
                    </div>
                    <div class="mt-auto ps-3 mb-4">
                        <a class="nav-logout text-danger" href="#" onclick="logout()"><i
                                class="fa fa-sign-out-alt me-1 nav-icon"></i>Logout</a>
                    </div>

                    <div class="bubble b1"></div>
                    <div class="bubble b2"></div>
                    <div class="bubble b3"></div>
                    <div class="bubble b4"></div>
                </div>
            </div>

            <div class="col-md-9 offset-md-3 col-lg-10 offset-lg-2 py-4 px-4">
                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js"
        integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://unpkg.com/cropperjs@1.6.2/dist/cropper.min.js"></script>

    <script>
        axios.defaults.headers.common['Authorization'] = 'Bearer ' + localStorage.getItem('auth_token');
        axios.defaults.headers.common['Accept'] = 'application/json';
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        if (csrfMeta) {
            axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfMeta.getAttribute('content');
        }
    </script>

    <script>
        window.baseUrl = "{{ url('/api') }}";

        function loadImageWithSkeleton(img, skeleton, src) {
            const imgEl = typeof img === 'string' ? document.querySelector(img) : img;
            const skeletonEl = typeof skeleton === 'string' ? document.querySelector(skeleton) : skeleton;

            if (!imgEl) return;

            const tempImg = new Image();
            tempImg.src = src;
            tempImg.onload = () => {
                imgEl.src = src;
                imgEl.style.display = 'block';
                if (skeletonEl) skeletonEl.remove();
            };
        }

        function timeAgo(dateString) {
            const now = new Date();
            const postDate = new Date(dateString);
            const diff = Math.floor((now - postDate) / 1000);

            if (diff < 60) return `${diff} seconds ago`;
            if (diff < 3600) return `${Math.floor(diff / 60)} minutes ago`;
            if (diff < 86400) return `${Math.floor(diff / 3600)} hours ago`;
            if (diff < 2592000) return `${Math.floor(diff / 86400)} days ago`;
            if (diff < 31104000) return `${Math.floor(diff / 2592000)} months ago`;
            return `${Math.floor(diff / 31104000)} years ago`;
        }


        function logout() {
            axios.post('{{ url('/api/auth/logout') }}')
                .then(function(response) {
                    window.location.href = '/auth/login';
                })
                .catch(function() {});
        }

        function getAuthMe() {
            axios.get('{{ url('/api/auth/me') }}')
                .then(function(response) {
                    const data = response.data.data;
                    const imageUrl = data.imagePathUrl || '/assets/img/default-avatar.png';

                    const $profileImg = $('.profile-sidebar');
                    const $skeleton = $('.avatar-profile-sidebar');

                    const tempImg = new Image();
                    tempImg.src = imageUrl;
                    tempImg.onload = function() {
                        $profileImg.attr('src', imageUrl).show();
                        $skeleton.remove();
                    };

                    const currentPath = window.location.pathname;

                    if (currentPath === '/profile') {
                        const $profileImgProfile = $('.profile-avatar');
                        const $skeletonProfile = $('.skeleton-profile-avatar');
                        const $editAvatarIcon = $('.edit-avatar-icon');

                        const tempImgProfile = new Image();
                        tempImgProfile.src = imageUrl;
                        tempImgProfile.onload = function() {
                            $profileImgProfile.attr('src', imageUrl).show();
                            $skeletonProfile.remove();
                            $editAvatarIcon.show();
                        };

                        $('.profile-item .username').text(data.username || '');
                        $('.profile-item .name').text(data.name || '');
                        $('.profile-item .post-count-profile').text(data.posts_count ?? 0);
                        $('.profile-item .followers-count-profile').text(data.followers_count ?? 0);
                        $('.profile-item .following-count-profile').text(data.followings_count ?? 0);
                    }

                })
                .catch(function() {
                    window.location.href = '/auth/login';
                });
        }

        $(document).ready(function() {
            getAuthMe();
        });
    </script>

    @stack('script')
</body>

</html>
