@php
    $currentSegment = Request::segment(1);
    $isActive = $currentSegment == $module['slug'];
    $isChildActive = $module->children->contains(function ($child) use ($currentSegment) {
        return $currentSegment == $child['slug'];
    });
@endphp

<div class="menu-item parent {{ $module->children->isNotEmpty() ? 'menu-accordion' : '' }} {{ $isActive || $isChildActive ? 'here show' : '' }}" data-kt-menu-trigger="{{ $module->children->isNotEmpty() ? 'click' : '' }}">

    <!-- Parent Module with Children -->
    @if ($module->children->isNotEmpty())
        <div class="menu-link" data-toggle="collapse" data-target="#module-{{ $module['id'] }}">
            <span class="menu-bullet">
                @if($module['icon'])
                    <span class="{{ $module['icon'] }}"></span>
                @else
                    <span class="bullet bullet-dot"></span>
                @endif
            </span>
            <span class="menu-title">{{ $module['name'] }}</span>
            <span class="menu-arrow"></span>
        </div>

        <!-- Collapsible Menu for Children -->
        <div id="module-{{ $module['id'] }}" class="menu-sub menu-sub-accordion collapse {{ $isChildActive ? 'show' : '' }}">
            @foreach ($module->children as $child)
                @if(hasPermissionForModule('view', $module['id']))
                    @include('layout.modules', ['module' => $child])
                @endif
            @endforeach
        </div>
    @else
        <!-- Regular Module Link -->
        @if(hasPermissionForModule('view', $module['id']))
            <a class="menu-link ms-5 {{ $isActive ? 'active' : '' }}" href="{{ url($module['slug']) }}">
                <span class="menu-bullet">
                    @if($module['icon'])
                      <span class="{{ $module['icon'] }}"></span>
                    @else
                      <span class="bullet bullet-dot"></span>
                    @endif
                </span>
                <span class="menu-title"> {{ $module['name'] }}</span>
            </a>
        @endif
    @endif
</div>