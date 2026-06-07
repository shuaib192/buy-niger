{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin (08122598372, 07049906420)
    
    View: Customer Addresses (Premium v2.0)
--}}
@extends('layouts.app')

@section('title', 'My Addresses')
@section('page_title', 'Saved Addresses')

@section('sidebar')
    @include('customer.partials.sidebar')
@endsection

@section('content')
<div class="addr-page">

    @if(session('success'))
    <div class="addr-alert addr-alert-ok">
        <i class="fas fa-check-circle"></i>
        <span>{{ session('success') }}</span>
        <button onclick="this.parentElement.remove()" class="addr-close-btn">&times;</button>
    </div>
    @endif

    {{-- Page Header --}}
    <div class="addr-page-header">
        <div>
            <h1 class="addr-page-title">My Addresses</h1>
            <p class="addr-page-sub">Manage your delivery locations</p>
        </div>
        <button type="button" class="addr-add-btn" onclick="toggleAddForm()">
            <i class="fas fa-plus"></i> Add Address
        </button>
    </div>

    <div class="addr-layout">
        {{-- Saved Addresses --}}
        <div class="addr-list-section">
            @if($addresses->count() > 0)
                <div class="addr-cards">
                    @foreach($addresses as $address)
                    <div class="addr-card {{ $address->is_default ? 'is-default' : '' }}">
                        {{-- Map Pin Decoration --}}
                        <div class="addr-card-pin">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>

                        @if($address->is_default)
                        <div class="addr-default-ribbon">
                            <i class="fas fa-star"></i> Default
                        </div>
                        @endif

                        <div class="addr-card-body">
                            <div class="addr-card-name">{{ $address->first_name }} {{ $address->last_name }}</div>
                            <div class="addr-card-line">
                                <i class="fas fa-road"></i>
                                <span>{{ $address->address_line_1 }}@if($address->address_line_2), {{ $address->address_line_2 }}@endif</span>
                            </div>
                            <div class="addr-card-line">
                                <i class="fas fa-city"></i>
                                <span>{{ $address->city }}, {{ $address->state }}</span>
                            </div>
                            <div class="addr-card-line addr-card-phone">
                                <i class="fas fa-phone-alt"></i>
                                <span>{{ $address->phone }}</span>
                            </div>
                        </div>

                        <div class="addr-card-footer">
                            @if(!$address->is_default)
                            <form action="{{ route('customer.addresses.default', $address->id) }}" method="POST" class="addr-inline-form">
                                @csrf
                                <button type="submit" class="addr-btn addr-btn-default">
                                    <i class="fas fa-check"></i> Set Default
                                </button>
                            </form>
                            @else
                            <span class="addr-btn addr-btn-default-active">
                                <i class="fas fa-star"></i> Default Address
                            </span>
                            @endif

                            <form action="{{ route('customer.addresses.delete', $address->id) }}" method="POST" 
                                  class="addr-inline-form" 
                                  onsubmit="return confirm('Are you sure you want to delete this address?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="addr-btn addr-btn-delete">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="addr-empty-state">
                    <div class="addr-empty-icon">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>
                    <h3>No saved addresses</h3>
                    <p>Add your first delivery address to speed up checkout</p>
                    <button type="button" class="addr-add-btn" onclick="toggleAddForm()">
                        <i class="fas fa-plus"></i> Add Your First Address
                    </button>
                </div>
            @endif
        </div>

        {{-- Add New Address Form --}}
        <div class="addr-form-panel" id="addAddressPanel" style="{{ $addresses->count() == 0 ? '' : 'display:none;' }}">
            <div class="addr-form-card">
                <div class="addr-form-header">
                    <div class="addr-form-icon"><i class="fas fa-plus-circle"></i></div>
                    <div>
                        <h2>New Address</h2>
                        <p>Fill in your delivery details</p>
                    </div>
                </div>

                <form action="{{ route('customer.addresses.store') }}" method="POST" class="addr-form">
                    @csrf
                    <div class="addr-form-row">
                        <div class="addr-field">
                            <label>First Name <span class="req">*</span></label>
                            <input type="text" name="first_name" class="addr-input" required placeholder="John">
                        </div>
                        <div class="addr-field">
                            <label>Last Name <span class="req">*</span></label>
                            <input type="text" name="last_name" class="addr-input" required placeholder="Doe">
                        </div>
                    </div>
                    <div class="addr-field">
                        <label>Phone Number <span class="req">*</span></label>
                        <div class="addr-input-icon">
                            <i class="fas fa-phone-alt"></i>
                            <input type="text" name="phone" class="addr-input icon-input" placeholder="08012345678" required>
                        </div>
                    </div>
                    <div class="addr-field">
                        <label>Street Address <span class="req">*</span></label>
                        <input type="text" name="address_line_1" class="addr-input" placeholder="House No. and street name" required>
                    </div>
                    <div class="addr-field">
                        <label>Apt, Suite, Floor (Optional)</label>
                        <input type="text" name="address_line_2" class="addr-input" placeholder="Optional">
                    </div>
                    <div class="addr-form-row">
                        <div class="addr-field">
                            <label>City <span class="req">*</span></label>
                            <input type="text" name="city" class="addr-input" placeholder="e.g. Abuja" required>
                        </div>
                        <div class="addr-field">
                            <label>State <span class="req">*</span></label>
                            <select name="state" class="addr-input" required>
                                <option value="">Select State</option>
                                @foreach(['Abia','Adamawa','Akwa Ibom','Anambra','Bauchi','Bayelsa','Benue','Borno','Cross River','Delta','Ebonyi','Edo','Ekiti','Enugu','FCT','Gombe','Imo','Jigawa','Kaduna','Kano','Katsina','Kebbi','Kogi','Kwara','Lagos','Nasarawa','Niger','Ogun','Ondo','Osun','Oyo','Plateau','Rivers','Sokoto','Taraba','Yobe','Zamfara'] as $state)
                                    <option value="{{ $state }}">{{ $state }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="addr-form-actions">
                        @if($addresses->count() > 0)
                        <button type="button" class="addr-cancel-btn" onclick="toggleAddForm()">Cancel</button>
                        @endif
                        <button type="submit" class="addr-submit-btn">
                            <i class="fas fa-plus"></i> Save Address
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.addr-page { animation: addrFade 0.35s ease; }
@keyframes addrFade { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

/* Alert */
.addr-alert {
    display: flex; align-items: center; gap: 12px;
    padding: 14px 18px; border-radius: 14px;
    font-size: 14px; font-weight: 600; margin-bottom: 22px;
}
.addr-alert-ok { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
.addr-close-btn { margin-left: auto; background: none; border: none; font-size: 20px; cursor: pointer; color: currentColor; opacity: 0.7; }

/* Page Header */
.addr-page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 14px; }
.addr-page-title { font-size: 22px; font-weight: 900; color: #0f172a; margin: 0 0 2px; letter-spacing: -0.02em; }
.addr-page-sub { font-size: 13px; color: #94a3b8; margin: 0; font-weight: 500; }
.addr-add-btn {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 11px 22px; background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white; border: none; border-radius: 14px;
    font-size: 13px; font-weight: 700; cursor: pointer;
    transition: all 0.2s;
    box-shadow: 0 4px 14px rgba(99,102,241,0.3);
}
.addr-add-btn:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(99,102,241,0.4); }

/* Layout */
.addr-layout { display: grid; grid-template-columns: 1fr 380px; gap: 24px; align-items: start; }

/* Address Cards Grid */
.addr-cards { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 14px; }
.addr-card {
    background: white;
    border: 2px solid #f1f5f9;
    border-radius: 20px;
    padding: 22px;
    position: relative;
    overflow: hidden;
    transition: all 0.25s;
    box-shadow: 0 2px 8px rgba(0,0,0,0.03);
}
.addr-card:hover { border-color: #c7d2fe; box-shadow: 0 4px 16px rgba(99,102,241,0.1); transform: translateY(-2px); }
.addr-card.is-default { border-color: #818cf8; background: linear-gradient(135deg, #fafbff, #f0f0ff); }

.addr-card-pin {
    position: absolute; top: 0; right: 0;
    width: 0; height: 0;
    border-style: solid;
    border-width: 0 50px 50px 0;
    border-color: transparent #f1f5f9 transparent transparent;
}
.addr-card.is-default .addr-card-pin { border-color: transparent #c7d2fe transparent transparent; }
.addr-card-pin > i { display: none; }

.addr-default-ribbon {
    position: absolute; top: 0; right: 0;
    display: flex; align-items: center; gap: 4px;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white; font-size: 9px; font-weight: 800;
    padding: 4px 10px 4px 14px;
    border-radius: 0 20px 0 12px;
    letter-spacing: 0.05em; text-transform: uppercase;
}

.addr-card-body { margin-bottom: 16px; }
.addr-card-name { font-size: 16px; font-weight: 800; color: #0f172a; margin-bottom: 10px; }
.addr-card-line {
    display: flex; align-items: flex-start; gap: 8px;
    font-size: 13px; color: #64748b; margin-bottom: 5px;
}
.addr-card-line i { font-size: 11px; color: #94a3b8; margin-top: 2px; width: 12px; flex-shrink: 0; }
.addr-card-phone { color: #6366f1 !important; font-weight: 600; }
.addr-card-phone i { color: #6366f1; }

.addr-card-footer {
    display: flex; align-items: center; gap: 8px;
    padding-top: 14px;
    border-top: 1px solid #f1f5f9;
    flex-wrap: wrap;
}
.addr-inline-form { display: inline; }
.addr-btn { display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; border-radius: 10px; font-size: 12px; font-weight: 700; cursor: pointer; border: none; transition: all 0.2s; }
.addr-btn-default { background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; }
.addr-btn-default:hover { background: #eef2ff; color: #6366f1; border-color: #c7d2fe; }
.addr-btn-default-active { display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; border-radius: 10px; font-size: 12px; font-weight: 700; background: #eef2ff; color: #6366f1; }
.addr-btn-delete { background: #fef2f2; color: #ef4444; padding: 7px 12px; margin-left: auto; }
.addr-btn-delete:hover { background: #fee2e2; }
.req { color: #ef4444; }

/* Empty State */
.addr-empty-state { text-align: center; padding: 60px 20px; background: white; border: 1px solid #f1f5f9; border-radius: 22px; }
.addr-empty-icon { width: 90px; height: 90px; background: linear-gradient(135deg, #f0f0ff, #eef2ff); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 18px; font-size: 36px; color: #6366f1; }
.addr-empty-state h3 { font-size: 17px; font-weight: 800; color: #0f172a; margin: 0 0 6px; }
.addr-empty-state p { font-size: 13px; color: #94a3b8; max-width: 280px; margin: 0 auto 20px; line-height: 1.6; }

/* Form Panel */
.addr-form-panel { position: sticky; top: 80px; }
.addr-form-card { background: white; border: 1px solid #f1f5f9; border-radius: 22px; padding: 28px; box-shadow: 0 4px 16px rgba(0,0,0,0.05); }
.addr-form-header { display: flex; align-items: center; gap: 16px; margin-bottom: 24px; padding-bottom: 18px; border-bottom: 1px solid #f8fafc; }
.addr-form-icon { width: 48px; height: 48px; border-radius: 14px; background: #eef2ff; color: #6366f1; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
.addr-form-header h2 { font-size: 16px; font-weight: 800; color: #0f172a; margin: 0 0 2px; }
.addr-form-header p { font-size: 12px; color: #94a3b8; margin: 0; }

.addr-form { display: flex; flex-direction: column; }
.addr-form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.addr-field { margin-bottom: 16px; }
.addr-field label { display: block; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 7px; }
.addr-input {
    width: 100%; padding: 11px 14px;
    border: 2px solid #e8edf5; border-radius: 11px;
    font-size: 14px; color: #0f172a; background: #fafbfc;
    transition: all 0.2s; outline: none; box-sizing: border-box;
}
.addr-input:focus { border-color: #6366f1; background: white; box-shadow: 0 0 0 4px rgba(99,102,241,0.08); }
select.addr-input { cursor: pointer; }
.addr-input-icon { position: relative; }
.addr-input-icon > i { position: absolute; left: 13px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 12px; pointer-events: none; }
.addr-input.icon-input { padding-left: 36px; }

.addr-form-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 6px; }
.addr-cancel-btn { padding: 11px 20px; border: 2px solid #e2e8f0; border-radius: 12px; background: white; color: #64748b; font-size: 13px; font-weight: 700; cursor: pointer; transition: all 0.2s; }
.addr-cancel-btn:hover { background: #f8fafc; }
.addr-submit-btn { display: inline-flex; align-items: center; gap: 8px; padding: 11px 22px; background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white; border: none; border-radius: 12px; font-size: 13px; font-weight: 700; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 14px rgba(99,102,241,0.3); }
.addr-submit-btn:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(99,102,241,0.4); }

/* Responsive */
@media (max-width: 1100px) { .addr-layout { grid-template-columns: 1fr; } .addr-form-panel { position: static; } }
@media (max-width: 600px) { .addr-cards { grid-template-columns: 1fr; } .addr-form-row { grid-template-columns: 1fr; } }
</style>

<script>
function toggleAddForm() {
    const panel = document.getElementById('addAddressPanel');
    if (panel.style.display === 'none') {
        panel.style.display = 'block';
        panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
    } else {
        panel.style.display = 'none';
    }
}
</script>
@endsection
