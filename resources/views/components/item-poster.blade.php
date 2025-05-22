@props(['placeholder' => false, 'poster_path', 'size'])

<img src={{ $placeholder ? env('POSTER_PLACEHOLDER') : "https://image.tmdb.org/t/p/$size" . $poster_path }} class="rounded-xl max-w-[185px]" />
