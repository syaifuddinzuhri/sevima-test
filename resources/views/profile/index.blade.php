@extends('layouts.app')

@section('content')
    <div class="row mb-5">
        <div class="col-md-6 offset-md-3">
            <div class="profile-item py-3">
                <div class="d-flex align-items-center gap-4">
                    <div class="profile-avatar-box">
                        <img src="https://i.pravatar.cc/300" alt="" class="profile-avatar" />
                        <span class="badge rounded-pill text-bg-light edit-avatar-icon p-2 cursor-pointer">
                            <i class="fas fa-pen"></i>
                        </span>
                        <input type="file" id="avatarInput" accept="image/*" class="d-none">
                    </div>
                    <div class="skeleton-profile-avatar"></div>
                    <div>
                        <p class="username"></p>
                        <p class="name"></p>
                        <div class="py-2 d-flex align-items-center justify-content-center gap-4 profile-data">
                            <p><span class="post-count-profile"></span> Post</p>
                            <p><span class="followers-count-profile"></span> Followers</p>
                            <p><span class="following-count-profile"></span> Following</p>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <button class="btn btn-secondary btn-sm btn-glass btn-padding" data-bs-toggle="modal"
                                data-bs-target="#editProfileModal">Edit Profile</button>
                            <button class="btn btn-primary btn-sm btn-glass btn-padding" data-bs-toggle="modal"
                                data-bs-target="#addPostModal">Add Post</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="row g-3 post-lists">
            </div>
        </div>
    </div>

    @component('posts.detail')
    @endcomponent
    @component('posts.create')
    @endcomponent
    @component('posts.crop-image')
    @endcomponent
    @component('profile.edit-profile')
    @endcomponent
    @component('profile.crop-avatar')
    @endcomponent
@endsection

@push('script')
    <script>
        let nextPageUrl = '{{ url('/api/posts') }}?limit=9';
        let isLoading = false;

        function renderSkeletonCardPost(count) {
            const container = document.querySelector('.post-lists');
            for (let i = 0; i < count; i++) {
                container.insertAdjacentHTML('beforeend', ` <div
        class="col-6 col-md-6 col-lg-4 post-item">
        <div class="card-wrapper">
            <div class="card card-skeleton"></div>
        </div>
        </div>
        `);
            }
        }

        function removeSkeletonCardPost() {
            const skeletons = document.querySelectorAll('.card-skeleton');
            skeletons.forEach(skel => skel.closest('.post-item').remove());
        }

        function renderPostCard(post) {
            const likes = post.likes_count ?? 0;
            const comments = post.comments_count ?? 0;
            const imageUrl = post.imagePathUrl || post.image || 'https://via.placeholder.com/500';

            const wrapper = document.createElement('div');
            wrapper.classList.add('col-6', 'col-md-6', 'col-lg-4', 'post-item');
            wrapper.innerHTML = `
        <div class="card-wrapper" data-bs-toggle="modal"
                                data-bs-target="#detailPostModal" data-id="${post.id}" data-me="true">
            <div class="card hover border-0 shadow-sm position-relative overflow-hidden cursor-pointer">
                <div class="img-skeleton"></div>
                <img src="${imageUrl}" class="card-img-top post-img" alt="Post image" loading="lazy">
                <div class="card-overlay d-flex justify-content-center align-items-center">
                    <div class="d-flex gap-3 text-white fw-bold">
                        <span><i class="fa fa-heart"></i> ${likes}</span>
                        <span><i class="fa fa-comment"></i> ${comments}</span>
                    </div>
                </div>
            </div>
        </div>
        `;

            const img = wrapper.querySelector('.card-img-top');
            const skeleton = wrapper.querySelector('.img-skeleton');

            const tempImg = new Image();
            tempImg.src = imageUrl;
            tempImg.onload = () => {
                img.src = imageUrl;
                img.style.display = 'block';
                skeleton.remove();
            };

            return wrapper;
        }

        function renderPosts(posts) {
            const container = document.querySelector('.post-lists');
            posts.forEach(post => {
                const card = renderPostCard(post);
                container.appendChild(card);
            });
        }

        function getPosts(url, reset = false) {
            if (!url || isLoading) return;
            isLoading = true;

            if (reset) {
                $('.post-lists').empty();
            }

            renderSkeletonCardPost(9);

            axios.get(url)
                .then(function(response) {
                    const posts = response.data.data.data;
                    nextPageUrl = response.data.data.next_page_url;
                    removeSkeletonCardPost();
                    renderPosts(posts);

                    isLoading = false;
                })
                .catch(function(error) {
                    console.error('Failed to load posts:', error.response || error);
                    isLoading = false;
                });
        }
        getPosts(nextPageUrl);

        window.addEventListener('scroll', () => {
            if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 200) {
                getPosts(nextPageUrl);
            }
        });
    </script>

    <script>
        $(document).ready(function() {
            $('.edit-avatar-icon').on('click', function() {
                $('#avatarInput').trigger('click');
            });

        });
    </script>
@endpush
