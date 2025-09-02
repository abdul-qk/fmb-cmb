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
        <form class="form" method="POST" action="{{ route($store) }}" enctype="multipart/form-data">
          @csrf
          <div class="row">
            <div class="col-12 my-3">
              <h2 class="border-bottom pb-2 border-bottom-3 border-primary mb-3">Personal Details</h2>
            </div>
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label">Full Name</label>
              <h5 style=" word-break: break-word;height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem; ">
                {{ $user->name }}
              </h5>
              <!-- <input required type="text" class="form-control" name="full_name" id="full_name" value="{{ old('full_name') }}"> -->
              <input type="number" hidden class="form-control" name="user_id" value="{{ $userId }}">
              <!-- @if ($errors->has('full_name'))
              <span class="text-danger">{{ $errors->first('full_name') }}</span>
              @endif -->
            </div>
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label required">Country</label>
              <select class="form-select " id="country_id" name="country_id" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                <option value=""></option>
                @foreach($countries as $country)
                <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                @endforeach
              </select>
              @if ($errors->has('country_id'))
              <span class="text-danger">{{ $errors->first('country_id') }}</span>
              @endif
            </div>
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label required">City</label>
              <div class="position-relative">
                <div id="city_id_loader" class="align-items-center bg-gray-100 d-none h-100 justify-content-center position-absolute start-50 top-50 translate-middle w-100 z-index-2">
                  <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                  </div>
                </div>
                <select class="form-select form-select-solid" id="city_id" name="city_id" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                  <option value=""></option>
                </select>
              </div>
              @if ($errors->has('city_id'))
              <span class="text-danger">{{ $errors->first('city_id') }}</span>
              @endif
            </div>
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label">Complete Address</label>
              <input type="text" class="form-control" name="complete_address" id="complete_address" value="{{ old('complete_address') }}">
              @if ($errors->has('complete_address'))
              <span class="text-danger">{{ $errors->first('complete_address') }}</span>
              @endif
            </div>
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label">Photo</label>
              <input type="file" class="form-control" name="photo" id="photo">
            </div>
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label">National Identity</label>
              <input type="text" class="form-control" name="national_identity" id="national_identity" value="{{ old('national_identity') }}" minlength="8" maxlength="15">
              @if ($errors->has('national_identity'))
              <span class="text-danger">{{ $errors->first('national_identity') }}</span>
              @endif
            </div>
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label">Upload National Identity <span class="text-danger">(jpeg, png, jpg) </span></label>
              <input type="file" class="form-control" name="upload_national_identity[]" id="upload_national_identity" multiple>
            </div>
          </div>

          <!-- Contact Details Section -->
          <div class="row">
            <div class="col-12 mb-3">
              <h2 class="border-bottom pb-2 border-bottom-3 border-primary mb-3 d-flex justify-content-between align-items-center">
                Contact
                <div>
                  <button type="button" class="btn btn-primary" onclick="addMoreContact()">Add Contact</button>
                  <button type="button" class="btn btn-primary" onclick="addMoreEmail()">Add Email</button>
                </div>
              </h2>
            </div>
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label">Email</label>
              <h5 class="mb-0" style=" word-break: break-word;height: 43px; border-bottom: 1px solid #ddd; display: flex; align-items: center; color: #4b5675; font-size: 1.1rem; ">
                {{ $user->email }}
              </h5>
              <div id="contact-details-email">
              @foreach (old('emails', []) as $index => $email)
              <div class="row mt-4" id="email-{{ $index }}">
                <div class="col-md-12 mb-5">
                  <div class='d-flex justify-content-between align-items-center'>
                    <label class="form-label required"><span> Email Address {{$index}}</span> </label>
                    <i  onclick="removeEmail(this)" class="cursor-pointer text-danger remove-email bi bi-trash"></i>
                  </div>
                  <input type="email" class="form-control" name="emails[{{ $index }}][email]" placeholder="Email Address" value="{{ old("emails.$index.email", $email['email'] ?? '') }}" required>
                </div>
              </div>
              @endforeach
            </div>
            </div>
            <div class="col-md-6">
              @foreach(old('contacts', [['contact_types' => '', 'contact_numbers' => '']]) as $index => $contact)
              <div class="row" id="contact-{{ $index }}">
                <div class="col-6 mb-5">
                  <label class="form-label required">Contact Type  {{($index) == 0 ? "": ($index + 1)}}</label>
                  <select required class="form-select" name="contacts[{{ $index }}][contact_types]" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                    <option value=""></option>
                    <option value="mobile" {{ old("contacts.$index.contact_types") == 'mobile' ? 'selected' : '' }}>Mobile</option>
                    <option value="home" {{ old("contacts.$index.contact_types") == 'home' ? 'selected' : '' }}>Home</option>
                  </select>
                  @if ($errors->has("contacts.$index.contact_types"))
                  <span class="text-danger">{{ $errors->first("contacts.$index.contact_types") }}</span>
                  @endif
                </div>
                <div class="col-6 mb-5">
                  <div class='d-flex justify-content-between align-items-center'>
                    <label class="form-label required"><span> Contact Number {{$index == 0 ? "": ($index + 1)}}</span> </label>
                    @if(($index + 1) != 1) 
                      <i onclick="removeContact(this)" class="cursor-pointer text-danger remove-email bi bi-trash"></i>
                    @endif
                  </div>
                  <input required type="text" class="form-control" name="contacts[{{ $index }}][contact_numbers]" placeholder="Contact Number" value="{{ old("contacts.$index.contact_numbers") }}">
                  @if ($errors->has("contacts.$index.contact_numbers"))
                  <span class="text-danger">{{ $errors->first("contacts.$index.contact_numbers") }}</span>
                  @endif
                </div>
              </div>
              @endforeach
              <div id="contact-details"></div>
            </div>
            
            
          </div>

          <!-- Work Details Section -->
          <div class="row">
            <div class="col-12 my-3">
              <h2 class="border-bottom pb-2 border-bottom-3 border-primary mb-3">Work</h2>
            </div>
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label">Designation</label>
              <select class="form-select" name="working_designation" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                @foreach($designations as $designation)
                <option></option>
                <option value="{{ $designation->id }}" {{ old('working_designation') == $designation->id ? 'selected' : '' }}>{{ $designation->name }}</option>
                @endforeach
              </select>
              @if ($errors->has('working_designation'))
              <span class="text-danger">{{ $errors->first('working_designation') }}</span>
              @endif
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Responsibilities</label>
              <textarea class="form-control" name="responsibilities" id="responsibilities">{{ old('responsibilities') }}</textarea>
            </div>
          </div>

          <!-- Education Details Section -->
          <div class="row">
            <div class="col-12 mb-3">
              <h2 class="border-bottom pb-2 border-bottom-3 border-primary mb-3">Last Education</h2>
            </div>
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label">Education</label>
              <select class="form-select" name="education_id" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                @foreach($educations as $education)
                <option></option>
                <option value="{{ $education->id }}" {{ old('education_id') == $education->id ? 'selected' : '' }}>
                  {{ $education->name }}
                </option>
                @endforeach
              </select>
              @if ($errors->has('education_id'))
              <span class="text-danger">{{ $errors->first('education_id') }}</span>
              @endif
            </div>
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label">Status</label>
              <select class="form-select" name="status" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                <option value=""></option>
                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="just_started" {{ old('status') == 'just_started' ? 'selected' : '' }}>Just Started</option>
                <option value="about_to_start" {{ old('status') == 'about_to_start' ? 'selected' : '' }}>About to Start</option>
              </select>
              @if ($errors->has('status'))
              <span class="text-danger">{{ $errors->first('status') }}</span>
              @endif
            </div>
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label">Start Year</label>
              <input value="{{ old('start_year') }}" type="number" class="form-control" name="start_year" placeholder="Start Year" min="1900" max="2099" step="1">
              @if ($errors->has('start_year'))
              <span class="text-danger">{{ $errors->first('start_year') }}</span>
              @endif
            </div>
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label">End Year</label>
              <input value="{{ old('end_year') }}" type="number" class="form-control" name="end_year" placeholder="End Year" min="1900" max="2099" step="1">
              @if ($errors->has('end_year'))
              <span class="text-danger">{{ $errors->first('end_year') }}</span>
              @endif
            </div>
          </div>

          <!-- Past Experience Section -->
          <div class="row" id="past-experience">
            <div class="col-12 mb-3">
              <h2 class="border-bottom pb-2 border-bottom-3 border-primary mb-3 d-flex justify-content-between align-items-center">
                Past Experience
                <div>
                  <button type="button" class="btn btn-primary" onclick="addMoreExperience()">Add Experience</button>
                </div>
              </h2>

            </div>
            @foreach(old('experiences', [['company' => '', 'years' => '', 'designation' => '']]) as $index => $experience)
            <div class="row mb-4" id="experience-{{ $index }}">
              <div class="col-md-4 col-lg-3 mb-5">
                <label class="form-label">Company/Organization {{$index == 0 ? "": $index}}</label>
                <input type="text" class="form-control" name="experiences[{{ $index }}][company]" placeholder="Company/Organization" value="{{ old("experiences.$index.company") }}">
                @if ($errors->has("experiences.$index.company"))
                <span class="text-danger">The experiences company field is required.</span>
                @endif
              </div>
              <div class="col-md-4 col-lg-3 mb-5">
                <label class="form-label">No. of Years {{$index == 0 ? "": $index}}</label>
                <input type="number" min="0" class="form-control" name="experiences[{{ $index }}][years]" placeholder="No. of Years" value="{{ old("experiences.$index.years") }}">
                @if ($errors->has("experiences.$index.years"))
                <span class="text-danger">The experiences years field is required.</span>
                @endif
              </div>
              <div class="col-md-4 col-lg-3 mb-5">
                <div class='d-flex justify-content-between align-items-center'>
                  <label class="form-label"><span> Designation {{$index == 0 ? "": $index}}</span> </label>
                  @if($index != 0) 
                    <i  onclick="removeExperience(this)" class="cursor-pointer text-danger remove-email bi bi-trash"></i>
                  @endif
                </div>
                <select class="form-select" name="experiences[{{ $index }}][designation]" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                  <option></option>
                  @foreach($designations as $designation)
                  <option value="{{ $designation->id }}" {{ old("experiences.$index.designation") == $designation->id ? 'selected' : '' }}>{{ $designation->name }}</option>
                  @endforeach
                </select>
                @if ($errors->has("experiences.$index.designation"))
                <span class="text-danger">The experiences designation field is required.</span>
                @endif
              </div>
            </div>
            @endforeach
          </div>

          <!-- Misc Documents Upload -->
          <div class="row">
            <div class="col-md-4 mb-5">
              <label class="form-label">Upload Misc Documents <span class="text-danger">(pdf, doc, docx, jpeg, png, jpg)</span></label>
              <input type="file" class="form-control" name="misc_documents[]" multiple>
            </div>
          </div>

          <!-- Medical Information Section -->
          <div class="row mt-4">
            <div class="col-12 mb-3">
              <h2 class="border-bottom pb-2 border-bottom-3 border-primary mb-3">Medical Information</h2>
            </div>
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label">Disease</label>
              <input type="text" class="form-control" value="{{ old('disease') }}" name="disease" placeholder="Disease">
            </div>
            <div class="col-md-4 col-lg-3 mb-5 ">
              <label class="form-label">Treatment</label>
              <input type="text" class="form-control" name="treatment" value="{{ old('treatment') }}" placeholder="Treatment">
            </div>
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label">No. of Years</label>
              <input type="number" min="1" class="form-control" name="no_of_years" value="{{ old('no_of_years') }}" placeholder="No. of Years">
            </div>
            <div class="col-md-4 col-lg-3 mb-5">
              <label class="form-label">Upload Medical Documents <span class="text-danger">(pdf, jpeg, png, jpg)</span></label>
              <input type="file" class="form-control" name="medical_documents[]" multiple>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12 mt-3">
              <input type="reset" value="Reset" class="btn btn btn-dark w-100px mr-2" style="margin-right: 5px">
              <input type="submit" value="Create" class="btn btn-primary hover-elevate-up w-100px">
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  $('#country_id').change(function() {
    $('#city_id_loader').addClass("d-flex").removeClass("d-none");
    let portDD = `<option value=""></option>`
    let data = {
      id: $(this).val(),
    };
    let cities_id = "{{old('city_id')}}";
    console.log({cities_id})

    $.get("/fetch-get-cities", data, function(response) {
      response?.cities?.forEach(function(item) {
        portDD += `<option value="${item.id}" ${cities_id == item.id ? "selected":''}>${item.name}</option>`;
      });
      $('#city_id').html(portDD);
      $('#city_id_loader').addClass("d-none").removeClass("d-flex");
      // $('#portDD').html(portDD);
    });

  });
  let emailCount = "{{ count(old('emails', [['contact_types' => '', 'contact_numbers' => '']])) }}"; // Initialize a counter for emails
  let contactCount = "{{ count(old('contacts', [['contact_types' => '', 'contact_numbers' => '']])) }}";
  let experienceCount = "{{ count(old('experiences', [['company' => '', 'years' => '', 'designation' => '']])) }}";

  function addMoreEmail() {
    emailCount++;
    const emailDiv = `
        <div class="row mb-4" id="email-${emailCount}">
            <div class="col-md-12 mt-5">
              <div class='d-flex justify-content-between align-items-center'>
                <label class="form-label required"><span> Email Address ${emailCount}</span> </label>
                <i  onclick="removeEmail(this)" class="cursor-pointer text-danger remove-email bi bi-trash"></i>
              </div>
              <input type="email" class="form-control" name="emails[${emailCount}][email]" placeholder="Email Address" required>
            </div>
        </div>
    `;
    document.getElementById('contact-details-email').insertAdjacentHTML('beforeend', emailDiv);
  }

  function removeEmail(button) {
    const emailDiv = button.closest('.row'); // Get the parent row of the button
    emailDiv.remove(); // Remove the emailDiv from the DOM
  }

  function addMoreExperience() {
    experienceCount++;
    const experienceDiv = `
        <div class="row mb-4" id="experience-${experienceCount}">
            <div class="col-md-4 col-lg-3 mb-5">
                <label class="form-label">Company/Organization</label>
                <input type="text" class="form-control" name="experiences[${experienceCount}][company]" placeholder="Company/Organization">
            </div>
            <div class="col-md-4 col-lg-3 mb-5">
                <label class="form-label">No. of Years</label>
                <input type="number" min="0" class="form-control" name="experiences[${experienceCount}][years]" placeholder="No. of Years">
            </div>
            <div class="col-md-4 col-lg-3 mb-5">
                <div class='d-flex justify-content-between align-items-center'>
                  <label class="form-label"><span> Designation</span> </label>
                  <i  onclick="removeExperience(this)" class="cursor-pointer text-danger remove-email bi bi-trash"></i>
                </div>
                <select class="form-select" name="experiences[${experienceCount}][designation]" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                    <option></option>
                    @foreach($designations as $designation)
                    <option value="{{ $designation->id }}">{{ $designation->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    `;
    document.getElementById('past-experience').insertAdjacentHTML('beforeend', experienceDiv);
    $('select').select2();
  }

  function removeExperience(button) {
    const experienceDiv = button.closest('.row'); // Get the parent row of the button
    experienceDiv.remove(); // Remove the experienceDiv from the DOM
  }

  function removeExperience(button) {
    const experienceDiv = button.closest('.row'); // Get the parent row of the button
    experienceDiv.remove(); // Remove the experienceDiv from the DOM
  }

  function removeContact(button) {
    const contactDiv = button.parentElement.parentElement.parentElement; // Get the parent div of the button
    const container = document.getElementById('contact-details');

    // Remove the contactDiv from the container
    container.removeChild(contactDiv);

    contactCount--;
  }

  function updateContactLabels() {
    const contactDivs = document.querySelectorAll('#contact-details > div');

    contactDivs.forEach((div, index) => {
      const typeLabel = div.querySelector('.col-6:nth-of-type(1) label'); // First label for Contact Type
      const numberLabel = div.querySelector('.col-6:nth-of-type(2) label'); // Second label for Contact Number

      // Ensure that typeLabel and numberLabel exist before trying to set their textContent
      if (typeLabel) {
        typeLabel.textContent = `Contact Type ${(index + 1) +1}`; // Update label for Contact Type
      }

      if (numberLabel) {
        numberLabel.textContent = `Contact Number ${(index + 1) +1}`; // Update label for Contact Number
      }
    });
  }

  function addMoreContact() {
    const container = document.getElementById('contact-details');
    const newDiv = document.createElement('div');
    newDiv.classList.add('row', 'mb-4');
    newDiv.id = `contact-${contactCount}`;

    newDiv.innerHTML = `
            <div class="col-md-6 mb-5">
                <label class="form-label required">Contact Type ${Number(contactCount) + 1}</label>
                <select required class="form-select" name="contacts[${contactCount}][contact_types]" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                  <option value=""></option>
                  <option value="mobile">Mobile</option>
                  <option value="home">Home</option>
                </select>
            </div>
            <div class="col-md-6 mb-5">
                  <div class='d-flex justify-content-between align-items-center'>
                    <label class="form-label required">Contact Number ${Number(contactCount) + 1}</label>
                    <i onclick="removeContact(this)" class="cursor-pointer text-danger remove-email bi bi-trash"></i>
                 </div>
                <input required type="text" class="form-control" name="contacts[${contactCount}][contact_numbers]" placeholder="Contact Number">
            </div>
        `;
    container.appendChild(newDiv);
    $('select').select2();
    contactCount++; // Increment the counter for the next contact
  }

  let anyError = <?php echo json_encode($errors->any()); ?>;
  console.log(<?php echo json_encode($errors->all()); ?>)

  if (anyError) {
    $('#country_id').trigger("change")
  }
</script>
@endpush