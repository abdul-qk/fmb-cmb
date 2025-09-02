@extends('layout.master')
@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <!--begin::Toolbar-->
  <div class="toolbar" id="kt_toolbar">
    <!--begin::Container-->
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <!--begin::Page title-->
      <div data-kt-swapper-mode="prepend" class="page-title d-flex align-items-center me-3 flex-wrap lh-1">
        <!--begin::Separator-->
        <!--end::Separator-->
        <!--begin::Breadcrumb-->
        @include('layout.breadcrumb')
        <!--end::Breadcrumb-->
      </div>
      <!--end::Page title-->
    </div>
    <!--end::Container-->
  </div>
  <!--end::Toolbar-->
  <!--begin::Post-->
  <div  id="kt_post">
    <!--begin::Container-->
     <div class="container-fluid" id="kt_content_container">
      @if(Session::has('error'))
      <!--begin::Alert-->
      <div class="alert alert-danger d-flex align-items-center p-5">
        <!--begin::Icon-->
        <i class="ki-duotone ki-shield-tick fs-2hx text-danger me-4"><span class="path1"></span><span class="path2"></span></i>
        <!--end::Icon-->

        <!--begin::Wrapper-->
        <div class="d-flex flex-column">
          <!--begin::Content-->
          <span>{{ Session::get('error') }}</span>
          <!--end::Content-->
        </div>
        <!--end::Wrapper-->
      </div>
      <!--end::Alert-->
      @endif
      <!--begin::Card-->
      <div class="bg-transparent border-0 card shadow-none pt-2">

        <div class="row">

          <div class="col-12 mb-3">
            <h2 class="border-bottom pb-2 border-bottom-3 border-primary mb-3">Vendor Detail</h2>
          </div>
          <div class="col-md-3 col-lg-3 mb-5">
            <label class="form-label">Name</label>
            <input hidden type="text" class="form-control" name="name" value="{{ $result->name }}">
            <h5 class="border-primary" style="word-break: break-word; height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem;">
            {{ $result->name  ?? '-'}}
              </h5>
          </div>
          <div class="col-md-3 col-lg-3 mb-5">
            <label class="form-label">Email</label>
            <input hidden type="text" class="form-control" name="email" value="{{ $result->email }}">
            <h5 class="border-primary" style="word-break: break-word; height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem;">
            {{ $result->email  ?? '-'}}
              </h5>
          </div>
          <div class="col-md-3 col-lg-3 mb-5">
            <label class="form-label">Country</label>
            <input hidden class="form-control" name="country" value="{{ $result->city->country->name  ?? '-' }}">
            <h5 class="border-primary" style="word-break: break-word; height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem;">
            {{ $result->city->country->name  ?? '-' }}
              </h5>
          </div>
          <div class="col-md-3 col-lg-3 mb-5">
            <label class="form-label">City</label>
            <input hidden class="form-control" name="city" value="{{ $result->city->name  ?? '-'}}">
            <h5 class="border-primary" style="word-break: break-word; height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem;">
            {{ $result->city->name  ?? '-'}}
              </h5>
          </div>
         
          <div class="col-md-3 col-lg-3 mb-5">
            <label class="form-label">Address</label>
            <input hidden class="form-control" name="address" value="{{ $result->address }}">
            <h5 class="border-primary" style="word-break: break-word; height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem;">
              {{ $result->address  ?? '-'}}
            </h5>
          </div>
          <div class="col-md-3 col-lg-3 mb-5">
            <label class="form-label">Created</label>
            <h5 class="border-primary" style="word-break: break-word; height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem;">
            {{ $result->createdBy ? $result->createdBy->name : '-' }}
            <br/>
            {{ $result->created_at->isoFormat('Do MMM YYYY, hh:mm A') }}
            </h5>
          </div>
          <div class="col-md-3 col-lg-3 mb-5">
            <label class="form-label">Last Modified</label>
            <h5 class="border-primary" style="word-break: break-word; height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem;">
            {{ $result->updatedBy ? $result->updatedBy->name : '-' }} 
              <br/>
              {{ $result->updated_at ? $result->updated_at->isoFormat('Do MMM YYYY, hh:mm A') : "-" }}
            </h5>
          </div>
          <div class="col-12 mb-3">
            <h2 class="border-bottom pb-2 border-bottom-3 border-primary mb-3">Contact Detail Primary</h2>
          </div>
          @foreach($result->contactPersons as $contactPerson)
            @if($contactPerson->primary == "1")
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label">Contact Number</label>
              <input hidden type="text" class="form-control" name="contact_number" value="{{ $contactPerson->contact_number }}">
              <h5 class="border-primary" style="word-break: break-word; height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem;">
              {{ $contactPerson->contact_number  ?? '-'}}
              </h5>
            </div>
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label">Office Number</label>
              <input hidden type="text" class="form-control" name="office_number" value="{{ $contactPerson->office_number }}">
              <h5 class="border-primary" style="word-break: break-word; height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem;">
              {{ $contactPerson->office_number  ?? '-'}}
              </h5>
            </div>
            @endif
          @endforeach
          <div class="col-12 mb-3">
            <h2 class="border-bottom pb-2 border-bottom-3 border-primary mb-3">Bank Detail Primary</h2>
          </div>
          @foreach($result->banks as $index => $bank)
          @if($bank->primary == "1")
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label">NTN Number</label>
              <input hidden type="text" class="form-control" name="ntn" value="{{ $bank->ntn }}">
              <h5 class="border-primary" style="word-break: break-word; height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem;">
              {{ $bank->ntn  ?? '-'}}
              </h5>
            </div>
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label">Bank</label>
              <input hidden type="text" class="form-control" name="bank" value="{{ $bank->bank }}">
              <h5 class="border-primary" style="word-break: break-word; height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem;">
              {{ $bank->bank  ?? '-'}}
              </h5>
            </div>
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label required">Bank Branch</label>
              <input hidden type="text" class="form-control" name="bank_branch" id="bank_branch" value="{{old('bank_branch',$bank->bank_branch)}}">
              <h5 class="border-primary" style="word-break: break-word; height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem;">
                {{old('bank_branch',$bank->bank_branch)  ?? '-'}}
              </h5>
            </div>
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label required">Account No</label>
              <input hidden type="text" class="form-control" name="account_no" id="account_no" value="{{old('account_no',$bank->account_no)}}">
              <h5 class="border-primary" style="word-break: break-word; height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem;">
                {{old('account_no',$bank->account_no)  ?? '-'}}
              </h5>
            </div>
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label">Bank Title</label>
              <input hidden type="text" class="form-control" name="bank_title" value="{{ $bank->bank_title }}">
              <h5 class="border-primary" style="word-break: break-word; height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem;">
                {{ $bank->bank_title  ?? '-' }}
              </h5>
            </div>
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label">Bank Address</label>
              <input hidden type="text" class="form-control" name="bank_address" value="{{ $bank->bank_address }}">
              <h5 class="border-primary" style="word-break: break-word; height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem;">
                {{ $bank->bank_address  ?? '-' }}
              </h5>
            </div>
          @endif
          @endforeach
        </div>
        <div class="row">
          <div class="col-12 mb-3">
            <h2 class="border-bottom pb-2 border-bottom-3 border-primary mb-3">Vendor Items</h2>
          </div>
          @foreach($result->items as $item)
          <div class="row">
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label">Category</label>
              <input hidden type="text" class="form-control" name="item_category" value="{{ $item->itemCategory->name }}">
              <h5 class="border-primary" style="word-break: break-word; height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem;">
                {{ $item->itemCategory->name  ?? '-' }}
              </h5>
            </div>
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label">Name</label>
              <input hidden type="text" class="form-control" name="item_name" value="{{ $item->name }}">
              <h5 class="border-primary" style="word-break: break-word; height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem;">
                {{ $item->name  ?? '-' }}
              </h5>
            </div>
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label">Description</label>
              <input hidden type="text" class="form-control" name="item_description" value="{{ $item->description }}">
              <h5 class="border-primary" style="word-break: break-word; height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem;">
              {{ $item->description  ?? '-' }}
              </h5>
            </div>
          </div>
          @endforeach
        </div>

        <!--  -->
        @if(count($result->contactPersons) != 0)
        <div class="row">
          @foreach($result->contactPersons as $index => $contactPerson)
          @if($contactPerson->primary != "1")
          @if($index == 0)
          <div class="border-bottom border-2 pb-2 my-2 d-flex justify-content-between">
            <h2>Vendor Secondary Contact Persons</h2>
          </div>
          @endif

          <div class="row">
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label">Name</label>
              <input hidden type="text" class="form-control" name="contact_person_name" value="{{ $contactPerson->name }}">
              <h5 class="border-primary" style="word-break: break-word; height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem;">
              {{ $contactPerson->name  ?? '-' }}
              </h5>
            </div>
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label">Email</label>
              <input hidden type="text" class="form-control" name="contact_person_email" value="{{ $contactPerson->email }}">
              <h5 class="border-primary" style="word-break: break-word; height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem;">
              {{ $contactPerson->email  ?? '-' }}
              </h5>
            </div>
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label">Contact Number</label>
              <input hidden type="text" class="form-control" name="contact_number" value="{{ $contactPerson->contact_number }}">
              <h5 class="border-primary" style="word-break: break-word; height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem;">
              {{ $contactPerson->contact_number  ?? '-' }}
              </h5>
            </div>
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label">Office Number</label>
              <input hidden type="text" class="form-control" name="contact_number" value="{{ $contactPerson->office_number }}">
              <h5 class="border-primary" style="word-break: break-word; height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem;">
              {{ $contactPerson->office_number  ?? '-' }}
              </h5>
            </div>
          </div>
          @endif
          @endforeach
        </div>
        @endif

        <!--  -->
        @if(count($result->banks) != 0)
        <div class="row">
          @foreach($result->banks as $index => $bank)
          @if($index == 1)
          <div class="border-bottom border-2 pb-2 my-2 d-flex justify-content-between">
            <h2>Vendor Secondary Bank</h2>
          </div>
          @endif
          @if($bank->primary != "1")

          <div class="row">
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label">NTN</label>
              <input hidden type="text" class="form-control" value="{{ $bank->ntn }}">
              <h5 class="border-primary" style="word-break: break-word; height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem;">
              {{ $bank->ntn  ?? '-' }}
              </h5>
            </div>
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label">Account No</label>
              <input hidden type="text" class="form-control" value="{{ $bank->account_no }}">
              <h5 class="border-primary" style="word-break: break-word; height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem;">
              {{ $bank->account_no  ?? '-' }}
              </h5>
            </div>
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label">Bank Title</label>
              <input hidden type="text" class="form-control" value="{{ $bank->bank_title }}">
              <h5 class="border-primary" style="word-break: break-word; height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem;">
              {{ $bank->bank_title  ?? '-' }}
              </h5>
            </div>
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label">Bank</label>
              <input hidden type="text" class="form-control" value="{{ $bank->bank }}">
              <h5 class="border-primary" style="word-break: break-word; height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem;">
              {{ $bank->bank  ?? '-' }}
              </h5>
            </div>
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label">Bank Branch</label>
              <input hidden type="text" class="form-control" value="{{ $bank->bank_branch }}">
              <h5 class="border-primary" style="word-break: break-word; height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem;">
              {{ $bank->bank_branch  ?? '-' }}
              </h5>
            </div>
            <div class="col-md-3 col-lg-3 mb-5">
              <label class="form-label">Bank Address</label>
              <textarea hidden type="text" class="form-control">{{ $bank->bank_address }}</textarea>
              <h5 class="border-primary" style="word-break: break-word; height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem;">
              {{ $bank->bank_address ?? '-' }}
              </h5>
            </div>
          </div>
          @endif
          @endforeach
        </div>
        @endif
      </div>
      <!--end::Card-->
    </div>
    <!--end::Container-->
  </div>
  <!--end::Post-->
</div>
@endsection

@push('scripts')
<script>
  $(document).ready(function() {
    $('#country_id').change(function() {
      $('#city_id_loader').addClass("d-flex").removeClass("d-none");
      let currentCity = '<?php echo $result->city_id  ?>'
      console.log(currentCity)
      let portDD = `<option value=""></option>`
      let data = {
        id: $(this).val(),
      };

      $.get("/fetch-get-cities", data, function(response) {
        response?.cities?.forEach(function(item) {
          portDD += `<option ${currentCity == item.id ? "selected": ""} value="${item.id}">${item.name}</option>`;
        });
        $('#city_id').html(portDD);
        $('#city_id_loader').addClass("d-none").removeClass("d-flex");
        // $('#portDD').html(portDD);
      });

    });
    var contactCounter = parseInt($('#total_contact').val());
    var supplyCounter = parseInt($('#total_supply').val());

    // Function to check and toggle the visibility of the "Other Contacts" section
    function toggleOtherContactsVisibility() {
      if ($('#total_contact').val() == 1) {
        $('#other-contacts').hide(); // Hide if no contact rows
      } else {
        $('#other-contacts').show(); // Show if there are contact rows
      }
    }

    function toggleOtherSupplyVisibility() {
      if ($('#total_supply').val() == 1) {
        $('#other-supplies').hide(); // Hide if no supply rows
      } else {
        $('#other-supplies').show(); // Show if there are supply rows
      }
    }

    // Initialize visibility on page load
    toggleOtherContactsVisibility();
    toggleOtherSupplyVisibility();

    // Function to add more contact fields
    $('#add-more-contact').click(function() {
      // Increment total contact count
      contactCounter++;
      $('#total_contact').val(contactCounter);

      // Create a new row for contact input
      var newContactRow = `<div class="col-6 contact-row">
      <div class="row">
        <div class="col-md-6 mb-5">
          <label class="form-label">Contact Person ${contactCounter}</label>
          <input required type="text" class="form-control" name="contact_person_${contactCounter}" id="contact_person_${contactCounter}" value="">
        </div>
        <div class="col-md-6 mb-5">
          <div class="d-flex justify-content-between align-items-center">
            <label class="form-label">Contact Number ${contactCounter}</label>
            <i class="fa-regular fa-trash-can text-danger cursor-pointer remove-contact" style="cursor: pointer;"></i>
          </div>
          <input required type="text" class="form-control" name="contact_number_${contactCounter}" id="contact_number_${contactCounter}" value="">
        </div>
      </div></div>`;

      // Append the new row to other contacts
      $('#other-contacts > .row').append(newContactRow);

      // Toggle visibility after adding a new row
      toggleOtherContactsVisibility();
    });

    // Function to remove a contact row
    $(document).on('click', '.remove-contact', function() {
      $(this).closest('.contact-row').remove();

      // Decrement total contact count
      contactCounter--;
      $('#total_contact').val(contactCounter);

      // Toggle visibility after removing a row
      toggleOtherContactsVisibility();
    });
    $('#add-more-supply').on('click', function() {
      let supply = parseInt($("#total_supply").val())
      $("#total_supply").val(++supply);
      let data = {
        id: supply,
      };
      $.get("/fetch-get-supply-category", data, function(response) {
        var moreSupply = response?.currentSupply;
        console.log({
          moreSupply
        })
        $('#other-supplies > .row').append(moreSupply);
        $('select').select2();
        toggleOtherSupplyVisibility();
      })
    });
    $(document).on('click', '.remove-supply', function() {
      $(this).closest('.supply-row').remove();

      // Decrement total supply count
      supplyCounter--;
      $('#total_supply').val(contactCounter);

      // Toggle visibility after removing a row
      toggleOtherSupplyVisibility();
    });

    function defaultFunction() {
      $('#country_id').trigger("change")
    }
    defaultFunction()
  });
</script>

@endpush