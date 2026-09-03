<script setup>
import Layout from "@/Layouts/Layout.vue";
import MoviePoster from "../Components/MoviePoster.vue";
import SeriesPoster from "../Components/SeriesPoster.vue";
import CastMembers from "../Components/CastMembers.vue";
import { computed } from "vue";

const props = defineProps({
    castMember: {
        type: CastMembers,
        required: true,
    },
    purchasedMemberMovies: {
        type: Array,
        required: true,
    },
    wishlistedMemberMovies: {
        type: Array,
        required: true,
    },
    memberSeries: {
        type: Array,
        required: true,
    },
    page_title: {
        type: String,
        default: "Cast Member",
    },
});
const castMemberProfileImage = computed(() => {
	if ( props.castMember.profile_path ) return "https://image.tmdb.org/t/p/w185/" + props.castMember.profile_path;
	return config('tmdb.placeholder.poster');

});
</script>

<template>
    <Layout :heading="page_title">
		<div class="md:grid md:grid-cols-[185px_1fr] gap-4">
			<div>
				<img :src="castMemberProfileImage" :alt="`Profile pic of ${castMember.name}`" class="border border-gray-900 rounded-xl max-w-[185]">
			</div>
			<div class="grid grid-cols-[repeat(auto-fill,minmax(187px,1fr))] place-items-center items-start gap-4">
				<h2 class="col-span-full text-2xl font-bold text-left w-full" v-if="purchasedMemberMovies.length">Purchased Movies</h2>
				<span v-for="movie in purchasedMemberMovies" >
					<MoviePoster :key="movie.slug" :movie="movie" :href="route('movies.show', movie)" />
					{{ movie.character }}
				</span>
				<h2 class="col-span-full text-2xl font-bold text-left w-full mt-4" v-if="wishlistedMemberMovies.length">Wishlist Movies</h2>
				<span v-for="movie in wishlistedMemberMovies" >
					<MoviePoster :key="movie.slug" :movie="movie" :href="route('movies.show', movie)" />
					{{ movie.character }}
				</span>
				<h2 class="col-span-full text-2xl font-bold text-left w-full mt-4" v-if="memberSeries.length">Series</h2>
				<span v-for="series in memberSeries" >
					<SeriesPoster :key="series.slug" :series="series" :href="route('series.show', series)" />
					{{ series.character ?? '' }}
				</span>
			</div>
		</div>
    </Layout>
</template>
