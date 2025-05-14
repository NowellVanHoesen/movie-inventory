<form method="POST" action="{{ route('series.create') }}">
	@csrf

	<div class="max-w-xl mx-auto">
		<div class="border-b border-gray-900/10 pb-2">
			<div class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-4">

				<!-- series title -->
				<x-form-field>
					<x-form-label for="query" :value="__('Title Search')" />

					<div class="mt-2">
						<x-form-input id="query" name="query" :value="old('query', $search_term ?: '')" required autofocus />
						<x-form-error name="query" />
					</div>
				</x-form-field>
			</div>
		</div>
	</div>
	<div class="flex items-center justify-end mt-4 gap-x-4 max-w-xl mx-auto">
		<x-form-button>{{ __('Search') }}</x-form-button>
	</div>
</form>
