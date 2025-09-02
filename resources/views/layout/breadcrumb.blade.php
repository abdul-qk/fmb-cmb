<!--begin::Breadcrumb-->
<ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-1">
<!-- Home Link -->
<li class="breadcrumb-item text-muted">
    <a href="{{ url('dashboard') }}" class="text-muted text-hover-primary">Home</a>
</li>
@foreach (generateBreadcrumbs() as $breadcrumb)
    <!-- Bullet -->
    <li class="breadcrumb-item">
        <span class="bullet bg-gray-300 w-5px h-2px"></span>
    </li>
    
    <!-- Breadcrumb Item -->
    @if ($breadcrumb['active'])
        <li class="breadcrumb-item text-gray-900 text-capitalize">{{ $breadcrumb['name'] }}</li>
    @else
        <li class="breadcrumb-item text-muted text-capitalize">
            <a href="{{ $breadcrumb['url'] }}" class="text-muted text-hover-primary">{{ $breadcrumb['name'] }}</a>
        </li>
    @endif
@endforeach
</ul>
<!--end::Breadcrumb-->