@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <h3 class="mb-3">Posts</h3>
            <div class="row g-3 post-lists">
            </div>
        </div>
        @component('posts.friend_lists')
        @endcomponent
        @component('posts.detail')

        @endcomponent
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            function renderPostCardAll(post) {
                const likes = post.likes_count ?? 0;
                const comments = post.comments_count ?? 0;
                const postImageUrl = post.imagePathUrl || 'https://via.placeholder.com/500';
                const userAvatarUrl = post.user?.imagePathUrl || 'https://via.placeholder.com/500';
                const timeAgoText = timeAgo(post.created_at);

                const wrapper = document.createElement('div');
                wrapper.classList.add('col-md-8', 'offset-md-2');
                wrapper.innerHTML = `
         <div class="insta-card card shadow-sm border-0 mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <div class="post-user-avatar-skeleton"></div>
                        <img src="https://i.pravatar.cc/40" class="rounded-circle post-user-avatar" width="40" height="40"
                        alt="User">
                        <span class="fw-semibold">${post.user?.username}</span>
                    </div>
                </div>

            <div class="post-img-box position-relative cursor-pointer" data-bs-toggle="modal"
                                data-bs-target="#detailPostModal" data-id="${post.id}" >
                <div class="post-image-skeleton"></div>
                <img src="https://picsum.photos/600/600" class="w-100 d-block post-image" alt="Post image" loading="lazy">
            </div>

            <div class="card-body px-3">
                <div class="d-flex align-items-center gap-4">
                    <div class="d-flex align-items-center gap-2">
                        <i class="${post.is_liked ? 'fa' : 'fa-regular'} fa-heart fa-lg cursor-pointer post-like" data-id="${post.id}"></i>
                        <span class="fw-semibold post-likes">${likes}</span>
                    </div>

                    <div class="d-flex align-items-center gap-2" data-bs-toggle="modal"
                                data-bs-target="#detailPostModal" data-id="${post.id}">
                        <i class="${post.is_commented ? 'fa' : 'fa-regular'} fa-comment fa-lg cursor-pointer"></i>
                        <span class="fw-semibold">${comments}</span>
                    </div>
                </div>
                <div class="caption mt-1">
                    <span class="fw-semibold">
                </div>

                <div class="text-light my-2 small">
                    ${post.caption}
                </div>

                <div class="text-uppercase text-light small mt-1">${timeAgoText}</div>
            </div>
        </div>
        `;

                loadImageWithSkeleton(wrapper.querySelector('.post-user-avatar'), wrapper.querySelector(
                    '.post-user-avatar-skeleton'), userAvatarUrl);

                loadImageWithSkeleton(wrapper.querySelector('.post-image'), wrapper.querySelector(
                    '.post-image-skeleton'), postImageUrl);


                return wrapper;
            }

            function renderPostAll(posts) {
                const container = document.querySelector('.post-lists');
                posts.forEach(post => {
                    const card = renderPostCardAll(post);
                    container.appendChild(card);
                });
            }


            let nextPageUrlPosts = '{{ url('/api/posts/all') }}?limit=10';
            let isLoadingPosts = false;

            function getPostsAll(url) {
                if (!url || isLoadingPosts) return;
                isLoadingPosts = true;

                axios.get(url)
                    .then(function(response) {
                        const posts = response.data.data.data;
                        nextPageUrlPosts = response.data.data.next_page_url;
                        renderPostAll(posts)
                        isLoadingPosts = false;
                    })
                    .catch(function(error) {
                        console.error('Failed to load posts:', error.response || error);
                        isLoadingPosts = false;
                    });
            }

            $(document).on('click', '.post-like', function(e) {
                e.preventDefault();

                const $this = $(this);
                const postId = $this.data('id');

                const url = `${window.baseUrl}/posts/${postId}/like`;

                axios.post(url)
                    .then(response => {
                        if(response.data.data.is_liked){
                            $this.removeClass('fa-regular')
                            $this.addClass('fa')
                        } else {
                            $this.removeClass('fa')
                            $this.addClass('fa-regular')
                        }
                        $this.closest('.insta-card').find('.post-likes').text(response.data.data.count)
                    })
                    .catch(error => {})
                    .finally(() => {});
            });

            getPostsAll(nextPageUrlPosts);

            window.addEventListener('scroll', () => {
                if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 200) {
                    getPostsAll(nextPageUrlPosts);
                }
            });
        });
    </script>
@endpush
