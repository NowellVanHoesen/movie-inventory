<form method="POST" action="{{ route('movies.create') }}">
	@csrf

	<div class="max-w-xl mx-auto">
		<div class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-4">

			<!-- movie title -->
			<x-form-field>
				<x-form-label for="query" :value="__('Title Search')" />

				<div class="mt-2">
					<x-form-input id="query" name="query" :value="old('query', $search_term ?? '')" minlength="2" required autofocus />
					<x-form-error name="query" />
				</div>
			</x-form-field>
			<x-form-field cols="2" class="col-start-1 col-end-3">
				<x-form-label for="year" :value="__('Year')" class="peer-required:italic" />
				<div class="mt-2">
					<x-form-input id="year" name="year" :value="old('year', $search_year ?? '')" autofocus />
					<x-form-error name="year" />
				</div>
			</x-form-field>
			<x-form-field cols="2" class="col-end-[-1] place-self-end mt-4 gap-x-4 max-w-xl">
				<x-form-button>{{ __('Search') }}</x-form-button>
			</x-form-field>
		</div>
	</div>
</form>
