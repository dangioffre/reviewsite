@php $level = isset($level) ? $level : 0; @endphp
<div class="flex items-start gap-4 {{ $level > 0 ? 'ml-10' : '' }} border border-[#292929] rounded-xl bg-[#18181B] p-4">
    <div class="w-10 h-10 rounded-full bg-[#232326] flex items-center justify-center text-white font-bold text-lg font-['Poppins']">
        {{ substr($comment->user->name ?? 'A', 0, 1) }}
    </div>
    <div class="flex-1">
        <div class="flex items-center gap-2 mb-1">
            <span class="font-semibold text-white font-['Inter'] text-base">{{ $comment->user->name ?? 'Anonymous' }}</span>
            <span class="text-xs text-[#A0A0A0] font-['Inter']">{{ $comment->created_at->diffForHumans() }}</span>
        </div>
        <div class="text-[#A1A1AA] text-sm font-['Inter'] leading-relaxed mb-2">{{ $comment->content }}</div>
        <div class="flex items-center gap-4 mt-2">
            <div class="flex items-center gap-4">
                @auth
                    <button type="button" class="text-xs text-[#2563EB] hover:underline font-semibold" onclick="document.getElementById('parent_id').value = '{{ $comment->id }}'; document.querySelector('textarea[name=content]').focus();">Reply</button>
                @endauth
                <!-- Thumbs Up Button (AJAX) -->
                <div x-data="likeComment({{ $comment->id }}, '{{ route('review-comments.like', $comment) }}', {{ auth()->check() && $comment->isLikedBy(auth()->id()) ? 'true' : 'false' }}, {{ $comment->likes_count }}, {{ auth()->check() ? 'true' : 'false' }})">
                    <button @click.prevent="canLike ? toggleLike() : window.location.href='{{ route('login') }}'" :class="[liked ? 'text-[#DC2626]' : 'text-[#A0A0A0] hover:text-[#DC2626]']" class="flex items-center gap-1 transition text-xs font-semibold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M7 10v12" />
                            <path d="M15 5.88 14 10h5.83a2 2 0 0 1 1.92 2.56l-2.33 8A2 2 0 0 1 17.5 22H4a2 2 0 0 1-2-2v-8a2 2 0 0 1 2-2h2.76a2 2 0 0 0 1.79-1.11L12 2a2 2 0 0 1 3 3.88Z" />
                        </svg>
                        <span x-text="count"></span>
                    </button>
                </div>
            </div>
            <div class="flex-1"></div>
            <!-- Report Button (AJAX/modal) -->
            <div x-data="reportComment({{ $comment->id }}, '{{ route('review-comments.report', $comment) }}', {{ auth()->check() ? 'true' : 'false' }})">
                <button @click.prevent="canReport ? openModal() : window.location.href='{{ route('login') }}'" class="flex items-center gap-1 text-[#A0A0A0] hover:text-[#DC2626] transition text-xs font-semibold ml-auto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6L6 18" />
                        <path d="M6 6l12 12" />
                    </svg>
                    Report
                </button>
                <!-- Modal -->
                <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60">
                    <div class="bg-[#232326] rounded-xl p-8 w-full max-w-md shadow-2xl border border-[#292929] relative">
                        <h3 class="text-xl font-bold text-white mb-4 font-['Poppins']">Report Comment</h3>
                        <form @submit.prevent="submitReport">
                            <label class="block text-sm font-semibold text-white mb-2">Reason</label>
                            <select x-model="reason" class="w-full bg-[#18181B] border border-[#292929] rounded-lg p-2 text-white mb-4" required>
                                <option value="">Select a reason...</option>
                                <option value="Spam">Spam</option>
                                <option value="Harassment">Harassment</option>
                                <option value="Off-topic">Off-topic</option>
                                <option value="Inappropriate">Inappropriate</option>
                                <option value="Other">Other</option>
                            </select>
                            <label class="block text-sm font-semibold text-white mb-2">Details (optional)</label>
                            <textarea x-model="details" rows="3" class="w-full bg-[#18181B] border border-[#292929] rounded-lg p-2 text-white mb-4"></textarea>
                            <div class="flex justify-end gap-2">
                                <button type="button" @click="closeModal" class="px-4 py-2 rounded-lg bg-[#292929] text-white font-semibold">Cancel</button>
                                <button type="submit" class="px-4 py-2 rounded-lg bg-[#DC2626] text-white font-bold hover:bg-[#B91C1C]">Submit</button>
                            </div>
                        </form>
                        <template x-if="success">
                            <div class="mt-4 text-green-500 font-semibold">Report submitted. Thank you!</div>
                        </template>
                        <template x-if="error">
                            <div class="mt-4 text-red-500 font-semibold" x-text="error"></div>
                        </template>
                        <button @click="closeModal" class="absolute top-2 right-2 text-[#A0A0A0] hover:text-white">&times;</button>
                    </div>
                </div>
            </div>
        </div>
        @if($comment->children && $comment->children->count() > 0)
            <div class="mt-4 space-y-4">
                @foreach($comment->children as $child)
                    @include('reviews.partials.comment', ['comment' => $child, 'level' => $level + 1])
                @endforeach
            </div>
        @endif
    </div>
</div>
<script>
function likeComment(commentId, likeUrl, initiallyLiked, initialCount, canLike) {
    return {
        liked: initiallyLiked,
        count: initialCount,
        canLike: canLike,
        toggleLike() {
            if (!this.canLike) return;
            fetch(likeUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                    'Accept': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.liked !== undefined) {
                    this.liked = data.liked;
                    this.count = data.likes_count;
                }
            });
        }
    }
}
function reportComment(commentId, reportUrl, canReport) {
    return {
        showModal: false,
        reason: '',
        details: '',
        success: false,
        error: '',
        canReport: canReport,
        openModal() {
            this.showModal = true;
            this.success = false;
            this.error = '';
            this.reason = '';
            this.details = '';
        },
        closeModal() {
            this.showModal = false;
        },
        submitReport() {
            this.error = '';
            fetch(reportUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ reason: this.reason, details: this.details })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.success = true;
                    setTimeout(() => { this.closeModal(); }, 1500);
                } else if (data.error) {
                    this.error = data.error;
                }
            })
            .catch(() => {
                this.error = 'An error occurred. Please try again.';
            });
        }
    }
}
</script> 