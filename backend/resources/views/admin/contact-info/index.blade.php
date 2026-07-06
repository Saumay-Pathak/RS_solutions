@extends('admin.layouts.admin')

@section('title', 'Contact Information Management')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title mb-0">
                        <h5 class="mb-1">Contact Information Management</h5>
                        <p class="card-subtitle mb-0">Manage all company contact details, support numbers, and office addresses</p>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-text-secondary btn-icon rounded-pill text-body-secondary border-0 me-n1" 
                                type="button" id="contactInfoActions" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="icon-base ti tabler-dots-vertical icon-22px text-body-secondary"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="contactInfoActions">
                            <a class="dropdown-item" href="{{ route('admin.api.contact-info') }}" target="_blank">
                                <i class="icon-base ti tabler-api me-2"></i>View API Data
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="javascript:void(0);" onclick="resetToDefaults()">
                                <i class="icon-base ti tabler-refresh me-2"></i>Reset to Defaults
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('admin.contact-info.update') }}" method="POST" id="contactInfoForm">
                        @csrf
                        @method('PATCH')

                        <div class="nav-align-top">
                            <ul class="nav nav-tabs nav-fill" role="tablist">
                                <li class="nav-item">
                                    <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" 
                                            data-bs-target="#support-numbers" aria-controls="support-numbers" aria-selected="true">
                                        <i class="icon-base ti tabler-phone me-2"></i>Support Numbers
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" 
                                            data-bs-target="#email-addresses" aria-controls="email-addresses" aria-selected="false">
                                        <i class="icon-base ti tabler-mail me-2"></i>Email Addresses
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" 
                                            data-bs-target="#office-locations" aria-controls="office-locations" aria-selected="false">
                                        <i class="icon-base ti tabler-building-store me-2"></i>Office Locations
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" 
                                            data-bs-target="#additional-info" aria-controls="additional-info" aria-selected="false">
                                        <i class="icon-base ti tabler-info-circle me-2"></i>Additional Info
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content">
                                <!-- Support Numbers Tab -->
                                <div class="tab-pane fade show active" id="support-numbers" role="tabpanel">
                                    <div class="card mt-4">
                                        <div class="card-header">
                                            <h6 class="card-title mb-0">Support & Contact Numbers</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="customer_support_number" class="form-label">Customer Support Number <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control @error('customer_support_number') is-invalid @enderror" 
                                                               id="customer_support_number" name="customer_support_number" 
                                                               value="{{ old('customer_support_number', $contactInfo->customer_support_number) }}" 
                                                               placeholder="+91-8743 8743 34" required>
                                                        @error('customer_support_number')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="partner_support_number" class="form-label">Registered Partner Support Number <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control @error('partner_support_number') is-invalid @enderror" 
                                                               id="partner_support_number" name="partner_support_number" 
                                                               value="{{ old('partner_support_number', $contactInfo->partner_support_number) }}" 
                                                               placeholder="+91-87 4409 4409" required>
                                                        @error('partner_support_number')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="enquiry_number" class="form-label">Enquiry Number <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control @error('enquiry_number') is-invalid @enderror" 
                                                               id="enquiry_number" name="enquiry_number" 
                                                               value="{{ old('enquiry_number', $contactInfo->enquiry_number) }}" 
                                                               placeholder="+91-8860 8860 86" required>
                                                        @error('enquiry_number')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="service_center_number" class="form-label">Service Center Number <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control @error('service_center_number') is-invalid @enderror" 
                                                               id="service_center_number" name="service_center_number" 
                                                               value="{{ old('service_center_number', $contactInfo->service_center_number) }}" 
                                                               placeholder="+91-7303 9600 28" required>
                                                        @error('service_center_number')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="whatsapp_number" class="form-label">WhatsApp Number</label>
                                                        <input type="text" class="form-control @error('whatsapp_number') is-invalid @enderror" 
                                                               id="whatsapp_number" name="whatsapp_number" 
                                                               value="{{ old('whatsapp_number', $contactInfo->whatsapp_number) }}" 
                                                               placeholder="+91-8743 8743 34">
                                                        @error('whatsapp_number')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="emergency_contact" class="form-label">Emergency Contact</label>
                                                        <input type="text" class="form-control @error('emergency_contact') is-invalid @enderror" 
                                                               id="emergency_contact" name="emergency_contact" 
                                                               value="{{ old('emergency_contact', $contactInfo->emergency_contact) }}" 
                                                               placeholder="+91-9999 9999 99">
                                                        @error('emergency_contact')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Email Addresses Tab -->
                                <div class="tab-pane fade" id="email-addresses" role="tabpanel">
                                    <div class="card mt-4">
                                        <div class="card-header">
                                            <h6 class="card-title mb-0">Email Addresses</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label for="general_email" class="form-label">General Email <span class="text-danger">*</span></label>
                                                        <input type="email" class="form-control @error('general_email') is-invalid @enderror" 
                                                               id="general_email" name="general_email" 
                                                               value="{{ old('general_email', $contactInfo->general_email) }}" 
                                                               placeholder="info@realtimebiometrics.com" required>
                                                        @error('general_email')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label for="business_email" class="form-label">Business Email <span class="text-danger">*</span></label>
                                                        <input type="email" class="form-control @error('business_email') is-invalid @enderror" 
                                                               id="business_email" name="business_email" 
                                                               value="{{ old('business_email', $contactInfo->business_email) }}" 
                                                               placeholder="business@realtimebiometrics.com" required>
                                                        @error('business_email')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label for="support_email" class="form-label">Support Email</label>
                                                        <input type="email" class="form-control @error('support_email') is-invalid @enderror" 
                                                               id="support_email" name="support_email" 
                                                               value="{{ old('support_email', $contactInfo->support_email) }}" 
                                                               placeholder="support@realtimebiometrics.com">
                                                        @error('support_email')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Office Locations Tab -->
                                <div class="tab-pane fade" id="office-locations" role="tabpanel">
                                    <div class="mt-4">
                                        <!-- Corporate Headquarters -->
                                        <div class="card mb-4">
                                            <div class="card-header">
                                                <h6 class="card-title mb-0">Corporate Headquarters - Delhi, India</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label for="hq_name" class="form-label">Office Name <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control @error('hq_name') is-invalid @enderror" 
                                                                   id="hq_name" name="hq_name" 
                                                                   value="{{ old('hq_name', $contactInfo->hq_name) }}" required>
                                                            @error('hq_name')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="hq_address" class="form-label">Address <span class="text-danger">*</span></label>
                                                            <textarea class="form-control @error('hq_address') is-invalid @enderror" 
                                                                      id="hq_address" name="hq_address" rows="3" required>{{ old('hq_address', $contactInfo->hq_address) }}</textarea>
                                                            @error('hq_address')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label for="hq_city" class="form-label">City <span class="text-danger">*</span></label>
                                                                    <input type="text" class="form-control @error('hq_city') is-invalid @enderror" 
                                                                           id="hq_city" name="hq_city" 
                                                                           value="{{ old('hq_city', $contactInfo->hq_city) }}" required>
                                                                    @error('hq_city')
                                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label for="hq_postal_code" class="form-label">Postal Code</label>
                                                                    <input type="text" class="form-control @error('hq_postal_code') is-invalid @enderror" 
                                                                           id="hq_postal_code" name="hq_postal_code" 
                                                                           value="{{ old('hq_postal_code', $contactInfo->hq_postal_code) }}">
                                                                    @error('hq_postal_code')
                                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label for="hq_state" class="form-label">State <span class="text-danger">*</span></label>
                                                                    <input type="text" class="form-control @error('hq_state') is-invalid @enderror" 
                                                                           id="hq_state" name="hq_state" 
                                                                           value="{{ old('hq_state', $contactInfo->hq_state) }}" required>
                                                                    @error('hq_state')
                                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label for="hq_country" class="form-label">Country <span class="text-danger">*</span></label>
                                                                    <input type="text" class="form-control @error('hq_country') is-invalid @enderror" 
                                                                           id="hq_country" name="hq_country" 
                                                                           value="{{ old('hq_country', $contactInfo->hq_country) }}" required>
                                                                    @error('hq_country')
                                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="hq_email" class="form-label">Office Email</label>
                                                            <input type="email" class="form-control @error('hq_email') is-invalid @enderror" 
                                                                   id="hq_email" name="hq_email" 
                                                                   value="{{ old('hq_email', $contactInfo->hq_email) }}">
                                                            @error('hq_email')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="hq_phone" class="form-label">Office Phone</label>
                                                            <input type="text" class="form-control @error('hq_phone') is-invalid @enderror" 
                                                                   id="hq_phone" name="hq_phone" 
                                                                   value="{{ old('hq_phone', $contactInfo->hq_phone) }}">
                                                            @error('hq_phone')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- UK Office -->
                                        <div class="card mb-4">
                                            <div class="card-header">
                                                <h6 class="card-title mb-0">United Kingdom Office - England</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label for="uk_name" class="form-label">Office Name <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control @error('uk_name') is-invalid @enderror" 
                                                                   id="uk_name" name="uk_name" 
                                                                   value="{{ old('uk_name', $contactInfo->uk_name) }}" required>
                                                            @error('uk_name')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="uk_address" class="form-label">Address <span class="text-danger">*</span></label>
                                                            <textarea class="form-control @error('uk_address') is-invalid @enderror" 
                                                                      id="uk_address" name="uk_address" rows="3" required>{{ old('uk_address', $contactInfo->uk_address) }}</textarea>
                                                            @error('uk_address')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label for="uk_city" class="form-label">City <span class="text-danger">*</span></label>
                                                                    <input type="text" class="form-control @error('uk_city') is-invalid @enderror" 
                                                                           id="uk_city" name="uk_city" 
                                                                           value="{{ old('uk_city', $contactInfo->uk_city) }}" required>
                                                                    @error('uk_city')
                                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label for="uk_postal_code" class="form-label">Postal Code</label>
                                                                    <input type="text" class="form-control @error('uk_postal_code') is-invalid @enderror" 
                                                                           id="uk_postal_code" name="uk_postal_code" 
                                                                           value="{{ old('uk_postal_code', $contactInfo->uk_postal_code) }}">
                                                                    @error('uk_postal_code')
                                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label for="uk_state" class="form-label">State/Region <span class="text-danger">*</span></label>
                                                                    <input type="text" class="form-control @error('uk_state') is-invalid @enderror" 
                                                                           id="uk_state" name="uk_state" 
                                                                           value="{{ old('uk_state', $contactInfo->uk_state) }}" required>
                                                                    @error('uk_state')
                                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label for="uk_country" class="form-label">Country <span class="text-danger">*</span></label>
                                                                    <input type="text" class="form-control @error('uk_country') is-invalid @enderror" 
                                                                           id="uk_country" name="uk_country" 
                                                                           value="{{ old('uk_country', $contactInfo->uk_country) }}" required>
                                                                    @error('uk_country')
                                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="uk_email" class="form-label">Office Email</label>
                                                            <input type="email" class="form-control @error('uk_email') is-invalid @enderror" 
                                                                   id="uk_email" name="uk_email" 
                                                                   value="{{ old('uk_email', $contactInfo->uk_email) }}">
                                                            @error('uk_email')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="uk_phone" class="form-label">Office Phone</label>
                                                            <input type="text" class="form-control @error('uk_phone') is-invalid @enderror" 
                                                                   id="uk_phone" name="uk_phone" 
                                                                   value="{{ old('uk_phone', $contactInfo->uk_phone) }}">
                                                            @error('uk_phone')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Manufacturing Unit -->
                                        <div class="card mb-4">
                                            <div class="card-header">
                                                <h6 class="card-title mb-0">Manufacturing Unit - Uttar Pradesh, India</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label for="manufacturing_name" class="form-label">Unit Name <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control @error('manufacturing_name') is-invalid @enderror" 
                                                                   id="manufacturing_name" name="manufacturing_name" 
                                                                   value="{{ old('manufacturing_name', $contactInfo->manufacturing_name) }}" required>
                                                            @error('manufacturing_name')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="manufacturing_address" class="form-label">Address <span class="text-danger">*</span></label>
                                                            <textarea class="form-control @error('manufacturing_address') is-invalid @enderror" 
                                                                      id="manufacturing_address" name="manufacturing_address" rows="3" required>{{ old('manufacturing_address', $contactInfo->manufacturing_address) }}</textarea>
                                                            @error('manufacturing_address')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label for="manufacturing_city" class="form-label">City <span class="text-danger">*</span></label>
                                                                    <input type="text" class="form-control @error('manufacturing_city') is-invalid @enderror" 
                                                                           id="manufacturing_city" name="manufacturing_city" 
                                                                           value="{{ old('manufacturing_city', $contactInfo->manufacturing_city) }}" required>
                                                                    @error('manufacturing_city')
                                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label for="manufacturing_postal_code" class="form-label">Postal Code</label>
                                                                    <input type="text" class="form-control @error('manufacturing_postal_code') is-invalid @enderror" 
                                                                           id="manufacturing_postal_code" name="manufacturing_postal_code" 
                                                                           value="{{ old('manufacturing_postal_code', $contactInfo->manufacturing_postal_code) }}">
                                                                    @error('manufacturing_postal_code')
                                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label for="manufacturing_state" class="form-label">State <span class="text-danger">*</span></label>
                                                                    <input type="text" class="form-control @error('manufacturing_state') is-invalid @enderror" 
                                                                           id="manufacturing_state" name="manufacturing_state" 
                                                                           value="{{ old('manufacturing_state', $contactInfo->manufacturing_state) }}" required>
                                                                    @error('manufacturing_state')
                                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label for="manufacturing_country" class="form-label">Country <span class="text-danger">*</span></label>
                                                                    <input type="text" class="form-control @error('manufacturing_country') is-invalid @enderror" 
                                                                           id="manufacturing_country" name="manufacturing_country" 
                                                                           value="{{ old('manufacturing_country', $contactInfo->manufacturing_country) }}" required>
                                                                    @error('manufacturing_country')
                                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="manufacturing_email" class="form-label">Unit Email</label>
                                                            <input type="email" class="form-control @error('manufacturing_email') is-invalid @enderror" 
                                                                   id="manufacturing_email" name="manufacturing_email" 
                                                                   value="{{ old('manufacturing_email', $contactInfo->manufacturing_email) }}">
                                                            @error('manufacturing_email')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="manufacturing_phone" class="form-label">Unit Phone</label>
                                                            <input type="text" class="form-control @error('manufacturing_phone') is-invalid @enderror" 
                                                                   id="manufacturing_phone" name="manufacturing_phone" 
                                                                   value="{{ old('manufacturing_phone', $contactInfo->manufacturing_phone) }}">
                                                            @error('manufacturing_phone')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Additional Info Tab -->
                                <div class="tab-pane fade" id="additional-info" role="tabpanel">
                                    <div class="card mt-4">
                                        <div class="card-header">
                                            <h6 class="card-title mb-0">Additional Information</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="website_url" class="form-label">Website URL</label>
                                                        <input type="url" class="form-control @error('website_url') is-invalid @enderror" 
                                                               id="website_url" name="website_url" 
                                                               value="{{ old('website_url', $contactInfo->website_url) }}" 
                                                               placeholder="https://realtimebiometrics.com">
                                                        @error('website_url')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="fax_number" class="form-label">Fax Number</label>
                                                        <input type="text" class="form-control @error('fax_number') is-invalid @enderror" 
                                                               id="fax_number" name="fax_number" 
                                                               value="{{ old('fax_number', $contactInfo->fax_number) }}">
                                                        @error('fax_number')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <!-- Social Media Links -->
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h6 class="card-title mb-0">Social Media Links</h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="mb-3">
                                                                <label for="facebook" class="form-label">Facebook</label>
                                                                <input type="url" class="form-control" name="social_media_links[facebook]" 
                                                                       value="{{ old('social_media_links.facebook', $contactInfo->social_media_links['facebook'] ?? '') }}">
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="linkedin" class="form-label">LinkedIn</label>
                                                                <input type="url" class="form-control" name="social_media_links[linkedin]" 
                                                                       value="{{ old('social_media_links.linkedin', $contactInfo->social_media_links['linkedin'] ?? '') }}">
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="twitter" class="form-label">Twitter</label>
                                                                <input type="url" class="form-control" name="social_media_links[twitter]" 
                                                                       value="{{ old('social_media_links.twitter', $contactInfo->social_media_links['twitter'] ?? '') }}">
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="instagram" class="form-label">Instagram</label>
                                                                <input type="url" class="form-control" name="social_media_links[instagram]" 
                                                                       value="{{ old('social_media_links.instagram', $contactInfo->social_media_links['instagram'] ?? '') }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <!-- Business Hours -->
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h6 class="card-title mb-0">Business Hours</h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="mb-3">
                                                                <label for="monday" class="form-label">Monday</label>
                                                                <input type="text" class="form-control" name="business_hours[monday]" 
                                                                       value="{{ old('business_hours.monday', $contactInfo->business_hours['monday'] ?? '') }}"
                                                                       placeholder="9:00 AM - 6:00 PM">
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="tuesday" class="form-label">Tuesday</label>
                                                                <input type="text" class="form-control" name="business_hours[tuesday]" 
                                                                       value="{{ old('business_hours.tuesday', $contactInfo->business_hours['tuesday'] ?? '') }}">
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="wednesday" class="form-label">Wednesday</label>
                                                                <input type="text" class="form-control" name="business_hours[wednesday]" 
                                                                       value="{{ old('business_hours.wednesday', $contactInfo->business_hours['wednesday'] ?? '') }}">
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="thursday" class="form-label">Thursday</label>
                                                                <input type="text" class="form-control" name="business_hours[thursday]" 
                                                                       value="{{ old('business_hours.thursday', $contactInfo->business_hours['thursday'] ?? '') }}">
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="friday" class="form-label">Friday</label>
                                                                <input type="text" class="form-control" name="business_hours[friday]" 
                                                                       value="{{ old('business_hours.friday', $contactInfo->business_hours['friday'] ?? '') }}">
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="saturday" class="form-label">Saturday</label>
                                                                <input type="text" class="form-control" name="business_hours[saturday]" 
                                                                       value="{{ old('business_hours.saturday', $contactInfo->business_hours['saturday'] ?? '') }}">
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="sunday" class="form-label">Sunday</label>
                                                                <input type="text" class="form-control" name="business_hours[sunday]" 
                                                                       value="{{ old('business_hours.sunday', $contactInfo->business_hours['sunday'] ?? '') }}"
                                                                       placeholder="Closed">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Settings -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_active" 
                                           name="is_active" value="1" {{ old('is_active', $contactInfo->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Active Contact Information
                                    </label>
                                </div>
                                <small class="form-text text-muted">When disabled, contact information will not be displayed publicly</small>
                            </div>
                            <div class="col-md-6 text-end">
                                <button type="button" class="btn btn-secondary me-2" onclick="window.location.reload()">
                                    <i class="icon-base ti tabler-refresh"></i> Reset
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="icon-base ti tabler-device-floppy"></i> Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function resetToDefaults() {
    if (confirm('This will reset all contact information to default values. Are you sure?')) {
        // Create a form for reset action
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.contact-info.reset") }}';
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        document.body.appendChild(form);
        form.submit();
    }
}

// Show toast notifications
function showToast(message, type) {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
    toast.style.top = '20px';
    toast.style.right = '20px';
    toast.style.zIndex = '9999';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        if (toast.parentNode) {
            toast.remove();
        }
    }, 5000);
}
</script>
@endsection