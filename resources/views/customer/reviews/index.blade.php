{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    View: Customer Reviews
--}}
@extends('layouts.app')

@section('title', 'My Reviews')
@section('page_title', 'My Reviews')

@section('sidebar')
    @include('customer.partials.sidebar')
@endsection

@section('content')
    <div class="dashboard-card">
        <div class="dashboard-card-header">
            <h3>My Reviews</h3>
        </div>
        <div class="dashboard-card-body">
            @if($reviews->count() > 0)
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Rating</th>
                                <th>Comment</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reviews as $review)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-sm">
                                            @if($review->product->primary_image_url)
                                                <img src="{{ $review->product->primary_image_url }}" alt="" class="review-product-img">
                                            @else
                                                <div class="review-product-placeholder"></div>
                                            @endif
                                            <a href="{{ route('product.detail', $review->product->slug ?? '#') }}" class="font-medium text-secondary-900">
                                                {{ Str::limit($review->product->name, 30) }}
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-warning">
                                            @for($i=1; $i<=5; $i++)
                                                <i class="fas fa-star {{ $i <= $review->rating ? '' : 'text-muted' }}"></i>
                                            @endfor
                                        </div>
                                    </td>
                                    <td>{{ Str::limit($review->comment, 50) }}</td>
                                    <td>{{ $review->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-secondary" onclick="openEditModal({{ $review }})" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('customer.reviews.destroy', $review->id) }}" method="POST" onsubmit="return confirm('Are you sure?');" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $reviews->links() }}
                </div>
            @else
                <div class="empty-state text-center py-5">
                    <i class="far fa-comment-alt fa-3x text-muted mb-3"></i>
                    <h3>No reviews yet</h3>
                    <p class="text-muted">You haven't reviewed any products yet.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Edit Review Modal -->
    <div id="editReviewModal" class="review-modal">
        <div class="review-modal-content">
            <span class="review-modal-close" onclick="closeEditModal()">&times;</span>
            <h2 class="mb-4">Edit Review</h2>
            <form id="editReviewForm" method="POST">
                @csrf
                @method('PUT')
                
                <div class="form-group mb-3">
                    <label class="form-label">Rating</label>
                    <div class="rating-select">
                        @for($i=5; $i>=1; $i--)
                            <input type="radio" id="star{{$i}}" name="rating" value="{{$i}}" required />
                            <label for="star{{$i}}" title="{{$i}} stars"><i class="fas fa-star"></i></label>
                        @endfor
                    </div>
                </div>

                <div class="form-group mb-4">
                    <label class="form-label">Comment</label>
                    <textarea name="comment" id="editComment" class="form-control" rows="4" required minlength="5" maxlength="1000"></textarea>
                </div>

                <div class="text-end">
                    <button type="button" class="btn btn-secondary me-2" onclick="closeEditModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Review</button>
                </div>
            </form>
        </div>
    </div>

    {{-- CSS classes in dashboard.css (review-modal, review-modal-content, review-modal-close, rating-select) --}}

    <script>
        function openEditModal(review) {
            const modal = document.getElementById('editReviewModal');
            const form = document.getElementById('editReviewForm');
            const comment = document.getElementById('editComment');
            form.action = '{{ route("customer.reviews.update", "") }}/' + review.id;
            comment.value = review.comment;
            const ratingRadio = document.getElementById('star' + review.rating);
            if (ratingRadio) ratingRadio.checked = true;
            modal.classList.add('open');
        }

        function closeEditModal() {
            document.getElementById('editReviewModal').classList.remove('open');
        }

        window.onclick = function(event) {
            const modal = document.getElementById('editReviewModal');
            if (event.target == modal) modal.classList.remove('open');
        }
    </script>
@endsection
