<div class="menu-item menu-accordion"
     data-kt-menu-trigger="click">

    <span class="menu-link">
        <span class="menu-icon">
            <i class="{{ $categoria->icon }} fs-3"></i>
        </span>
        <span class="menu-title text-dark fs-5">{{ $categoria->name }}</span>
        <span class="menu-arrow"></span>
    </span>

    <div class="menu-sub menu-sub-accordion">
        {{-- Opciones --}}
        @foreach($categoria->options as $opcion)
            @if($opcion->permissions->isNotEmpty())
                <div class="menu-item">
                    <a class="menu-link" onclick="cargarRuta('{{ url($opcion->link) }}', 'kt_app_content_container', this);" href="javascript:void(0);">
                        <span class="menu-bullet">
                            <i class="{{ $opcion->icon }} fs-5"></i>
                        </span>
                        <span class="menu-title text-dark fs-6">{{ $opcion->name }}</span>
                    </a>
                </div>
            @endif
        @endforeach

        {{-- Hijos --}}
        @foreach($categoria->children as $child)
            <x-menu-item :categoria="$child"/>
        @endforeach
    </div>
</div>
