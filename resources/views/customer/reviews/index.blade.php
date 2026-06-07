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
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            @if($review->product->primary_image_url)
                                                <img src="{{ $review->product->primary_image_url }}" alt="" style="width: 40px; height: 40px; border-radius: 6px; object-fit: cover;">
                                            @else
                                                <div style="width: 40px; height: 40px; background: #eee; border-radius: 6px;"></div>
                                            @endif
                                            <a href="{{ route('product.detail', $review->product->slug ?? '#') }}" style="font-weight: 500; color: var(--secondary-900);">
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
    <div id="editReviewModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeEditModal()">&times;</span>
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

    <style>
        .modal {
            display: none; 
            position: fixed; 
            z-index: 1000; 
            left: 0; 
            top: 0; 
            width: 100%; 
            height: 100%; 
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color: white;
            margin: 10% auto; 
            padding: 30px; 
            border-radius: 12px;
            width: 90%; 
            max-width: 500px;
            position: relative;
        }
        .close-modal {
            position: absolute;
            right: 20px;
            top: 15px;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            color: #aaa;
        }
        .close-modal:hover { color: black; }

        /* Star Rating in Modal */
        .rating-select {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
            gap: 5px;
        }
        .rating-select input { display: none; }
        .rating-select label {
            cursor: pointer;
            font-size: 24px;
            color: #ddd;
            transition: color 0.2s;
        }
        .rating-select input:checked ~ label,
        .rating-select label:hover,
        .rating-select label:hover ~ label {
            color: #fbbf24;
        }
    </style>

    <script>
        function openEditModal(review) {
            const modal = document.getElementById('editReviewModal');
            const form = document.getElementById('editReviewForm');
            const comment = document.getElementById('editComment');
            
            // Set action URL
            form.action = '{{ route("customer.reviews.update", "") }}/' + review.id;
            
            // Set value
            comment.value = review.comment;
            
            // Set rating
            const ratingRadio = document.getElementById('star' + review.rating);
            if (ratingRadio) ratingRadio.checked = true;

            modal.style.display = 'block';
        }

        function closeEditModal() {
            document.getElementById('editReviewModal').style.display = 'none';
        }

        // Close when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('editReviewModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
@endsection
