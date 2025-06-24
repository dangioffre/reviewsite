import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Alpine.js
import Alpine from 'alpinejs';
window.Alpine = Alpine;

// Global likeReview Alpine.js component
window.likeReview = function(reviewId, likeUrl, initiallyLiked, initialCount, canLike) {
    return {
        liked: initiallyLiked,
        count: initialCount,
        canLike: canLike,
        toggleLike() {
            if (!this.canLike) {
                window.location.href = '/login';
                return;
            }
            window.axios.post(likeUrl)
                .then(response => {
                    this.liked = response.data.liked;
                    this.count = response.data.likes_count;
                })
                .catch(error => {
                    if (error.response && error.response.status === 401) {
                        window.location.href = '/login';
                    }
                });
        }
    }
}

Alpine.start();
