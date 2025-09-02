@push('styles')
<link href="{{asset('assets/plugins/custom/datatables/datatables.bundle.css')}}" rel="stylesheet" type="text/css" />
@endpush
<!--begin::Permissions-->
<div class="fv-row">
<!--begin::Label-->
<label class="fs-5 fw-bold form-label mb-2">Role Permissions</label>
<!--end::Label-->
<!--begin::Table wrapper-->
<div class="table-responsive">
    <!--begin::Table-->
    <table class="table align-middle table-row-dashed fs-6 gy-5">
        <!--begin::Table body-->
        <tbody class="text-gray-600 fw-semibold">
            <!--begin::Table row-->
            <tr>
                <td class="text-gray-800">Administrator Access 
                <span class="ms-2" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-html="true" data-bs-content="Allows a full access to the system">
                    <i class="ki-outline ki-information fs-7"></i>
                </span></td>
                <td>
                    <!--begin::Checkbox-->
                    <label class="form-check form-check-custom form-check-solid me-9">
                        <input class="form-check-input" type="checkbox" value="" id="kt_roles_select_all" />
                        <span class="form-check-label" for="kt_roles_select_all">Select all</span>
                    </label>
                    <!--end::Checkbox-->
                </td>
            </tr>
            <!--end::Table row-->
            @foreach($modules as $module)
                <!--begin::Table row-->
                <tr>
                    <!--begin::Label-->
                    <td class="text-gray-800">{{ $module->name }}</td>
                    <!--end::Label-->
                    <!--begin::Options-->
                    <td>
                        <!--begin::Wrapper-->
                        <div class="d-flex">
                            @foreach($module->permissions as $permission)
                            <!--begin::Checkbox-->
                            <label class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">
                                <input class="form-check-input module-checkbox" 
                                    type="checkbox" 
                                    value="{{ $permission->id }}" 
                                    name="modules[{{ $module->id }}][]" 
                                    {{ is_array(old('modules.' . $module->id)) && in_array($permission->id, old('modules.' . $module->id)) ? 'checked' : '' }} />
                                <span class="form-check-label">{{ $permission->name }}</span>
                            </label>
                            <!--end::Checkbox-->
                            @endforeach
                        </div>
                        <!--end::Wrapper-->
                    </td>
                    <!--end::Options-->
                </tr>
                <!--end::Table row-->
            @endforeach
        </tbody>
        <!--end::Table body-->
    </table>
    <!--end::Table-->
</div>
<!--end::Table wrapper-->
</div>
<!--end::Permissions-->
@push('scripts')
    <script src="{{asset('assets/plugins/custom/datatables/datatables.bundle.js')}}"></script>
    <script src="{{asset('assets/js/custom/apps/user-management/roles/list/add.js')}}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selectAllCheckbox = document.getElementById('kt_roles_select_all');
            const moduleCheckboxes = document.querySelectorAll('.module-checkbox');
            selectAllCheckbox.addEventListener('change', function () {
                moduleCheckboxes.forEach(function (checkbox) {
                    checkbox.checked = selectAllCheckbox.checked;
                });
            });
            moduleCheckboxes.forEach(function (checkbox) {
                checkbox.addEventListener('change', function () {
                    if (!checkbox.checked) {
                        selectAllCheckbox.checked = false;
                    }
                });
            });
            moduleCheckboxes.forEach(function (checkbox) {
                checkbox.addEventListener('change', function () {
                    const allChecked = Array.from(moduleCheckboxes).every(checkbox => checkbox.checked);
                    if (allChecked) {
                        selectAllCheckbox.checked = true;
                    }
                });
            });
        });
    </script>
@endpush