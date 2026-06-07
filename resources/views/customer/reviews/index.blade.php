{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    View: Customer Reviews (Premium v2.0)
--}}
@extends('layouts.app')

@section('title', 'My Reviews')
@section('page_title', 'My Reviews')

@section('sidebar')
    @include('customer.partials.sidebar')
@endsection

@section('content')
<div class="rev-page">

    {{-- Page Header --}}
    <div class="rev-header">
        <div>
            <h1 class="rev-title">My Reviews</h1>
            <p class="rev-sub">Products you've reviewed and your feedback</p>
        </div>
        @if($reviews->count() > 0)
        <div class="rev-stat">
            <span class="rev-stat-num">{{ $reviews->total() }}</span>
            <span class="rev-stat-label">Reviews</span>
        </div>
        @endif
    </div>

    @if($reviews->count() > 0)
        <div class="rev-list">
            @foreach($reviews as $review)
            <div class="rev-card">
                {{-- Product Info --}}
                <div class="rev-product-col">
                    <a href="{{ route('product.detail', $review->product->slug ?? '#') }}" class="rev-product-link">
                        @if($review->product->primary_image_url)
                            <img src="{{ $review->product->primary_image_url }}" alt="" class="rev-product-img">
                        @else
                            <div class="rev-product-img-placeholder"><i class="fas fa-box"></i></div>
                        @endif
                        <div class="rev-product-info">
                            <span class="rev-product-name">{{ Str::limit($review->product->name, 35) }}</span>
                            @if($review->product->vendor)
                                <span class="rev-vendor-name"><i class="fas fa-store"></i> {{ $review->product->vendor->store_name }}</span>
                            @endif
                        </div>
                    </a>
                </div>

                {{-- Stars + Comment --}}
                <div class="rev-content-col">
                    <div class="rev-stars-row">
                        <div class="rev-stars">
                            @for($i=1; $i<=5; $i++)
                                <i class="fas fa-star {{ $i <= $review->rating ? 'filled' : 'empty' }}"></i>
                            @endfor
                        </div>
                        <span class="rev-rating-num">{{ $review->rating }}/5</span>
                        <span class="rev-date"><i class="far fa-clock"></i> {{ $review->created_at->format('M d, Y') }}</span>
                    </div>
                    @if($review->comment)
                    <div class="rev-comment">
                        <i class="fas fa-quote-left rev-quote-icon"></i>
                        {{ $review->comment }}
                    </div>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="rev-actions-col">
                    <button type="button" class="rev-edit-btn" onclick="openEditModal({{ $review }})" title="Edit review">
                        <i class="fas fa-edit"></i>
                    </button>
                    <form action="{{ route('customer.reviews.destroy', $review->id) }}" method="POST"
                          onsubmit="return confirm('Delete this review?')" class="rev-del-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rev-del-btn" title="Delete review">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>

        @if($reviews->hasPages())
        <div class="rev-pagination">{{ $reviews->links() }}</div>
        @endif
    @else
        <div class="rev-empty">
            <div class="rev-empty-icon">
                <i class="fas fa-star"></i>
            </div>
            <h3>No reviews yet</h3>
            <p>After purchasing products, share your experience to help other shoppers make better decisions.</p>
            <a href="{{ route('shop') }}" class="rev-shop-btn">
                <i class="fas fa-shopping-bag"></i> Browse Products
            </a>
        </div>
    @endif
</div>

{{-- Edit Review Modal --}}
<div id="editReviewModal" class="rev-modal-overlay" onclick="if(event.target===this)closeEditModal()">
    <div class="rev-modal">
        <div class="rev-modal-header">
            <div class="rev-modal-icon"><i class="fas fa-edit"></i></div>
            <div>
                <h2>Edit Review</h2>
                <p>Update your rating and comment</p>
            </div>
            <button onclick="closeEditModal()" class="rev-modal-close">&times;</button>
        </div>
        <form id="editReviewForm" method="POST" class="rev-modal-form">
            @csrf
            @method('PUT')
            <div class="rev-field">
                <label>Your Rating</label>
                <div class="rev-star-picker">
                    @for($i=5; $i>=1; $i--)
                        <input type="radio" id="star{{$i}}" name="rating" value="{{$i}}" required>
                        <label for="star{{$i}}" title="{{$i}} stars"><i class="fas fa-star"></i></label>
                    @endfor
                </div>
            </div>
            <div class="rev-field">
                <label for="editComment">Your Review</label>
                <textarea name="comment" id="editComment" class="rev-textarea" rows="4" 
                          required minlength="5" maxlength="1000" 
                          placeholder="Share your honest experience with this product..."></textarea>
            </div>
            <div class="rev-modal-actions">
                <button type="button" onclick="closeEditModal()" class="rev-modal-cancel">Cancel</button>
                <button type="submit" class="rev-modal-submit">
                    <i class="fas fa-save"></i> Update Review
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.rev-page { animation: revFade 0.35s ease; }
@keyframes revFade { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

/* Header */
.rev-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 14px; }
.rev-title { font-size: 22px; font-weight: 900; color: #0f172a; margin: 0 0 2px; letter-spacing: -0.02em; }
.rev-sub { font-size: 13px; color: #94a3b8; margin: 0; font-weight: 500; }
.rev-stat { background: white; border: 1px solid #f1f5f9; border-radius: 16px; padding: 12px 22px; text-align: center; box-shadow: 0 2px 6px rgba(0,0,0,0.03); }
.rev-stat-num { display: block; font-size: 24px; font-weight: 900; color: #6366f1; line-height: 1; }
.rev-stat-label { font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; }

/* Review Cards */
.rev-list { display: flex; flex-direction: column; gap: 12px; }
.rev-card {
    background: white;
    border: 1px solid #f1f5f9;
    border-radius: 20px;
    padding: 20px 24px;
    display: grid;
    grid-template-columns: 240px 1fr auto;
    gap: 20px;
    align-items: center;
    box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    transition: all 0.25s;
}
.rev-card:hover { border-color: #c7d2fe; box-shadow: 0 4px 16px rgba(99,102,241,0.08); transform: translateY(-1px); }

/* Product Col */
.rev-product-link { display: flex; align-items: center; gap: 12px; text-decoration: none; }
.rev-product-img { width: 52px; height: 52px; border-radius: 12px; object-fit: cover; border: 1px solid #f1f5f9; flex-shrink: 0; }
.rev-product-img-placeholder { width: 52px; height: 52px; border-radius: 12px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; color: #94a3b8; font-size: 18px; flex-shrink: 0; }
.rev-product-info { display: flex; flex-direction: column; gap: 2px; min-width: 0; }
.rev-product-name { font-size: 14px; font-weight: 700; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.rev-vendor-name { font-size: 11px; color: #94a3b8; display: flex; align-items: center; gap: 4px; }

/* Content Col */
.rev-stars-row { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; flex-wrap: wrap; }
.rev-stars { display: flex; gap: 2px; }
.rev-stars i { font-size: 14px; }
.rev-stars i.filled { color: #f59e0b; }
.rev-stars i.empty { color: #e2e8f0; }
.rev-rating-num { font-size: 13px; font-weight: 800; color: #0f172a; }
.rev-date { font-size: 11px; color: #94a3b8; font-weight: 500; display: flex; align-items: center; gap: 4px; margin-left: auto; }
.rev-comment { font-size: 13px; color: #475569; line-height: 1.6; position: relative; padding-left: 16px; }
.rev-quote-icon { font-size: 10px; color: #c7d2fe; position: absolute; left: 0; top: 2px; }

/* Actions Col */
.rev-actions-col { display: flex; flex-direction: column; gap: 8px; }
.rev-del-form { display: block; }
.rev-edit-btn, .rev-del-btn {
    width: 38px; height: 38px; border-radius: 10px; border: none;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; cursor: pointer; transition: all 0.2s;
}
.rev-edit-btn { background: #eef2ff; color: #6366f1; }
.rev-edit-btn:hover { background: #c7d2fe; }
.rev-del-btn { background: #fef2f2; color: #ef4444; }
.rev-del-btn:hover { background: #fee2e2; }

/* Empty State */
.rev-empty { text-align: center; padding: 70px 20px; background: white; border: 1px solid #f1f5f9; border-radius: 22px; }
.rev-empty-icon { width: 90px; height: 90px; background: linear-gradient(135deg, #fff7ed, #fffbeb); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 18px; font-size: 36px; color: #f59e0b; }
.rev-empty h3 { font-size: 18px; font-weight: 800; color: #0f172a; margin: 0 0 8px; }
.rev-empty p { font-size: 14px; color: #94a3b8; max-width: 320px; margin: 0 auto 24px; line-height: 1.6; }
.rev-shop-btn { display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white; border-radius: 14px; font-weight: 700; text-decoration: none; transition: all 0.2s; box-shadow: 0 4px 14px rgba(99,102,241,0.3); }
.rev-shop-btn:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(99,102,241,0.4); }

/* Pagination */
.rev-pagination { margin-top: 20px; display: flex; justify-content: center; }

/* Modal */
.rev-modal-overlay { display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.5); backdrop-filter: blur(4px); z-index: 1000; align-items: center; justify-content: center; padding: 20px; }
.rev-modal-overlay.active { display: flex; }
.rev-modal { background: white; border-radius: 24px; width: 100%; max-width: 500px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.2); animation: revModalIn 0.3s ease; }
@keyframes revModalIn { from { opacity: 0; transform: scale(0.95) translateY(10px); } to { opacity: 1; transform: scale(1) translateY(0); } }
.rev-modal-header { display: flex; align-items: center; gap: 14px; padding: 24px 24px 18px; border-bottom: 1px solid #f1f5f9; }
.rev-modal-icon { width: 44px; height: 44px; border-radius: 13px; background: #eef2ff; color: #6366f1; display: flex; align-items: center; justify-content: center; font-size: 17px; flex-shrink: 0; }
.rev-modal-header h2 { font-size: 16px; font-weight: 800; color: #0f172a; margin: 0 0 2px; }
.rev-modal-header p { font-size: 12px; color: #94a3b8; margin: 0; }
.rev-modal-close { margin-left: auto; background: none; border: none; font-size: 24px; color: #94a3b8; cursor: pointer; line-height: 1; padding: 0; transition: color 0.2s; }
.rev-modal-close:hover { color: #0f172a; }

.rev-modal-form { padding: 24px; }
.rev-field { margin-bottom: 20px; }
.rev-field label { display: block; font-size: 12px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 10px; }

/* Star Picker */
.rev-star-picker { display: flex; flex-direction: row-reverse; gap: 6px; justify-content: flex-end; }
.rev-star-picker input { display: none; }
.rev-star-picker label { cursor: pointer; font-size: 30px; color: #e2e8f0; transition: color 0.15s; }
.rev-star-picker input:checked ~ label,
.rev-star-picker label:hover,
.rev-star-picker label:hover ~ label { color: #f59e0b; }

.rev-textarea {
    width: 100%; padding: 12px 14px;
    border: 2px solid #e8edf5; border-radius: 12px;
    font-size: 14px; color: #0f172a; background: #fafbfc;
    resize: vertical; min-height: 100px; outline: none;
    font-family: inherit; transition: all 0.2s;
    box-sizing: border-box;
}
.rev-textarea:focus { border-color: #6366f1; background: white; box-shadow: 0 0 0 4px rgba(99,102,241,0.08); }

.rev-modal-actions { display: flex; gap: 10px; justify-content: flex-end; }
.rev-modal-cancel { padding: 11px 20px; border: 2px solid #e2e8f0; border-radius: 12px; background: white; color: #64748b; font-size: 13px; font-weight: 700; cursor: pointer; transition: all 0.2s; }
.rev-modal-cancel:hover { background: #f8fafc; }
.rev-modal-submit { display: inline-flex; align-items: center; gap: 8px; padding: 11px 22px; background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white; border: none; border-radius: 12px; font-size: 13px; font-weight: 700; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 14px rgba(99,102,241,0.3); }
.rev-modal-submit:hover { transform: translateY(-1px); }

/* Responsive */
@media (max-width: 900px) { .rev-card { grid-template-columns: 1fr; gap: 14px; } .rev-actions-col { flex-direction: row; } .rev-date { margin-left: 0; } }
@media (max-width: 480px) { .rev-card { padding: 16px; } }
</style>

<script>
function openEditModal(review) {
    const overlay = document.getElementById('editReviewModal');
    const form = document.getElementById('editReviewForm');
    form.action = '{{ route("customer.reviews.update", "") }}/' + review.id;
    document.getElementById('editComment').value = review.comment;
    const ratingRadio = document.getElementById('star' + review.rating);
    if (ratingRadio) ratingRadio.checked = true;
    overlay.classList.add('active');
}
function closeEditModal() {
    document.getElementById('editReviewModal').classList.remove('active');
}
</script>
@endsection
