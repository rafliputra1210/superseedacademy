@if ($paginator->hasPages())
    <nav class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 py-2 w-100" role="navigation" aria-label="Navigasi Halaman">
        {{-- Info Counter Data --}}
        <div class="text-muted small text-center text-md-start mb-0">
            Menampilkan
            <span class="fw-bold text-dark">{{ $paginator->firstItem() ?? 0 }}</span>
            sampai
            <span class="fw-bold text-dark">{{ $paginator->lastItem() ?? 0 }}</span>
            dari
            <span class="fw-bold text-dark">{{ $paginator->total() }}</span>
            data
        </div>

        {{-- Navigasi Angka Halaman --}}
        <div class="d-flex justify-content-center">
            <ul class="pagination pagination-sm mb-0 flex-wrap justify-content-center shadow-none">
                {{-- Tombol Sebelumnya --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled" aria-disabled="true" aria-label="Sebelumnya">
                        <span class="page-link shadow-none" aria-hidden="true">&laquo;</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link shadow-none" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Sebelumnya">&laquo;</a>
                    </li>
                @endif

                {{-- Elemen Halaman --}}
                @foreach ($elements as $element)
                    {{-- Separator Tiga Titik (...) --}}
                    @if (is_string($element))
                        <li class="page-item disabled" aria-disabled="true"><span class="page-link shadow-none">{{ $element }}</span></li>
                    @endif

                    {{-- Link Angka Halaman --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li class="page-item active" aria-current="page"><span class="page-link shadow-none">{{ $page }}</span></li>
                            @else
                                <li class="page-item"><a class="page-link shadow-none" href="{{ $url }}">{{ $page }}</a></li>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Tombol Berikutnya --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <a class="page-link shadow-none" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Berikutnya">&raquo;</a>
                    </li>
                @else
                    <li class="page-item disabled" aria-disabled="true" aria-label="Berikutnya">
                        <span class="page-link shadow-none" aria-hidden="true">&raquo;</span>
                    </li>
                @endif
            </ul>
        </div>
    </nav>
@endif
