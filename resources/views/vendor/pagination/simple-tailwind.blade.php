@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex gap-2 items-center justify-between">

        @if ($paginator->onFirstPage())
            <span class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 bg-cold-steel-800 border border-gray-600 cursor-not-allowed leading-5 rounded-md">
                {!! __('pagination.previous') !!}
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center px-4 py-2 text-sm font-medium border leading-5 rounded-md focus:outline-none focus:ring transition ease-in-out duration-150 text-cold-steel-200 active:text-cold-steel-300 hover:text-cold-steel-200 bg-cold-steel-800 active:bg-cold-steel-700 hover:bg-cold-steel-900 ring-cold-steel-300 border-cold-steel-600 focus:border-blue-700">
                {!! __('pagination.previous') !!}
            </a>
        @endif

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center px-4 py-2 text-sm font-medium border leading-5 rounded-md focus:outline-none focus:ring transition ease-in-out duration-150 text-cold-steel-200 active:text-cold-steel-300 hover:text-cold-steel-200 bg-cold-steel-800 active:bg-cold-steel-700 hover:bg-cold-steel-900 ring-cold-steel-300 border-cold-steel-600 focus:border-blue-700">
                {!! __('pagination.next') !!}
            </a>
        @else
            <span class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 bg-cold-steel-800 border border-gray-600 cursor-not-allowed leading-5 rounded-md">
                {!! __('pagination.next') !!}
            </span>
        @endif

    </nav>
@endif
